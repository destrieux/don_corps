<?php
namespace Civi\DonCorps\Install;

/**
 * Applique les réglages CiviCRM définis dans Install/settings.php.
 */
class Params {

  /**
   * @throws \CRM_Core_Exception si un réglage est inconnu ou invalide
   */
  public static function apply(): void {
    $settings = require __DIR__ . '/settings.php';

    civicrm_api4('Setting', 'set', [
      'values' => $settings,
      'checkPermissions' => FALSE,
    ]);

    \Civi::log()->info('don_corps : ' . count($settings) . ' réglages CiviCRM appliqués');
  }

}