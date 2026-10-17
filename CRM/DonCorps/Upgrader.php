<?php
use CRM_DonCorps_ExtensionUtil as E;
use Civi\DonCorps\UpdateSearchGroups;   // impote update search group

/**
 * Collection of upgrade steps.
 */
class CRM_DonCorps_Upgrader extends \CRM_Extension_Upgrader_Base {

  public function upgrade_2030() {
    echo 'Upgrade vers 2.0.3'.PHP_EOL;
    \Civi::log('don_corps')->info('Upgrade vers 2.0.3');

    echo '- Vérification de la version CiviRules'.PHP_EOL;
    $ext = civicrm_api4('Extension', 'get', [
      'where' => [
        ['key', '=', 'org.civicoop.civirules'],
      ],
      'select' => ['version'],
      'checkPermissions' => FALSE,
    ]);

    if (empty($ext[0]['version']) || version_compare($ext[0]['version'], '3.36.0', '<')) {
      ECHO' ** ** Echec : org.civicoop.civirules 3.36.0 ou supérieure est requise ** **'.PHP_EOL;
      \Civi::log('don_corps')->info('Echec : org.civicoop.civirules 3.36.0 ou supérieure est requise');
      return FALSE ;

    } else {
      echo '- Mise à jour des rules'.PHP_EOL;
      \Civi::log('don_corps')->info('- Mise à jour des rules');
      supprime_rules();
      create_rules();

    echo"- Mise à jour du cache".PHP_EOL;
    CRM_Core_Invoke::rebuildMenuAndCaches();

    return TRUE;
    }
  }

  public function upgrade_2031(): bool {
    echo "******** UPGRADE #2031 DON_CORPS : update search groups ********" . PHP_EOL;
    \Civi::log('don_corps')->error('******** UPGRADE #2031 DON_CORPS : update search groups ********');
    UpdateSearchGroups::updateAll();
    return TRUE;
    }

}
