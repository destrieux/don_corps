<?php
namespace Civi\DonCorps\Install;

use CRM_DonCorps_ExtensionUtil as E;

/**
 * Intégration avec l'extension CiviRules.
 */
class CiviRules {

  /**
   * Clés possibles de l'extension CiviRules (récente, puis ancienne).
   */
  private const KEYS = ['civirules', 'org.civicoop.civirules'];

  /**
   * Version minimale de CiviRules requise par don_corps.
   */
  public const MIN_VERSION = '3.36.0';

  /**
   * CiviRules est-il installé et actif ?
   */
  public static function isInstalled(): bool {
    return self::installedKey() !== NULL;
  }

  /**
   * Version de CiviRules installée, ou NULL s'il n'est pas installé.
   */
  public static function version(): ?string {
    $key = self::installedKey();
    if (!$key) {
      return NULL;
    }
    return \CRM_Extension_System::singleton()->getMapper()->keyToInfo($key)->version ?: NULL;
  }

  /**
   * Vérifie que CiviRules est installé dans une version suffisante.
   * Écrit un avertissement dans le journal sinon.
   *
   * @return bool TRUE si la version est suffisante
   */
  public static function checkVersion(string $min = self::MIN_VERSION): bool {
    $version = self::version();

    if ($version === NULL) {
      \Civi::log()->warning("don_corps : CiviRules $min ou supérieur est requis (non installé)");
      return FALSE;
    }
    if (version_compare($version, $min, '<')) {
      \Civi::log()->warning("don_corps : CiviRules $min ou supérieur est requis (version installée : $version)");
      return FALSE;
    }

    \Civi::log()->info("don_corps : CiviRules $version");
    return TRUE;
  }

  /**
   * Charge les déclencheurs définis dans civirules/triggers.json,
   * si CiviRules est installé.
   *
   * @return bool TRUE si les déclencheurs ont été chargés
   */
  public static function installTriggers(): bool {
    if (!self::isInstalled()) {
      \Civi::log()->warning('don_corps : CiviRules non installé, déclencheurs ignorés');
      return FALSE;
    }

    \CRM_Civirules_Utils_Upgrader::insertTriggersFromJson(E::path('civirules/triggers.json'));
    \Civi::log()->info('don_corps : déclencheurs CiviRules chargés');
    return TRUE;
  }

  /**
   * Clé sous laquelle CiviRules est installé, ou NULL.
   */
  private static function installedKey(): ?string {
    $manager = \CRM_Extension_System::singleton()->getManager();
    foreach (self::KEYS as $key) {
      if ($manager->getStatus($key) === \CRM_Extension_Manager::STATUS_INSTALLED) {
        return $key;
      }
    }
    return NULL;
  }

}