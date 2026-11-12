<?php


/*VERIFICATION

9/11/26 - Issue signalée : lors de l'affichage descontacts supprimés via advanceed search 
https://lab.civicrm.org/dev/core/-/work_items/6918
Dans /Applications/MAMP/htdocs/dev/wp-content/plugins/civicrm/civicrm/CRM/Contact/Selector.php, line 792-5
Ajouter "weight' => 100,"
            [
              'name' => ts('Restore'),
              'url' => 'civicrm/contact/view/delete',
              'qs' => 'reset=1&cid=%%id%%&restore=1',
              'title' => ts('Restore Contact'),  ## modif CD pour afficher contacts supprimés
              'weight' => 100,
            ],

*/
/* 
schema/Saisine.entityType.php → décrit l'entité et sa table ;
CRM/DonCorps/DAO/Saisine.php → DAO PHP correspondant à cette table ;
Civi/Api4/Saisine.php → expose cette entité à l'API4 ;
 */


require_once 'don_corps.civix.php';

use CRM_DonCorps_ExtensionUtil as E;
use Symfony\Component\DependencyInjection\ContainerBuilder;  ## pour créer nouvelles actions pour utilation des corps
use Civi\DonCorps\UpdateSearchGroups;
use Civi\DonCorps\Install\SearchUpdateTag;      ## MAJ des tokens des searches
use Civi\DonCorps\Contacts\ImportCDC;           ## import des CDC, de leurz adresses, telephones ...
use Civi\DonCorps\WPUsers\WPPriviledges;        ## export - import des privilèges civicrm/WP
use Civi\DonCorps\Entities\InstallEntities;     ## création des entités
use Civi\DonCorps\Install\InstallProfile;       ## installation des profils depuis managed/UFGroup...mgd.php.disable
use Civi\DonCorps\Install\Params;               ## règle les parametres de localisation, date... Parametres dans Civi/Install/Settings.php
use Civi\DonCorps\Install\Menus;                ## désactive / active les menus
use Civi\DonCorps\ICal\Calendar;                ## gestion calendrier
use Civi\DonCorps\Install\CiviRules;            ## gestion des rules


    

### Flux iCalendar : interception WordPress (template_redirect) 
  \Civi\DonCorps\ICal\Calendar::register();

### Implements hook_civicrm_permission construit la liste de toutes les permissions du site. 
  # Il est déclenché notamment :
  #   - quand vous ouvrez la page Administrer → Utilisateurs et permissions → Contrôle d’accès WordPress (la liste des permissions) ;
  #   - quand CiviCRM vérifie un droit et doit connaître les permissions déclarées ;
  #   - quand la liste est reconstruite, par exemple après un cv flush, car cette liste est mise en cache. */
  
  function don_corps_civicrm_permission(&$permissions) {
    $permissions['access saisines'] = [
      'label' => 'don_corps : accéder aux saisines',
      'description' => 'Consulter et modifier les saisines',
    ];
}

### Implements hook_civicrm_angularModules() pour afficher le contenu dasi saisienes dans event
  #
  ##Utilisation dans Form Builder
  #
  # Dans le formulaire Event, à l'intérieur du fieldset Event (là où se trouve le champ saisine), ajouter le bloc **« Données de la saisine »** (section Blocs de l'éditeur), juste après le champ saisine.
  #
  # Le bloc se met à jour dès que la saisine sélectionnée change.
  #
  ## Réglages (attributs de `<saisine-display>`)
  # Le bloc livré utilise les valeurs par défaut. Pour les changer, modifier `ang/afblockSaisineDisplay.aff.html` :
  #
  #  ```html
  # <saisine-display
  #    field-name="Evenement_Saisine.saisine_id"
  #    api-entity="Saisine"
  #    fields="titre,date_reception,statut_id"
  #    heading="Saisine liée"></saisine-display>
  #  ```
  #  | Attribut | Rôle | Défaut |
  #  |---|---|---|
  #  | `field-name` | Nom du champ du formulaire qui contient l'id | premier champ dont le nom finit par `saisine_id` |
  #  | `api-entity` | Entité APIv4 à lire | `Saisine` |
  #  | `fields` | Champs à afficher, séparés par des virgules | tous sauf `id` |
  #  | `heading` | Titre du bloc | « Données de la saisine » |
  #
  # Après modification : `cv flush`.
  #
  ## Dépannage
  #
  #- Rien ne s'affiche : ouvrir la console du navigateur (F12). Une erreur du type `Module 'xxx' is not available` indique un nom de module Angular à corriger dans `ang/saisineDisplay.ang.php` (ligne `requires`).
  #  - « Saisine introuvable ou accès refusé » : vérifier le nom de l'entité (`api-entity`) dans l'explorateur APIv4 et les permissions de l'utilisateur.
  ######

  function don_corps_civicrm_angularModules(&$angularModules): void {
    $angularModules['saisineDisplay'] = include __DIR__ . '/ang/saisineDisplay.ang.php';
} 

