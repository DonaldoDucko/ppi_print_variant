<?php

namespace Drupal\ppi_print_variant\Controller;

use Drupal\ppi_print_variant\Entity\PpiPrintVariantEntityFormBuilder;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\node\Entity\Node;
use Drupal\node\Controller\NodeController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for PPI Print Variant Node routes.
 */
class PpiPrintVariantNodeController extends NodeController {

  /**
   * The entity form builder.
   *
   * @var \Drupal\ppi_print_variant\Form\PpiPrintVariantEntityFormBuilder
   */
  protected $qncEntityFormBuilder;

  /**
   * Constructs a NodeController object.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(DateFormatterInterface $date_formatter, RendererInterface $renderer, PpiPrintVariantEntityFormBuilder $entity_form_builder) {
    parent::__construct($date_formatter, $renderer);
    $this->qncEntityFormBuilder = $entity_form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('renderer'),
      $container->get('ppi_print_variant.entity.form_builder')
    );
  }

  /**
   * Retrieves the entity form builder.
   *
   * @return \Drupal\ppi_print_variant\Form\PpiPrintVariantFormBuilder
   *   The entity form builder.
   */
  protected function entityFormBuilder() {
    return $this->qncEntityFormBuilder;
  }

  /**
   * Provides the node submission form.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The node entity to Print Variant.
   *
   * @return array
   *   A node submission form.
   */
  public function printVariantNode(\Drupal\node\Entity\Node $node) {
    if(!empty($node)){
      $form = $this->entityFormBuilder()->getForm($node, 'ppi_print_variant');
      return $form;
    }
    else {
      throw new NotFoundHttpException();
    }
  }

  /**
   * The _title_callback for the node.add route.
   *
   * @param int $node_id
   *   The current node id.
   *
   * @return string
   *   The page title.
   */
  public function createPageTitle($node) {
    $prepend_text = "";
    $config = \Drupal::config('ppi_print_variant.settings');
    if(!empty($config->get('text_to_prepend_to_title'))) {
      $prepend_text = $config->get('text_to_prepend_to_title') . " ";
    }
    return $prepend_text . $node->getTitle();
  }

}
