<?php

namespace Drupal\ppi_print_variant;

use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\NodeType;

class PpiPrintVariantPermissions {

    use StringTranslationTrait;

  /**
   * Returns an array of permissions.
   *
   * @return array
   *   The permissions.
   *   @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function printVariantTypePermissions() {
    $perms = [];
    // Generate node permissions for all node types.
    foreach (NodeType::loadMultiple() as $type) {
      $type_id = $type->id();
      $type_params = ['%type' => $type->label()];
      $perms += [
        "print_variant $type_id content" => [
          'title' => $this->t('%type: print_variant content', $type_params),
        ]
      ];
    }
    return $perms;
  }
}