# DEFINITION DES FONCTIONS


/*   function after ($thisv, $inthatv){ // récupère la partie de chaine $inthatv située après $thisv
        if (!is_bool(strpos($inthatv, $thisv)))
        return substr($inthatv, strpos($inthatv,$thisv)+strlen($thisv));
    };
  function before ($thisv, $inthatv){ // récupère la partie de chaine $inthatv située avant $thisv
        return substr($inthatv, 0, strpos($inthatv, $thisv));
    }; */

  function don_corps_civicrm_pageRun($page) {
    // Vérifie si c’est notre page personnalisée
    if ($page->getVar('_name') === 'don_corps') {
        require_once __DIR__ . '/CRM/DonCorps/Form/SetValue.php';
        $form = new CRM_DonCorps_Form_SetValue();
        $form->preProcess();
        $form->buildQuickForm();
        $form->postProcess();
    }
   }   // fin de définition de fonction don_corps_civicrm_pageRun


    ############
    ## Les fonctions _myextension_remove_wp_capabilities et _myextension_add_wp_capabilities
    ##  modifient les privileges des utilisateurs wp selon leur rôle wordpress
    ##  elles sont lancées à l'installation
    ##  ces modifications ne sont pas accessibles depuis l'API
    ##
    ##  syntaxe : _myextension_remove_wp_capabilities(role, caps)
    ##      role : role wordpress (chaine : author, contributor...)
    ##      caps : privilèges (array)
    ##
    ##  Pour lister les utilisateurs : wp user list
    ##  Pour Lister les privileges d'un utilisateur : wp user list-caps <user id>
    ############

  

  


  

  function change_icon (){      // LOG OK invoquée en post installation pour remplacer l'icone du menu
    // syntaxe : change_icon  ('nom_du_menu_dont les sous_rubriques_sont_a_desactiver', 'icone');
    //
    // elle est appelée par : function don_corps_civicrm_postinstall()
    //////////////
    $menu = func_get_arg(0);
    $icon = func_get_arg(1);

    $navigations = civicrm_api4('Navigation', 'get', [
        'where' => [
          ['name', '=', $menu],
        ],
        'checkPermissions' => FALSE,
      ]);
    if (isset($navigations[0])){                            // si ce menu
        $results = civicrm_api4('Navigation', 'update', [   // on change l'icone
          'values' => [
            'icon' => $icon,
          ],
          'where' => [
            ['name', '=', $menu],
          ],
          'checkPermissions' => FALSE,
        ]);
        $msg= "         icone du menu ".$menu." changée pour : ".$icon;
    } else {
        $msg= "         Pas de sous rubrique pour ".$menu;
    }
    
    \Civi::log()->info("don_corps : ".$msg);
    // echo $msg.PHP_EOL;
    

    }   // Fin de définition de la fonction : change_icon()

  
  function serialize_custom_fields(){
    //////////// focntion serialize_custom_fields() /////////
    // Cette fonction est invoquée à en post installation pour :
    //    - récupérer les champs customs utilisés par les conditions civirules
    //    - les écrire en format seialisé
    //    - les injecter dans les civirules écrites par le hook don_corps_civicrm_postinstall()
    //
    // syntaxe : serialize_custom_fields($civirule_rule_name, $civirule_condition_name, $CustomField_name1, $CustomField_name2, $CustomField_name3...);
    //
    //      $civirule_rule_name :nom de la règle à modifier
    //      $civirule_condition_name : nom de la condition à modifier
    //      $CustomField_name1, $CustomField_name2, $CustomField_name3... : liste de custom fields
    //
    // elle est appelée par : le hook post installation
    //////////////
    $args = func_get_args();

    // Si des custom fields ne sont pas passés à la fonction : return
    if (count($args)==0){
      $msg="Pas de custom field passé à la fonction ";
      \Civi::log()->info("don_corps : ".$msg);
      if (VERBOSE==1){
        // echo $msg.PHP_EOL;
      }
      
      return;
    }

    // Est ce que les custom fields existent
    foreach ($args as $custom){
      //echo "custom recherché : ".$custom.PHP_EOL;
        $id = civicrm_api4('CustomField', 'get', [   // on récupère l'id du custom field
          'select' => [
            'id',
          ],
          'where' => [
            ['name', '=', $custom],
          ],
          'checkPermissions' => FALSE,
        ]);

      //echo $id[0]['id'].PHP_EOL;
      $array[] = $id[0]['id'];                       // on liste les id des champs custom dans $array
    }

    $array = [                                       // on ajoute une colonne Custom_field_id à $array
      "custom_field_id" => $array
    ];

    return serialize($array);                           // retourne la valeur en sértialisant (foramt atendu par condition_params)

   }   // Fin de la définition de la fonction : serialize_custom_fields()

  function delete_contact_type (){
    //////////// function delete_contact_type /////////
    // Cette fonction est invoquée à la désinstallation pour supprimer les contacts crées à l'installation
    //
    // syntaxe : delete_contact_type ()
    //    $params = "Emprunteur"
    //
    //  delete_contact_type (name);
    //
    // elle est appelée par : function don_corps_civicrm_uninstall()
    //////////////
    $name = func_get_arg(0);


    // Vérification si le type de contact existe déjà
      $results = civicrm_api4('ContactType', 'get', [
        'where' => [
          ['name', '=', $name],
        ],
        'checkPermissions' => FALSE,
      ]);

      if ($results[0]['id']!=0) {       // si le soustype de contact existe on commence par vérifier s'il est utilisé
        $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
          'COUNT(id) AS number',
        ],
        'where' => [
          ['contact_sub_type:name', '=', $name],
          ],
        'limit' => 1,
        'checkPermissions' => FALSE,
        ]);


        if ($contacts[0]['number']==0) {  // le sous type de contact n'est utilisé par aucun contact alors on le supprime
          try{
            $deleteResult = civicrm_api4('ContactType', 'delete', [
                'where' => [
                  ['name', '=', $name],
                  ],
                'checkPermissions' => FALSE,
                ],
            );
            //CRM_Core_Session::setStatus('Le type de contact '.$name.' a été supprimé.', 'Succès', 'success');
            //echo 'Le type de contact '.$name.' a été supprimé.'."\n";

            } catch (API_Exception $e) {
              //CRM_Core_Session::setStatus('Erreur lors de la suppression du type de contact '.$name, 'Erreur', 'error');
              //echo 'Erreur lors de la suppression du type de contact '.$name."\n";



            }

        } else {                      // le sous type de contact est utilisé par au moins un contact, alors on le conserve
          //CRM_Core_Session::setStatus('Le type de contact '.$name.' est utilisé, on le conserve.', 'Info', 'info');
          //echo 'Le type de contact '.$name.' est utilisé, on le conserve.'."\n";
        }

      }


   }   // Fin de la définition de la fonction : delete_contact_type()

  //function modif_filtre() reecrite : \Civi\DonCorps\Install\CustomFieldFilter::applyGroupFilter('Mon_champ', 'Mon_groupe');
   

