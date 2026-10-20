<?php
use CRM_DonCorps_ExtensionUtil as E;
use Civi\DonCorps\UpdateSearchGroups;   // impote update search group


## pour récuperer le schema-version actuel
## mysql -u XXX -p
## use votre_BDD
## SELECT schema_version FROM civicrm_extension WHERE `name` = 'don_corps';

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

  public function upgrade_2032(): bool {
    /**
     * Création de la table de gestion des tokens iCalendar.
     *
     * Cette migration est exécutée lors de la mise à jour de l'extension.
     * La table est également présente dans sql/install.sql pour les
     * nouvelles installations.
     */
    echo "******** UPGRADE #2032 DON_CORPS : ajoute table civicrm_don_corps_ical_token dans BDD ********" . PHP_EOL;
    \Civi::log('don_corps')->error('******** UPGRADE #2032 DON_CORPS : ajoute table civicrm_don_corps_ical_token dans BDD ********');
    UpdateSearchGroups::updateAll();
    return TRUE;

        CRM_Core_DAO::executeQuery("
            CREATE TABLE IF NOT EXISTS civicrm_don_corps_ical_token (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                contact_id INT UNSIGNED NOT NULL,
                token_hash CHAR(64) NOT NULL,
                created_date DATETIME NOT NULL,
                last_access_date DATETIME NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,

                PRIMARY KEY (id),
                UNIQUE KEY token_hash (token_hash),
                KEY contact_id (contact_id),
                KEY is_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        return TRUE;
    }

  public function upgrade_2033(): bool {
    echo "******** UPGRADE #2033 DON_CORPS : ajoute profils liés aux organisateurs ********" . PHP_EOL;
    \Civi::log('don_corps')->error('******** UPGRADE #2031 DON_CORPS : ajoute profils liés aux organisateurs ********');
    
    install_UFGroup ('Organisateur');
    install_UFGroup ('Nouvel_organisateur', 'ContactsDDC', 'Nouvel organisateur');


    return TRUE;
    }


}
