<?php

namespace Drupal\ppi_print_variant\Controller;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\node\Entity\Node;

class PpiPrintVariantNodeAccess {
  /**
   * Limit access to the PPI Print Variant according to their restricted state.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   * @param \Drupal\node\Entity\Node $node
   */
  public function printVariantNode(AccountInterface $account, $node) {
    $node = Node::load($node);
    $node_type = $node->bundle();

    if (_ppi_print_variant_has_print_variant_permission($node_type)) {
      $result = AccessResult::allowed();
    } else {
      $result = AccessResult::forbidden();
    }

    $result->addCacheableDependency($node);

    return $result;
  }
}