# FIN DE LA DECLARATION DES FONCTIONS

### IMPLEMENTS hook don_corps_civicrm_custom
  # Point d’entrée du hook hook_civicrm_custom. 
  # CiviCRM l’appelle automatiquement chaque fois que des valeurs de champs personnalisés sont enregistrées.
  # le nom suit le schema <nom_de_l_extension>_civicrm_<nom_du_hook>
  # $op	l’opération : create, edit, delete...
  # $groupID	l’id du groupe de champs personnalisés concerné
  # $entityID	l’id de l’enregistrement qui porte ces valeurs  
  # &$params	la liste des valeurs enregistrées, avec leur détail
  #   & devant $params signifie « passage par référence » : la fonction travaille sur l’original et pourrait le modifier.
  function don_corps_civicrm_custom($op, $groupID, $entityID, &$params) {
    \Civi\DonCorps\Hook\Custom::handle($op, (int) $groupID, (int) $entityID);
  }


### IMPLEMENTS hook_civicrm_managed()
  # @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed*/
  function don_corps_civicrm_managed(&$entities) {

  } 

### IMPLEMENTS hook_civicrm_config() 
  #@link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
  function don_corps_civicrm_config(&$config): void {
    _don_corps_civix_civicrm_config($config);
  }

### IMPLEMENTS hook_civicrm_install() 
  # @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install/
  function don_corps_civicrm_install(): void {    


    $msg= "*** Création de la table de stockage des tokens pour ical";
      \Civi::log()->info("don_corps : ".$msg);
      Calendar::installTable();

    $msg= "*** Création/inactivation des entités";
      \Civi::log()->info("don_corps : ".$msg);
      ## les entités sont définies dans les fichiers appelés, situés dans 
      ## Civi/DonCorps/Entities/
      InstallEntities::installFile('Entities_to_create_intervalles_dates.php');
      InstallEntities::installFile('Entities_to_create_prefixes_de_contacts.php');
      InstallEntities::installFile('Entities_to_create_types_contacts.php');
      InstallEntities::installFile('Entities_to_create_Financial_Types.php');
      InstallEntities::installFile('Entities_to_create_Contribution_Status.php');
      InstallEntities::installFile('Entities_to_create_Modes_Paiement.php');
      InstallEntities::installFile('Entities_to_create_type_participants.php');
      InstallEntities::installFile('Entities_to_create_statut_participants.php');     
      InstallEntities::installFile('Entities_to_create_relationships.php');
      InstallEntities::installFile('Entities_to_create_messages_templates.php'); // a installer avnt les rules

    echo PHP_EOL."##### L'etape suivante est longue (plusieurs minutes) - Soyez patient ! #####".PHP_EOL;
    _don_corps_civix_civicrm_install();
  }

