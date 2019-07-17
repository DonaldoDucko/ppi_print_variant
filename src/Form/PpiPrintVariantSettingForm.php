<?php

namespace Drupal\ppi_print_variant\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class PpiPrintVariantSettingForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['ppi_print_variant.settings'];
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ppi_print_variant_setting_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
      $settings = $this->configFactory->get('ppi_print_variant.settings');
      $form['text_to_prepend_to_title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Text to prepend to title'),
        '#default_value' => $settings->get('text_to_prepend_to_title'),
        '#description' => $this->t('Enter text to add to the title of a print variant. A space will be added between this text and the title. Example: Print Variant -')
      ];
      return parent::buildForm($form, $form_state);
  }
   /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      $text_to_prepend_to_title = $form_state->getValue('text_to_prepend_to_title');
      $this->config('ppi_print_variant.settings')->set('text_to_prepend_to_title', $text_to_prepend_to_title)->save();
  }
}