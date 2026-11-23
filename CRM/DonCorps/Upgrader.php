<?php
use CRM_DonCorps_ExtensionUtil as E;
use Civi\DonCorps\Install\UpdateSearchGroups;   ## MAJ du nom des grouopes utilisés dans les requêtes
use Civi\DonCorps\Install;
use Civi\DonCorps\Install\SearchUpdateToken;  // MAJ des tokens des searches
use Civi\DonCorps\Install\CustomFieldFilter;  // MAJ des filtres par groupes sur champs de recherche d'individus   
use Civi\DonCorps\WPUsers\WPPriviledges;      // MAJ des permissions pour inclure gesiont saisines
use Civi\DonCorps\Entities\InstallEntities;     ## création des entités
use Civi\DonCorps\Install\InstallProfile;       ## installation des profils depuis managed/UFGroup...mgd.php.disable
use Civi\DonCorps\Install\SearchUpdateTag;
use Civi\DonCorps\Install\CiviRules;            ## gestion des rules

// Crée ou met à jour les entités des fichiers .mgd.php



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
  public function upgrade_2031() {
    $vers_msg="** UPGRADE 2031 : ";
    ## Vérifie la version instaléee de civirules, puis crée de nouvelles règles
      $msg= $vers_msg.' Mise à jour des rules';
      \Civi::log()->info("don_corps : ".$msg);
      
      $check = CiviRules::checkVersion();      // Vérifie la version installée de civirules
      if(!$check){
      return FALSE;
      }

      CiviRules::installTriggers();   // Charge les déclencheurs définis dans civirules/triggers.json,

      ## les mgd files ne fonctionnent pas correctement car font référence 
      ## aux id des activités, culstom fields...qui varient d'une installation à l'autre
      InstallEntities::installFile('Entities_to_create_rules_deplace_lot.php');
      InstallEntities::installFile('Entities_to_create_rules_supprime_lot.php');
      InstallEntities::installFile('Entities_to_create_rules_inventaires.php');
      InstallEntities::installFile('Entities_to_create_rules_corrige_civilite.php');    
      InstallEntities::installFile('Entities_to_create_rules_compile_pieces_utilisations.php');
      InstallEntities::installFile('Entities_to_create_rules_copy_code_barres.php');
      InstallEntities::installFile('Entities_to_create_rules_demande_cremation.php');
      InstallEntities::installFile('Entities_to_create_rules_neutralise_adresse.php');
      return TRUE;

  }
  public function upgrade_2032(): bool {
    $vers_msg="** UPGRADE 2032 : ";
    ## modifie le nom des groupes utilisés dans les requetes (par exemple Archives_64 en Archives_52)
    $msg= $vers_msg.' Mise à jour du nom des groupes dans les recherches searchkir';
    \Civi::log('don_corps')->info($msg);
    UpdateSearchGroups::updateAll();

    $msg= $vers_msg."  Ajout des profils personnalisés depuis mgd.php.deactivate";
      \Civi::log()->info("don_corps : ".$msg);
      InstallProfile::install ('Centre_d_accueil_des_corps','Centres de don du corpsDDC','New CDC');
      InstallProfile::install ('CESP_29');
      InstallProfile::install ('D_mographie_animal');
      InstallProfile::install ('Dates_naissance_et_d_c_s_17');
      InstallProfile::install ('Demandeur_information_22','ContactsDDC','New Demandeur_d_informationDDC');
      InstallProfile::install ('Employeur');
      InstallProfile::install ('Inscription_anat_compar_e', 'ContactsDDC','Nouvelle pièce anat comparée');
      InstallProfile::install ('Inscription_donateur','ContactsDDC','New DonateurDDC');
      InstallProfile::install ('inscription_pompes', 'Pompes funebresDDC','New Pompes');
      InstallProfile::install ('Inscription_proche_donateur_14','ContactsDDC','Ajouter proche donateurDDC');
      InstallProfile::install ('Lieu_de_stockage', 'Pièces anatomiquesDDC','New Emprunteur');
      InstallProfile::install ('Mairie', 'MairiesDDC','New Mairies');
      InstallProfile::install ('Op_rations_fun_raires_r_alis_es_30');
      InstallProfile::install ('Personnel_de_centre_de_don_de_corps', 'Centres de don du corpsDDC','New Personnel');
      InstallProfile::install ('Profil_sans_nom_20'); // Adresse incorrecte OK
      InstallProfile::install ('Restitution_28');
      InstallProfile::install ('Type_de_contact_23');
      InstallProfile::install ('Organisateur');
      InstallProfile::install ('Nouvel_organisateur', 'ContactsDDC', 'Nouvel organisateur');


    $msg= $vers_msg.' Recharge les fichiers mgd.php';
    \Civi::log()->info("don_corps : ".$msg);
    \CRM_Core_ManagedEntities::singleton(TRUE)->reconcile();
    return TRUE;
  }
  public function upgrade_2033(): bool {
    $vers_msg="** UPGRADE 2033 : ";
    ## Inactive le type d'évenement Cérémonie Hommage (historique)
    ## Ajoute la table civicrm_don_corps_ical_token qui gère les tokens utilisés pour les abonnements icalendar
    ## Crée le type de contact Intervenant extérieur
    ## Ajoute les profils personnalisés pour saisie de nouveauxorganisateurs d'évènements
    ## Crée la table civicrm_saisine 
    ## Modifie les filtres sur les champs de type contact pour 'Déposants' et 'Demandeurs'
    ## Modifie les tags des saved searches
    $msg= $vers_msg." Inactivation du type évenements Cérémonie Hommage (historique)";
      \Civi::log()->info("don_corps : ".$msg);
      $to_create =  [
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'event_type',
          'name' => 'Conference',
          'is_active' => FALSE,
        ],
      ];
      InstallEntities::install($to_create);

    $msg= $vers_msg." Création du type d'activité Devis";
      \Civi::log()->info("don_corps : ".$msg);
      $to_create =  [
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'activity_type',
          'name' => 'Devis',
          'label' => E::ts('Devis'),
          'description' => E::ts('Devis pour une activité se déroulant au laboratoire/centre de don'),
        ],
      ];
      InstallEntities::install($to_create);


      $msg= $vers_msg." Ajout table civicrm_don_corps_ical_token";
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


      $msg= $vers_msg." Création du type de contact Intervenant extérieur";
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
      InstallEntities::install($to_create);

      $msg= $vers_msg."  Ajout des profils personnalisés organisateurs";
      \Civi::log()->info("don_corps : ".$msg);
      InstallProfile::install ('Organisateur');
      InstallProfile::install ('Nouvel_organisateur', 'ContactsDDC', 'Nouvel organisateur');
     
      $msg= $vers_msg." Création de la table civicrm_saisine";
      if (!CRM_Core_DAO::checkTableExists('civicrm_saisine')) {
        E::schema()->createEntityTable('schema/Saisine.entityType.php');
        \Civi::log()->info("don_corps : ".$msg);
        echo $msg.PHP_EOL;
      }
    ### Modifie les permissions civicrm pour ajouter la gestion des saisines
     WPPriviledges::ModifyCaps('00_Civicrm_WP_perm.txt');

      $msg= $vers_msg." Modification des filtres de champs personnalisés";
        \Civi::log()->info("don_corps : ".$msg);
        CustomFieldFilter::applyGroupFilter('D_posant', 'groupe_d_posants_porteurs_potent_24');
        CustomFieldFilter::applyGroupFilter('Demandeur', 'groupe_d_posants_porteurs_potent_24');

      $msg= $vers_msg." Ajout des tags aux SavedSearches";
      $tag_search_file = E::path() .'/managed/tags_searches.txt';     // fichier contenant les tags attachés aux recherches
      SearchUpdateTag::updateTag($tag_search_file);
      \Civi::log()->info("don_corps : ".$msg);
      return TRUE;
  }   

  public function upgrade_2034(): bool {
      $vers_msg="** UPGRADE 2034 : ";
      ## MAJ les provilèges civicrm
      $msg= $vers_msg." Modification des privilèges civicrm selon le rôle wordpress";
        \Civi::log()->info("don_corps : ".$msg);
        WPPriviledges::ModifyCaps('00_Civicrm_WP_perm.txt');
              return TRUE;
    }   

  public function upgrade_2042(): bool {
    $vers_msg="** UPGRADE 2042 : ";
    ## MAJ les provilèges civicrm
    $msg= $vers_msg." Saisine : passage du champ description (Intitulé) à 150 caractères'";
      \Civi::log()->info("don_corps : ".$msg);
    if (CRM_Core_BAO_SchemaHandler::checkIfFieldExists('civicrm_saisine', 'description')) {
          CRM_Core_DAO::executeQuery("
            ALTER TABLE `civicrm_saisine`
            MODIFY `description` VARCHAR(150) NULL DEFAULT NULL
            COMMENT 'Intitulé de la saisine'
          ");
    }
    return TRUE;
  } 
}