### IMPLEMENTS hook_civicrm_enable(). // LOG OK
  #@link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable

  function don_corps_civicrm_enable(): void {   // pas d'affcicahge des messages consoles lors installation
     }


### IMPLEMENTS hook_civicrm_postInstall().
  #@link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
  function don_corps_civicrm_postInstall() {

    $msg="*** Modification des privilèges civicrm selon le rôle wordpress";
      \Civi::log()->info("don_corps : ".$msg);
      WPPriviledges::ModifyCaps('00_Civicrm_WP_perm.txt');

    $msg="*** Modification des parametres de localisation, date..."; 
      \Civi::log()->info("don_corps : ".$msg);
      Params::Apply();

    $msg="  - Modification des tags"; 
      // echo $msg.PHP_EOL;
      
      \Civi::log()->info("don_corps : ".$msg);
      
      $entities[] =
      [
        'module' => 'don_corps',
        'name' => 'Tag_bilan',
        'entity' => 'Tag',
        'cleanup' => 'unused',
        'update' => 'unused',
        'params' => [
          'version' => 4,
          'values' => [
            'name' => 'bilan',
            'label' => E::ts('bilan'),
            'used_for' => [
              E::ts('civicrm_saved_search'),
            ],
            'color' => '#3df542',
          ],
          'match' => [E::ts('name')],
        ],
      ];
    
    $msg="*** Ajout des centres de don du corps";
      \Civi::log()->info("don_corps : ".$msg);
      ImportCDC::ImportMain("05_organisations.txt");
      ImportCDC::ImportTel("20_telephone.txt");
      ImportCDC::ImportEmail("25_Email.txt");
      ImportCDC::ImportAdresse("15_adresses.txt"); 

      /* PROBABLEMENT A SUPPRIMER
      $locationTypes = civicrm_api4('LocationType', 'get', [.  
            'where' => [
            ['name', '=', 'CESP'],
            ],
            'checkPermissions' => FALSE,
        ]);

        if(isset($locationTypes[0])){
            $results = civicrm_api4('LocationType', 'update', [
            'values' => [
                'name' => 'CESP',
                'display_name' => 'CESP',
            ],
            'where' => [
                ['id', '=', $locationTypes[0]['id']],
            ],
            'checkPermissions' => FALSE,
            ]);

            $msg= "      -> MAJ ";

        }else{
            $results = civicrm_api4('LocationType', 'create', [
            'values' => [
            'name' => 'CESP',
            'display_name' => 'CESP',
            'is_active' => TRUE,
            ],
            'checkPermissions' => FALSE,
            ]);

            $msg= "      -> Création ";
        }
            \Civi::log()->info("don_corps : ".$msg);
            // echo $msg;


        $msg= "de la location type : ".$results[0]['name']." (".$results[0]['id'].")"; 

        \Civi::log()->info("don_corps : ".$msg);
        // echo $msg.PHP_EOL;*/


    $msg="*** Désactivation des menus par défaut";
      \Civi::log()->info("don_corps : ".$msg);
      Menus::Disable(
        ['Contacts',
          'Search',
          'Contributions',
          'Events',
          'Mailings',
          'Reports',
          'Support',
          ]
      );

    $msg="  - Reglage des règles de dédoublonage";
      
      \Civi::log()->info("don_corps : ".$msg);
      // echo $msg.PHP_EOL;
      #####
      # Lors de l acréation des dedupe rules en utilisant les fichier mgd, les champs sont mal mappés
      # en raison d'une référence non au nom des champs mais de la tabloe et de la colonne dans la bdd, variabl ed'une installaiton ) l'autre
      # ici, on récupère les valeurs de table et de colonne dans la nouvelle bdd à parir du nom du champ
      # et on modifie la regle de dédoublonage
      #
      # dans $custom_dedupes lister le name des custom fields utilisés dans les regles de dédoublonage
      #####

      ### Passage des regles existantes pour les individus et organizations à l'utilisation "general"
          # "Supervised" : lancée si création depuis l'interface utilisateur
          # "Unsupervised" : lancée si creation online ou import de contact
          $msg= "      -> Passage des usages de tous les groupes de règles de dédoublonage à General";
            \Civi::log()->info("don_corps : ".$msg);
            if (VERBOSE==1){
            // echo $msg.PHP_EOL;
            }

            $results = civicrm_api4('DedupeRuleGroup', 'update', [
            'values' => [
                'used' => 'General',
            ],
            'where' => [
                ['OR', [['contact_type', '=', 'Organization'], ['contact_type', '=', 'Individual']]],
            ],
            'checkPermissions' => FALSE,
            ]);

      ### individuals : création de la table $custom_dedupes qui contient les valeurs de table et de colonne pour chque champ

          $custom_dedupes = ['N_annulation', 'N_de_don', 'N_de_d_c_s'];
          $custom_table=array();

          foreach($custom_dedupes as $custom_dedupe){
              //echo $custom_dedupe.PHP_EOL;
              $customFields = civicrm_api4('CustomField', 'get', [
                  'select' => [
                      'column_name',
                      'custom_group_id.table_name',
                  ],
                  'where' => [
                      ['name', '=', $custom_dedupe],
                  ],
                  'checkPermissions' => FALSE,
              ]);

              if(isset($customFields[0])){
                  $custom_table[$custom_dedupe]['table']=$customFields[0]['custom_group_id.table_name'];
                  $custom_table[$custom_dedupe]['column']=$customFields[0]['column_name'];
              }
          }

      ### individuals : création règle supervisée de dédoublonage utilisant les n° de don d'annulation et de deces
          $rule_name = 'num_don_annulation_deces';
          $dedupeRuleGroups = civicrm_api4('DedupeRuleGroup', 'get', [
              'select' => [
                  'id',
              ],
              'where' => [
                  ['name', '=', $rule_name],
              ],
              'checkPermissions' => FALSE,
          ]);

          if(isset($dedupeRuleGroups[0])){
              $results = civicrm_api4('DedupeRuleGroup', 'update', [
                  'values' => [
                      'contact_type' => 'Individual',
                      'threshold' => 10,
                      'used' => 'Supervised',
                      'title' => E::ts('num don ou annulation ou deces (supervisée)'),
                  ],
                  'where' => [
                      ['id', '=', $dedupeRuleGroups[0]['id']],
                  ],
                  'checkPermissions' => FALSE,
                  ]);
                  $msg= "      -> MAJ groupe de règles de dédoublonage";
                  \Civi::log()->info("don_corps : ".$msg);
                  if (VERBOSE==1){
                    // echo $msg;
                  }
                  $dedupeRules = civicrm_api4('DedupeRule', 'get', [
                      'where' => [
                          ['dedupe_rule_group_id.name', '=', $rule_name],
                      ],
                      'checkPermissions' => FALSE,
                      ]);

                  if(isset($dedupeRules[0])){
                      $results = civicrm_api4('DedupeRule', 'delete', [
                      'where' => [
                          ['dedupe_rule_group_id.name', '=', $rule_name],
                      ],
                      'checkPermissions' => FALSE,
                      ]);
                      $msg= " et suppression des règles attachées";
                      \Civi::log()->info("don_corps : ".$msg);
                      if (VERBOSE==1){
                        // echo $msg;
                      }
                  }

          } else {
              $results = civicrm_api4('DedupeRuleGroup', 'create', [
                  'values' => [
                      'contact_type' => 'Individual',
                      'threshold' => 10,
                      'used' => 'Supervised',
                      'title' => E::ts('num don ou annulation ou deces (supervisée)'),
                      'name' => $rule_name,
                  ],
                  'checkPermissions' => FALSE,
                  ]);

              $msg= "      -> Création groupe de règles de dédoublonage";
              \Civi::log()->info("don_corps : ".$msg);
              if (VERBOSE==1){
                // echo $msg;
              }
          }
          $msg= " : ".$rule_name.' (id : '.$results['0']['id'].')' ;
          \Civi::log()->info("don_corps : ".$msg);
          if (VERBOSE==1){
            // echo $msg.PHP_EOL;
          }

          foreach($custom_dedupes as $custom_dedupe){
              $results = civicrm_api4('DedupeRule', 'create', [
              'values' => [
                  'dedupe_rule_group_id.name' => $rule_name,
                  'rule_table' => $custom_table[$custom_dedupe]['table'],
                  'rule_field' => $custom_table[$custom_dedupe]['column'],
                  'rule_weight' => '10',
              ],
              'checkPermissions' => FALSE,
              ]);
              $msg= "         Rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'];
              \Civi::log()->info("don_corps : ".$msg);
              if (VERBOSE==1){
                // echo $msg.PHP_EOL;
              }
              }



      ### individuals : création règle automatique de dédoublonage utilisant les n° de don d'annulation et de deces
          $rule_name = 'numeros_don_annulation_dc_2';
          $dedupeRuleGroups = civicrm_api4('DedupeRuleGroup', 'get', [
              'select' => [
                  'id',
              ],
              'where' => [
                  ['name', '=', $rule_name],
              ],
              'checkPermissions' => FALSE,
          ]);

          if(isset($dedupeRuleGroups[0])){
              $results = civicrm_api4('DedupeRuleGroup', 'update', [
                  'values' => [
                      'contact_type' => 'Individual',
                      'threshold' => 10,
                      'used' => 'Unsupervised',
                      'title' => E::ts('num don ou annulation ou deces (automatique)'),
                  ],
                  'where' => [
                      ['id', '=', $dedupeRuleGroups[0]['id']],
                  ],
                  'checkPermissions' => FALSE,
                  ]);
                  $msg= "      -> MAJ groupe de règles de dédoublonage";
                  \Civi::log()->info("don_corps : ".$msg);
                  if (VERBOSE==1){
                    // echo $msg;
                  }

                  $dedupeRules = civicrm_api4('DedupeRule', 'get', [
                      'where' => [
                          ['dedupe_rule_group_id.name', '=', $rule_name],
                      ],
                      'checkPermissions' => FALSE,
                      ]);

                  if(isset($dedupeRules[0])){
                      $results = civicrm_api4('DedupeRule', 'delete', [
                      'where' => [
                          ['dedupe_rule_group_id.name', '=', $rule_name],
                      ],
                      'checkPermissions' => FALSE,
                      ]);
                      $msg= " et suppression des règles attachées";
                      \Civi::log()->info("don_corps : ".$msg);
                      if (VERBOSE==1){
                        // echo $msg;
                      }
                  }

          } else {
              $results = civicrm_api4('DedupeRuleGroup', 'create', [
                  'values' => [
                      'contact_type' => 'Individual',
                      'threshold' => 10,
                      'used' => 'Unsupervised',
                      'title' => E::ts('num don ou annulation ou deces (automatique)'),
                      'name' => $rule_name,
                  ],
                  'checkPermissions' => FALSE,
                  ]);

              $msg= "      -> Création groupe de règles de dédoublonage";
              \Civi::log()->info("don_corps : ".$msg);
              if (VERBOSE==1){
                // echo $msg;
              }
          }
          $msg= " : ".$rule_name.' (id : '.$results['0']['id'].')' ;
          \Civi::log()->info("don_corps : ".$msg);
          if (VERBOSE==1){
            // echo $msg.PHP_EOL;
          }

          foreach($custom_dedupes as $custom_dedupe){
              $results = civicrm_api4('DedupeRule', 'create', [
              'values' => [
                  'dedupe_rule_group_id.name' => $rule_name,
                  'rule_table' => $custom_table[$custom_dedupe]['table'],
                  'rule_field' => $custom_table[$custom_dedupe]['column'],
                  'rule_weight' => '10',
              ],
              'checkPermissions' => FALSE,
              ]);
              $msg= "         Rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'];
                        \Civi::log()->info("don_corps : ".$msg);
              if (VERBOSE==1){
                // echo $msg.PHP_EOL;
              }
            }



      ### Organisation : création de la table $custom_dedupes qui contient les valeurs de table et de colonne pour chque champ

              $custom_dedupes = ['name', 'email'];
              $custom_table=array();

              $custom_table['name']['table']='civicrm_contact';
              $custom_table['name']['column']='organization_name';
              $custom_table['email']['table']='civicrm_email';
              $custom_table['email']['column']='email';


      ### Organisation : création règle supervisée de dédoublonage utilisant le nom ou courriel
          $rule_name = 'OrganizationSupervised';
          $dedupeRuleGroups = civicrm_api4('DedupeRuleGroup', 'get', [
              'select' => [
                  'id',
              ],
              'where' => [
                  ['name', '=', $rule_name],
              ],
              'checkPermissions' => FALSE,
          ]);

          if(isset($dedupeRuleGroups[0])){
              $results = civicrm_api4('DedupeRuleGroup', 'update', [
                  'values' => [
                      'contact_type' => 'Organization',
                      'threshold' => 20,
                      'used' => 'Supervised',
                      'title' => E::ts('Nom ou courriel (supervisée)'),
                  ],
                  'where' => [
                      ['id', '=', $dedupeRuleGroups[0]['id']],
                  ],
                  'checkPermissions' => FALSE,
                  ]);
                  $msg= "      -> MAJ groupe de règles de dédoublonage";
                  \Civi::log()->info("don_corps : ".$msg);
                  if (VERBOSE==1){
                    // echo $msg;
                  }

                  $dedupeRules = civicrm_api4('DedupeRule', 'get', [
                      'where' => [
                          ['dedupe_rule_group_id.name', '=', $rule_name],
                      ],
                      'checkPermissions' => FALSE,
                      ]);

                  if(isset($dedupeRules[0])){
                      $results = civicrm_api4('DedupeRule', 'delete', [
                      'where' => [
                          ['dedupe_rule_group_id.name', '=', $rule_name],
                      ],
                      'checkPermissions' => FALSE,
                      ]);
                      $msg= " et suppression des règles attachées";
                      \Civi::log()->info("don_corps : ".$msg);
                      if (VERBOSE==1){
                        // echo $msg;
                      }
                  }

          } else {
              $results = civicrm_api4('DedupeRuleGroup', 'create', [
                  'values' => [
                      'contact_type' => 'Organization',
                      'threshold' => 20,
                      'used' => 'Supervised',
                      'title' => E::ts('Nom ou courriel (supervisée)'),
                      'name' => $rule_name,
                  ],
                  'checkPermissions' => FALSE,
                  ]);

              $msg= "      -> Création groupe de règles de dédoublonage";
              \Civi::log()->info("don_corps : ".$msg);
              if (VERBOSE==1){
                // echo $msg;
              }
          }
          $msg= " : ".$rule_name.' (id : '.$results['0']['id'].')' ;
          \Civi::log()->info("don_corps : ".$msg);
          if (VERBOSE==1){
            // echo $msg.PHP_EOL;
          }

          foreach($custom_dedupes as $custom_dedupe){
              $results = civicrm_api4('DedupeRule', 'create', [
              'values' => [
                  'dedupe_rule_group_id.name' => $rule_name,
                  'rule_table' => $custom_table[$custom_dedupe]['table'],
                  'rule_field' => $custom_table[$custom_dedupe]['column'],
                  'rule_weight' => '10',
              ],
              'checkPermissions' => FALSE,
              ]);
              $msg= "         Rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'];
              \Civi::log()->info("don_corps : ".$msg);
              if (VERBOSE==1){
                // echo $msg.PHP_EOL;
              }
            }



      ### Organization : création règle automatique de dédoublonage utilisant le nom ou courriel
          $rule_name = 'OrganizationUnsupervised';
          $dedupeRuleGroups = civicrm_api4('DedupeRuleGroup', 'get', [
              'select' => [
                  'id',
              ],
              'where' => [
                  ['name', '=', $rule_name],
              ],
              'checkPermissions' => FALSE,
          ]);

          if(isset($dedupeRuleGroups[0])){
              $results = civicrm_api4('DedupeRuleGroup', 'update', [
                  'values' => [
                      'contact_type' => 'Organization',
                      'threshold' => 20,
                      'used' => 'Unsupervised',
                      'title' => E::ts('Nom ou courriel (automatique)'),
                  ],
                  'where' => [
                      ['id', '=', $dedupeRuleGroups[0]['id']],
                  ],
                  'checkPermissions' => FALSE,
                  ]);
                  $msg= "      -> MAJ groupe de règles de dédoublonage";
                  \Civi::log()->info("don_corps : ".$msg);
                  if (VERBOSE==1){
                    // echo $msg;
                  }

                  $dedupeRules = civicrm_api4('DedupeRule', 'get', [
                      'where' => [
                          ['dedupe_rule_group_id.name', '=', $rule_name],
                      ],
                      'checkPermissions' => FALSE,
                      ]);

                  if(isset($dedupeRules[0])){
                      $results = civicrm_api4('DedupeRule', 'delete', [
                      'where' => [
                          ['dedupe_rule_group_id.name', '=', $rule_name],
                      ],
                      'checkPermissions' => FALSE,
                      ]);

                      $msg= " et suppression des règles attachées";
                      \Civi::log()->info("don_corps : ".$msg);
                      if (VERBOSE==1){
                        // echo $msg;
                      }
                  }

          } else {
              $results = civicrm_api4('DedupeRuleGroup', 'create', [
                  'values' => [
                      'contact_type' => 'Organization',
                      'threshold' => 10,
                      'used' => 'Supervised',
                      'title' => E::ts('Nom ou courriel (automatique)'),
                      'name' => $rule_name,
                  ],
                  'checkPermissions' => FALSE,
                  ]);

              $msg= "      -> Création groupe de règles de dédoublonage";
              \Civi::log()->info("don_corps : ".$msg);
              if (VERBOSE==1){
                // echo $msg;
              }
          }

          $msg= " : ".$rule_name.' (id : '.$results['0']['id'].')';
          \Civi::log()->info("don_corps : ".$msg);
          if (VERBOSE==1){
            // echo $msg.PHP_EOL;
          }

          foreach($custom_dedupes as $custom_dedupe){
              $results = civicrm_api4('DedupeRule', 'create', [
              'values' => [
                  'dedupe_rule_group_id.name' => $rule_name,
                  'rule_table' => $custom_table[$custom_dedupe]['table'],
                  'rule_field' => $custom_table[$custom_dedupe]['column'],
                  'rule_weight' => '10',
              ],
              'checkPermissions' => FALSE,
              ]);

              $msg= "         Rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'];
              \Civi::log()->info("don_corps : ".$msg);
              if (VERBOSE==1){
                // echo $msg.PHP_EOL;
              }
          }

      // ferme fichier de log

     /// Fin de Reglage des règles de dédoublonage


    $msg="  - Modification des filtres de champs personnalisés"; // permet de n'afficher que certains contacts pour certains champs
      \Civi::log()->info("don_corps : ".$msg);
      //\Civi\DonCorps\Install\CustomFieldFilter::applyGroupFilter('Pr_par_par', 'Personnel_centre_de_don_77'); // INUTILE CAR DEFINITION SUR LE SUBTYPE NON SUR GROUPE
      \Civi\DonCorps\Install\CustomFieldFilter::applyGroupFilter('Lacalisation', 'Emprunteurs_44');
      \Civi\DonCorps\Install\CustomFieldFilter::applyGroupFilter('D_posant', 'groupe_d_posants_porteurs_potent_24');
      \Civi\DonCorps\Install\CustomFieldFilter::applyGroupFilter('Demandeur', 'groupe_d_posants_porteurs_potent_24');

    

    $msg="*** Création/MAJ des Rules";
        \Civi::log()->info("don_corps : ".$msg);
        CiviRules::checkVersion();      // Vérifie la version installée de civirules
        CiviRules::installTriggers();   // Charge les déclencheurs définis dans civirules/triggers.json,

      ## les mgd files ne fonctionnent pas correctement car font référence 
      ## aux id des activités, culstom fields...qui varient d'une installation à l'autre
      InstallEntities::installFile('Entities_to_create_rules_deplace_lot.php');
      InstallEntities::installFile('Entities_to_create_rules_supprime_lot.php');
      InstallEntities::installFile('Entities_to_create_rules_inventaires.php');
      InstallEntities::installFile('Entities_to_create_rules_corrige_civilité.php');    
      InstallEntities::installFile('Entities_to_create_rules_compile_pieces_utilisations.php');
      InstallEntities::installFile('Entities_to_create_rules_copy_code_barres.php');
      InstallEntities::installFile('Entities_to_create_rules_demande_cremation.php');
      InstallEntities::installFile('Entities_to_create_rules_neutralise_adresse.php');

    $msg="  - Ajout des tags aux SavedSearches";
      // Pour lister les requetes : cv api4 SavedSearch.get '{"select":["id","name","label"],"checkPermissions":false}' --out=table
      $tag_search_file = E::path() .'/managed/tags_searches.txt';  // fichier contenant les tags attachés aux recherches
      SearchUpdateTag::updateTag($tag_search_file);
      \Civi::log()->info("don_corps : ".$msg);
      

  
    $msg="  - Changement des icones de menus";
      
      \Civi::log()->info("don_corps : ".$msg);
      // echo $msg.PHP_EOL;
    
      change_icon('Contributions','crm-i fa-money-bill-1');
      change_icon('Events','crm-i fa-users');
      // Fin du Changement des icones de menus


    $msg="*** Création des Templates de cérémonie"; //les statuts et les roles des participants doivent etre crées en amont (dans install)
      \Civi::log()->info("don_corps : ".$msg);
      InstallEntities::installFile('Entities_to_create_ceremonies_templates.php');

    $msg="*** Modification des regles de message liés aux cérémonies"; 
      \Civi::log()->info("don_corps : ".$msg);
      InstallEntities::installFile('Entities_to_create_ceremonies_rules.php');

    $msg="*** Modification des profils personnalisés";
      \Civi::log()->info("don_corps : ".$msg);
      
      # Lors de la création de profils de formulaires ou de custom layouts, des profils personnalisés sont générés
      # ils regroupent des champs personnalisés qui sont identifiés par custom_XX avec XX l'id du customfield correspondant
      # Lors d'une nouvelle installation les id des custom fields peuvent varier ce qui induit une incohérence
      # Les profils sont définis dans des fichiers /managed:UFGroup...mgd.php.disable car leprocess normal ne fonctinne pas bien
  
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
     

    $msg="*** Mise à jour des groupes des SavedSearch";
      UpdateSearchGroups::updateAll();

  }   // fin Implements hook_civicrm_postInstall().

