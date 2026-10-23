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
    echo "******** UPGRADE #2031 DON_CORPS" . PHP_EOL;
    \Civi::log('don_corps')->error('******** UPGRADE #2031 DON_CORPS : update search groups ********');
    UpdateSearchGroups::updateAll();
    return TRUE;
    }

  public function upgrade_2032(): bool {

    echo "******** UPGRADE #2032 DON_CORPS ********" . PHP_EOL;
    
     $msg= "  - Inactivation du type évenements Cérémonie Hommage (historique)".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);

      \Civi::log('don_corps')->error('Inactivation du type évenements Cérémonie Hommage (historique)');
      $to_create =  [
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'event_type',
          'name' => 'Conference',
          'is_active' => FALSE,
        ],
      ];
      create_entity($to_create);
    
    $msg= "  - Ajout table civicrm_don_corps_ical_token".PHP_EOL;
      /**
       * Création de la table de gestion des tokens iCalendar.
       *
       * Cette migration est exécutée lors de la mise à jour de l'extension.
       * La table est également présente dans sql/install.sql pour les
       * nouvelles installations.
       */
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);

      \Civi::log('don_corps')->error('Ajout table civicrm_don_corps_ical_token');

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



    $msg= "  - Création du type de contact Intervenant extérieur".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
      \Civi::log('don_corps')->error('Création du types de contact Intervenant extérieur');
      
      $to_create =  [       // Création des types de contact : Organisateurs
          'entity' => 'ContactType',
          'values' => [
            'name' => 'Intervenant_ext_rieur',
            'label' => E::ts('Intervenant extérieur'),
            'description' => E::ts('Organisateur d une activité utilisant des corps'),
            'parent_id.name' => 'Individual',
            'icon' => 'fa-person-walking-dashed-line-arrow-right',
            'is_active' => TRUE,
          ],
        ];
      create_entity($to_create);

    $msg="  - Ajout des profils personnalisés organisateurs".PHP_EOL;// LOG OK
      echo $msg;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      fclose($fp);
      \Civi::log('don_corps')->error('Ajout des profils personnalisés organisateurs');
      
      install_UFGroup ('Organisateur');
      install_UFGroup ('Nouvel_organisateur', 'ContactsDDC', 'Nouvel organisateur');

    return TRUE;
  }
}