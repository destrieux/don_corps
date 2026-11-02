<?php
use CRM_DonCorps_ExtensionUtil as E;
use Civi\DonCorps\UpdateSearchGroups;         // importe update search group
use Civi\DonCorps\Install;
use Civi\DonCorps\Install\SearchUpdateToken;  // MAJ des tokens des searches
use Civi\DonCorps\Install\CustomFieldFilter;  // MAJ des filtres par groupes sur champs de recherche d'individus   


## pour récuperer le schema-version actuel
## mysql -u XXX -p
## use votre_BDD
## SELECT schema_version FROM civicrm_extension WHERE `name` = 'don_corps';
## 2030 (v2.0.3) en prod ; 2031 (v2.0.4) sur main

/**
 * Collection of upgrade steps.
 */
class CRM_DonCorps_Upgrader extends \CRM_Extension_Upgrader_Base {

  public function install(): void {
    // Cette méthode n'est jouée qu'une fois à l'installation
    // Elle en'est pas rejouée à l'upgrade
    if (!CRM_Core_DAO::checkTableExists('civicrm_saisine')) {
      E::schema()->createEntityTable('schema/Saisine.entityType.php');
      $msg= "** UPGRADE INITIAL : - Création de la table civicrm_saisine";
      \Civi::log()->info("don_corps : ".$msg);
    }
  }
  public function upgrade_2030() {
    ## Vérifie la version instaléee de civirules, puis en crée de nouvelles

    $ext = civicrm_api4('Extension', 'get', [
      'where' => [
        ['key', '=', 'org.civicoop.civirules'],
      ],
      'select' => ['version'],
      'checkPermissions' => FALSE,
    ]);

    if (empty($ext[0]['version']) || version_compare($ext[0]['version'], '3.36.0', '<')) {
      $msg= "** UPGRADE 2030 : - org.civicoop.civirules 3.36.0 ou supérieure est requise ** **";
      \Civi::log('don_corps')->error($msg);
      return FALSE ;

    } else {
      $msg= '** UPGRADE 2030 : - Mise à jour des rules';
      \Civi::log()->info("don_corps : ".$msg);
      supprime_rules();
      create_rules();

    CRM_Core_Invoke::rebuildMenuAndCaches();

    return TRUE;
    }
  }
  public function upgrade_2031(): bool {
    ## modifie le nom des groupes utilisés dans les requetes (par exemple Archives_64 en Archives_52)
    $msg= "** UPGRADE 2030 : - update search groups";
    \Civi::log('don_corps')->info($msg);
    UpdateSearchGroups::updateAll();
    return TRUE;
  }
  public function upgrade_2032(): bool {
    ## Inactive le type d'évenement Cérémonie Hommage (historique)
    ## Ajoute la table civicrm_don_corps_ical_token qui gère les tokens utilisés pour les abonnements icalendar
    ## Crée le type de contact Intervenant extérieur
    ## Ajoute les profils personnalisés pour saisie de nouveauxorganisateurs d'évènements
    ## Crée la table civicrm_saisine 
    ## Modifie les filtres sur les champs de type contact pour 'Déposants' et 'Demandeurs'
    ## Modifie les tags des saved searches
    $msg= "** UPGRADE 2032 : - Inactivation du type évenements Cérémonie Hommage (historique)";
      \Civi::log()->info("don_corps : ".$msg);

      $to_create =  [
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'event_type',
          'name' => 'Conference',
          'is_active' => FALSE,
        ],
      ];
      create_entity($to_create);
    
    $msg= "** UPGRADE 2032 : - Ajout table civicrm_don_corps_ical_token";
      \Civi::log()->info("don_corps : ".$msg);

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


    $msg= "** UPGRADE 2032 : - Création du type de contact Intervenant extérieur";
      \Civi::log()->info("don_corps : ".$msg);
      $to_create =  [ 
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

    $msg= "** UPGRADE 2032 : - Ajout des profils personnalisés organisateurs";
      \Civi::log()->info("don_corps : ".$msg);
      install_UFGroup ('Organisateur');
      install_UFGroup ('Nouvel_organisateur', 'ContactsDDC', 'Nouvel organisateur');

    $msg= "** UPGRADE 2032 : - Création de la table civicrm_saisine";
      if (!CRM_Core_DAO::checkTableExists('civicrm_saisine')) {
        E::schema()->createEntityTable('schema/Saisine.entityType.php');
        \Civi::log()->info("don_corps : ".$msg);
        echo $msg.PHP_EOL;
      }
    $msg= "** UPGRADE 2032 : - Modification des filtres de champs personnalisés";
        \Civi::log()->info("don_corps : ".$msg);
        CustomFieldFilter::applyGroupFilter('D_posant', 'groupe_d_posants_porteurs_potent_24');
        CustomFieldFilter::applyGroupFilter('Demandeur', 'groupe_d_posants_porteurs_potent_24');

    $msg= "** UPGRADE 2032 : - Ajout des tags aux SavedSearches";
      $tag_search_file = E::path() .'/managed/tags_searches.txt';     // fichier contenant les tags attachés aux recherches
      SearchUpdateToken::updateToken($tag_search_file);
      \Civi::log()->info("don_corps : ".$msg);
      return TRUE;
  }
}