# IMPLEMENTS hook_civicrm_container().
  function don_corps_civicrm_container(ContainerBuilder $container): void {
  ## Ajoute l'action imprimer document civioffice dans les utilisations du corps
  $container->autowire(\Civi\EventSubscriber\CiviOfficeSearchKitTaskSubscriber::class)->addTag('kernel.event_subscriber');
  ## Contournement d'un défaut de CiviOffice : le droit est déclaré pour l'action « renderWeb », alors que l'action s'appelle « render ».
  $container->autowire(\Civi\EventSubscriber\CiviOfficeAuthorizeSubscriber::class)->addTag('kernel.event_subscriber');
  
  ## Remonte tous les tokens d'utilisation du corps pour les rendre disponibles
  $container->autowire(\Civi\EventSubscriber\CiviOfficeTokenSubscriber::class)->addTag('kernel.event_subscriber');
  
  ## Idem pour les champs perso des devis
  $container->autowire(\Civi\EventSubscriber\DevisTokenSubscriber::class)->addTag('kernel.event_subscriber');
  $container->autowire(\Civi\EventSubscriber\DevisSearchKitTaskSubscriber::class)->addTag('kernel.event_subscriber');
}

  # IMPLEMENTS hook_civicrm_uninstall().
  function don_corps_civicrm_uninstall() {
    $msg="*** Activation des menus par défaut";
      \Civi::log()->info("don_corps : ".$msg);
      Menus::Enable(
        ['Contacts',
          'Search',
          'Contributions',
          'Events',
          'Mailings',
          'Reports',
          'Support',
          ]
      );


  }
