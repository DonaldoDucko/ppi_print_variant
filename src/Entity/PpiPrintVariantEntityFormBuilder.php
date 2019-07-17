<?php

namespace Drupal\ppi_print_variant\Entity;

use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Core\Form\FormState;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Builds entity forms.
 */
class PpiPrintVariantEntityFormBuilder extends EntityFormBuilder {

  /**
   * {@inheritdoc}
   */
  public function getForm(EntityInterface $original_entity, $operation = 'default', array $form_state_additions = array()) {

    // Create a duplicate of the node using the awesome createDuplicate() core function.
    /** @var \Drupal\node\Entity\Node $new_node */
    $new_node = $original_entity->createDuplicate();

    // Create duplicates of all translations of a node.
    foreach ($new_node->getTranslationLanguages() as $langcode => $language) {
      /** @var \Drupal\node\Entity\Node $translated_node */
      $translated_node = $new_node->getTranslation($langcode);
      $translated_node = $this->duplicateParagraphs($translated_node);
      \Drupal::moduleHandler()->alter('created_print_variant_node', $node, $field_name, $field_settings);
      $prepend_text = "";
      $config = \Drupal::config('ppi_print_variant.settings');
      if(!empty($config->get('text_to_prepend_to_title'))) {
        $prepend_text = $config->get('text_to_prepend_to_title') . " ";
      }
      $translated_node->setTitle(t($prepend_text . '@title', ['@title' => $original_entity->getTitle()], ['langcode' => $langcode]));
    }

    // Get the form object for the entity defined in entity definition
    $form_object = $this->entityManager->getFormObject($new_node->getEntityTypeId(), $operation);

    // Assign the form's entity to our duplicate!
    $form_object->setEntity($new_node);

    $form_state = (new FormState())->setFormState($form_state_additions);
    $new_form = $this->formBuilder->buildForm($form_object, $form_state);

    // If we are cloning addresses, we need to reset our delta counter
    // once the form is built.
    $tempstore = \Drupal::service('user.private_tempstore')->get('ppi_print_variant');
    if ($tempstore->get('address_initial_value_delta') != NULL) {
      $tempstore->set('address_initial_value_delta', NULL);
    }

    return $new_form;
  }

  /**
   * Create duplicates of the paragraphs of a designated node. If we do not duplicate the
   * paragraphs attached to the node, the linked paragraphs will be linked
   * to two nodes which is not ideal.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The node to duplicate.
   *
   * @return \Drupal\node\Entity\Node
   *   The node with duplicated paragraph fields.
   */
  public function duplicateParagraphs($node) {
    foreach ($node->getFieldDefinitions() as $field_definition) {
      $field_storage_definition = $field_definition->getFieldStorageDefinition();
      $field_settings = $field_storage_definition->getSettings();
      $field_name = $field_storage_definition->getName();

      if (isset($field_settings['target_type']) && $field_settings['target_type'] == "paragraph") {
        if (!$node->get($field_name)->isEmpty()) {
          $i = 0;
          $values = $node->get($field_name);
          foreach ($values as $value) {
            if ($value->entity) {
              // Handle only supported paragraph types (image, text) and remove all others
              $type = $value->entity->getType();
              if ($this->isSupportedParagraphType($type)) {
                $value->entity = $value->entity->createDuplicate();

                foreach($value->entity->getFieldDefinitions() as $field_definition) {
                  $field_storage_definition = $field_definition->getFieldStorageDefinition();
                  $pfield_settings = $field_storage_definition->getSettings();
                  $pfield_name = $field_storage_definition->getName();
                  \Drupal::moduleHandler()->alter('create_print_variant_node_paragraph_field', $value->entity, $pfield_name, $pfield_settings);
                }
                $i++;
              } else {
                $values->removeItem($i);
              }
            }
          }
        }
      }
    }
    return $node;
  }

  private function isSupportedParagraphType($type) {
    $valid = in_array($type, ['text', 'image']);
    return $valid;
  }
}
