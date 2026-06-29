<?php


/*VERIFICATION

affichage groupes dynamiques dans CONTACT LAYOUT DB error
civirules supprime lots
civirules déplace lots
civirules inventaires

que les formules de politess par courrier et par mail suivantes sont actives
madame; monsieur; madamoiselle; MadameMonsieur, nom prénom

*/

require_once 'don_corps.civix.php';

use CRM_DonCorps_ExtensionUtil as E;
use Symfony\Component\DependencyInjection\ContainerBuilder;  ## pour créer nouvelles actions pour utilation des corps

define("VERBOSE","1"); // mettre à 1 pour afficher les messages lors de l'installation en plus des logs
define("LOGFILE", CRM_Core_Config::singleton()->configAndLogDir."/civicrm_ddc_installation.log"); // définition du ficher de log dans wp-content/uploads/civicrm/ConfigAndLogs


if (is_writable(LOGFILE)) {  // le ficher log existe 

      // Dans notre exemple, nous ouvrons le fichier $logfile en mode d'ajout
      // Le pointeur de fichier est placé à la fin du fichier
      // c'est là que $logtext sera placé
      if (!$fp = fopen(LOGFILE, 'a')) {
          echo "Impossible d'ouvrir le fichier (".LOGFILE.")"."\n";
          exit;
      }

  } else {  // le fichier log n'existe pas  ; le créer
      $fp = fopen(LOGFILE, 'c+b');
      echo "Création nouveau log : ".LOGFILE."\n";
      fclose($fp);
  }

// change le propriétaire et le groupe du fichier de log identique au repertoire de log principal
// dans le cas contraire, les menus ne s'affichent pas 
$own= fileowner(CRM_Core_Config::singleton()->configAndLogDir);
$grp= filegroup(CRM_Core_Config::singleton()->configAndLogDir);
chown(LOGFILE, $own);
chgrp(LOGFILE, $grp);



# DEFINITION DES FONCTIONS
  
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

  function _don_corps_is_civirules_installed() {   //  LOG OK checks whether civirules is installed.
      if (civicrm_api3('Extension', 'get', ['key' => 'civirules', 'status' => 'installed'])['count']) {
        return true;
      } elseif (civicrm_api3('Extension', 'get', ['key' => 'org.civicoop.civirules', 'status' => 'installed'])['count']) {
        return true;
      }
      return false;
    }   // fin de définition de fonction _don_corps_is_civirules_installed(

  function import_stuffCDC(){     // LOG OK utilisée pour importer les CDC
        $count=1;
        $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
        $values = func_get_arg(1);     // parametres de cette entité
        $check=array();
        
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log

        foreach ($values as $value) {
            unset ($value['external_identifier']);   
            unset ($value['id']);
            unset ($value['hash']);
            unset ($value['email_greeting_id']);
            unset ($value['email_greeting_custom']);
            unset ($value['email_greeting_display']);

            unset ($value['postal_greeting_id']);
            unset ($value['postal_greeting_custom']);
            unset ($value['postal_greeting_display']);

            unset ($value['suffix_id']);
            unset ($value['communication_style_id']);

            unset ($value['prefix_id']);
            $value['gender_id']=NULL;

            if (isset($value['employer_id'])){                     // si le contact a un champ employer_id non null
            
            $msg="employer_id : ".$value['employer_id'].PHP_EOL;   // on en modifie la valeur par l'id de l'institution crée au préalable
            fwrite($fp, $msg);

            if (VERBOSE==1){
              echo $msg;
            }

            $employeur = civicrm_api4('Contact', 'get', [
                    'select' => [
                    'id',
                    ],
                    'where' => [
                    ['contact_type', '=', 'Organization'],
                    ['external_identifier', '=', $value['employer_id']],
                    ],
                    'checkPermissions' => FALSE,
                ]);

                if (isset($employeur)){
                    $value['employer_id']=$employeur[0]['id'];
                    $msg="employer_id new: ".$value['employer_id'].PHP_EOL;
                    fwrite($fp, $msg);
                    if (VERBOSE==1){
                      echo $msg;
                    }
                } else {
                    $msg = "employer_id  extereal identifier n'existe pas : ".$value['employer_id '].PHP_EOL;
                    fwrite($fp, $msg);
                    if (VERBOSE==1){
                      echo $msg;
                    }
                 }
            }

            $contacts = civicrm_api4('Contact', 'get', [  // on recheche contacts avec le meme siret
                'where' => [
                ['legal_identifier', '=', $value['legal_identifier']],
                ],
                'limit' => 1,
                'checkPermissions' => FALSE,
            ]);

            $value['addressee_id']=1;

            if (!isset($contacts[0]['id'])){             // si le contact n'existe pas on le crée
                $results = civicrm_api4('Contact', 'create', [
                'values' => $value,
                'checkPermissions' => FALSE,
                ]);
                $msg = "         ".$count." CREATION : ".$value['sort_name'].PHP_EOL;
                fwrite($fp, $msg);
                if (VERBOSE==1){
                  echo $msg;
                }
                ++$count;

            } else {                                    // si le contact exite on l'update

                $id_to_update=$contacts[0]['id'];

                $results = civicrm_api4('Contact', 'update', [
                'values' => $value,
                'where' => [
                    ['id', '=', $id_to_update],
                ],
                'checkPermissions' => FALSE,
                ]);
                $msg= "         ".$count." MAJ : ".$value['sort_name']." | id : ".$id_to_update.PHP_EOL;
                fwrite($fp, $msg);
                if (VERBOSE==1){
                  echo $msg;
                }
                ++$count;
            }

            array_push($check, $results[0]['id']);
        }
        
        $msg= "         ".$entity." : ".count($check)." lignes ont été importées sur ".count($values);
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }

        if (count($check)==count($values)) {    // le bon nombre de lignes a été importées
            $msg= " ---> OK".PHP_EOL.PHP_EOL;
            fwrite($fp, $msg);
            if (VERBOSE==1){
              echo $msg;
            }
        }else {
        $msg ="---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
        fwrite($fp, $msg);
        echo $msg;
        }
        return ($check);

      fclose($fp); // ferme le fichier de log
    }   // fin de définition de fonction import_stuffCDC

  function import_addressCDC(){   // LOG OK utilisée pour importer les adresses des CDC
      $count=1;
      $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
      $values = func_get_arg(1);     // parametres de cette entité
      $check=array();
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log

      foreach ($values as $value) {
          // on verifie que le CDC rattaché à l'adresse a bien été crée,
          // c'est à dire qu'un CDC avect le meme SIRET (legal_identifier) existe bien

          $contacts = civicrm_api4('Contact', 'get', [
              'select' => [
                  'id',
              ],
              'where' => [
                  ['legal_identifier', '=', $value['contact_id.legal_identifier']],
              ],
              'limit' => 1,
              'checkPermissions' => FALSE,
              ]);

          $contact_id=$contacts[0]['id'];

          if (isset($contact_id)){          // le CDC lié à l'adresse existe
              $addresses = civicrm_api4('Address', 'get', [   // on recherche si une adresse identique existe pour ce contact

              'select' => [
                  'id',
              ],
              'where' => [
                  ['contact_id', '=', $contact_id],
                  ['OR', [['street_address', '=', $value['street_address']], ['street_address', 'IS NULL']]],
                  ['OR', [['postal_code', '=', $value['postal_code']], ['postal_code', 'IS NULL']]],
                  ['OR', [['city', '=', $value['city']], ['city', 'IS NULL']]],
                  ['location_type_id', '=',$value['location_type_id']],
              ],
              'checkPermissions' => FALSE,

              ]);
              $old_contact_id=$value['contact_id'];   // id du contact dans le fichier export 
              $value['contact_id']=$contact_id;       // id du contact dans la nouvelle base

              if (!isset($addresses[0]['id'])){       //  cette adresse n'existe pas pour ce contact ; on la crée

              $results = civicrm_api4('Address', 'create', [
              'values' => $value,
                  'checkPermissions' => FALSE,
              ]);
              $msg= "         ".$count." CREATION adresse : ".$value['street_address']." pour CDC id ".$value['contact_id'].PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
              ++$count;

              }  else {                                // cette adresse existe pour ce contact ; on la cmodifie
                  $address_to_create = $addresses[0]['id'];
                  $creation = civicrm_api4('Address', 'update', [
                  'values' => $value,
                  'where' => [
                  ['id', '=', $address_to_create],
                  ],
                  'checkPermissions' => FALSE,
              ]);
              $msg= "         ".$count." MAJ adresse : ".$value['street_address']." pour CDC id ".$value['contact_id'].PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
              ++$count;
          }
          }
          array_push($check, $old_contact_id); // crée un tableau avec les n° originaux des contacts (external id dans la nouvelle base)
                                              // à utiliser avec check_address.php
      }
      $msg= "         ".$entity." : ".count($check)." lignes ont été importées sur ".count($values);
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }

          if (count($check)==count($values)) {// le bon nombre de lignes a été importées
              $msg= " ---> OK".PHP_EOL.PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
          }else {
          $msg= "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
          fwrite($fp, $msg);
          echo $msg;
          }
          return ($check);

      fclose($fp); // ferme le fichier de log
    }   // fin de définition de fonction import_addressCDC

  function import_phoneCDC(){     // LOG OK utilisée pour importer les telephones des CDC
      $count=1;
      $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
      $values = func_get_arg(1);     // parametres de cette entité
      $check=array();
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log

      foreach ($values as $value) {
          // on verifie que le CDC rattaché au téléphone existe bien 
          // i.e., avec le meme numéro de SIRET (legal_identifier)
          $contacts = civicrm_api4('Contact', 'get', [
              'select' => [
                  'id',
              ],
              'where' => [
                  ['legal_identifier', '=', $value['contact_id.legal_identifier']],
              ],
              'limit' => 1,
              'checkPermissions' => FALSE,
              ]);

          if (isset($contacts[0]['id'])){          // le contact lié à l'adresse existe
              $contact_id=$contacts[0]['id'];       // adresse du contact dans la nouvelle base

              $phones = civicrm_api4('Phone', 'get', [   // on recherche si un téléphone identique existe pour ce contact
              'select' => [
                  'id',
              ],
              'where' => [
                  ['contact_id', '=', $contact_id],
                  ['phone', '=',$value['phone']],
              ],
              'checkPermissions' => FALSE,

              ]);
              $old_contact_id=$value['contact_id'];     // on remplace le contact_id de l'anceinne base par celui dans la nouvelle
              $value['contact_id']=$contact_id;

              if (!isset($phones[0]['id'])){             //  ce tel n'existe pas pour ce CDC ; on la crée

              $results = civicrm_api4('Phone', 'create', [
              'values' => $value,
                  'checkPermissions' => FALSE,
              ]);
              $msg = "         ".$count." CREATION téléphone: ".$value['phone']." pour CDC id ".$value['contact_id'].PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
              ++$count;

              }  else {                                // ce tel existe pour ce CDC ; on la cmodifie
                  $phone_to_create = $phones[0]['id'];
                  $creation = civicrm_api4('Phone', 'update', [
                  'values' => $value,
                  'where' => [
                  ['id', '=', $phone_to_create],
                  ],
                  'checkPermissions' => FALSE,
              ]);
              $msg= "         ".$count." MAJ téléphone : ".$value['phone']." pour CDC id ".$value['contact_id'].PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
              ++$count;
              ++$count;
          }
          }
          array_push($check, $old_contact_id); // crée un tableau avec les n° originaux des contacts (external id dans la nouvelle base)
                                              // à utiliser avec check_address.php
      }

      $msg= "         ".$entity." : ".count($check)." lignes ont été importées sur ".count($values);
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }

          if (count($check)==count($values)) {// le bon nombre de lignes a été importées
              $msg= " ---> OK".PHP_EOL.PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
          }else {
          $msg= "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
          fwrite($fp, $msg);
          echo $msg;
          }
          return ($check);

      fclose($fp); // ferme le fichier de log
    }   // fin de définition de fonction import_phoneCDC

  function import_emailCDC(){     // LOG OK utilisée pour importer les mails des CDC
      $count=1;
      $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
      $values = func_get_arg(1);     // parametres de cette entité
      $check=array();
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log

      foreach ($values as $value) {
          // on verifie que le contact rattaché au mail existe bien 
          // CAD avec le meme SIRET (legal-identifier)
          $contacts = civicrm_api4('Contact', 'get', [
              'select' => [
                  'id',
              ],
              'where' => [
                  ['legal_identifier', '=', $value['contact_id.legal_identifier']],
              ],
              'limit' => 1,
              'checkPermissions' => FALSE,
              ]);

          $contact_id=$contacts[0]['id'];

          if (isset($contacts[0]['id'])){               // le CDC lié au mail existe dans la nouvelle base
              $contact_id=$contacts[0]['id'];             // id du CDC dans la nouvelle base
              $emails = civicrm_api4('Email', 'get', [   // on recherche si un mail identique existe pour ce CDC
              'select' => [
                  'id',
              ],
              'where' => [
                  ['contact_id', '=', $contact_id],
                  ['email', '=',$value['email']],
              ],
              'checkPermissions' => FALSE,

              ]);
              $old_contact_id=$value['contact_id'];
              $value['contact_id']=$contact_id;       // on remplace le contact_id de l'anceinne base par celui dans la nouvelle




              if (!isset($emails[0]['id'])){             //  ce mail n'existe pas pour ce contact ; on le crée

              $results = civicrm_api4('Email', 'create', [
              'values' => $value,
                  'checkPermissions' => FALSE,
              ]);
              $msg= "         ".$count." CREATION email: ".$value['email']." pour CDC id ".$value['contact_id'].PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }

              ++$count;

              }  else {                                // ce tel existe pour ce contact ; on la cmodifie
                  $email_to_create = $emails[0]['id'];
                  $creation = civicrm_api4('Email', 'update', [
                  'values' => $value,
                  'where' => [
                  ['id', '=', $email_to_create],
                  ],
                  'checkPermissions' => FALSE,
              ]);
              $msg= "         ".$count." MAJ email : ".$value['email']." pour CDC id ".$value['contact_id'].PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
              ++$count;

          }
          }
          array_push($check, $old_contact_id); // crée un tableau avec les n° originaux des contacts (external id dans la nouvelle base)
                                              // à utiliser avec check_address.php

      }


      $msg= "         ".$entity." : ".count($check)." lignes ont été importées sur ".count($values);
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }

      if (count($check)==count($values)) {// le bon nombre de lignes a été importées
        $msg=" ---> OK".PHP_EOL.PHP_EOL;
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }
      }else {
      $msg= "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;
      }
    





      fclose($fp); // ferme le fichier de log
    }   // fin de définition de fonction import_emailCDC

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

  function _myextension_remove_wp_capabilities() { // LOG OK arguments role, caps
    if (!function_exists('get_role')) {
        return;
    }
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    $roleName = func_get_arg(0);
    $caps = func_get_arg(1);

    $role = get_role($roleName);
    if ($role) {
      $msg= "         SUPPRESSION de toutes les permissions pour le role ".$roleName.PHP_EOL;
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }
      fclose($fp);

      foreach ($caps as $cap) {
        $role->remove_cap($cap);
        }
      }
    }   // fin de définition de fonction _myextension_remove_wp_capabilities

  function _myextension_add_wp_capabilities() { // LOG OK arguments role, caps
    if (!function_exists('get_role')) {
        return;
    }
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    $roleName = func_get_arg(0);
    $caps = func_get_arg(1);
  
    $role = get_role($roleName);
    if ($role) {
      $msg="         AJOUT des permissions pour le role ".$roleName.PHP_EOL;
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }
      fclose($fp);
      foreach ($caps as $cap) {
        $role->add_cap($cap);
        }
      }
    }   // fin de définition de _myextension_add_wp_capabilities

  function deactivate_menu(){   // // LOG OK invoquée à l'installation pour desactiver un menu original 
    // syntaxe : deactivate_menu ('nom_du_menu_a_desactiver');
    // elle est appelée par : function don_corps_civicrm_install()
    //////////////
    $menu = func_get_arg(0);

    try {
      $results = civicrm_api4('Navigation', 'update', [  
      'values' => [
        'is_active' => FALSE,
        ],
        'where' => [
            ['name', '=', $menu],
        ],
        'checkPermissions' => FALSE,
      ]); 

      $msg= "         Menu ".$menu." désactivé"."\n";

    } catch (API_Exception $e) {                  // si l'update échoue
      $msg= "         Pas de Menu ".$menu."\n";
    }

      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }
      fclose($fp);


   }   // Fin de définition de la fonction : deactivate_menu()


  function activate_menu(){     // invoquée à la désinstallation pour activer les menus originaux les sous rubriques inutiles des menus qui sont definis par un fichier mgd
    // syntaxe : activate_menu ('nom_du_menu_dont les sous_rubriques_sont_a_desactiver');
    // elle est appelée par : function don_corps_civicrm_desinstall()
    //////////////
    $menu = func_get_arg(0);

    try {
      $results = civicrm_api4('Navigation', 'update', [  
      'values' => [
        'is_active' => TRUE,
        ],
        'where' => [
            ['name', '=', $menu],
        ],
        'checkPermissions' => FALSE,
      ]); 

      $msg="         Menu ".$menu." activé"."\n";

    } catch (API_Exception $e) {    // si l'update échoue
      $msg="         Pas de Menu ".$menu."\n";
      //CRM_Core_Session::setStatus('Pas de Menu '.$menu, 'Info', 'info');
    }
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }
      fclose($fp);

   }   // Fin de définition de la fonction : activate_menu()

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
        $msg= "         icone du menu ".$menu." changée pour : ".$icon."\n";
    } else {
        $msg= "         Pas de sous rubrique pour ".$menu."\n";
    }
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    fwrite($fp, $msg);
    echo $msg;
    fclose($fp);

    }   // Fin de définition de la fonction : change_icon()

  function create_entity(){ // LOG OK
    //////////// function create_entity /////////
    // Cette fonction est invoquée à l'installation pour créer les types de contacts, des options ... préalablement à la création des Custom groups/fields
    // depuis les fichiers managed/CustomGroup_...
    //
    // syntaxe : create_contact_type ($to create)
    //    $to_create =  [                                   // entité à modifier upgrader
    //      'entity' => 'UFJoin',
    //      'values' => [                                   // valeurs pour cette entité
    //        'uf_group_id:name' => $profile_to_update,
    //        'module' => 'Profile',
    //        'is_active' => TRUE,
    //        'module_data' => NULL,
    //      ],
    //    ];
    //
    //////////////
    $entity = func_get_arg(0)['entity'];     // nom de l'entité à créer (rule, condition....)
    $values = func_get_arg(0)['values'];     // parametres de cette entité
      
    switch ($entity) {
        case 'CiviRulesRuleAction':                           // CiviRulesRuleAction
            $check_entity = civicrm_api4($entity, 'get', [    
              'where' => [
                ['rule_id.name', '=', $values['rule_id.name']],
                ['action_id.name', '=', $values['action_id.name']],
              ],
              'checkPermissions' => FALSE,
            ]);
            $descr=$values['rule_id.name'];
        break;

        case 'CiviRulesRuleCondition':                        // CiviRulesRuleCondition
            $check_entity = civicrm_api4($entity, 'get', [    
              'where' => [
                ['rule_id.name', '=', $values['rule_id.name']],
                ['condition_id.name', '=', $values['condition_id.name']],
              ],
              'checkPermissions' => FALSE,
            ]);
            $descr=$values['rule_id.name'];
        break;

        case 'MessageTemplate':
            $check_entity = civicrm_api4($entity, 'get', [    // rmessage template
              'where' => [
                ['msg_title', '=', $values['msg_title']],
              ],
              'checkPermissions' => FALSE,
            ]);
            $descr=$values['msg_title'];
        break;

        case 'Event':
          $check_entity = civicrm_api4($entity, 'get', [    // message template
            'where' => [
              ['is_template', '=', TRUE],
              ['template_title', 'CONTAINS', 'Modèle de cérémonie test'],
            ],
            'checkPermissions' => FALSE,
          ]);
          $descr=$values['template_title'];
      break;

      case 'EventMessageRule':

        $check_entity = civicrm_api4($entity, 'get', [    // EventMessageRule
          'where' => [
            ['event_id', '=', $values['event_id']],
            ['template_id', '=', $values['template_id']],
          ],
          'checkPermissions' => FALSE,
        ]);

        //print_r($values);
        $descr=" règle";
    break;

        case 'OptionValue':                                             // Option value
          $check_entity = civicrm_api4($entity, 'get', [    
              'where' => [
                  ['option_group_id:name', '=', $values['option_group_id:name']],
                  [ 'name', "=", $values['name']],
              ],
              'checkPermissions' => FALSE,
          ]);
          $descr=$values['name'];
        break;

        case 'UFJoin':                                             // UF join (utilisation des profiles)
          $check_entity = civicrm_api4($entity, 'get', [    
              'where' => [
                  ['uf_group_id:name', "=", $values['uf_group_id:name']],
                  [ 'module', "=", $values['module']],
              ],
              'checkPermissions' => FALSE,
          ]);
          $descr=$values['uf_group_id:name'];
        break;

        case 'Navigation':                                             // Menus de navigation
          $check_entity = civicrm_api4($entity, 'get', [    
              'where' => [
                  ['parent_id:name', "=", $values['parent_id:name']],
                  [ 'name', "=", $values['name']],
              ],
              'checkPermissions' => FALSE,
          ]);
          $descr=$values['name'];
        break;

        case 'RelationshipType':                                             // Relations
          $check_entity = civicrm_api4($entity, 'get', [    
              'where' => [
                  ['name_a_b', '=', $values['name_a_b']],
              ],
              'checkPermissions' => FALSE,
          ]);
          $descr=$values['name_a_b'];
        break;

        case 'Afform':                                                      // Afform (traitement différent car pas d'id)
          $check_entity = civicrm_api4($entity, 'get', [    
              'where' => [
                  ['name', '=', $values['name']],
              ],
              'checkPermissions' => FALSE,
          ]);
          $descr=$values['name'];

          if(isset($check_entity[0])){            // si l'entité existe on l'update
            $msg= "         entité ".$entity." ".$descr." existe - update ".PHP_EOL;
            $results = civicrm_api4($entity, 'update', [
              'values' => $values,
              'where' => [
                ['name', '=', $check_entity[0]['name']],
              ],
              'checkPermissions' => FALSE,
            ]);
        
          }else{                                  // si l'entité n'existe pas, on la crée
            $msg= "         entité ".$entity." ".$descr." n'existe pas - creation".PHP_EOL;
            $results = civicrm_api4($entity, 'create', [
              'values' => $values,
              'checkPermissions' => FALSE,
            ]);
          }
          $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
          fwrite($fp, $msg);
          if (VERBOSE==1){
            echo $msg;
          }
          return; 
        break;

        default:
          $check_entity = civicrm_api4($entity, 'get', [    // rule, action, condition,OptionValue
            'where' => [
            ['name', '=', $values['name']],
            ],
            'checkPermissions' => FALSE,
          ]);
          $descr=$values['name'];
        break;
    }


    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log

    if(isset($check_entity[0])){            // si l'entité existe on l'update
      $msg= "         entité ".$entity." ".$descr." existe - update (".$check_entity[0]['id'].")".PHP_EOL;
      
      $results = civicrm_api4($entity, 'update', [
        'values' => $values,
        'where' => [
          ['id', '=', $check_entity[0]['id']],
        ],
        'checkPermissions' => FALSE,
      ]);

    }else{                                  // si l'entité n'existe pas, on la crée
      if($values['is_active']==TRUE){       // on verifie qu'elle est bien active sinon pas de création
        $msg= "         entité ".$entity." ".$descr." n'existe pas - creation".PHP_EOL;
        $results = civicrm_api4($entity, 'create', [
          'values' => $values,
          'checkPermissions' => FALSE,
        ]);
      } else {
        $msg=  "         entité ".$entity." ".$descr." n'existe pas mais inactive - ignorée".PHP_EOL;
        return;
      }
    }

      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }

    return $results[0]['id']; // retourne l'id de l'entité créée
    }   // Fin de la définition de la fonction : create entity()

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
      $msg="Pas de custom field passé à la fonction ".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }
      fclose($fp);
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

  function modif_filtre(){ // LOG OK
    //////////// function modif_filtre /////////
    // Cette fonction est invoquée après l'installation des champs personalisés qui filtrent les contacts
    //
    // syntaxe : modif_filtre (Custom_field_name Groupe_servant_de_filtre
    //   ;
    //
    // elle est appelée par : function don_corps_civicrm_install()
    //////////////
    $Custom_name = func_get_arg(0);
    $Group_name =func_get_arg(1);
    
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    
    $msg="      -> filtre du champ ".$Custom_name." par groupe ".$Group_name." : ";
    fwrite($fp, $msg);
    if (VERBOSE==1){
      echo $msg;
    }   

    $results = civicrm_api4('CustomField', 'get', [
      'select' => [
        'id',
      ],
      'where' => [
        ['name', '=', $Custom_name],
      ],
      'checkPermissions' => FALSE,
    ]);


    //echo "Custom filed id :".$results[0]['id']."\n";

    if (isset($results[0]['id'])){									      // si le champ personnalisé existe bien
      $groups = civicrm_api4('Group', 'get', [				// récupère l'id du groupe
        'select' => [
          'id',
        ],
        'where' => [
          ['name', '=', $Group_name],
        ],
        'checkPermissions' => FALSE,
      ]);

      if (isset($groups[0]['id'])){                       // si le groupe existe lui aussi

        $filter="action=lookup&group=".strval($groups[0]['id']);	// la valeur du filtre est définie à partir du groupe
        try{
          $results = civicrm_api4('CustomField', 'update', [		// injecte la valeur du filtre dans le champ personnalisé
            'values' => [
              'filter' => $filter,
            ],
            'where' => [
              ['name', '=', $Custom_name],
            ],
            'checkPermissions' => FALSE,
          ]);
          
          $msg= "modifié".PHP_EOL;

        } catch  (API_Exception $e) {
          $msg= "erreur lors de l'injection du filtre".PHP_EOL;
        }

        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }      

        }else{
          $msg= "le groupe n'existe pas".PHP_EOL;
          fwrite($fp, $msg);
          if (VERBOSE==1){
            echo $msg;
          }     
        }

      }else{
        $msg= "le champ personalisé n'existe pas".PHP_EOL;
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }     
      }
    fclose($fp);  
   }   // Fin de la définition de la fonction : modif_filtre()

  function deactivate_relation_type(){ // LOG OK
    //////////// function deactivate_relation_type /////////
    // Cette fonction est invoquée à l'activation pour désactiver les types de relation instalés par défauts et inutiles
    //
    // syntaxe : deactivate_relation_type ('name_a_b_de_la_relation_a_desactiver');
    //
    // elle est appelée par : function don_corps_civicrm_install()
    //////////////
    $relation_type = func_get_arg(0);
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log

    $relationshipTypes = civicrm_api4('RelationshipType', 'get', [
      'where' => [
        ['name_a_b', '=', $relation_type],
      ],
      'checkPermissions' => FALSE,
    ]);

    if (isset($relationshipTypes[0])){                          // si cette relation existe
        $results = civicrm_api4('RelationshipType', 'update', [   // on l'inactive
      'values' => [
        'is_active' => FALSE,
        ],
        'where' => [
          ['name_a_b', '=', $relation_type],
        ],
        'checkPermissions' => FALSE,
      ]);

        $msg= "         Type de relation ".$relation_type." désactivé"."\n";
      

    } else {
        $msg= "         Type de relation ".$relation_type." inexistant"."\n";
    }
    fwrite($fp, $msg);
    if (VERBOSE==1){
      echo $msg;
    }
    fclose($fp);


    }   // Fin de la définition de la fonction : deactivate_relation_type()

  function update_search(){ // LOG OK
    //////////// update_search() /////////
    // Cette fonction est invoquée à en post installation pour changer les icones de certains tabs de layouts et en inactiver d'autres
    //
    // syntaxe : update_search($searchname, $inactive_tabs);
    //
    //      $$searchname : nom der la recherche
    //      $search_api_params : parametres des api à modifier
    //      $tag [optionnel] : tag à ajouter 
    //
    // elle est appelée par : function don_corps_civicrm_postinstall()
    //////////////
    ##### update des requetes
      $nbargs = func_num_args();
      $searchname = func_get_arg(0);              // nom de la recherche sauvegardée à modifier
      $search_api_params = func_get_arg(1);       // parametres de l'api
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      
      if (func_num_args()==3){                    // s'il y a 3 arguments, c'est qu'il y a un tag
          $tags = civicrm_api4('Tag', 'get', [
            'select' => [
              'id',
            ],
            'where' => [
              ['name', '=', func_get_arg(2)],
            ],
            'checkPermissions' => FALSE,
          ]);
    
          if (isset($tags[0]['id'])){
            $tag= func_get_arg(2);                  // valeur du tag s'il existe sinon nul
          } else {
            $tag="";
          }
        
      }
      //echo "Tag : ".$tag.PHP_EOL;
      
      $savedSearches = civicrm_api4('SavedSearch', 'get', [
        'select' => [
          'id',
        ],
        'where' => [
          ['name', '=', $searchname],
        ],
        'checkPermissions' => FALSE,
      ]);
    
      //echo "id : ".$savedSearches[0]['id'].PHP_EOL;
    
      if(isset($savedSearches[0]['id'])){      // si la requete existe on l'update
        if ($search_api_params != "onlytag"){  // si on ne change pas que le tag
            try {
            $results = civicrm_api4('SavedSearch', 'update', [
              'values' => [
                'api_params' => $search_api_params,
              ],
              'where' => [
                ['id', '=', $savedSearches[0]['id']],
              ],
              'checkPermissions' => FALSE,
            ]);

            $msg= "         Requete ".$searchname." MAJ".PHP_EOL;
            
            } catch (API_Exception $e) {
              $msg= "         Erreur lors de la MAJ de la requete ".$searchname.": ".$e->getMessage().PHP_EOL;
            }

            fwrite($fp, $msg);
            if (VERBOSE==1){
              echo $msg;
            }

          }else{
            $msg= "         Requete ".$searchname." MAJ du seul tag".PHP_EOL;
            fwrite($fp, $msg);
            if (VERBOSE==1){
              echo $msg;
            }
          }
    
        if ($tag!=""){                                       /// la requete existe et le tag est non nul
    
          $entityTags = civicrm_api4('EntityTag', 'get', [
            'select' => [
              'id',
            ],
            'where' => [
              ['entity_table', '=', 'civicrm_saved_search'],
              ['entity_id', '=', $savedSearches[0]['id']] // $results[0]['id']],
            ],
            'checkPermissions' => FALSE,
          ]);
    
          if (!isset($entityTags[0]['id'])) {                     /// si le tag n'est pas associé à cette requete
              $tags = civicrm_api4('EntityTag', 'create', [      /// associe le tag  à la reqauete
                'values' => [
                  'entity_table' => 'civicrm_saved_search',
                  'tag_id.name' => $tag,
                  'entity_id' => $savedSearches[0]['id'],
                ],
                'checkPermissions' => FALSE,
              ]);
          //echo "      & Tag ajouté".PHP_EOL;
          }          
    
        }
      } else {                                              // si la requete n'exste pas
        $msg= "         La requete ".$searchname." n'existe pas".PHP_EOL;
          fwrite($fp, $msg);
          if (VERBOSE==1){
            echo $msg;
          }

      }
      fclose($fp);
    }   // Fin de la définition de la fonction : update_search() 

  function modif_profils_perso(){ // LOG OK
    #####
    # Lors de la création de profils de formulaires ou de custom layouts, des profils personnalisés sont générés
    # ils regroupent des champs personnalisés qui sont identifiés par custom_XX avec XX l'id du customfield correspondant
    # Lors d'une nouvelle installation les id des custom fields peuvent varier ce qui induit une incohérence
    # Ici on utilise un tableau donnant la correspondance entre le nom original du champ personnlisé (uf id) 
    # et son nom ; cela permt de modifier celui-ci dans la nouvelle installation
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    $toimport_file = func_get_arg(0); // nom du fichier à importer

    $json = file_get_contents($toimport_file);
    $convert = json_decode($json, true);

    $msg="         Table de conversion : ".$toimport_file.PHP_EOL;

    ### Modification des UF Fields pour corrier les custom_id

    $new = [];
    $new[0]['id']=NULL;
    $new[0]['field_name']=NULL;
    $new[0]['field_name:name']=NULL;
    $new[0]['label']=NULL;

    foreach ($convert as $k => $v) {
        $new[$k + 1] = $v;
    }

    $convert=$new;

    $labels_table = array_column($convert, 'label');
    $names_table = array_column($convert, 'field_name:name');
    $customs_table = array_column($convert, 'field_name');

    $uFFields = civicrm_api4('UFField', 'get', [
        'select' => [
          'field_name',
          'field_name:name',
          'label',
        ],
        'where' => [
          ['field_name', 'CONTAINS', 'custom_'],
        ],
        'orderBy' => [
          'uf_group_id:name' => 'ASC',
        ],
        'checkPermissions' => FALSE,
      ]);

    if (isset($uFFields[0])){      
      foreach ($uFFields as $uFField){

        if (isset($uFField['label'])) {           // si un label existe, on récupère la valeur de field_name_name 
          $label=$uFField['label'];
          $key = array_search($label, $labels_table); 
          if ($key){
                $name=$convert[$key]['field_name:name'];
          } else {
            $msg="ERREUR : pas de label ".$label.' dans le fichier de correspondance : '.$toimport_file.PHP_EOL;
            fwrite($fp, $msg);
            echo $msg;
            fclose($fp);
            exit;
          }
        }  else { // si ce label n'existe pas 
            //echo 'pas de label'.PHP_EOL;
            if (isset($uFField['field_name:name'])){    // si un field_name:name de type customgroup.customfield existe
              $name=$uFField['field_name:name'];          // on la conserve
              $key = array_search($name, $names_table);   
              if ($key){
                    $label=$convert[$key]['field_name:label'];  // on récupère label depuis la table de correspondance en utilisant le field_name:name comme critere de concordance
              } else {
                $msg= "ERREUR : pas de name ".$name.'dans le fichier de correspondance : '.$toimport_file.PHP_EOL;
                fwrite($fp, $msg);
                echo $msg;
                fclose($fp);
                exit;
              }
            } else {                                          // pas de field _name:name de type customgroup.customfield 
              $key = array_search($name, $customs_table);

              if ($key){
                $label=$convert[$key]['field_name:label'];      // on récupère label et name depuis table correspondance
                $name=$convert[$key]['field_name:name'];        // en utilisant le custom_name comme critere de concordance
              } else {
                $msg ="ERREUR : pas de name ".$name.'dans le fichier de correspondance : '.$toimport_file.PHP_EOL;
                fwrite($fp, $msg);
                echo $msg;
                fclose($fp);
                exit;
              }
            }
        }

        $position = strpos($name, '.');             // retrouve la position du point dans le nom
        if ($position !== false) {
          $group = substr($name, 0, $position);    // ne garde que ce qui est à gauche du point, donc le prefixe
          $custom = substr($name, $position + 1);// ne garde que ce qui est à droite du point, donc le nom du custom group ou du profile
          //echo 'group : '.$group.'        custom field :'.$custom.PHP_EOL;
            $customFields = civicrm_api4('CustomField', 'get', [
              'select' => [
                'id',
              ],
              'where' => [
                ['custom_group_id:name', '=', $group],
                ['name', '=', $custom],
              ],
              'checkPermissions' => FALSE,
            ]);

            if(isset($customFields[0])){
              $field_name='custom_'.$customFields[0]['id'];
            }

          $msg= "         UFFIELF id : ".$uFField['id']." name : ".$name." | label : ".$label." | field name : ".$field_name;
          fwrite($fp, $msg);
          if (VERBOSE==1){
            echo $msg;
          }

          if($field_name!=$uFField['field_name']){
            $msg= " - MAJ".PHP_EOL;  
            fwrite($fp, $msg);
            if (VERBOSE==1){
              echo $msg;
            }
            

            $results = civicrm_api4('UFField', 'update', [        // on inject les nouvelles valeurs dans
              'values' => [
                'label' => $label,
                'field_name' => $field_name,
              ],

              'where' => [
                //['field_name', '=', $uFField['field_name']],
                
                ['id', '=', $uFField['id']],
              ],

              'checkPermissions' => FALSE,
            ]); 
            //print_r($results);

            }else {
              $msg= " - Inchangé".PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
              echo $msg;
            }
            }
        }
      }
    }

    #### Creation / MAJ des UFJOINS
    # Les UFjoins mettren en relation un profil avec les contacts layous ; sinon ils ne s'affichent pas 
    # Il faut un UFjoin pour les modeles CustomSummary et un pour Profile par groupe de profil

    $contactLayouts = civicrm_api4('ContactLayout', 'get', [
      'select' => [
        'blocks',
      ],
      'checkPermissions' => FALSE,
    ]);

    $summary_profile_list=array();

    foreach($contactLayouts as $contactLayout){
      $block_cols=$contactLayout['blocks'];
      foreach($block_cols as $block_col){
        $blocks=$block_col;
        foreach($blocks as $block){
          $profiles=$block;
          foreach ($profiles as $profile){
            $position = strpos($profile['name'], '.');             // retrouve la position du point dans le nom
            if ($position !== false) {
              $prefix = substr($profile['name'], 0, $position);    // ne garde que ce qui est à gauche du point, donc le prefixe
              $postfix = substr($profile['name'], $position + 1);// ne garde que ce qui est à droite du point, donc le nom du custom group ou du profile
              if ($prefix=='profile'){
                array_push($summary_profile_list, $postfix);
              }
            }
          }
        }
      }
    }

    foreach($summary_profile_list as $profile){
        $uFJoins = civicrm_api4('UFJoin', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['uf_group_id:name', '=', $profile],
            ['module', '=', 'Contact Summary'],
          ],
          'checkPermissions' => FALSE,
        ]);

        $msg= "         UFJoin pour Contact Summary et profil : ".$profile;
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }

      if(!isset($uFJoins[0]['id'])){
          $results = civicrm_api4('UFJoin', 'create', [
            'values' => [
              'module' => 'Contact Summary',
              'uf_group_id.name' => $profile,
            ],
            'checkPermissions' => FALSE,
          ]);
          $msg=" - Créé".PHP_EOL;
      }else{
          $results = civicrm_api4('UFJoin', 'update', [
          'values' => [
            'uf_group_id.name' => $profile,
          ],
          'where' => [
            ['id', '=', $uFJoins[0]['id']],
          ],
          'checkPermissions' => FALSE,
        ]);
              $msg=" - MAJ".PHP_EOL;
      }
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }

    }


    foreach($summary_profile_list as $profile){
        $uFJoins = civicrm_api4('UFJoin', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['uf_group_id:name', '=', $profile],
            ['module', '=', 'Profile'],
          ],
          'checkPermissions' => FALSE,
        ]);

        $msg="         UFJoin pour Profile (standalone form) et profil : ".$profile;
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }

      if(!isset($uFJoins[0]['id'])){
          $results = civicrm_api4('UFJoin', 'create', [
            'values' => [
              'module' => 'Profile',
              'uf_group_id.name' => $profile,
            ],
            'checkPermissions' => FALSE,
          ]);
          $msg= " - Créé".PHP_EOL;
      }else{
          $results = civicrm_api4('UFJoin', 'update', [
          'values' => [
            'uf_group_id.name' => $profile,
          ],
          'where' => [
            ['id', '=', $uFJoins[0]['id']],
          ],
          'checkPermissions' => FALSE,
        ]);
              $msg= " - MAJ".PHP_EOL;
      }
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }

    }
    fclose($fp);
   }    // Fin de la définition de la fonction : modif_profils_perso

  function modif_profils_utilisation(){ // LOG OK
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      $uFGroups = civicrm_api4('UFGroup', 'get', [   // récupère la liste des profils
      'select' => [
          'name',
          'id',
      ],
      'checkPermissions' => FALSE,
      ]);
          
          
      foreach ($uFGroups as $uFGroup){                        // crée un tableau avec [id_UFGroup][name_UFGroup]
          $profile_names[$uFGroup['id']]=$uFGroup['name'];
      }
      $cancel_url = admin_url("admin.php?page=CiviCRM");  // url à charger si annulation 
      $url = admin_url("admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid=")."{contact.id}";  // url à charger apres creation du contact utilisant le profil 
      
      $profiles_to_update = ['Mairie','Lieu_de_stockage','Centre_d_accueil_des_corps','Personnel_de_centre_de_don_de_corps','Inscription_proche_donateur_14', 'Demandeur_information_22', 'inscription_pompes', 'Inscription_donateur','Inscription_anat_compar_e'];

      // Liste de profils à associer à un role (ceux utilisés pour creation contacts) name_and_address = ionscription donneur ; Inscription_proche_donateur_27 : pompes
      
      foreach ($profiles_to_update as $profile_to_update) {
          $position = array_search($profile_to_update, $profile_names);
          if ($position !== false) {                                          // Si le profil est déja créé 
              $to_create =  [                                                 // modifie l'URL à afficher apres la creation (post url) par un profil
                  'entity' => 'UFGroup',
                  'values' => [
                      'post_url' => $url,
                      'cancel_url' => $cancel_url,
                      'name' => $profile_to_update,
                  ],
                ];
                create_entity($to_create);  // create ou update UFGROUP

              $to_create =  [                                                 // ajoute à chacun de ces profils l'utilisaiton "Profile" = "Formulaire ou Liste à afficher"
                'entity' => 'UFJoin',
                'values' => [
                    'uf_group_id:name' => $profile_to_update,
                    'module' => 'Profile',
                    'is_active' => TRUE,
                    'module_data' => NULL,
                ],
              ];
              create_entity($to_create);  // create ou update UFJOIN
            
            } else {
                $msg= $profile_to_update." : Profil non trouvé ////////.".PHP_EOL;
                fwrite($fp, $msg);
                echo $msg;
            }
      }
    fclose($fp);
   }    // Fin de la définition de la fonction : modif_profils_utilisation

  function modif_profils_navigation(){ // LOG OK
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    /// Modifie les menus de navigation liés aux profil de création de contacts
    $url_menus_to_change = func_get_arg(0); // liste des menus à modifier
                                            // Profil name, parent_id:name, name du menu navigation

    foreach ($url_menus_to_change as $url_menu_to_change){
      $uFGroups = civicrm_api4('UFGroup', 'get', [                        // récupere l'id du profil
          'select' => [
          'id',
          ],
          'where' => [
          ['name', '=', $url_menu_to_change[0]],
          ],
          'checkPermissions' => FALSE,
      ]);

      if (isset($uFGroups[0]['id'])){                                       // si le profil existe

          $msg= "         ".$url_menu_to_change[1]." / ".$url_menu_to_change[2]." / ".$url_menu_to_change[0]." : ".PHP_EOL;

          $to_create =  [                                                 // modifie l'URL pour le menu
              'entity' => 'Navigation',
              'values' => [
                  'parent_id:name' => $url_menu_to_change[1],
                  'name' => $url_menu_to_change[2],
                  'url' => 'civicrm/profile/create/?gid='.$uFGroups[0]['id'].'&reset=1',
                  'permission' => 'add contacts',
                  'is_active' =>true,
                  ],
              ];
          create_entity($to_create);                                     // create ou update navigation menu  

      }else {

          $msg= "***** Le profil ".$url_menu_to_change[0]." n'existe pas *****".PHP_EOL;
      }
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }
    }
    fclose($fp);
   }    // Fin de la définition de la fonction : modif_profils_navigation

  function ajoute_CDC(){ // LOG OK
       $exp_dir = func_get_arg(0); // liste des menus à modifier
       $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    ## On vérifie qu'il existe bien une location_type pour le cesp - Sinon on la crée

        $locationTypes = civicrm_api4('LocationType', 'get', [
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
            fwrite($fp, $msg);
            echo $msg;


        $msg= "de la location type : ".$results[0]['name']." (".$results[0]['id'].")".PHP_EOL;

        fwrite($fp, $msg);
        echo $msg;

    ## Fin verification location_type pour CESP

      // import organisations
          $name =  "05_organisations";
          $toimport_file = $exp_dir.$name.".txt";
          $json = file_get_contents($toimport_file);
          $toimport = json_decode($json, true);
          $msg= "      -> ".count($toimport)." Organisations à importer".PHP_EOL;
          fwrite($fp, $msg);
          echo $msg;
          $check=import_stuffCDC('Contact',$toimport);
          $chk_file = $exp_dir."check_".$name.".txt";
          file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
          //echo $chk_file." écrit".PHP_EOL;

      // import adresses
          $name =  "15_adresses";
          $toimport_file = $exp_dir.$name.".txt";
          $json = file_get_contents($toimport_file);
          $toimport = json_decode($json, true);
          $msg= "      -> ".count($toimport)." Adresses à importer".PHP_EOL;
          fwrite($fp, $msg);
          echo $msg;
          $check=import_addressCDC('Address',$toimport); // appelle la fonction import  et assigne à check la liste des anciennes id de contact
          $chk_file = $exp_dir."check_".$name.".txt";
          file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
          //echo $chk_file." écrit".PHP_EOL;

      // import telephones
          $name = '20_telephone';                     // nom du fichier à importer sans le suffixe
          $toimport_file = $exp_dir.$name.".txt";
          $json = file_get_contents($toimport_file);
          $toimport = json_decode($json, true);
          $msg = "      -> ".count($toimport)." Téléphones à importer".PHP_EOL;
          fwrite($fp, $msg);
          echo $msg;
          $check=import_phoneCDC('Phone',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact
          $chk_file = $exp_dir."check_".$name.".txt";
          file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
          //echo $chk_file." écrit".PHP_EOL;

      // import email
          $name = '25_Email';                     // nom du fichier à importer sans le suffixe
          $toimport_file = $exp_dir.$name.".txt";
          $json = file_get_contents($toimport_file);
          $toimport = json_decode($json, true);

          $msg= "      -> ".count($toimport)." Emails à importer".PHP_EOL;
          fwrite($fp, $msg);
          echo $msg;
          $check=import_emailCDC('Email',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact
          $chk_file = $exp_dir."check_".$name.".txt";
          file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
          //echo $chk_file." écrit".PHP_EOL;
          fclose($fp);
       # fin de Ajout des centres de don du corps


   }    // Fin de la définition de la fonction ajoute_CDC
# FIN DE LA DECLARATION DES FONCTIONS

# IMPLEMENTS hook_civicrm_managed() // LOGS OK
  # @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed*/

  function don_corps_civicrm_managed(&$entities) {
   // _don_corps_civix_civicrm_managed($entities);

    // Load the triggers when civirules is installed.
    if (_don_corps_is_civirules_installed()) {
      CRM_Civirules_Utils_Upgrader::insertTriggersFromJson(E::path('civirules/triggers.json'));
    }
    #$fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log

    #$msg=PHP_EOL.date("Y-m-d H:i:s")." @@@  hook_civicrm_managed ".PHP_EOL;
    #fwrite($fp, $msg);
    #echo $msg;

      } # fin de la définition de don_corps_civicrm_managed

# IMPLEMENTS hook_civicrm_config() // LOGS OK
  #@link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/


  function don_corps_civicrm_config(&$config): void {
    #$fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    #$msg=PHP_EOL.date("Y-m-d H:i:s")." @@@  hook_civicrm_config ".PHP_EOL;
    #fwrite($fp, $msg);
    #echo $msg;
    #fclose($fp);
    //function don_corps_civicrm_config(&$config) {
    _don_corps_civix_civicrm_config($config);
    
  }

# IMPLEMENTS hook_civicrm_install() //LOG OK
  # @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install/

  function don_corps_civicrm_install(): void {
    //function don_corps_civicrm_install() {
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    $msg=PHP_EOL.date("Y-m-d H:i:s")." @@@  hook_civicrm_install ".PHP_EOL;
    fwrite($fp, $msg);
    echo $msg;

    $msg= "  - Vérification de la version de l'extension CiviRules".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;
      $ext = civicrm_api4('Extension', 'get', [
        'where' => [
          ['full_name', '=', 'org.civicoop.civirules'],
        ],
        'select' => ['version'],
      ]);

      if (empty($ext[0]['version']) || version_compare($ext[0]['version'], '3.36.0', '<')) {
        $msg= "  L’extension org.civicoop.civirules 3.36.0 ou supérieure est requise".PHP_EOL;
        fwrite($fp, $msg);
        echo $msg;
      }


    $msg= "  - Modification des privilèges civicrm selon le rôle wordpress".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;
      $all_caps=[                   // liste tous les privileges
        'access_all_custom_data',
        'access_civicontribute',
        'access_civicrm',
        'access_civievent',
        'access_civimail_subscribe_unsubscribe_pages',
        'access_civimail',
        'access_civimember',
        'access_civioffice',
        'access_civireport',
        'access_contact_dashboard',
        'access_contact_reference_fields',
        'access_deleted_contacts',
        'access_report_criteria',
        'access_uploaded_files',
        'activate_plugins',
        'add_contact_notes',
        'add_contacts',
        'administer_afform',
        'administer_dedupe_rules',
        'administer_private_reports',
        'administer_reports',
        'administer_reserved_groups',
        'administer_reserved_reports',
        'administer_reserved_tags',
        'administer_search_kit',
        'administer_tagsets',
        'administrator',
        'author',
        'contributor',
        'create_users',
        'delete_activities',
        'delete_contacts',
        'delete_in_civicontribute',
        'delete_in_civievent',
        'delete_in_civimail',
        'delete_in_civimember',
        'delete_others_pages',
        'delete_others_posts',
        'delete_pages',
        'delete_plugins',
        'delete_posts',
        'delete_private_pages',
        'delete_private_posts',
        'delete_published_pages',
        'delete_published_posts',
        'delete_themes',
        'delete_users',
        'edit_all_contacts',
        'edit_all_events',
        'edit_contributions',
        'edit_dashboard',
        'edit_event_participants',
        'edit_files',
        'edit_groups',
        'edit_inbound_email_basic_information_and_content',
        'edit_inbound_email_basic_information',
        'edit_memberships',
        'edit_message_templates',
        'edit_my_contact',
        'edit_others_pages',
        'edit_others_posts',
        'edit_pages',
        'edit_plugins',
        'edit_posts',
        'edit_private_pagesv',
        'edit_private_posts',
        'edit_published_pages',
        'edit_published_posts',
        'edit_system_workflow_message_templates',
        'edit_theme_options',
        'edit_themes',
        'edit_user_driven_message_templates',
        'edit_users',
        'export',
        'force_merge_duplicate_contacts',
        'import',
        'install_plugins',
        'install_themes',
        'level_0',
        'level_1',
        'level_10',
        'level_2',
        'level_3',
        'level_4',
        'level_5',
        'level_6',
        'level_7',
        'level_8',
        'level_9',
        'list_users',
        'make_online_contributions',
        'manage_categories',
        'manage_event_profiles',
        'manage_links',
        'manage_options',
        'manage_tags',
        'merge_duplicate_contacts',
        'moderate_comments',
        'profile_create',
        'profile_edit',
        'profile_listings',
        'profile_view',
        'promote_users',
        'publish_pages',
        'publish_posts',
        'read_private_pages',
        'read_private_posts',
        'read',
        'register_for_events',
        'remove_users',
        'save_report_criteria',
        'sign_civicrm_petition',
        'switch_themes',
        'unfiltered_html',
        'unfiltered_upload',
        'update_core',
        'update_plugins',
        'update_themes',
        'upload_files',
        'view_all_activities',
        'view_all_contacts',
        'view_all_notes',
        'view_event_info',
        'view_event_participants',
        'view_my_contact',
        'view_my_invoices',
        'view_public_civimail_content',
        'view_report_sql',
        ];

      $author_caps = [              // liste des privileges du role author
        'upload_files',
        'edit_posts',
        'edit_published_posts',
        'publish_posts',
        'read',
        'level_2',
        'level_1',
        'level_0',
        'delete_posts',
        'delete_published_posts',
        'sign_civicrm_petition',
        'access_civimember',
        'edit_memberships',
        'delete_in_civimember',
        'add_contacts',
        'view_all_contacts',
        'edit_all_contacts',
        'view_my_contact',
        'edit_my_contact',
        'delete_contacts',
        'access_deleted_contacts',
        'edit_groups',
        'access_uploaded_files',
        'profile_listings',
        'profile_create',
        'profile_edit',
        'profile_view',
        'access_all_custom_data',
        'view_all_activities',
        'delete_activities',
        'edit_inbound_email_basic_information',
        'edit_inbound_email_basic_information_and_content',
        'access_civicrm',
        'access_contact_dashboard',
        'manage_tags',
        'administer_reserved_groups',
        'administer_tagsets',
        'administer_reserved_tags',
        'administer_dedupe_rules',
        'merge_duplicate_contacts',
        'force_merge_duplicate_contacts',
        'view_all_notes',
        'add_contact_notes',
        'access_contact_reference_fields',
        'edit_message_templates',
        'edit_system_workflow_message_templates',
        'edit_user_driven_message_templates',
        'view_my_invoices',
        'access_civievent',
        'edit_event_participants',
        'edit_all_events',
        'register_for_events',
        'view_event_info',
        'view_event_participants',
        'delete_in_civievent',
        'manage_event_profiles',
        'access_civicontribute',
        'edit_contributions',
        'make_online_contributions',
        'delete_in_civicontribute',
        'access_civimail',
        'access_civimail_subscribe_unsubscribe_pages',
        'delete_in_civimail',
        'view_public_civimail_content',
        'access_civireport',
        'access_report_criteria',
        'save_report_criteria',
        'administer_private_reports',
        'administer_reserved_reports',
        'administer_reports',
        'view_report_sql',
        'administer_search_kit',
        'administer_afform',
        'access_civioffice',
        'author',
        ];
      

      
      $contributor_caps = [         // liste des privileges du role contributor
        'edit_posts',
        'read',
        'level_1',
        'level_0',
        'delete_posts',
        'sign_civicrm_petition',
        'view_all_contacts',
        'view_my_contact',
        'access_uploaded_files',
        'profile_listings',
        'profile_create',
        'profile_edit',
        'profile_view',
        'access_all_custom_data',
        'view_all_activities',
        'access_civicrm',
        'access_contact_dashboard',
        'view_all_notes',
        'add_contact_notes',
        'view_my_invoices',
        'access_civievent',
        'register_for_events',
        'view_event_info',
        'view_event_participants',
        'make_online_contributions',
        'access_civimail_subscribe_unsubscribe_pages',
        'view_public_civimail_content',
        'contributor',
        ];


      $msg = "      -> Suppression des privilèges de tous les rôles".PHP_EOL;
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }
      fclose($fp);
      _myextension_remove_wp_capabilities('editor', $all_caps);
      _myextension_remove_wp_capabilities('author', $all_caps);
      _myextension_remove_wp_capabilities('contributor', $all_caps);
      _myextension_remove_wp_capabilities('subscriber', $all_caps);
      _myextension_remove_wp_capabilities('anonymous_user', $all_caps);

      $msg = "      -> Ajout des privilèges aux rôles utlilisés".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      if (VERBOSE==1){
        echo $msg;
      }
      fclose($fp);
      _myextension_add_wp_capabilities('author', $author_caps);
      _myextension_add_wp_capabilities('contributor', $contributor_caps);


    $msg= "  - Création/inactivation des intervales de date personnalisés".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      $to_create =  [       // intervales de date personnalisés
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'relative_date_filters',
          'label' => E::ts("Derniers 5 ans incluant aujourd'hui"),
          'value' => 'ending_5.year',
          'name' => "Derniers 5 ans incluant aujourd'hui",
          'grouping' => NULL,
          'filter' => 0,
          'is_default' => FALSE,
          'weight' => 65,
          'description' => NULL,
          'is_optgroup' => FALSE,
          'is_reserved' => FALSE,
          'is_active' => TRUE,
          'component_id' => NULL,
          'domain_id' => NULL,
          'visibility_id' => NULL,
          'icon' => NULL,
          'color' => NULL,
        ],
      ];
      create_entity($to_create);

    $msg= "  - Création/inactivation des prefixes de contact".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
      $to_create =  [       // Création des prefixes de contact : Mme
        'entity' => 'OptionValue',
        'values' => [
          'label' => 'Mme',
          'value' => '1',
          'name' => 'Mrs.',
          'grouping' => NULL,
          'filter' => 0,
          'is_default' => NULL,
          'weight' => 1,
          'description' => NULL,
          'is_optgroup' => FALSE,
          'is_reserved' => FALSE,
          'is_active' => TRUE,
          'component_id' => NULL,
          'domain_id' => NULL,
          'visibility_id' => NULL,
          'icon' => NULL,
          'color' => NULL,
          'option_group_id:name' => 'individual_prefix',
        ],
      ];
        create_entity($to_create);

        $to_create =  [       // Création des prefixes de contact : Melle
          'entity' => 'OptionValue',
          'values' => [
              'label' => 'Melle',
              'value' => '2',
              'name' => 'Ms.',
              'grouping' => NULL,
              'filter' => 0,
              'is_default' => NULL,
              'weight' => 2,
              'description' => NULL,
              'is_optgroup' => FALSE,
              'is_reserved' => FALSE,
              'is_active' => TRUE,
              'component_id' => NULL,
              'domain_id' => NULL,
              'visibility_id' => NULL,
              'icon' => NULL,
              'color' => NULL,
              'option_group_id:name' => 'individual_prefix',
          ],
        ];
        create_entity($to_create);

        $to_create =  [       // Création des prefixes de contact : M.
          'entity' => 'OptionValue',
          'values' => [
            'label' => 'M.',
            'value' => '3',
            'name' => 'Mr.',
            'grouping' => NULL,
            'filter' => 0,
            'is_default' => NULL,
            'weight' => 3,
            'description' => NULL,
            'is_optgroup' => FALSE,
            'is_reserved' => FALSE,
            'is_active' => TRUE,
            'component_id' => NULL,
            'domain_id' => NULL,
            'visibility_id' => NULL,
            'icon' => NULL,
            'color' => NULL,
            'option_group_id:name' => 'individual_prefix',
          ],
        ];
        create_entity($to_create);

        $to_create =  [       // Création des prefixes de contact : Mx
          'entity' => 'OptionValue',
          'values' => [
            'label' => 'Mx.',
            'value' => '4',
            'name' => 'Dr.',
            'grouping' => NULL,
            'filter' => 0,
            'is_default' => FALSE,
            'weight' => 4,
            'description' => NULL,
            'is_optgroup' => FALSE,
            'is_reserved' => FALSE,
            'is_active' => TRUE,
            'component_id' => NULL,
            'domain_id' => NULL,
            'visibility_id' => NULL,
            'icon' => NULL,
            'color' => NULL,
            'option_group_id:name' => 'individual_prefix',
          ],
        ];
        create_entity($to_create);
    
    
    
    $msg= "  - Création/inactivation des types de contact".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
      
        $to_create =  [       // Création des types de contact : donnneur
          'entity' => 'ContactType',
          'values' => [
              'name' => 'Donateur',
              'label' => 'Donneur',
              'description' => 'Donneur inscrit',
              'icon' => 'fa-user',
              'parent_id.name' => 'Individual',
              'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);


        $to_create =  [       // Création des types de contact : proches
          'entity' => 'ContactType',
          'values' => [
                'name' => 'Proches',
                'label' => 'Proches',
                'description' => 'Proche lié à un donneur',
                'parent_id.name' => 'Individual',
                'icon' => 'fa-user',
                'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [       // Création des types de contact : Demandeur_d_information
        'entity' => 'ContactType',
        'values' => [
            'name' => 'Demandeur_d_information',
            'label' => 'Demandeur information',
            'description' => 'Personne demandant un dossier,  non encore inscrite',
            'parent_id.name' => 'Individual',
            'icon' => 'fa-user',
            'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [       // Création des types de contact : Personnels
          'entity' => 'ContactType',
          'values' => [
            'name' => 'Personnel',
            'label' => E::ts('Personnel'),
            'description' => "Employés du centre d'accueil des corps",
            'image_URL' => NULL,
            'icon' => NULL,
            'parent_id.name' => 'Individual',
            'is_active' => TRUE,
            'is_reserved' => FALSE,
            ],
          ];
          create_entity($to_create);


          $to_create =  [       // Création des types de contact : Animal
            'entity' => 'ContactType',
            'values' => [
              'name' => 'Animal',
              'label' => E::ts('Animal'),
              'description' => "Spécimens non humains",
              'image_URL' => NULL,
              'icon' => "fa-cow",
              'parent_id.name' => 'Individual',
              'is_active' => TRUE,
              'is_reserved' => FALSE,
              ],
            ];
          create_entity($to_create);
      

        $to_create =  [       // Création des types de contact : Pompes
          'entity' => 'ContactType',
          'values' => [
          'name' => 'Pompes',
          'label' => 'Pompes',
          'description' =>'Entreprise de Pompes Funèbres',
          'parent_id.name' => 'Organization',
          'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);


        $to_create =  [       // Création des types de contact : Centre de don du corps
          'entity' => 'ContactType',
          'values' => [
            'name' => 'CDC',
            'label' => E::ts('CDC'),
            'description' => E::ts('Centre de don du corps'),
            'parent_id.name' => 'Organization',
            'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [       // Création des types de contact : piece de conservation
          'entity' => 'ContactType',
          'values' => [
            'name' => 'Emprunteur',
            'label' => E::ts('Localisation pièces'),
            'description' => E::ts('Localisation des pièces dans le CDC ou à l extérieur'),
            'parent_id.name' => 'Organization',
            'is_active' => TRUE,
          ],
        ];

        create_entity($to_create);

        $to_create =  [       // Création des types de contact : Mairies
          'entity' => 'ContactType',
          'values' => [
            'name' => 'Mairies',
            'label' => E::ts('Mairies'),
            'description' => E::ts('Mairies'),
            'parent_id.name' => 'Organization',
            'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);
      // fin de la création des types de contact

    $msg= "  - Création/inactivation des types de contributions financieres".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
    
      $to_create =  [       
      'entity' => 'FinancialType',
      'values' => [
              'name' => 'Donation',
              'label' => 'Don',
              'description' => NULL,
              'is_deductible' => TRUE,
              'is_reserved' => FALSE,
              'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);


      $to_create =  [       
        'entity' => 'FinancialType',
        'values' => [
                'name' => 'Don',
                'label' => 'Don',
                'description' => NULL,
                'is_deductible' => TRUE,
                'is_reserved' => FALSE,
                'is_active' => TRUE,
        ],
        ];
        create_entity($to_create);

      $to_create =  [       
          'entity' => 'FinancialType',
          'values' => [
              'name' => 'Member Dues',
              'label' => 'Cotisations',
              'description' => NULL,
              'is_deductible' => TRUE,
              'is_reserved' => FALSE,
              'is_active' => FALSE,   
          ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'FinancialType',
        'values' => [
            'name' => 'Cotisation des membres',
            'label' => 'Cotisation des membres',
            'description' => NULL,
            'is_deductible' => TRUE,
            'is_reserved' => FALSE,
            'is_active' => FALSE,   // a mettre a false hors Tours
        ],
      ];
      create_entity($to_create);


      $to_create =  [       
        'entity' => 'FinancialType',
        'values' => [
            'name' => 'Contribution Prise en charge Corps',
            'label' => 'Contribution Prise en charge Corps',
            'description' => NULL,
            'is_deductible' => TRUE,
            'is_reserved' => FALSE,
            'is_active' => FALSE,   // a mettre a false hors Tours
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
          'entity' => 'FinancialType',
          'values' => [
              'name' => 'Campaign Contribution',
              'label' => 'Contribution à la campagne',
              'description' => NULL,
              'is_deductible' => FALSE,
              'is_reserved' => FALSE,
              'is_active' => FALSE,
          ],
      ];
      create_entity($to_create);

      $to_create =  [       
          'entity' => 'FinancialType',
          'values' => [
              'name' => 'Event Fee',
              'label' => 'Inscription évenement',
              'description' => NULL,
              'is_deductible' => FALSE,
              'is_reserved' => FALSE,
              'is_active' => FALSE,
          ],
      ];
      create_entity($to_create);
      // fin de la création des types  de contributions financieres

    $msg= "  - Création/inactivation des status de contribution ".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);

      $to_create =  [       
          'entity' => 'OptionValue',
          'values' => [
              'option_group_id:name' => 'contribution_status',
              'label' => 'Encaissé',
              'name' => 'Completed',
              'grouping' => NULL,
              'filter' => 0,
              'is_default' => FALSE,
              'weight' => 1,
              'description' => NULL,
              'is_optgroup' => FALSE,
              'is_reserved' => TRUE,
              'is_active' => TRUE,
              'component_id' => NULL,
              'domain_id' => NULL,
              'visibility_id' => NULL,
              'icon' => NULL,
              'color' => NULL,
          ],
      ];
      create_entity($to_create);

      $to_create =  [      
          'entity' => 'OptionValue',
          'values' => [
              'option_group_id:name' => 'contribution_status',
              'label' => 'Promesse de don',
              'name' => 'Pending',
              'grouping' => NULL,
              'filter' => 0,
              'is_default' => TRUE,
              'weight' => 1,
              'description' => NULL,
              'is_optgroup' => FALSE,
              'is_reserved' => TRUE,
              'is_active' => TRUE,
              'component_id' => NULL,
              'domain_id' => NULL,
              'visibility_id' => NULL,
              'icon' => NULL,
              'color' => NULL,
          ],
      ];
      create_entity($to_create);

      $to_create =  [       
          'entity' => 'OptionValue',
          'values' => [
              'option_group_id:name' => 'contribution_status',
              'label' => 'Annulé',
              'name' => 'Cancelled',
              'grouping' => NULL,
              'filter' => 0,
              'is_default' => FALSE,
              'weight' => 3,
              'description' => NULL,
              'is_optgroup' => FALSE,
              'is_reserved' => TRUE,
              'is_active' => TRUE,
              'component_id' => NULL,
              'domain_id' => NULL,
              'visibility_id' => NULL,
              'icon' => NULL,
              'color' => NULL,
          ],
      ];
      create_entity($to_create);

      $to_create =  [       
          'entity' => 'OptionValue',
          'values' => [
              'name' => 'Failed',
              'is_active' => FALSE,
              'option_group_id:name' => 'contribution_status',
          ],
      ];
      create_entity($to_create);

      $to_create =  [       
          'entity' => 'OptionValue',
          'values' => [
              'name' => 'Refunded',
              'is_active' => FALSE,
              'option_group_id:name' => 'contribution_status',
          ],
      ];
      create_entity($to_create);

      $to_create =  [       
          'entity' => 'OptionValue',
          'values' => [
              'name' => 'Partially paid',
              'is_active' => FALSE,
              'option_group_id:name' => 'contribution_status',
          ],
      ];
      create_entity($to_create);

      $to_create =  [       
          'entity' => 'OptionValue',
          'values' => [
              'name' => 'Pending refund',
              'is_active' => FALSE,
              'option_group_id:name' => 'contribution_status',
          ],
      ];
      create_entity($to_create);

      $to_create =  [       
          'entity' => 'OptionValue',
          'values' => [
              'name' => 'Chargeback',
              'is_active' => FALSE,
              'option_group_id:name' => 'contribution_status',
          ],
      ];
      create_entity($to_create);


      $to_create =  [       
          'entity' => 'OptionValue',
          'values' => [
              'name' => 'Template',
              'is_active' => FALSE,
              'option_group_id:name' => 'contribution_status',
          ],
      ];
      create_entity($to_create);
      // Fin de la création des statuts de contribution  

    $msg= "  - Création/inactivation des modes de paiement".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
    
      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'payment_instrument',
          'is_active' => FALSE,
          'name' => 'Credit Card',
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'payment_instrument',
          'is_active' => FALSE,
          'name' => 'Debit Card',
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'payment_instrument',
          'is_active' => TRUE,
          'name' => 'Cash',
          'label' => 'Espèces',
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'payment_instrument',
          'is_active' => TRUE,
          'name' => 'Check',
          'label' => 'Chèque Bancaire',
          'weight' => 1,
          'is_default' => TRUE,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'payment_instrument',
          'is_active' => TRUE,
          'name' => 'EFT',
          'label' => 'Virement',
          'weight' => 2,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'payment_instrument',
          'is_active' => TRUE,
          'name' => 'Leg',
          'label' => 'Leg',
          'value' => '6',
          'grouping' => NULL,
          'filter' => 0,
          'is_default' => FALSE,
          'weight' => 3,
          'description' => NULL,
          'is_optgroup' => FALSE,
          'is_reserved' => FALSE,
          'component_id' => NULL,
          'domain_id' => NULL,
          'visibility_id' => NULL,
          'icon' => NULL,
          'color' => NULL,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'payment_instrument',
          'is_active' => TRUE,
          'name' => 'Assurance',
          'label' => 'Assurance',
          'option_group_id' => 10,
          'value' => '7',
          'grouping' => NULL,
          'filter' => 0,
          'is_default' => FALSE,
          'weight' => 4,
          'description' => NULL,
          'is_optgroup' => FALSE,
          'is_reserved' => FALSE,
          'component_id' => NULL,
          'domain_id' => NULL,
          'visibility_id' => NULL,
          'icon' => NULL,
          'color' => NULL,
        ],
      ];
      create_entity($to_create);
      // Fin de Création des modes de paiement

    $msg= "  - Création/inactivation des types d'evenements".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'event_type',
          'label' => "Cérémonie d'hommage",
          'value' => '100',
          'name' => 'Cérémonie Hommage',
          'grouping' => NULL,
          'filter' => 0,
          'is_default' => TRUE,
          'weight' => 1,
          'description' => NULL,
          'is_optgroup' => FALSE,
          'is_reserved' => FALSE,
          'is_active' => TRUE,
          'component_id' => NULL,
          'domain_id' => NULL,
          'visibility_id' => NULL,
          'icon' => NULL,
          'color' => NULL,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'event_type',
          'name' => 'Exhibition',
          'is_active' => FALSE,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'event_type',
          'name' => 'Fundraiser',
          'is_active' => FALSE,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'event_type',
          'name' => 'Meeting',
          'is_active' => FALSE,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'event_type',
          'name' => 'Performance',
          'is_active' => FALSE,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'event_type',
          'name' => 'Workshop',
          'is_active' => FALSE,
        ],
      ];
      create_entity($to_create);
      // fin de Création des types d'evenements

    $msg= "  - Création/inactivation des types de participants".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'participant_role',
          'label' => 'Invité',
          'value' => '1',
          'name' => 'Attendee',
          'grouping' => NULL,
          'filter' => 1,
          'is_default' => TRUE,
          'weight' => 1,
          'description' => NULL,
          'is_optgroup' => FALSE,
          'is_reserved' => FALSE,
          'is_active' => TRUE,
          'component_id' => NULL,
          'domain_id' => NULL,
          'visibility_id' => NULL,
          'icon' => NULL, 
          'color' => NULL,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'participant_role',
          'label' => 'Organisateur',
          'value' => '2',
          'name' => 'Volunteer',
          'weight' => 2,
          'is_active' => TRUE,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'participant_role',
          'name' => 'Host',
          'is_active' => FALSE,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'OptionValue',
        'values' => [
          'option_group_id:name' => 'participant_role',
          'name' => 'Speaker',
          'is_active' => FALSE,
        ],
      ];
      create_entity($to_create);

      // Fin de création des types de participants

    $msg= "  - Création/inactivation des Statuts de participants".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
      $results = civicrm_api4('ParticipantStatusType', 'update', [ // on inactive tous les sttuts non reliée à l'appli
        'values' => [
          'is_active' => FALSE,
        ],
        'where' => [
          ['base_module', 'NOT CONTAINS ONE OF', 'don_corps'],
        ],
        'checkPermissions' => FALSE,
      ]);

       $to_create =  [   // On waitlist / invité
        'entity' => 'ParticipantStatusType',
        'values' => [
          'name' => 'On waitlist',
          'label' => 'Invité',
          'class' => 'Waiting',
          'is_reserved' => TRUE,
          'is_active' => TRUE,
          'is_counted' => FALSE,
          'weight' => 1,
          'visibility_id' => 1,
        ],
        ];
        create_entity($to_create);

      $to_create =  [   // Registered / confirmé
        'entity' => 'ParticipantStatusType',
        'values' => [
          'name' => 'Registered',
          'label' => 'Confirmé',
          'class' => 'Positive',
          'is_reserved' => TRUE,
          'is_active' => TRUE,
          'is_counted' => TRUE,
          'weight' => 2,
          'visibility_id' => 1,
        ],
        ];
        create_entity($to_create);

      $to_create =  [   // Cancelled / Annulé - Refus
        'entity' => 'ParticipantStatusType',
        'values' => [
          'name' => 'Cancelled',
          'label' => 'Annulé - Refus',
          'class' => 'Negative',
          'is_reserved' => TRUE,
          'is_active' => TRUE,
          'is_counted' => FALSE,
          'weight' => 3,
          'visibility_id' => 2,
        ],
        ];
        create_entity($to_create);     
        // Fin de création des statuts de participants

    
    
    $msg= "  - Création des relations spécifiques à l'application".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
        $to_create =  [       
          'entity' => 'RelationshipType',
          'values' => [
            'name_a_b' => 'Child of',
            'label_a_b' => 'Enfant de',
            'name_b_a' => 'Parent of',
            'label_b_a' => 'Parent de',
            'description' => 'Relation enfant/Parent.',
            'contact_type_a' => 'Individual',
            'contact_type_b' => 'Individual',
            'contact_sub_type_a' => NULL,
            'contact_sub_type_b' => NULL,
            'is_reserved' => FALSE,
            'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [       
          'entity' => 'RelationshipType',
          'values' => [
            'name_a_b' => 'Partner of',
            'label_a_b' => 'Conjoint de',
            'name_b_a' => 'Partner of',
            'label_b_a' => 'Conjoint de',
            'description' => 'Relation conjoints',
            'contact_type_a' => 'Individual',
            'contact_type_b' => 'Individual',
            'contact_sub_type_a' => NULL,
            'contact_sub_type_b' => NULL,
            'is_reserved' => FALSE,
            'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [       
          'entity' => 'RelationshipType',
          'values' => [
            'name_a_b' => 'a pour personne de confiance',
            'label_a_b' => E::ts('a pour personne de confiance'),
            'name_b_a' => 'est la personne de confiance de',
            'label_b_a' => E::ts('est la personne de confiance de'),
            'description' => E::ts('personne confiance'),
            'contact_type_a' => 'Individual',
            'contact_type_b' => 'Individual',
            'contact_sub_type_a' => NULL,
            'contact_sub_type_b' => NULL,
            'is_reserved' => FALSE,
            'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [       
          'entity' => 'RelationshipType',
          'values' => [
            'name_a_b' => 'a pour personne de confiance 2',
            'label_a_b' => E::ts('a pour personne de confiance 2'),
            'name_b_a' => 'est la personne de confiance 2',
            'label_b_a' => E::ts('est la personne de confiance 2'),
            'description' => E::ts('personne de confiance alternative'),
            'contact_type_a' => 'Individual',
            'contact_type_b' => 'Individual',
            'contact_sub_type_a' => NULL,
            'contact_sub_type_b' => NULL,
            'is_reserved' => FALSE,
            'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [       
          'entity' => 'RelationshipType',
          'values' => [
            'name_a_b' => 'a pour PAQPF',
            'label_a_b' => E::ts('a pour PAQPF'),
            'name_b_a' => 'est la PAQPF',
            'label_b_a' => E::ts('est la PAQPF'),
            'description' => E::ts('Personne ayant qualité pour pourvoir aux funérailles'),
            'contact_type_a' => 'Individual',
            'contact_type_b' => 'Individual',
            'contact_sub_type_a' => NULL,
            'contact_sub_type_b' => NULL,
            'is_reserved' => FALSE,
            'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [       
          'entity' => 'RelationshipType',
          'values' => [
            'name_a_b' => 'en attente',
            'label_a_b' => E::ts('en attente'),
            'name_b_a' => 'en attente',
            'label_b_a' => E::ts('en attente'),
            'description' => E::ts('en attente de creation'),
            'contact_type_a' => NULL,
            'contact_type_b' => NULL,
            'contact_sub_type_a' => NULL,
            'contact_sub_type_b' => NULL,
            'is_reserved' => FALSE,
            'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [       
          'entity' => 'RelationshipType',
          'values' => [
            'name_a_b' => 'Sibling of',
            'label_a_b' => 'Frère/Soeur de',
            'name_b_a' => 'Sibling of',
            'label_b_a' => 'Frère/Soeur de',
            'description' => 'Frère/Soeur',
            'contact_type_a' => 'Individual',
            'contact_type_b' => 'Individual',
            'contact_sub_type_a' => NULL,
            'contact_sub_type_b' => NULL,
            'is_reserved' => FALSE,
            'is_active' => TRUE,
          ],
        ];
        create_entity($to_create);
      
        $to_create =  [       
          'entity' => 'RelationshipType',
          'values' => [
            'name_a_b' => 'Employee of',
            'label_a_b' => 'Employé de',
            'name_b_a' => 'Employer of',
            'label_b_a' => 'Employeur de',
            'description' => 'Relation employé/employeur',
            'contact_type_a' => 'Individual',
            'contact_type_b' => 'Organization',
            'contact_sub_type_a' => NULL,
            'contact_sub_type_b' => NULL,
            'is_reserved' => TRUE,
            'is_active' => TRUE,      
          ],
        ];
        create_entity($to_create);
      // Fin de création des relations
    
    $msg= "  - Déactivation des relations par défaut".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
      deactivate_relation_type ('Spouse of');
      deactivate_relation_type ('Volunteer for');
      deactivate_relation_type ('Head of Household for');
      deactivate_relation_type ('Household Member of');
      deactivate_relation_type ('Case Coordinator is');
      deactivate_relation_type ('Supervised by');
      // Fin de la Déactivation des types de relation par défaut  

      
   /*     $msg= "  - Déactivation des champs du profil par defaut ".PHP_EOL; // sans cela le profil contient de champs suppléntaiers
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
      $results = civicrm_api4('UFField', 'update', [
        'values' => [
          'is_active' => FALSE,
        ],
        'where' => [
          ['uf_group_id:name', '=', 'name_and_address'],
        ],
        'checkPermissions' => FALSE,
      ]);

      // Fin de Déactivation des champs du profil par defaut */

    _don_corps_civix_civicrm_install();
  }

# IMPLEMENTS hook_civicrm_enable(). // LOG OK
  #@link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 

  function don_corps_civicrm_enable(): void {   // pas d'affcicahge des messages consoles lors installation
    /* $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    $msg=PHP_EOL.date("Y-m-d H:i:s")." @@@  hook_civicrm_enable ".PHP_EOL;
    fwrite($fp, $msg);
    echo $msg;
  
    _don_corps_civix_civicrm_enable();

    
    $msg= "  - Activation des groupes de champs personnalisés crées par l'extension".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;
      $results = civicrm_api4('CustomGroup', 'update', [
        'values' => [
          'is_active' => TRUE,
        ],
        'where' => [
          ['base_module', '=', 'don_corps'],
          ],
        'checkPermissions' => FALSE,
      ]);
      /// FIN Activation des groupes de champs personalisés crées par l'extension

    $msg= "  - Activation des champs personnalisés crées par l'extension".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;
      $results = civicrm_api4('CustomField', 'update', [
        'values' => [
          'is_active' => TRUE,
        ],
        'where' => [
          ['base_module', '=', 'don_corps'],
        ],
        'checkPermissions' => FALSE,
      ]);
      /// FIN Activation des champs personalisés crées par l'extension

    $msg= "  - Activation des groupes de contact crées par l'extension".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;
      $results = civicrm_api4('Group', 'update', [
        'values' => [
          'is_active' => TRUE,
        ],
        'where' => [
          ['base_module', '=', 'don_corps'],
        ],
        'checkPermissions' => FALSE,
      ]);
      /// FIN Activation des groupes de contacts personalisés crées par l'extension

    
      $msg= "  - Activation des relations personnalisés crées par l'extension".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;
      $results = civicrm_api4('RelationshipType', 'update', [
        'values' => [
          'is_active' => TRUE,
        ],
        'where' => [
          ['base_module', '=', 'don_corps'],
        ],
        'checkPermissions' => FALSE,
      ]);
      // FIN Activation des relations personnalisés crées par l'extension;

    $msg= "  - Activation des profils créés par l'extension".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;
      $results = civicrm_api4('UFGroup', 'update', [
        'values' => [
          'is_active' => TRUE,
        ],
        'where' => [
          ['base_module', '=', 'don_corps'],
        ],
        'checkPermissions' => FALSE,
      ]);
      /// Fin Activation des profils crées par l'extension

    $msg= "  - Activation des champs de profils créés par l'extension".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;
      $results = civicrm_api4('UFField', 'update', [
        'values' => [
          'is_active' => TRUE,
        ],
        'where' => [
          ['base_module', '=', 'don_corps'],
        ],
        'checkPermissions' => FALSE,
      ]);
      /// Fin Activation des champs de profils crées par l'extension

    $msg= "  - Déactivation des tags par défaut".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;
      $tags = civicrm_api4('Tag', 'delete', [
        'where' => [
          ['base_module', 'NOT CONTAINS', 'don_corps'],
        ],
        'checkPermissions' => FALSE,
      ]);
      // Fin Déactivation des Tags par défaut

    $msg= "  - Création des correspondances de mots".PHP_EOL;
      fwrite($fp, $msg);
      echo $msg;

      $translats = [
      [
          'find_word' => 'Contribution',
          'replace_word' => 'Don financier',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'add',
          'replace_word' => 'ajouter',
          'is_active' => TRUE,
          'match_type' => 'exactMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'amount',
          'replace_word' => 'montant',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Surnom',
          'replace_word' => 'Nom de naissance',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Attendee List',
          'replace_word' => 'liste des participants',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Événement',
          'replace_word' => 'Cérémonie',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Event',
          'replace_word' => 'Cérémonie',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'New',
          'replace_word' => 'Nouveau/Nouvelle',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Activity',
          'replace_word' => 'Activité',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Find',
          'replace_word' => 'Cherche',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Report',
          'replace_word' => 'Rapport',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Home',
          'replace_word' => 'Domicile',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Don financier Source',
          'replace_word' => 'Origine du don financier',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Don financier Status',
          'replace_word' => 'Statut du Don Financier',
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Id. de transaction',
          'replace_word' => "Référence de l'opération",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Title',
          'replace_word' => "Titre",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Summary',
          'replace_word' => "Résumé",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Template',
          'replace_word' => "Modèle",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Register for',
          'replace_word' => "Inscrire à",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Child of',
          'replace_word' => "Enfant de",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Employee of',
          'replace_word' => "Employé par",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Parent of',
          'replace_word' => "Parent de",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Partner of',
          'replace_word' => "Conjoint de",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Sibling of',
          'replace_word' => "Frère/Soeur de",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Create',
          'replace_word' => "Créer",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],
        [
          'find_word' => 'Record',
          'replace_word' => "Enregistrer",
          'is_active' => TRUE,
          'match_type' => 'wildcardMatch',
          'domain_id' => 1,
        ],

      ];

      foreach ($translats as $translat){
        $wordReplacements = civicrm_api4('WordReplacement', 'get', [
        'where' => [
          ['find_word', '=', $translat['find_word']],
        ],
        'checkPermissions' => FALSE,
        ]);


        if (isset($wordReplacements[0]['id']))  {  // si le Wordreplacement existe

            $results = civicrm_api4('WordReplacement', 'update', [
              'values' => [
                'replace_word' => $translat['replace_word'],
                'is_active' => TRUE,
                'match_type' => $translat['match_type'],
                ],
              'where' => [
                ['find_word', '=', $translat['find_word']],
              ],
              'checkPermissions' => FALSE,
            ]);

        


        }  else {
          //echo "create"."\n";
          $results = civicrm_api4('WordReplacement', 'create', [
          'values' => [
          'find_word' => $translat['find_word'],
          'replace_word' => $translat['replace_word'],
            'is_active' => TRUE,
            'match_type' => $translat['match_type'],
          ],
          'checkPermissions' => FALSE,
          ]);

        }
        //echo $translat['find_word']." : done".PHP_EOL;
      } 
      // Fin de Création des correspondances de mots
  fclose($fp);
 */  }


# IMPLEMENTS hook_civicrm_postInstall().
  #@link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 
  function don_corps_civicrm_postInstall() {
    $msg= PHP_EOL."@@@  hook_civicrm_postinstall ".PHP_EOL;
    echo $msg;
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
    fwrite($fp, $msg);

    $msg="  - Modification des parametres de localisation, date...".PHP_EOL; //LOG OK // modification des parametres de localisation, date....utilise apiv3 car non porté en V4 pour le moment
      echo $msg;
      fwrite($fp, $msg);
      fclose($fp);
      $result = civicrm_api3('Setting', 'create', [
        'dateformatDatetime'=> "%e %B %Y",
        // 'dateformatDatetime'=> "%e %B %Y %H:%M",
        'theme_backend'  => "greenwich",
        'smartGroupCacheTimeout' => 0,
        'dateformatFull' => "%e %B %Y",
        'dateformatTime' => "%H:%M",
        'dateformatFinancialBatch' => "dd-mm-yyyy",
        'dateformatshortdate' => "%d/%m/%Y",
        'dateInputFormat' => "dd/mm/yy",
        'weekBegins' => "1",
        'timeInputFormat' => "2",
        'uiLanguages' => ["fr_FR"],
        'partial_locales' => "0",
        'defaultCurrency' => "EUR",
        'monetaryDecimalPoint' => ",",
        'monetaryThousandSeparator' => " ",
        'moneyformat' => "%a %c ",
        'defaultContactCountry' => "1076",
        'mailing_format' => "{contact.address_name}\r\n{contact.street_address}\r\n{contact.supplemental_address_1}\r\n{contact.supplemental_address_2}\r\n{contact.supplemental_address_3}\r\n{contact.postal_code}{ }{contact.city}\r\n{contact.country}",
        'hideCountryMailingLabels' => "1",
        'address_format' => "{contact.address_name}\r\n{contact.street_address}\r\n{contact.supplemental_address_1}\r\n{contact.supplemental_address_2}\r\n{contact.supplemental_address_3}\r\n{contact.postal_code}{ }{contact.city}\r\n{contact.country}",
        'address_options' => [
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "10",
        ],
        'quicksearch_options' => [
          "sort_name",
          "first_name",
          "Promesse_de_don.N_de_don",
          "Prise_en_charge_au_d_c_s.N_de_d_c_s",
          "Annulation.N_annulation",
          "Utilisation_du_corps.N_de_pi_ce_ou_de_corps"
        ],
          'contact_edit_options' => [
            "8",
            "7",
            "2",
            "5",
            "1",
            "4",
            "6",
            "12",
            "14",
            "16"
        ],
        'contact_ajax_check_similar' => "0",
        'includeWildCardInName' => "0",
        'includeEmailInName' => "0",
        'searchPrimaryDetailsOnly' => "0",
        'enable_components' => [
          "CiviEvent",
          "CiviContribute",
          "CiviMail",
          "CiviReport"
        ],
      ]);
   

    $msg="  - Modification des tags".PHP_EOL; // LOG OK
      echo $msg;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      fclose($fp);
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
    
    $msg="  - Modification des profils personnalisés".PHP_EOL;// LOG OK
      echo $msg;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      fclose($fp);
      # Lors de la création de profils de formulaires ou de custom layouts, des profils personnalisés sont générés
      # ils regroupent des champs personnalisés qui sont identifiés par custom_XX avec XX l'id du customfield correspondant
      # Lors d'une nouvelle installation les id des custom fields peuvent varier ce qui induit une incohérence
      # Ici on utilise un tableau donnant la correspondance entre le nom original du champ personnlisé (uf id) 
      # et son nom ; cela permt de modifier celui-ci dans la nouvelle installation
      
      $toimport_file = Civi::paths()->getPath("[civicrm.root]/ext/don_corps/managed/ufnameconversion.txt");
      modif_profils_perso($toimport_file);

    $msg="  - Modification de l'utilisation des profils".PHP_EOL;// LOG OK
      echo $msg;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      fclose($fp);
      modif_profils_utilisation();
  
    $msg="  - Modification des menus de navigation liés aux profils".PHP_EOL;// LOG OK
      echo $msg;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      fclose($fp);
      $url_menus_to_change =[           // Profil name, parent_id:name, name du menu navigation
        ['Inscription_donateur', 'ContactsDDC','New DonateurDDC'],  //// MODIFIE
        ['Inscription_proche_donateur_14', 'ContactsDDC','Ajouter proche donateurDDC'],///MODIFIE
        ['Demandeur_information_22', 'ContactsDDC','New Demandeur_d_informationDDC'],///MODIFIE
        ['inscription_pompes', 'Pompes funebresDDC','New Pompes'],  // 'Inscription_proche_donateur_27' correpond au profil pompes
        ['Mairie', 'MairiesDDC','New Mairies'],
        ['Personnel_de_centre_de_don_de_corps', 'Centres de don du corpsDDC','New Personnel'],
        ['Centre_d_accueil_des_corps', 'Centres de don du corpsDDC','New CDC'],
        ['Lieu_de_stockage', 'Pièces anatomiquesDDC','New Emprunteur'],
        ['Inscription_anat_compar_e', 'ContactsDDC','Nouvelle pièce anat comparée'],
      ];
      modif_profils_navigation($url_menus_to_change);

      
    $msg="  - Ajout des centres de don du corps".PHP_EOL;// LOG OK
      echo $msg;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      fclose($fp);
      $exp_dir = Civi::paths()->getPath("[civicrm.root]/ext/don_corps/managed/");;    // racine du répertoire d'import export
      ajoute_CDC($exp_dir);


    $msg="  - Désactivation des menus par défaut".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      fclose($fp);

      deactivate_menu('Contacts');
      deactivate_menu('Search');
      deactivate_menu('Contributions');
      deactivate_menu('Events');
      deactivate_menu('Mailings');
      deactivate_menu('Reports');
      deactivate_menu('Support');

    $msg="  - Reglage des règles de dédoublonage".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
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
          $msg= "      -> Passage des usages de tous les groupes de règles de dédoublonage à General".PHP_EOL;
            fwrite($fp, $msg);
            if (VERBOSE==1){
            echo $msg;
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
                  fwrite($fp, $msg);
                  if (VERBOSE==1){
                    echo $msg;
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
                      fwrite($fp, $msg);
                      if (VERBOSE==1){
                        echo $msg;
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
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
          }
          $msg= " : ".$rule_name.' (id : '.$results['0']['id'].')'.PHP_EOL ;
          fwrite($fp, $msg);
          if (VERBOSE==1){
            echo $msg;
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
              $msg= "         Rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'].PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
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
                  fwrite($fp, $msg);
                  if (VERBOSE==1){
                    echo $msg;
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
                      fwrite($fp, $msg);
                      if (VERBOSE==1){
                        echo $msg;
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
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
          }
          $msg= " : ".$rule_name.' (id : '.$results['0']['id'].')'.PHP_EOL ;
          fwrite($fp, $msg);
          if (VERBOSE==1){
            echo $msg;
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
              $msg= "         Rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'].PHP_EOL;
                        fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
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
                  fwrite($fp, $msg);
                  if (VERBOSE==1){
                    echo $msg;
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
                      fwrite($fp, $msg);
                      if (VERBOSE==1){
                        echo $msg;
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
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
          }
          $msg= " : ".$rule_name.' (id : '.$results['0']['id'].')'.PHP_EOL ;
          fwrite($fp, $msg);
          if (VERBOSE==1){
            echo $msg;
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
              $msg= "         Rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'].PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
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
                  fwrite($fp, $msg);
                  if (VERBOSE==1){
                    echo $msg;
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
                      fwrite($fp, $msg);
                      if (VERBOSE==1){
                        echo $msg;
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
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              }
          }

          $msg= " : ".$rule_name.' (id : '.$results['0']['id'].')'.PHP_EOL ;
          fwrite($fp, $msg);
          if (VERBOSE==1){
            echo $msg;
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

              $msg= "         Rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'].PHP_EOL;
              fwrite($fp, $msg);
              if (VERBOSE==1){
                echo $msg;
              } 
          }

     fclose($fp); // ferme fichier de log

     /// Fin de Reglage des règles de dédoublonage

  
    $msg="  - Modification des filtres de champs personnalisés".PHP_EOL; // permet de n'afficher que certains contacts pour certains champs
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);

      modif_filtre('Pr_par_par','Personnel_centre_de_don_77');
      modif_filtre('Lacalisation','Emprunteurs_44');

      // fin de modification des filtre de champs personnalisés


    
      

    $msg="  - Création des Rules".PHP_EOL;
      ## les mgd files ne fonctionnent pas correctement car font référence aux id des activités, culstom fields...
      ## qui varient d'une installation à l'autre
      function create_rules(){
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        echo $msg;

        $msg="      -> Civirule : Déplace lot de pièces ".PHP_EOL;
          fwrite($fp, $msg);
          if (VERBOSE==1){
            echo $msg;
          }
          fclose($fp);

          $to_create =  [        //Déplace lot : Déclaration de l'Action
            'entity' => 'CiviRulesAction',
            'values' => [
            'name' => 'deplacelot',
            'label' => 'Déplace un lot de pièces anatomiques',
            'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Deplacelot',
              'is_active' => TRUE,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
            ],
          ];
          create_entity($to_create);

          $to_create =  [         //Déplace lot : Rule
                      
            'entity' => 'CiviRulesRule',
            'values' => [
              'trigger_id:name' => 'new_activity',
              'name' => 'déplace_un_lot_de_pièces_anatomiques_',
              'label' =>'Déplace un lot de pièces anatomiques ',
              'trigger_params' => 'a:1:{s:11:"record_type";s:1:"3";}',
              'is_active' => TRUE,
              'description' => 'Déplace un lot de pièces vers le local depuis lequel une activité déplacer pièces anatomiques est créée',
              'help_text' => "<p>Déplace un lot de pièces identifiées par leurs codes-Barres.</p>\r\n\r\n<p>Lorsqu'une action de type Déplacement de lot de pièce anatomique est créée, elle déplace les pièces figurant dans le champ détails de l'activité vers le contact depuis lequel l'activité est crée (local de conservation).</p>\r\n\r\n<p>Les pièces manquantes ou déjà détruites sont localisées dans cette pièce de stockage et leur statut est modifié en <em>Non Eliminé.</em></p>\r\n",
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
              'is_debug' => FALSE,
            ],
          ];
          create_entity($to_create);

          $to_create =  [    //Déplace lot : Rule Action
            'entity' => 'CiviRulesRuleAction',
            'values' => [
              'action_params' => NULL,
              'delay' => NULL,
              'ignore_condition_with_delay' => 0,
              'is_active' => TRUE,
              'rule_id.name' => 'déplace_un_lot_de_pièces_anatomiques_',
              'action_id.name' => 'deplacelot',
            ],
          ];
          create_entity($to_create);

          //Déplace lot : Rule condition

          $weight = civicrm_api4('OptionValue', 'get', [ // récupère le weight du type de l'activité qui est utilisé comme activity_type_id
              'select' => [
                  'weight',
              ],
              'where' => [
                  ['name', '=', 'Déplacer un lot de pièces/corps'],
              ],
              'checkPermissions' => FALSE,
              ]);

          $activity_type_id = $weight[0]['weight'];
          
          $to_create =  [                   
            'entity' => 'CiviRulesRuleCondition',
            'values' => [
            'condition_link' => NULL,
            'is_active' => TRUE,
            'condition_id.name' => 'activity_of_type',
            'rule_id.name' => 'déplace_un_lot_de_pièces_anatomiques_',
            'condition_link' => NULL,
            'condition_params' => [
              'operator' => '0',
              'activity_type_id' => [
                  $activity_type_id,
              ],
              ],
            ],
          ];
          create_entity($to_create);

        $msg="      -> Civirule : Supprime lot de pièces ".PHP_EOL;
          $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
          fwrite($fp, $msg);
          fclose($fp);
            if (VERBOSE==1){
              echo $msg;
            }

          $to_create =  [        //Supprime lot : Déclaration de l'Action
            'entity' => 'CiviRulesAction',
            'values' => [
            'name' => 'supprimelot',
            'label' => 'Supprime un lot de pièces anatomiques',
            'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Supprimelot',
              'is_active' => TRUE,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
            ],
          ];
          create_entity($to_create);

          $to_create =  [         //Supprime Lot : Rule
                    
            'entity' => 'CiviRulesRule',
            'values' => [
              'trigger_id:name' => 'new_activity',
              'name' => 'supprime_lot_de_pièces',
              'label' => E::ts('Supprime lot de pièces'),
              'trigger_params' => 'a:1:{s:11:"record_type";s:1:"3";}',
              'is_active' => TRUE,
              'description' => E::ts('Supprime un lot de pièces identifiées par leur code Barres.'),
              'help_text' => "<p>Lorsqu'une action de type Suppression de lot de pièce anatomique est créée, elle supprime les pièces figurant dans le champ détails de l'activité.</p>\r\n\r\n<p>Si un code-barres de corps est saisi, l'utilisateur est invité à utiliser le tableau de bord des corps.&nbsp;Les pièces manquantes ou déja détruites sont ignorées.</p>\r\n\r\n<p>Sinon, la pièce est passée en \"Crémation\" et sa localisation est supprimée.</p>\r\n\r\n<p>Un rapport remplace les données du champ Détails</p>\r\n\r\n<p>&nbsp;</p>\r\n",
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
              'is_debug' => FALSE,
            ],
          ];
          create_entity($to_create);

          $to_create =  [    //Supprime lot : Rule Action
            'entity' => 'CiviRulesRuleAction',
            'values' => [
              'action_params' => NULL,
              'delay' => NULL,
              'ignore_condition_with_delay' => 0,
              'is_active' => TRUE,
              'rule_id.name' => 'supprime_lot_de_pièces',
              'action_id.name' => 'supprimelot',
            ],
          ];
          create_entity($to_create);
      

          //Supprime lot : Rule condition
          $weight = civicrm_api4('OptionValue', 'get', [ // récupère le weight du type de l'activité qui est utilisé comme activity_type_id
              'select' => [
                  'weight',
              ],
              'where' => [
                  ['name', '=', "Suppression d'une pièces"],
              ],
              'checkPermissions' => FALSE,
              ]);

          $activity_type_id = $weight[0]['weight'];

          $to_create =  [                   
            'entity' => 'CiviRulesRuleCondition',
            'values' => [
              'condition_link' => NULL,
              'is_active' => TRUE,
              'rule_id.name' => 'supprime_lot_de_pièces',
              'condition_id.name' => 'activity_of_type',
              'condition_link' => NULL,
              'condition_params' => [
                  'operator' => '0',
                  'activity_type_id' => [
                      $activity_type_id,
                  ],
              ],
              #'condition_params' => 'a:2:{s:8:"operator";s:1:"0";s:16:"activity_type_id";a:1:{i:0;s:2:"61";}}',
            ],
          ];
          create_entity($to_create);

        $msg="      -> Civirule : Inventaire ".PHP_EOL;
          $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
          fwrite($fp, $msg);
          fclose($fp);
            if (VERBOSE==1){
              echo $msg;
            }

          $to_create =  [        //Inventaire : Déclaration de l'Action
            'entity' => 'CiviRulesAction',
            'values' => [
              'name' => 'creeinventaire',
              'label' => 'Crée un inventaire de pièces anatomiques',
              'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Creeinventaire',
              'is_active' => TRUE,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
              ],
            ];
          create_entity($to_create);

          $to_create =  [         //Inventaire : Rule
            'entity' => 'CiviRulesRule',
            'values' => [
              'trigger_id:name' => 'new_activity',
              'name' => "création_d'inventaire",
              'label' => "Création d'inventaire",
              'trigger_params' => "a:1:{s:11:\"record_type\";s:1:\"3\";}",
              'is_active' => TRUE,
              'description' => 'Crée un nouvel inventaire',
              'help_text' => "<p>Lorsqu'une activité de type inventaire est créée depuis un lieu de conservation, un rapport remplace la liste des pièces dans le champ détail ; les pièces sont éventuellement relocalisées et leur statut est corrigé. Le champ 'Inventaires' des pièces et des corps concernés est mis à jour.</p>\r\n\r\n<p>&nbsp;</p>\r\n",
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
              'is_debug' => FALSE,
            ],
          ];
          create_entity($to_create);

          $to_create =  [    //Inventaire : Rule Action
            'entity' => 'CiviRulesRuleAction',
            'values' => [
              'action_params' => NULL,
              'delay' => NULL,
              'ignore_condition_with_delay' => 0,
              'is_active' => TRUE,
              'rule_id.name' => "création_d'inventaire",
              'action_id.name' => 'creeinventaire'
            ],
          ];
          create_entity($to_create);

          //Inventaire : Rule condition
          $weight = civicrm_api4('OptionValue', 'get', [ // récupère le weight du type de l'activité qui est utilisé comme activity_type_id
              'select' => [
                  'weight',
              ],
              'where' => [
                  ['name', '=', "Inventaire"],
              ],
              'checkPermissions' => FALSE,
          ]);

          $activity_type_id = $weight[0]['weight'];
          
          $to_create =  [                   
            'entity' => 'CiviRulesRuleCondition',
            'values' => [
            'condition_link' => NULL,
            'condition_params' => [
                  'operator' => '0',
                  'activity_type_id' => [
                      $activity_type_id,
                  ],
              ],
              #'condition_params' => 'a:2:{s:8:"operator";s:1:"0";s:16:"activity_type_id";a:1:{i:0;s:2:"60";}}',
              'is_active' => TRUE,
              'rule_id.name' => "création_d'inventaire",
              'condition_id.name' => 'activity_of_type',
            ],
          ];
          create_entity($to_create);

      
        $msg="      -> Civirule : MAJ civilités ".PHP_EOL;
          $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
          fwrite($fp, $msg);
          fclose($fp);
            if (VERBOSE==1){
              echo $msg;
            } 
          $to_create =  [        //Corriger_civililite : Déclaration de l'Action
              'entity' => 'CiviRulesAction',
              'values' => [
                'name' => 'Corriger_civililite',
                'label' => 'Corriger la civilite',
                'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_FixCivilite',
                'is_active' => TRUE,
                'created_date' => date('Y-m-d'),
                'created_user_id' => 1,
                'modified_date' => NULL,
                'modified_user_id' => NULL,
              ],
          ];
          create_entity($to_create);

          $to_create =  [         //Corriger_civililite : Rule
                  
            'entity' => 'CiviRulesRule',
            'values' => [
              'trigger_id:name' => 'changed_contact_custom_data',
              'name' => 'maj_genre_',
              'label' => 'MAJ Genre ',
              'trigger_params' => NULL,
              'is_active' => TRUE,
              'description' => E::ts('Met à jour le genre et les formules de politesse'),
              'help_text' => NULL,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
              'is_debug' => FALSE,
            ],
          ];
          create_entity($to_create);

          $to_create =  [    //Corriger_civililite : Rule Action
            'entity' => 'CiviRulesRuleAction',
            'values' => [
              'action_params' => NULL,
              'delay' => NULL,
              'ignore_condition_with_delay' => 0,
              'is_active' => TRUE,
              'rule_id.name' => 'maj_genre_',
              'action_id.name' => 'Corriger_civililite',
            ],
          ];
          create_entity($to_create);

          //Corriger_civililite : Rule condition
          #$condition_params=serialize_custom_fields('Civilit_user');

          $customFields = civicrm_api4('CustomField', 'get', [ // récupère l'id du champ personnalisé
              'select' => [
                  'id',
              ],
              'where' => [
                  ['name', '=', 'Civilit_user'],
              ],
              'checkPermissions' => FALSE,
              ]);

          $custom_field_id = $customFields[0]['id'];

          $to_create =  [      
            'entity' => 'CiviRulesRuleCondition',
            'values' => [
              'condition_link' => NULL,
              'condition_params' => [
                  'custom_field_id' => [
                      $custom_field_id,
                  ],
                  ],
              #'condition_params' => $condition_params,
              'is_active' => TRUE,
              'rule_id.name' => 'maj_genre_',
              'condition_id.name' => 'contact_custom_field_changed',
            ],
          ];
          create_entity($to_create);
                      

      
        $msg="      -> Civirule : Compile pieces et utilisations".PHP_EOL;
          $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
          fwrite($fp, $msg);
          fclose($fp);
            if (VERBOSE==1){
              echo $msg;
            }   

          $to_create =  [        //compile pieces et utilisations : Déclaration de l'Action
              'entity' => 'CiviRulesAction',
              'values' => [
                  'name' => 'compile_pieces_et_utlisations',
                  'label' => "Compile les pièces et utilisations d'un corps",
                  'class_name' => 'CRM_DonCorps_CivirulesActions_Piece_Compilepiecesutilisations',
                  'is_active' => TRUE,
                  'created_date' => date('Y-m-d'),
                  'created_user_id' => 1,
                  'modified_date' => NULL,
                  'modified_user_id' => NULL,
              ],
          ];
          create_entity($to_create);

          $to_create =  [         //compile pieces et utilisations : Rule
            'entity' => 'CiviRulesRule',
            'values' => [
                'name' => 'update_pieces_et_utilisations',
                'label' => "update pieces et utilisations",
                'is_active' => TRUE,
                'trigger_id:name' => 'changed_individual_custom_data',
                'trigger_params' => NULL,
                'description' => "Update la liste des pieces utilisées et des utilisations d'un corps",
                'help_text' => NULL,
                'created_date' => date('Y-m-d'),
                'created_user_id' => 1,
                'modified_date' => NULL,
                'modified_user_id' => NULL,
                'is_debug' => FALSE,
            ],
          ];
          create_entity($to_create);

          $to_create =  [    //compile pieces et utilisations : Rule Action
            'entity' => 'CiviRulesRuleAction',
            'values' => [
              'action_params' => NULL,
              'delay' => NULL,
              'ignore_condition_with_delay' => 0,
              'is_active' => TRUE,
              'rule_id.name' => 'update_pieces_et_utilisations',
              'action_id.name' => 'compile_pieces_et_utlisations',
            ],
          ];
          create_entity($to_create);

          //compile pieces et utilisations : Rule condition
          //$condition_params=serialize_custom_fields('Type_de_poi_ce_3', 'Utilisation2');
          $customFields = civicrm_api4('CustomField', 'get', [ // récupère l'id du champ personnalisé
              'select' => [
                  'id',
              ],
              'where' => [
                  ['OR', [['name', '=', 'Utilisation2'], ['name', '=', 'Type_de_poi_ce_3']]],
              ],
              'checkPermissions' => FALSE,
              ]);


          $custom_field_id = array_column($customFields->getArrayCopy(), 'id');

          $to_create =  [      
            'entity' => 'CiviRulesRuleCondition',
            'values' => [
              'rule_id.name' => 'update_pieces_et_utilisations',
              'condition_id.name' => 'contact_custom_field_changed',
              'is_active' => TRUE,
              #'condition_params' => $condition_params, 
              'condition_params' => [
                  'custom_field_id' => $custom_field_id
                  ],
            ],
          ];
          create_entity($to_create);


        $msg="      -> Civirule : Copie code barre corps".PHP_EOL;
          $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
          fwrite($fp, $msg);
          fclose($fp);
            if (VERBOSE==1){
              echo $msg;
            }  
          $to_create =  [         //Copie code barre corps : Déclaration de Action
            'entity' => 'CiviRulesAction',
            'values' => [
              'name' => 'contact_CopyBarCode',
              'label' => "Copie du code barre du corps d'un donneur dans le champ cache piece principale",
              'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_CopyBarCode',
              'is_active' => TRUE,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
            ],
          ];
          create_entity($to_create);

          $to_create =  [       // Copie code barre corps : Rule
            'entity' => 'CiviRulesRule',
            'values' => [
              'name' => 'copie_code_barre_corps',
              'label' => "Copie code barre corps",
              'is_active' => TRUE,
              'trigger_id:name' => 'changed_individual_custom_data',
              'trigger_params' => NULL,
              'description' => 'Copie le code barre du corps du donneur dans le champ caché "piece principale" lors des modif de n° de piece',
              'help_text' => NULL,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
              'is_debug' => FALSE,
            ],
          ];
          create_entity($to_create);

          // Copie code barre corps : Rule Condition

          $customFields = civicrm_api4('CustomField', 'get', [ // récupère l'id du champ personnalisé
              'select' => [
                  'id',
              ],
              'where' => [
                  ['name', '=', 'N_de_pi_ce_ou_de_corps']
              ],
              'checkPermissions' => FALSE,
              ]);


          $custom_field_id = array_column($customFields->getArrayCopy(), 'id');


          //$condition_params=serialize_custom_fields('N_de_pi_ce_ou_de_corps');

          $to_create =  [       
            'entity' => 'CiviRulesRuleCondition',
            'values' => [
              'rule_id.name' => 'copie_code_barre_corps',
              'condition_id.name' => 'contact_custom_field_changed',
              'is_active' => TRUE,
              //'condition_params' => $condition_params,
              'condition_params' => [
                  'custom_field_id' => $custom_field_id
                  ],
              ],
            ];
          create_entity($to_create);

          $to_create =  [        //Copie code barre corps : Rule Action
            'entity' => 'CiviRulesRuleAction',
            'values' => [
              'action_params' => NULL,
              'delay' => NULL,
              'ignore_condition_with_delay' => 0,
              'is_active' => TRUE,
              'rule_id.name' => 'copie_code_barre_corps',
              'action_id.name' => 'contact_CopyBarCode',
            ],
          ];
          create_entity($to_create);


        $msg="      -> Civirule : Envoyer_mail_si_demande_cremation".PHP_EOL;
          $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
          fwrite($fp, $msg);
          fclose($fp);
            if (VERBOSE==1){
              echo $msg;
            }  

          $to_create =  [     //envoyer_mail_si_demande_cremation : Message template à envoyer 
            'entity' => 'MessageTemplate',
            'values' => [
              'msg_title' => 'Demander crémation au secrétariat',
              'msg_subject' => 'Merci de demander crémation pour : {contact.display_name}',
              'msg_text' => NULL,
              'msg_html' => '<p>Bonjour</p>
                <p>Merci de prévoir le transfert et de nous communiquer la date de crémation de :</p>
                <p>{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label} {contact.first_name} {Tokens_for_contact_Champs_de_fu.CONCAT_WS_last_name_nick_name}</p>
                <p>né(e)&nbsp; le {contact.birth_date} à {Tokens_for_contact_Champs_de_fu.Compl_m_nt_tat_civil.Ville_de_naissance}</p>
                <p>décédé(e) le {contact.deceased_date} à {Tokens_for_contact_Champs_de_fu.Prise_en_charge_au_d_c_s.Ville_de_d_c_s}</p>
                <p><br />
                Nous restons à votre disposition pour tout renseignement complémentaire</p>
                <p>Les techniciens du laboratoire de {domain.city}</p>',
              'is_active' => TRUE,
              'workflow_id' => NULL,
              'workflow_name' => NULL,
              'is_default' => TRUE,
              'is_reserved' => FALSE,
              'is_sms' => FALSE,
              'pdf_format_id' => 0,
            ],
          ];

          #$msg_id = serialize(create_entity($to_create));  // récupère l'id du message qui vient d'être créé et sera utilisé dans CiviRulesRuleAction (1)
          $msg_id = create_entity($to_create);  // récupère l'id du message qui vient d'être créé et sera utilisé dans CiviRulesRuleAction (1)


          $to_create =  [         //envoyer_mail_si_demande_cremation : Déclaration de l'Action
            'entity' => 'CiviRulesAction',
            'values' => [
              'name' => 'changer_elimination_pour_cremation_demandee',
              'label' => "Change le mode d'élimination pour crémation demandée",
              'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_Changeelimination',
              'is_active' => TRUE,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
            ],
          ];
          create_entity($to_create);

          $to_create =  [         //envoyer_mail_si_demande_cremation : Déclaration de la condition
            'entity' => 'CiviRulesCondition',
            'values' => [
              'name' => 'demander_cremation_du_contact',
              'label' => "Vérifie si le mode d'élimination est demander crémation",
              'class_name' => 'CRM_DonCorps_CivirulesConditions_Contact_Demandercrema',
              'is_active' => TRUE,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
            ],
          ];
          create_entity($to_create);

          $to_create =  [  // envoyer_mail_si_demande_cremation : Rule
            'entity' => 'CiviRulesRule',
            'values' => [
              'name' => 'envoyer_mail_si_demande_cremation',
              'label' => 'Envoyer mail si demande cremation',
              'is_active' => TRUE,
              'trigger_id:name' => 'changed_individual_custom_data',
              'trigger_params' => NULL,
              'description' => 'Envoi un mail aux PF si un corps passe en "demander crémation" et le passe en crémation demandée (pas de délai)',
              'help_text' => NULL,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
              'is_debug' => FALSE,
            ],
          ];
          create_entity($to_create);

          // envoyer_mail_si_demande_cremation : Rule Condition_1


          $customFields = civicrm_api4('CustomField', 'get', [ // récupère l'id du champ personnalisé
              'select' => [
                  'id',
              ],
              'where' => [
                  ['name', '=', 'Mode_limination_hors_corps_2']
              ],
              'checkPermissions' => FALSE,
              ]);


          $custom_field_id = array_column($customFields->getArrayCopy(), 'id');


          //$condition_params=serialize_custom_fields('Mode_limination_hors_corps_2');
          $to_create =  [
            'entity' => 'CiviRulesRuleCondition',
            'values' => [
              'condition_link' => NULL,
              'rule_id.name' => 'envoyer_mail_si_demande_cremation',
              'condition_id.name' => 'contact_custom_field_changed',
              'is_active' => TRUE,
              //'condition_params' => $condition_params,  
              'condition_params' => [
                  'custom_field_id' => $custom_field_id
              ],
            ],
          ];
          create_entity($to_create);

          $to_create =  [       // envoyer_mail_si_demande_cremation : Rule Condition_2
            'entity' => 'CiviRulesRuleCondition',
            'values' => [
              'condition_link' => 'AND',
              'rule_id.name' => 'envoyer_mail_si_demande_cremation',
              'condition_id.name' => 'demander_cremation_du_contact',
              'is_active' => TRUE,
              'condition_params' => NULL, 
            ],
          ];
          create_entity($to_create);

          $to_create =  [      //envoyer_mail_si_demande_cremation : Rule Action 1
            'entity' => 'CiviRulesRuleAction',
            'values' => [    
              'action_params' => [
                  'from_name' => 'Techniciens labo anatomie',
                  'from_email' => 'dons.corps@med.univ-tours.fr',
                  'template_id' => $msg_id,
                  'disable_smarty' => FALSE,
                  'location_type_id' => '',
                  'from_email_option' => '',
                  'alternative_receiver_address' => 'pompes@domain.fr',
                  'cc' => 'votre.labo@domain.fr',
                  'bcc' => '',
                  'file_on_case' => FALSE,
              ],
              #'action_params' => 'a:10:{s:9:"from_name";s:25:"Techniciens labo anatomie";s:10:"from_email";s:28:"dons.corps@med.univ-tours.fr";s:11:"template_id";'.$msg_id.'s:14:"disable_smarty";b:0;s:16:"location_type_id";s:0:"";s:17:"from_email_option";s:0:"";s:28:"alternative_receiver_address";s:23:"destrieux@univ-tours.fr";s:2:"cc";s:0:"";s:3:"bcc";s:0:"";s:12:"file_on_case";b:0;}',    
              'delay' => NULL,
              'ignore_condition_with_delay' => 0,
              'is_active' => TRUE,
              'rule_id.name' => 'envoyer_mail_si_demande_cremation',
              'action_id.name' => 'emailapi_send',
            ],
          ];
          create_entity($to_create);

          $to_create =  [       //envoyer_mail_si_demande_cremation : Rule Action 2
            'entity' => 'CiviRulesRuleAction',
            'values' => [
              'action_params' => NULL,
              'delay' => NULL,
              'ignore_condition_with_delay' => 0,
              'is_active' => TRUE,
              'rule_id.name' => 'envoyer_mail_si_demande_cremation',
              'action_id.name' => 'changer_elimination_pour_cremation_demandee',
            ],
          ];
          create_entity($to_create);


        $msg="      -> Civirule : Neutralise adresse postale en cas de retour de courrier".PHP_EOL;
          $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
          fwrite($fp, $msg);
          fclose($fp);
            if (VERBOSE==1){
              echo $msg;
            }  
          //reécupère l'id de l'action "modification des coordonnées"
          $optionValues = civicrm_api4('OptionValue', 'get', [
            'select' => [
              'value',
            ],
            'where' => [
              ['name', '=', 'modification des coordonnées'],
            ],
            'checkPermissions' => FALSE,
          ]);

          $id_activité_modif_coord = $optionValues[0]['value'];

          $activité_modif_coord_id = array_column($optionValues->getArrayCopy(), 'value');

          $to_create =  [       //  créer action "modification des coordonnées"
            'entity' => 'Afform',
            'values' => [
              'type' => 'form',
              'requires' => NULL,
              'entity_type' => NULL,
              'join_entity' => NULL,
              'title' => E::ts("Désactivation de l'adresse postale"),
              'description' => E::ts('Active le champ Adresse Incorrecte'),
              'placement' => [
                'dashboard_dashlet',
              ],
              'summary_contact_type' => NULL,
              'summary_weight' => NULL,
              'icon' => 'fa-list-alt',
              'server_route' => '',
              'is_public' => FALSE,
              'permission' => [
                'access CiviCRM',
              ],
              'permission_operator' => 'AND',
              'redirect' => NULL,
              'submit_enabled' => TRUE,
              'submit_limit' => NULL,
              'create_submission' => TRUE,
              'manual_processing' => FALSE,
              'allow_verification_by_email' => FALSE,
              'email_confirmation_template_id' => NULL,
              'navigation' => NULL,
              'modified_date' => date('Y-m-d'),
              'layout' => [
                [
                  '#tag' => 'af-form',
                  'ctrl' => 'afform',
                  '#children' => [
                    [
                      '#tag' => 'af-entity',
                      'data' => [
                        'source_contact_id' => 'user_contact_id',
                        'activity_type_id' => $id_activité_modif_coord,
                        'status_id' => '2',
                        'details' => 'Retour mail postal pour adresse erronée',
                        'subject' => 'Retour mail postal pour adresse erronée',
                      ],
                      'type' => 'Activity',
                      'name' => 'Activity1',
                      'label' => E::ts('Activité 1'),
                      'actions' => [
                        'create' => TRUE,
                        'update' => TRUE,
                      ],
                      'security' => 'RBAC',
                    ],
                    [
                      '#tag' => 'p',
                      'class' => 'af-text',
                      '#children' => [
                        [
                          '#text' => "Scannez le code barre du contact dont l'adresse doit être inactivée. Son identité s'affiche. Validez en tapant entrée. Une fois les contacts saisis, pressez le bouton Neutraliser l'adresse postale.",
                        ],
                      ],
                    ],
                    [
                      '#text' => '
            ',
                    ],
                    [
                      '#tag' => 'fieldset',
                      'af-fieldset' => 'Activity1',
                      'class' => 'af-container',
                      '#children' => [
                        [
                          '#tag' => 'af-field',
                          'name' => 'target_contact_id',
                          'defn' => [
                            'label' => E::ts('Codes barres (id) des contacts à modifier'),
                            'input_attrs' => [],
                          ],
                        ],
                      ],
                    ],
                    [
                      '#tag' => 'button',
                      'class' => 'af-button btn btn-primary',
                      'crm-icon' => 'fa-check',
                      'ng-click' => 'afform.submit()',
                      'ng-if' => 'afform.showSubmitButton',
                      '#children' => [
                        [
                          '#text' => "Neutraliser l'adresse postale",
                        ],
                      ],
                    ],
                    [
                      '#text' => '
          ',
                    ],
                  ],
                ],
              ],
              'name' => 'afformDSactiveAdressePostale',
            ],
          ];
          create_entity($to_create);

          // la variable $mail_content_triger sera utilisée pour la création de la condition 2
          $mail_content_triger = serialize($to_create['values']['layout'][0]['#children'][0]['data']['details']);

          //$mail_content_triger = $to_create['values']['layout'][0]['#children']['data']['details'];
          $to_create =  [                                                       // passer l'adresse en erroné : Rule
            'entity' => 'CiviRulesRule',
            'values' => [
              'name' => 'neutralise_adresse_postale',
              'label' => E::ts('Neutralise adresse postale'),
              'trigger_id:name' => 'new_activity',
              'trigger_params' => 'a:1:{s:11:"record_type";s:1:"0";}',
              'is_active' => TRUE,
              'description' => E::ts('Neutralise adresse postale en cas de retour de courrier'),
              'help_text' => '<p>Si une activité de type "Modification des coordonnées" avec le sujet "Retour mail postal pour adresse erronnée" est créee&nbsp;</p>
              <p>passage à OUI de adresse erronée</p>
              ',
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
              'is_debug' => FALSE,
            ],
          ];
          create_entity($to_create);


          $to_create =  [
            'entity' => 'CiviRulesRuleCondition',
            'values' => [
              'condition_link' => NULL,
              //'condition_params' => 'a:2:{s:8:"operator";s:1:"0";s:16:"activity_type_id";a:1:{i:0;'.$id_activité_modif_coord_ser.'}}',
              'condition_params' => [
                  'operator' => '0',
                  'activity_type_id' => $activité_modif_coord_id,
              ],
              'is_active' => TRUE,
              'rule_id.name' => 'neutralise_adresse_postale',
              'condition_id.name' => 'activity_of_type',
            ], 
          ];
          create_entity($to_create);

          $to_create =  [                                                     // passer l'adresse en erroné : Rule Condition 1
            'entity' => 'CiviRulesRuleCondition',
            'values' => [
                'condition_link' => 'AND',
                //'condition_params' => 'a:2:{s:8:"operator";s:8:"contains";s:4:"text";'.$mail_content_triger.'}',
                'condition_params' => [
                      'operator' => 'contains',
                      'text' => E::ts('Retour mail postal pour adresse erronée'),
                  ],
                'is_active' => TRUE,
                'rule_id.name' => 'neutralise_adresse_postale',
                'condition_id.name' => 'contact_has_activity_with_details',
              ],
          ];
          create_entity($to_create);

            // passer l'adresse en erroné : recupere l'id du champ adresse erronée 
            $customFields = civicrm_api4('CustomField', 'get', [
              'select' => [
                'id',
              ],
              'where' => [
                ['name', '=', 'Adresse_incorrecte'],
              ],
              'checkPermissions' => FALSE,
            ]);

            #$custom_adresse_incorrecte = serialize ($customFields[0]['id']);
            

          $to_create =  [                                  // passer l'adresse en erroné : Rule Action 1
            'entity' => 'CiviRulesRuleAction',
            'values' => [    
              #'action_params' => 'a:2:{s:8:"field_id";'.$custom_adresse_incorrecte.'s:5:"value";s:1:"1";}',
              'action_params' => [
                  'field_id' => $customFields[0]['id'],
                  'value' => '1',
              ],
              'delay' => NULL,
              'ignore_condition_with_delay' => 0,
              'is_active' => TRUE,
              'rule_id.name' => 'neutralise_adresse_postale',
              'action_id.name' => 'set_custom_field',
            ],
          ];
          create_entity($to_create);
      }     // fin de la création des Rules

      create_rules();
      // fin de la création des Rules
    
    $msg="  - Modification des requetes".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);
      // Modifie les requetes qui ne sont pas correctement importées
      //  sur le serveur maitre,
      //  - dans Searchkit retrouver l'id de la requete en posant la souris sur modifier, l'adresse se modifie ds la basse du bas et donne l'id
      //  - APIV4, get, avec id de la recherche,
      //
      //    $savedSearches = civicrm_api4('SavedSearch', 'get', [
      //      'select' => [
      //        'name',
      //        'api_params',
      //      ],
      //      'where' => [
      //        ['id', '=', 24], (id que vous avez retrouvée)
      //      ],
      //      'checkPermissions' => FALSE,
      //    ]);
      //   Afficher les resultats en php ; vérifier que le nom est le bon ;
      //    copier de 'api_params' => [..... à  .... 'having' => [], ] dans un editeur php,
      //    REMOVE LINE BREAK pour compacter et
      //    copier le résultat dans la commande api
      //
      //      Pour tester avec lexplorer api : afficher le resultat en json et copier la valeur dans le cahmp api_params encadres de {}
      //   { "version": 4,   ...  "having": []}
      //    $searchname = '';     //       
      //      $api_params = [];
      //  update_search($searchname, $api_params, 'bilan');*/
      //
      //  si l'import se passe bien on peut utiliser cette fonction pour seulement modifier le tag ; il faut que la valeur $api_params="onlytag";

        $api_params="onlytag";  // ne modifie que le tag

        // Requetes de tokens
        $searchname = 'tokens_lastDons';            // requete search creation tokens dernier don  //
        update_search($searchname, $api_params, 'tokens');

        $searchname = 'Tokens_PAQPF';            // requete search creation tokens PAQPF  //
        update_search($searchname, $api_params, 'tokens');

        $searchname = 'Tokens_for_contact';            // requete search creation Tokens_for_contact  //
        update_search($searchname, $api_params, 'tokens');

        $searchname = 'Tokens_pour_personne_de_confinace_1';            // requete search creation Tokens_pour_personne_de_confinace_1  //
        update_search($searchname, $api_params, 'tokens');
        
        $searchname = 'tokens_for_a_pour_personne_de_confiance_1';            // requete search creation tokens_for_a_pour_personne_de_confiance_2 (et pas 1)  //
        update_search($searchname, $api_params, 'tokens');

        // Requetes d'affichage de listes et de groupes

        $searchname = 'Donneurs_sans_PAQPF';            // requete liste donneurs sans PAQPF et avec pers referente  //
        update_search($searchname, $api_params, 'civi_ddc');

        $searchname = 'A_PAQPF';            // groupe dynamique donneurs avec PAQPF  //
        update_search($searchname, $api_params, 'civi_ddc');

        $searchname = 'Personnes_r_f_rentes_dont_le_donneur_est_d_c_d_sans_les_deux_mois_pr_c_dents';            // requete PAQF //
        update_search($searchname, $api_params, 'civi_ddc');
        
        $searchname = 'Tous_les_contacts';            // requete Tous les contacts //
        update_search($searchname, $api_params, 'civi_ddc');

        $searchname = 'Donneurs_vivants';            // requete Tous les contacts //
        update_search($searchname, $api_params, 'civi_ddc');

        $searchname = 'Donneurs_DCD';                // requete Donneurs_décédés
        update_search($searchname, $api_params, 'civi_ddc');

        $searchname = 'Annulation';                 // requete Donneurs_annulés
        update_search($searchname, $api_params,'civi_ddc');

        $searchname = 'Mairies';                   // requete mairies
        update_search($searchname, $api_params,'civi_ddc');

        $searchname = 'tableau_bord_2';            // requete tableau de bord   
        update_search($searchname, $api_params,'civi_ddc');

        $searchname = 'Donneurs_vivants_ano_ville_CP';        // requete donneurs vivants avec anomalie de ville d'adresse ou CP  
        update_search($searchname, $api_params,'civi_ddc');

        $searchname = 'Emprunteurs';                // requete emprunteurs et lieux de stockage 
        //$api_params = ['version' => 4, 'select' => [ 'id', 'contact_sub_type:label', 'display_name', 'Contact_Address_contact_id_01.street_address', 'Contact_Address_contact_id_01.supplemental_address_1', 'Contact_Address_contact_id_01.postal_code', 'Contact_Address_contact_id_01.city', 'phone_primary.phone', ], 'orderBy' => [], 'where' => [ [ 'OR', [ [ 'contact_sub_type:name', 'CONTAINS', 'Emprunteur', ], [ 'contact_sub_type:name', 'CONTAINS', 'CDC', ], ], ], ], 'groupBy' => [], 'join' => [ [ 'Address AS Contact_Address_contact_id_01', 'LEFT', [ 'id', '=', 'Contact_Address_contact_id_01.contact_id', ], [ 'Contact_Address_contact_id_01.is_primary', '=', TRUE, ], ], ], 'having' => [],];
        update_search($searchname, $api_params,'civi_ddc');

        $searchname = 'Personnel_centre_de_don';     // requete personnels centre de don (crée groupe dyn)
        update_search($searchname, $api_params,'civi_ddc');

        $searchname = 'Centres_d_accueil_des_corps';     // requete centre de don 
        update_search($searchname, $api_params,'civi_ddc');

        $searchname = 'Pompes_funebres';            // requete pompes funebres (crée groupe dyn)
        update_search($searchname, $api_params,'civi_ddc');

        $searchname = 'Inventaires';                // requete inventaires
        update_search($searchname, $api_params,'civi_ddc');

        $searchname = 'Toutes_pi_ces_corps';        // requete utilisée pour lister corps et pieces presents     
        update_search($searchname, $api_params, 'civi_ddc');

        $searchname = 'participants';        // requete utilisée pour lister participants aux ceremonies    
        update_search($searchname, $api_params, 'civi_ddc');

        $searchname = 'in_memoriam';        // requete utilisée pour lister les donneurs défunts souhaitant inscription sur stele   
        update_search($searchname, $api_params, 'civi_ddc');

        // Requetes utilisées par les purges
        $searchname = 'Donneurs_annul_s';          // requete donneurs annulés sans ceux placés en archive, ie deja purgé       
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Anonymis_s_par_la_purge';   // requete contacts ayant été anonymisés par les purges
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Anonymises_sans_protocole';      // requete Donneurs anonymisés dont les ATCD n'ont pas été purgés et qui ne sont pas inclus dans un protocole.
                                                  // Utilisé pour préserver les ATCD des donneurs inclus dans un ptotocole
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Proches_sans_relation_1';                      // requete Proches sans relation    
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Personnels';                                   // requete Personnels partis depuis plus de 5 ans      
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Demandeurs_d_informations_plus_d_un_an';      // requete demandeurs d'informations de plus d'un an      
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Donneurs_120_ans';                        // requete Donneurs âgés de plus de 120 ans et sans numéro de DC  : anonymisation 
        update_search($searchname, $api_params, 'purge');

        $searchname = 'donneurs_DC_un_an_et_refus';              // requete  Donneurs décédés depuis plus d'un an et refusés en raison du motif de décès ou de l'état de conservation : suppression"),     
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Donneurs_dont_op_fun_raires_1_an';        // requeteDonneurs dont les opérations funéraires ont été achevées il y a plus d'un an : suppression des relations (avant purge des proches sans relations)\nLes donneurs dont les relations sont déja purgés (tag \"relations purgées\" sont exclus"),
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Op_rations_fun_raires_de_plus_de_5_ans'; // requete Op_rations_fun_raires_de_plus_de_5_ans  
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Proches_dont_donneur_DC_1_an_et_refuses'; // requete Proches_dont_donneur_DC_1_an_et_refuses     
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Contacts_impliqu_s_dans_un_protocole_ex_vivo';                   //    %    Contacts ayant au moins une piece impliquée dans un protocole ex vivo
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Archives_dans_protocole_in_ni_ex_vivo';                   //    %    Contacts impliques dans aucun protocole
        update_search($searchname, $api_params, 'purge');

        $searchname = 'Personnels_tous';                   //    %    Liste les personnels des CDC ; utilisé pour crer un groupe pour filtrer les contacs dans utilisaito ncorps
        update_search($searchname, $api_params, 'purge');

        // Requetes utilisées pour le bilan annuel (aform activité)
        $searchname = 'corps_pr_sents_au_1_1_ann_e_en_cours';                   // % requete Bilan : corps présents au 1/1 année en cours      
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'bilan_corps_presents_au_31_12_ann_e_A_1';                // % requete bilan : corps presents au 31/12 année A -1         
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'bilan_corps_presents_31_12_ann_e_en_cours';             // bilan : nombre de corps presents 31/12 année en cours          
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'bilan_nombre_de_corps_pr_sents_1_1_ann_e_A_1';         // % bilan : nombre de corps présents 1/1 année A-1        
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'bilan_nombre_de_corps_recus_dans_l_ann_e_pr_cdente';   //  bilan : corps recus année A-1'         
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Dons_ann_e_en_cours';                                  //  % Bilan : dons année en cours
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Inscriptions_annulations_ann_e_en_cours';              // Bilan : Inscriptions année en cours     
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_annulations_ann_e_A_1';                          // Bilan : annulations année A-1      
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_c_r_monies_ann_e_en_cours';                      // % Bilan : cérémonies année en cours      
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_c_r_monies_ann_e_A_1';                           // % Bilan : cérémonies année en cours      
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_corps_recus_et_devenir_ann_e_en_cours';          // Bilan : corps sortis année en cours      
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_corps_sortis_ann_e_A_1';                         // % Bilan : corps sortis année A -1      
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_demandeurs_information_ann_e_en_cours';          // Bilan : demandeurs information année en cours      
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_dons_ann_e_A_1';                                 // Bilan : dons année A -1       
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_inscription_ann_e_A_1';                          //  Bilan : inscription année A-1     
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_nombre_d_annualtion_ann_e_en_cours';             //  Bilan : nombre d'annulations année en cours     
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_nombre_de_corps_rec_us_ann_e_en_cours';          //  Bilan : nombre de corps reçus année en cours     
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'corps_pr_sents_au_1_1_ann_e_en_cours';             // bilan : nombre de corps presents 1/1 année en cours          
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_nombre_de_demandeurs_d_information';             // Bilan : nombre de demandeurs d'information année A-1      
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Personnels_id3q';                                           //  Personnels     
        update_search($searchname, $api_params, 'bilan');

        $searchname = 'Bilan_refus_ou_non_reception_corps_A_1';               // % Corps refusés ou non recus année A -1 
        update_search($searchname, $api_params, 'bilan');
      
        $searchname = 'Bilan_refus_ou_non_reception_corps';                   //    %    Corps refusés ou non recus année en cours
        update_search($searchname, $api_params, 'bilan');


      
       // fin de Modifie les requetes qui ne sont pas correctement importées

    

    /*  création des layouts (inutile avec managed)

        /// DEFINITION DE LA VARIABLE $layout QUI COMPREND LES PARAMETRES DE TOUS LES LAYOUTS
        ///
        /// la variable layout doit être récupérée depuis un site maitre via Support>Developperu>explorateur APIv4
        /// Entité : ContactLayout
        /// Action : get
        /// Récuperer la variable dans la boite résltats, au format php (cliquer sur voir en JSON -> voir en PHP)
        /// copier coller la sortie et ramplacer return par $layouts
        ///

      echo "  - Installation des layouts".PHP_EOL;

        $layouts = [
          [
            'id' => 1,
            'label' => E::ts('Donneur'),
            'contact_type' => 'Individual',
            'contact_sub_type' => [
              'Donateur',
            ],
            'groups' => NULL,
            'weight' => 1,
            'blocks' => [
              [
                [
                  [
                    'name' => 'profile.Type_de_contact_23',
                    'title' => E::ts('Type de contact'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                  [
                    'name' => 'profile.Dates_naissance_et_d_c_s_17',
                    'title' => E::ts('Dates naissance et décès'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'core.Address',
                    'title' => E::ts('Adresse'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'profile.Profil_sans_nom_20',
                    'title' => E::ts('Vérification adresse'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                  [
                    'name' => 'core.Email',
                    'title' => E::ts('Courriel'),
                  ],
                  [
                    'name' => 'core.Phone',
                    'title' => E::ts('Téléphone'),
                  ],
                  [
                    'name' => 'custom.Ant_c_dents_m_dicaux',
                    'title' => E::ts('Antécédents médicaux'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'custom.Demandeur_information',
                    'title' => E::ts("Demande d'information"),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'custom.Promesse_de_don',
                    'title' => E::ts('Promesse de don'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'custom.Annulation',
                    'title' => E::ts('Annulation'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                ],
                [
                  [
                    'name' => 'custom.Prise_en_charge_au_d_c_s',
                    'title' => E::ts('Prise en charge au décès'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'custom.Transfert_vers_autre_centre',
                    'title' => E::ts('En cas de transfert vers un autre centre'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'profile.CESP_29',
                    'title' => E::ts('CESP'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'profile.Op_rations_fun_raires_r_alis_es_30',
                    'title' => E::ts('Opérations funéraires réalisées'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'profile.Restitution_28',
                    'title' => E::ts('Restitution'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'custom.champs_caches',
                    'title' => E::ts('champs caches'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                ],
              ],
            ],
            'tabs' => [
              [
                'id' => 'summary',
                'is_active' => 1,
              ],
              [
                'id' => 'contact_documents',
                'is_active' => 1,
              ],
              [
                'id' => 'contribute',
                'is_active' => 1,
                'icon' => 'crm-i fa-money',
              ],
              [
                'id' => 'participant',
                'is_active' => 1,
                'icon' => 'crm-i fa-users',
              ],
              [
                'id' => 'mailing',
                'is_active' => 1,
              ],
              [
                'id' => 'activity',
                'is_active' => 1,
              ],
              [
                'id' => 'rel',
                'is_active' => 1,
              ],
              [
                'id' => 'note',
                'is_active' => 1,
              ],
              [
                'id' => 'tag',
                'is_active' => 1,
              ],
              [
                'id' => 'group',
                'is_active' => 1,
              ],
              [
                'id' => 'log',
                'is_active' => 1,
              ],
              [
                'id' => 'custom_4',
                'is_active' => 1,
                'icon' => 'crm-i fa-ambulance',
              ],
              [
                'id' => 'custom_13',
                'is_active' => 1,
                'icon' => 'crm-i fa-sign-language',
              ],
              [
                'id' => 'custom_11',
                'is_active' => 1,
                'icon' => 'crm-i fa-flask',
              ],
            ],
            'settings' => [
              'sub_type_operator' => 'OR',
            ],
          ],
          [
            'id' => 2,
            'label' => E::ts('Proches'),
            'contact_type' => 'Individual',
            'contact_sub_type' => [
              'Proches',
            ],
            'groups' => NULL,
            'weight' => 3,
            'blocks' => [
              [
                [
                  [
                    'name' => 'profile.Type_de_contact_23',
                    'title' => E::ts('Type de contact'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                ],
                [
                  [
                    'name' => 'core.Address',
                    'title' => E::ts('Adresse'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'core.Email',
                    'title' => E::ts('Courriel'),
                  ],
                  [
                    'name' => 'core.Phone',
                    'title' => E::ts('Téléphone'),
                  ],
                ],
              ],
            ],
            'tabs' => [
              [
                'id' => 'summary',
                'is_active' => 1,
              ],
              [
                'id' => 'contact_documents',
                'is_active' => 1,
              ],
              [
                'id' => 'contribute',
                'is_active' => 1,
                'icon' => 'crm-i fa-money',
              ],
              [
                'id' => 'participant',
                'is_active' => 1,
                'icon' => 'crm-i fa-users',
              ],
              [
                'id' => 'mailing',
                'is_active' => 1,
              ],
              [
                'id' => 'activity',
                'is_active' => 1,
              ],
              [
                'id' => 'rel',
                'is_active' => 1,
              ],
              [
                'id' => 'group',
                'is_active' => 1,
              ],
              [
                'id' => 'note',
                'is_active' => 1,
              ],
              [
                'id' => 'tag',
                'is_active' => 1,
              ],
              [
                'id' => 'log',
                'is_active' => 1,
              ],
              [
                'id' => 'custom_4',
                'is_active' => 0,
                'icon' => 'crm-i fa-ambulance',
              ],
              [
                'id' => 'custom_13',
                'is_active' => 0,
                'icon' => 'crm-i fa-sign-language',
              ],
              [
                'id' => 'custom_11',
                'is_active' => 0,
                'icon' => 'crm-i fa-flask',
              ],
            ],
            'settings' => [
              'sub_type_operator' => 'OR',
            ],
          ],
          [
            'id' => 3,
            'label' => E::ts('Personnel'),
            'contact_type' => 'Individual',
            'contact_sub_type' => [
              'Personnel',
            ],
            'groups' => NULL,
            'weight' => 4,
            'blocks' => [
              [
                [
                  [
                    'name' => 'profile.Type_de_contact_23',
                    'title' => E::ts('Type de contact'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                  [
                    'name' => 'profile.Employeur',
                    'title' => E::ts('Employeur'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                  [
                    'name' => 'custom.infos_personnel',
                    'title' => E::ts('Informations Personnel'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                ],
                [
                  [
                    'name' => 'core.Address',
                    'title' => E::ts('Adresse'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'core.Email',
                    'title' => E::ts('Courriel'),
                  ],
                  [
                    'name' => 'core.Phone',
                    'title' => E::ts('Téléphone'),
                  ],
                ],
              ],
            ],
            'tabs' => [
              [
                'id' => 'summary',
                'is_active' => 1,
              ],
              [
                'id' => 'contribute',
                'is_active' => 1,
                'icon' => 'crm-i fa-money',
              ],
              [
                'id' => 'participant',
                'is_active' => 1,
                'icon' => 'crm-i fa-users',
              ],
              [
                'id' => 'mailing',
                'is_active' => 1,
              ],
              [
                'id' => 'activity',
                'is_active' => 1,
              ],
              [
                'id' => 'rel',
                'is_active' => 1,
              ],
              [
                'id' => 'group',
                'is_active' => 1,
              ],
              [
                'id' => 'note',
                'is_active' => 1,
              ],
              [
                'id' => 'tag',
                'is_active' => 1,
              ],
              [
                'id' => 'log',
                'is_active' => 1,
              ],
              [
                'id' => 'contact_documents',
                'is_active' => 1,
              ],
              [
                'id' => 'custom_4',
                'is_active' => 0,
                'icon' => 'crm-i fa-ambulance',
              ],
              [
                'id' => 'custom_13',
                'is_active' => 0,
                'icon' => 'crm-i fa-sign-language',
              ],
              [
                'id' => 'custom_11',
                'is_active' => 0,
                'icon' => 'crm-i fa-flask',
              ],
            ],
            'settings' => [
              'sub_type_operator' => 'OR',
            ],
          ],
          [
            'id' => 4,
            'label' => E::ts('Organisation'),
            'contact_type' => 'Organization',
            'contact_sub_type' => NULL,
            'groups' => NULL,
            'weight' => 9,
            'blocks' => [
              [
                [
                  [
                    'name' => 'core.Address',
                    'title' => E::ts('Adresse'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'core.Email',
                    'title' => E::ts('Courriel'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                ],
                [
                  [
                    'name' => 'core.Phone',
                    'title' => E::ts('Téléphone'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                ],
              ],
              [
                [
                  [
                    'name' => 'custom.centre_de_don',
                    'title' => E::ts('Informations centre de don'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                ],
              ],
            ],
            'tabs' => [
              [
                'id' => 'summary',
                'is_active' => 1,
              ],
              [
                'id' => 'contact_documents',
                'is_active' => 1,
              ],
              [
                'id' => 'contribute',
                'is_active' => 0,
                'icon' => 'crm-i fa-money',
              ],
              [
                'id' => 'participant',
                'is_active' => 0,
                'icon' => 'crm-i fa-users',
              ],
              [
                'id' => 'mailing',
                'is_active' => 1,
              ],
              [
                'id' => 'activity',
                'is_active' => 1,
              ],
              [
                'id' => 'rel',
                'is_active' => 0,
              ],
              [
                'id' => 'group',
                'is_active' => 1,
              ],
              [
                'id' => 'note',
                'is_active' => 1,
              ],
              [
                'id' => 'tag',
                'is_active' => 1,
              ],
              [
                'id' => 'log',
                'is_active' => 1,
              ],
              [
                'id' => 'custom_4',
                'is_active' => 0,
                'icon' => 'crm-i fa-ambulance',
              ],
              [
                'id' => 'custom_13',
                'is_active' => 0,
                'icon' => 'crm-i fa-sign-language',
              ],
              [
                'id' => 'custom_11',
                'is_active' => 0,
                'icon' => 'crm-i fa-flask',
              ],
            ],
            'settings' => [
              'sub_type_operator' => 'OR',
            ],
          ],
          [
            'id' => 5,
            'label' => E::ts('Demandeur information<br>'),
            'contact_type' => 'Individual',
            'contact_sub_type' => [
              'Demandeur_d_information',
            ],
            'groups' => NULL,
            'weight' => 2,
            'blocks' => [
              [
                [
                  [
                    'name' => 'profile.Type_de_contact_23',
                    'title' => E::ts('Type de contact'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                  [
                    'name' => 'custom.Demandeur_information',
                    'title' => E::ts("Demande d'information"),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                ],
                [
                  [
                    'name' => 'core.Address',
                    'title' => E::ts('Adresse'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'core.Email',
                    'title' => E::ts('Courriel'),
                  ],
                  [
                    'name' => 'core.Phone',
                    'title' => E::ts('Téléphone'),
                  ],
                ],
              ],
            ],
            'tabs' => [
              [
                'id' => 'summary',
                'is_active' => 1,
              ],
              [
                'id' => 'contact_documents',
                'is_active' => 1,
              ],
              [
                'id' => 'contribute',
                'is_active' => 0,
                'icon' => 'crm-i fa-money',
              ],
              [
                'id' => 'participant',
                'is_active' => 0,
                'icon' => 'crm-i fa-users',
              ],
              [
                'id' => 'mailing',
                'is_active' => 1,
              ],
              [
                'id' => 'activity',
                'is_active' => 1,
              ],
              [
                'id' => 'rel',
                'is_active' => 0,
              ],
              [
                'id' => 'group',
                'is_active' => 1,
              ],
              [
                'id' => 'note',
                'is_active' => 1,
              ],
              [
                'id' => 'tag',
                'is_active' => 1,
              ],
              [
                'id' => 'log',
                'is_active' => 1,
              ],
              [
                'id' => 'custom_4',
                'is_active' => 0,
                'icon' => 'crm-i fa-ambulance',
              ],
              [
                'id' => 'custom_13',
                'is_active' => 0,
                'icon' => 'crm-i fa-sign-language',
              ],
              [
                'id' => 'custom_11',
                'is_active' => 0,
                'icon' => 'crm-i fa-flask',
              ],
            ],
            'settings' => [
              'sub_type_operator' => 'OR',
            ],
          ],
          [
            'id' => 6,
            'label' => E::ts('Pompes Funebres<br>'),
            'contact_type' => 'Organization',
            'contact_sub_type' => [
              'Pompes',
            ],
            'groups' => NULL,
            'weight' => 5,
            'blocks' => [
              [
                [
                  [
                    'name' => 'core.Address',
                    'title' => E::ts('Adresse'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'core.Email',
                    'title' => E::ts('Courriel'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                ],
                [
                  [
                    'name' => 'core.Phone',
                    'title' => E::ts('Téléphone'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                ],
              ],
            ],
            'tabs' => [
              [
                'id' => 'summary',
                'is_active' => 1,
              ],
              [
                'id' => 'contact_documents',
                'is_active' => 1,
              ],
              [
                'id' => 'contribute',
                'is_active' => 0,
                'icon' => 'crm-i fa-money',
              ],
              [
                'id' => 'participant',
                'is_active' => 0,
                'icon' => 'crm-i fa-users',
              ],
              [
                'id' => 'mailing',
                'is_active' => 1,
              ],
              [
                'id' => 'activity',
                'is_active' => 1,
              ],
              [
                'id' => 'rel',
                'is_active' => 0,
              ],
              [
                'id' => 'group',
                'is_active' => 1,
              ],
              [
                'id' => 'note',
                'is_active' => 1,
              ],
              [
                'id' => 'tag',
                'is_active' => 1,
              ],
              [
                'id' => 'log',
                'is_active' => 1,
              ],
              [
                'id' => 'custom_4',
                'is_active' => 0,
                'icon' => 'crm-i fa-ambulance',
              ],
              [
                'id' => 'custom_13',
                'is_active' => 0,
                'icon' => 'crm-i fa-sign-language',
              ],
              [
                'id' => 'custom_11',
                'is_active' => 0,
                'icon' => 'crm-i fa-flask',
              ],
            ],
            'settings' => [
              'sub_type_operator' => 'OR',
            ],
          ],
          [
            'id' => 7,
            'label' => E::ts('Centre de don<br>'),
            'contact_type' => 'Organization',
            'contact_sub_type' => [
              'CDC',
              'Emprunteur',
            ],
            'groups' => NULL,
            'weight' => 7,
            'blocks' => [
              [
                [
                  [
                    'name' => 'custom.CDC_admin',
                    'title' => E::ts('CDC admin'),
                    'collapsible' => TRUE,
                    'collapsed' => FALSE,
                  ],
                ],
                [
                  [
                    'name' => 'core.Phone',
                    'title' => E::ts('Téléphone'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'core.Address',
                    'title' => E::ts('Adresse'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'core.Email',
                    'title' => E::ts('Courriel'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                ],
              ],
            ],
            'tabs' => [
              [
                'id' => 'summary',
                'is_active' => 1,
              ],
              [
                'id' => 'contact_documents',
                'is_active' => 1,
              ],
              [
                'id' => 'contribute',
                'is_active' => 0,
                'icon' => 'crm-i fa-money',
              ],
              [
                'id' => 'participant',
                'is_active' => 0,
                'icon' => 'crm-i fa-users',
              ],
              [
                'id' => 'mailing',
                'is_active' => 1,
              ],
              [
                'id' => 'activity',
                'is_active' => 1,
              ],
              [
                'id' => 'rel',
                'is_active' => 0,
              ],
              [
                'id' => 'group',
                'is_active' => 1,
              ],
              [
                'id' => 'note',
                'is_active' => 1,
              ],
              [
                'id' => 'tag',
                'is_active' => 1,
              ],
              [
                'id' => 'log',
                'is_active' => 1,
              ],
              [
                'id' => 'custom_4',
                'is_active' => 0,
                'icon' => 'crm-i fa-ambulance',
              ],
              [
                'id' => 'custom_13',
                'is_active' => 0,
                'icon' => 'crm-i fa-sign-language',
              ],
              [
                'id' => 'custom_11',
                'is_active' => 0,
                'icon' => 'crm-i fa-flask',
              ],
            ],
            'settings' => [
              'sub_type_operator' => 'OR',
            ],
          ],
          [
            'id' => 8,
            'label' => E::ts('Mairie'),
            'contact_type' => 'Organization',
            'contact_sub_type' => NULL,
            'groups' => NULL,
            'weight' => 6,
            'blocks' => [
              [
                [
                  [
                    'name' => 'core.Address',
                    'title' => E::ts('Adresse'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                  [
                    'name' => 'core.Email',
                    'title' => E::ts('Courriel'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                ],
                [
                  [
                    'name' => 'core.Phone',
                    'title' => E::ts('Téléphone'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => TRUE,
                  ],
                ],
              ],
            ],
            'tabs' => [
              [
                'id' => 'summary',
                'is_active' => 1,
              ],
              [
                'id' => 'contact_documents',
                'is_active' => 1,
              ],
              [
                'id' => 'contribute',
                'is_active' => 0,
                'icon' => 'crm-i fa-money',
              ],
              [
                'id' => 'participant',
                'is_active' => 0,
                'icon' => 'crm-i fa-users',
              ],
              [
                'id' => 'mailing',
                'is_active' => 1,
              ],
              [
                'id' => 'activity',
                'is_active' => 1,
              ],
              [
                'id' => 'rel',
                'is_active' => 0,
              ],
              [
                'id' => 'group',
                'is_active' => 1,
              ],
              [
                'id' => 'note',
                'is_active' => 1,
              ],
              [
                'id' => 'tag',
                'is_active' => 1,
              ],
              [
                'id' => 'log',
                'is_active' => 1,
              ],
              [
                'id' => 'custom_4',
                'is_active' => 0,
                'icon' => 'crm-i fa-ambulance',
              ],
              [
                'id' => 'custom_13',
                'is_active' => 0,
                'icon' => 'crm-i fa-sign-language',
              ],
              [
                'id' => 'custom_11',
                'is_active' => 0,
                'icon' => 'crm-i fa-flask',
              ],
            ],
            'settings' => [
              'sub_type_operator' => 'OR',
            ],
          ],
          [
            'id' => 9,
            'label' => E::ts('Animal'),
            'contact_type' => 'Individual',
            'contact_sub_type' => [
              'Animal',
            ],
            'groups' => NULL,
            'weight' => 8,
            'blocks' => [
              [
                [
                  [
                    'name' => 'custom.animal',
                    'title' => E::ts('animal'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                ],
                [
                  [
                    'name' => 'profile.D_mographie_animal',
                    'title' => E::ts('Démographie animal'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                  [
                    'name' => 'custom.champs_caches',
                    'title' => E::ts('champs caches'),
                    'collapsible' => FALSE,
                    'collapsed' => FALSE,
                    'showTitle' => FALSE,
                  ],
                ],
              ],
            ],
            'tabs' => [
              [
                'id' => 'summary',
                'is_active' => 1,
              ],
              [
                'id' => 'contact_documents',
                'is_active' => 1,
              ],
              [
                'id' => 'contribute',
                'is_active' => 0,
                'icon' => 'crm-i fa-money',
              ],
              [
                'id' => 'participant',
                'is_active' => 0,
                'icon' => 'crm-i fa-users',
              ],
              [
                'id' => 'mailing',
                'is_active' => 0,
              ],
              [
                'id' => 'activity',
                'is_active' => 1,
              ],
              [
                'id' => 'rel',
                'is_active' => 0,
              ],
              [
                'id' => 'note',
                'is_active' => 1,
              ],
              [
                'id' => 'tag',
                'is_active' => 1,
              ],
              [
                'id' => 'group',
                'is_active' => 1,
              ],
              [
                'id' => 'log',
                'is_active' => 1,
              ],
              [
                'id' => 'custom_4',
                'is_active' => 0,
                'icon' => 'crm-i fa-ambulance',
              ],
              [
                'id' => 'custom_13',
                'is_active' => 1,
                'icon' => 'crm-i fa-sign-language',
              ],
              [
                'id' => 'custom_11',
                'is_active' => 1,
                'icon' => 'crm-i fa-flask',
              ],
            ],
            'settings' => [
              'sub_type_operator' => 'OR',
            ],
          ],
        ];

              /// FIN DE LA DEFINITION DE LA VARIABLE LAYOUT QUI COMPRENT LES PARAMETRES DE TOUS LES LAYOUTS
              install_layouts ($layouts);


              /// inactivation des onglets inutiles et changement des icones dans les layouts installés
        
              echo "  - inactivation des onglets inutiles et changement des icones dans les layouts installés".PHP_EOL;
        
              $icons_default = [                      /// modifier ici les icones à afficher par tab
                ["name" => "Arriv_e_du_corps_new",
                "id" => "",
                "icon" => "crm-i fa-ambulance"],
        
                ["name" => "Utilisation_du_corps",
                "id" => "",
                "icon" => "crm-i fa-sign-language"],
        
                ["name" => "Protocoles_in_vivo",
                "id" => "",
                "icon" => "crm-i fa-flask"],
        
                ["name" => "contribute",
                  'id' => 'contribute',
                "icon" => "crm-i fa-money"],
        
                ["name" => "participant",
                  'id' => 'participant',
                "icon" => "crm-i fa-users"],
              ];

        $inactive_tabs =[                       /// indiquer pour chaque profil la liste des tabs à desactiver
          ['Proches' =>["Protocoles_in_vivo", "Arriv_e_du_corps_new","Utilisation_du_corps"]],
          ['Organisation' =>["Protocoles_in_vivo", "Arriv_e_du_corps_new","Utilisation_du_corps","contribute","participant","rel"]],
          ['Personnel' =>["Protocoles_in_vivo", "Arriv_e_du_corps_new","Utilisation_du_corps"]],
          ['Pompes Funebres<br>' =>["Protocoles_in_vivo", "Arriv_e_du_corps_new","Utilisation_du_corps","contribute","participant","rel"]],
          ['Centre de don<br>' =>["Protocoles_in_vivo", "Arriv_e_du_corps_new","Utilisation_du_corps","contribute","participant","rel"]],
          ['Mairie' =>["Protocoles_in_vivo", "Arriv_e_du_corps_new","Utilisation_du_corps", "contribute","participant","rel"]],
          ['Demandeur information<br>' =>["Protocoles_in_vivo", "Arriv_e_du_corps_new","Utilisation_du_corps","participant","contribute","rel"]],
          ['Animal' =>["contribute", "Arriv_e_du_corps_new","participant","rel","mailing"]],
          ];

        change_tabs($icons_default, $inactive_tabs);
      // fin de creation des Layouts
      */

    $msg="  - Changement des icones de menus".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);

      change_icon('Contributions','crm-i fa-money-bill-1');
      change_icon('Events','crm-i fa-users');
      // Fin du Changement des icones de menus

    $msg="  - Modification des url de redirection et d'annulation des profils".PHP_EOL; 
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);

      $uFGroups = civicrm_api4('UFGroup', 'get', [   // récupère la liste des profils
        'select' => [
            'name',
            'id',
        ],
        'checkPermissions' => FALSE,
        ]);
            
        foreach ($uFGroups as $uFGroup){                        // crée un tableau avec [id_UFGroup][name_UFGroup]
            $profile_names[$uFGroup['id']]=$uFGroup['name'];
        }
        $cancel_url = admin_url("admin.php?page=CiviCRM");  // url à charger si annulation 
        $url = admin_url("admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid=")."{contact.id}";  // url à charger apres creation du contact utilisant le profil 
        
        $profiles_to_update = ['Mairie','Lieu_de_stockage','Centre_d_accueil_des_corps','Personnel_de_centre_de_don_de_corps','Inscription_proche_donateur_14', 'Demandeur_information_22', 'inscription_pompes', 'Inscription_donateur','Inscription_anat_compar_e'];
  
        // Liste de profils à associer à un role (ceux utilisés pour creation contacts) name_and_address = ionscription donneur ; Inscription_proche_donateur_27 : pompes
        
        foreach ($profiles_to_update as $profile_to_update) {
            $position = array_search($profile_to_update, $profile_names);
            if ($position !== false) {                                          // Si le profil est déja créé 
                $to_create =  [                                                 // modifie l'URL à afficher apres la creation (post url) par un profil
                    'entity' => 'UFGroup',
                    'values' => [
                        'post_url' => $url,
                        'cancel_url' => $cancel_url,
                        'name' => $profile_to_update,
                    ],
                  ];
                  create_entity($to_create);  // create ou update UFJOIN
        
                $to_create =  [                                                 // ajoute à chacun de ces profils l'utilisaiton "Profile" = "Formulaire ou Liste à afficher"
                  'entity' => 'UFJoin',
                  'values' => [
                      'uf_group_id:name' => $profile_to_update,
                      'module' => 'Profile',
                      'is_active' => TRUE,
                      'module_data' => NULL,
                  ],
                ];
                create_entity($to_create);  // create ou update UFJOIN
              
              } else {
                  $msg="         ".$profile_to_update." : Profil non trouvé ////////.".PHP_EOL;
                  $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
                  fwrite($fp, $msg);
                  if (VERBOSE==1){
                    echo $msg;
                  }
                  fclose($fp);
              }
        }
     // fin de Modification des url de redirection et d'annulation des profils

     /// Modification des menus de navigation liés aux profil de création de contacts
    $msg="  - Modification des menus de navigation liés aux profils".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);

      $url_menus_to_change =[                             // Profil name, parent_id:name, name du menu navigation
        ['Inscription_donateur', 'ContactsDDC','New DonateurDDC'],  //// MODIFIE
        ['Inscription_proche_donateur_14', 'ContactsDDC','Ajouter proche donateurDDC'],///MODIFIE
        ['Demandeur_information_22', 'ContactsDDC','New Demandeur_d_informationDDC'],///MODIFIE
        ['inscription_pompes', 'Pompes funebresDDC','New Pompes'],  // 'Inscription_proche_donateur_27' correpond au profil pompes
        ['Mairie', 'MairiesDDC','New Mairies'],
        ['Personnel_de_centre_de_don_de_corps', 'Centres de don du corpsDDC','New Personnel'],
        ['Centre_d_accueil_des_corps', 'Centres de don du corpsDDC','New CDC'],
        ['Lieu_de_stockage', 'Pièces anatomiquesDDC','New Emprunteur'],
        ['Inscription_anat_compar_e', 'ContactsDDC','Nouvelle pièce anat comparée'],
      ];

      foreach ($url_menus_to_change as $url_menu_to_change){
        $uFGroups = civicrm_api4('UFGroup', 'get', [                        // récupere l'id du profil
            'select' => [
            'id',
            ],
            'where' => [
            ['name', '=', $url_menu_to_change[0]],
            ],
            'checkPermissions' => FALSE,
        ]);

        if ($uFGroups[0]['id']!=0){                                       // si le profil existe

            //echo $url_menu_to_change[1]." / ".$url_menu_to_change[2]." / ".$url_menu_to_change[0]." : ";
            $to_create =  [                                                 // modifie l'URL pour le menu
                'entity' => 'Navigation',
                'values' => [
                    'parent_id:name' => $url_menu_to_change[1],
                    'name' => $url_menu_to_change[2],
                    'url' => 'civicrm/profile/create/?gid='.$uFGroups[0]['id'].'&reset=1',
                    ],
                ];
            create_entity($to_create);                                     // create ou update navigation menu  

        }else {

            $msg= "***** Le profil ".$url_menu_to_change[0]." n'existe pas *****".PHP_EOL;
            $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
            fwrite($fp, $msg);
            if (VERBOSE==1){
              echo $msg;
            }
            fclose($fp);
              }
      }
     /// Fin de Modification des menus de navigation liés aux profil de création de contacts

    $msg="  - Création des templates d'emails".PHP_EOL; // (hors ceux crees par les rules)
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);

      $to_create =  [       
        'entity' => 'MessageTemplate',
        'values' => [
          'msg_title' => '001 - Préinscription: envoi informations MAIL',
          'msg_subject' => "Envoi d'informations d'inscription",
          'msg_text' => NULL,
          'msg_html' => "<p>{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</p>\r\n\r\n<p>Je vous remercie de votre volonté de donner votre corps à des fins d’enseignement et de recherche et vous engage à consulter notre site web : <strong><a href=\"https://dons-corps.univ-tours.fr\">{domain.description}</a>.</strong></p>\r\n\r\n<p>Vous y trouverez de nombreuses informations et des réponses aux principales questions que vous pourriez vous poser.</p>\r\n\r\n<p>Vous pourrez télécharger les documents nécessaires à votre inscription :<br />\r\n<strong>1) le guide d’information officiel : <a href=\"https://dons-corps.univ-tours.fr/medias/fichier/guide-information-juil2024-avectours_1733124203133-pdf?ID_FICHE=402072&amp;INLINE=FALSE\">Téléchargez le guide</a></strong></p>\r\n\r\n<p><strong>2)&nbsp; le formulaire de promesse de don à nous retourner par courrier si vous poursuivez votre démarche : <a href=\"https://dons-corps.univ-tours.fr/medias/fichier/declaration-consentement-don-corps-avecrgpd-2023-09-22_1759243997537-pdf?ID_FICHE=402072&amp;INLINE=FALSE\">Téléchargez le dossier d'inscription</a></strong></p>\r\n\r\n<p>N’hésitez pas à nous contacter si vous avez besoin d’information complémentaire</p>\r\n\r\n<p>Je vous remercie à nouveau de votre intérêt pour le don du corps et vous prie d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de ma parfaite considération.</p>\r\n\r\n<p>{domain.supplemental_address_3}<br />\r\nCentre d'accueil des corps de {domain.city}</p>",
          'is_active' => TRUE,
          'workflow_id' => NULL,
          'workflow_name' => NULL,
          'is_default' => TRUE,
          'is_reserved' => FALSE,
          'is_sms' => FALSE,
          'pdf_format_id' => 0,
        ],
      ];
      create_entity($to_create);


      $to_create =  [       
        'entity' => 'MessageTemplate',
        'values' => [
          'msg_title' => '002 - Préinscription: envoi informations POSTALE',
          'msg_subject' => "Envoi d'informations d'inscription",
          'msg_text' => NULL,
          'msg_html' => "<p>{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</p>\r\n\r\n<p>Je vous remercie de votre volonté de donner votre corps à des fins d’enseignement et de recherche.</p>\r\n\r\n<p>Nous allons vous adresser très prochainement votre dossier d'inscription par voie postale.</p>\r\n\r\n<p>Vous pouvez consulter notre site web : <strong><a href=\"https://dons-corps.univ-tours.fr\">{domain.description}</a>&nbsp;</strong>où vous<strong>&nbsp;</strong>trouverez de nombreuses informations et des réponses aux questions que vous pourriez vous poser.</p>\r\n\r\n<p>Je vous remercie à nouveau de votre intérêt pour le don du corps et vous prie d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de ma parfaite considération.</p>\r\n\r\n<p>{domain.supplemental_address_3}<br />\r\nCentre d'accueil des corps de {domain.city}</p>",
          'is_active' => TRUE,
          'workflow_id' => NULL,
          'workflow_name' => NULL,
          'is_default' => TRUE,
          'is_reserved' => FALSE,
          'is_sms' => FALSE,
          'pdf_format_id' => 0,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'MessageTemplate',
        'values' => [
          'msg_title' => '010 - Inscription : Mail confirmation inscription',
          'msg_subject' => "Confirmation inscription Centre d'accueil des corps",
          'msg_text' => NULL,
          'msg_html' => "<p>{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</p>\r\n\r\n<p>Nous vous remercions de votre demande d'inscription à notre centre d'accueil des corps, dont nous accusons réception.<br />\r\nVotre numéro d'inscription est le {Tokens_for_contact_Champs_de_fu.Promesse_de_don.N_de_don}.<br />\r\nVotre carte définitive et les documents relatifs à votre inscription vous parviendront par courrier dans les semaines qui viennent.<br />\r\nNous vous prions de bien vouloir excuser ce délai, dû à un grand nombre de demandes.</p>\r\n\r\n<p><strong>Votre inscription est néanmoins effective dès aujourd'hui et nous vous engageons à conserver une copie de ce message avec vos papiers d'identité</strong>.</p>\r\n\r\n<p>Nous restons à votre disposition pour tout renseignement complémentaire et vous prions d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de notre parfaite considération.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>{domain.supplemental_address_3}<br />\r\nCentre d'accueil des corps de {domain.city}</p>",
          'is_active' => TRUE,
          'workflow_id' => NULL,
          'workflow_name' => NULL,
          'is_default' => TRUE,
          'is_reserved' => FALSE,
          'is_sms' => FALSE,
          'pdf_format_id' => 0,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'MessageTemplate',
        'values' => [
          'msg_title' => '300 - Cérémonie invitation (email)',
          'msg_subject' => "Cérémonie d'hommage {event.start_date} à {event.start_date|crmDate:\"Time\"}",
          'msg_text' => NULL,
          'msg_html' => "<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous avons l'honneur de vous inviter à la cérémonie organisée par le Centre d'accueil des corps de {domain.city} en l'honneur des donneurs et de leurs proches.<br />\r\nElle aura lieu le<strong> {event.start_date} à {event.start_date|crmDate:\"Time\"} au&nbsp;</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><strong>{event.location}</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Cette manifestation, d'une durée d'environ deux heures, sera l'occasion de nous recueillir en mémoire des personnes qui, comme votre proche, ont donné récemment leur corps à des fins d'enseignement médical et de recherche.<br />\r\nMerci de nous indiquer par retour de mail si vous souhaitez y participer et de nous communiquer le nombre de personnes présentes.</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous restons à votre disposition pour tout renseignement complémentaire et vous prions d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de notre parfaite considération.</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"2\">{domain.supplemental_address_3}<br />\r\n{domain.supplemental_address_2}<br />\r\ndu centre d'accueil des corps de {domain.city}</font></p>",
          'is_active' => TRUE,
          'workflow_id' => NULL,
          'workflow_name' => NULL,
          'is_default' => TRUE,
          'is_reserved' => FALSE,
          'is_sms' => FALSE,
          'pdf_format_id' => 0,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'MessageTemplate',
        'values' => [
          'msg_title' => '310 - Cérémonie confirmation  (email)',
          'msg_subject' => "Cérémonie d'hommage {event.start_date} à {event.start_date|crmDate:\"Time\"}",
          'msg_text' => NULL,
          'msg_html' => "<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous vous confirmons votre inscription à la cérémonie d'hommage aux donneurs et à leurs proches qui débutera le<strong> {event.start_date} à {event.start_date|crmDate:\"Time\"} au&nbsp;</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><strong>{event.location}</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Merci de vous présenter au funérarium 15 minutes avant le début de la cérémonie et de respecter le nombre de personnes que vous nous avez communiqué.</font></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous restons à votre disposition pour tout renseignement complémentaire et vous prions d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de notre parfaite considération.</font></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"2\">{domain.supplemental_address_3}<br />\r\n{domain.supplemental_address_2}<br />\r\ndu Centre de Don du Corps de {domain.city}</font></font></p>",
          'is_active' => TRUE,
          'workflow_id' => NULL,
          'workflow_name' => NULL,
          'is_default' => TRUE,
          'is_reserved' => FALSE,
          'is_sms' => FALSE,
          'pdf_format_id' => 0,
        ],
      ];
      create_entity($to_create);

      $to_create =  [       
        'entity' => 'MessageTemplate',
        'values' => [
          'msg_title' => '320 - Cérémonie non inscription  (email)',
          'msg_subject' => "Cérémonie d'hommage {event.start_date} à {event.start_date|crmDate:\"Time\"}",
          'msg_text' => NULL,
          'msg_html' => "<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous avons bien noté que vous ne participerez pas à la cérémonie d'hommage aux donneurs et à leurs proches du<strong> {event.start_date}.</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous vous prions d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de notre parfaite considération.</font></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"2\">{domain.supplemental_address_3}<br />\r\n{domain.supplemental_address_2}<br />\r\ndu Centre de Don du Corps de {domain.city}</font></font></p>",
          'is_active' => TRUE,
          'workflow_id' => NULL,
          'workflow_name' => NULL,
          'is_default' => TRUE,
          'is_reserved' => FALSE,
          'is_sms' => FALSE,
          'pdf_format_id' => 0,
        ],
      ];
      create_entity($to_create);



      /// Fin de Création des messages templates


    $msg="  - Création des Templates de cérémonie".PHP_EOL; //les statuts et les roles des participants doivent etre crées en amont
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);   
      
      $to_create =  [        
          'entity' => 'Event',
          'values' => 
          [ 'title' => 'Cérémonie test',
              'summary' => "Cérémonie d'hommage aux donneurs et à leurs familles",
              'description' => NULL,
              'participant_listing_id' => NULL,
              'is_public' => FALSE,
              'start_date' => NULL,
              'end_date' => NULL,
              'is_online_registration' => FALSE,
              'registration_link_text' => NULL,
              'registration_start_date' => NULL,
              'registration_end_date' => NULL,
              'max_participants' => NULL,
              'event_full_text' => 'Cet événement est actuellement complet.',
              'is_monetary' => FALSE,
              'financial_type_id' => NULL,
              'payment_processor' => NULL,
              'is_map' => FALSE,
              'is_active' => TRUE,
              'fee_label' => NULL,
              'is_show_location' => FALSE,
              'loc_block_id' => '',
              'intro_text' => NULL,
              'footer_text' => NULL,
              'confirm_title' => NULL,
              'confirm_text' => NULL,
              'confirm_footer_text' => NULL,
              'is_email_confirm' => FALSE,
              'confirm_email_text' => NULL,
              'confirm_from_name' => NULL,
              'confirm_from_email' => NULL,
              'cc_confirm' => NULL,
              'bcc_confirm' => NULL,
              'default_fee_id' => NULL,
              'default_discount_fee_id' => NULL,
              'thankyou_title' => NULL,
              'thankyou_text' => NULL,
              'thankyou_footer_text' => NULL,
              'is_pay_later' => FALSE,
              'pay_later_text' => NULL,
              'pay_later_receipt' => NULL,
              'is_partial_payment' => FALSE,
              'initial_amount_label' => NULL,
              'initial_amount_help_text' => NULL,
              'min_initial_amount' => NULL,
              'is_multiple_registrations' => FALSE,
              'max_additional_participants' => 0,
              'allow_same_participant_emails' => FALSE,
              'has_waitlist' => FALSE,
              'requires_approval' => FALSE,
              'expiration_time' => NULL,
              'allow_selfcancelxfer' => FALSE,
              'selfcancelxfer_time' => 0,
              'waitlist_text' => NULL,
              'approval_req_text' => NULL,
              'is_template' => TRUE,
              'template_title' => 'Modèle de cérémonie test',
              'created_id' => NULL,
              'currency' => NULL,
              'is_share' => FALSE,
              'is_confirm_enabled' => TRUE,
              'parent_event_id' => NULL,
              'slot_label_id' => NULL,
              'dedupe_rule_group_id' => NULL,
              'is_billing_required' => FALSE,
              'is_show_calendar_links' => TRUE,
              'event_type_id:name' => 'Cérémonie Hommage',
              'default_role_id:name' => 'Attendee',
          ],
      ];
      $event_id=create_entity($to_create);

  



    $msg="  - Modification des regles de message liés aux cérémonies".PHP_EOL; // si non supprimé un message est envoyé à l'inscription prevenant d'une liste d attente
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;

      $msg="      -> Inactivation du message template de confirmation d'inscription".PHP_EOL; // si non supprimé un message est envoyé à l'inscription prevenant d'une liste d attente
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }
        fclose($fp);

        $to_create =  [       
            'entity' => 'MessageTemplate',
            'values' => [
              'msg_title' => "Événements - Confirmation d'inscription et reçu (hors ligne)",
              'is_active' => FALSE,
              'msg_subject' => '',
              'msg_text' => '',
              'msg_html' => '',
            ],
          ];
          create_entity($to_create);

        $msg="      -> Modification des règles de message".PHP_EOL;
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }

      $msg="         Message : 300 Cérémonie invitation (email) pour statut On waitlist (Invité)".PHP_EOL;
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }

        $messageTemplates = civicrm_api4('MessageTemplate', 'get', [                /// récupère l'id du MessageTemplate
          'select' => [
            'id',
          ],
          'where' => [
            ['msg_title', '=', '300 - Cérémonie invitation (email)'],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

        $participantStatusTypes = civicrm_api4('ParticipantStatusType', 'get', [    /// récupère l'id de statut pour 'On waitlist' (invité)
          'where' => [
            ['is_active', '=', TRUE],
            ['name', '=', 'On waitlist'],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

        if(isset($messageTemplates[0]) && isset($participantStatusTypes[0]) &&isset($event_id)){

          $to_create =  [        //Corriger_civililite : Déclaration de l'Action
            'entity' => 'EventMessageRule',
            'values' => [
                'event_id' => $event_id,
                'is_active' => 1,
                'template_id' => $messageTemplates[0]['id'],
                'from_status' => [],
                'to_status' => [
                  $participantStatusTypes[0]['id'],
                ],
                'languages' => [],
                'roles' => [],
                'attachments' => NULL,
            ],
          ];

          //print_r($to_create);

        } else {
          echo "         Message template, statut, ou évenement manquent".PHP_EOL;
          fwrite($fp, $msg);
          echo $msg;
        }
        fclose($fp);
        create_entity($to_create);


      $msg="         Message : 310 Cérémonie confirmation  (email) pour statut Registered (Confirmé)".PHP_EOL;
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }
        $messageTemplates = civicrm_api4('MessageTemplate', 'get', [                /// récupère l'id du MessageTemplate
          'select' => [
            'id',
          ],
          'where' => [
            ['msg_title', '=', '310 - Cérémonie confirmation  (email)'],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

        $participantStatusTypes = civicrm_api4('ParticipantStatusType', 'get', [    /// récupère l'id de statut pour 'Registered' (Confirmé)
          'where' => [
            ['is_active', '=', TRUE],
            ['name', '=', 'Registered'],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

        if(isset($messageTemplates[0]) && isset($participantStatusTypes[0]) &&isset($event_id)){

          $to_create =  [        //Corriger_civililite : Déclaration de l'Action
            'entity' => 'EventMessageRule',
            'values' => [
                'event_id' => $event_id,
                'is_active' => 1,
                'template_id' => $messageTemplates[0]['id'],
                'from_status' => [],
                'to_status' => [
                  $participantStatusTypes[0]['id'],
                ],
                'languages' => [],
                'roles' => [],
                'attachments' => NULL,
            ],
          ];

          //print_r($to_create);

        } else {
          $msg="         Message template, statut, ou évenement manquent".PHP_EOL;
          fwrite($fp, $msg);
          echo $msg;
        }
        fclose($fp);
        create_entity($to_create);


      $msg="         Message : 320 Cérémonie non inscription  (email) pour statut Cancelled (Annulé)".PHP_EOL;
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        echo $msg;

        $messageTemplates = civicrm_api4('MessageTemplate', 'get', [                /// récupère l'id du MessageTemplate
          'select' => [
            'id',
          ],
          'where' => [
            ['msg_title', '=', '320 - Cérémonie non inscription  (email)'],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

        $participantStatusTypes = civicrm_api4('ParticipantStatusType', 'get', [    /// récupère l'id de statut pour 'Registered' (Confirmé)
          'where' => [
            ['is_active', '=', TRUE],
            ['name', '=', 'Cancelled'],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

        if(isset($messageTemplates[0]) && isset($participantStatusTypes[0]) &&isset($event_id)){

          $to_create =  [        //Corriger_civililite : Déclaration de l'Action
            'entity' => 'EventMessageRule',
            'values' => [
                'event_id' => $event_id,
                'is_active' => 1,
                'template_id' => $messageTemplates[0]['id'],
                'from_status' => [],
                'to_status' => [
                  $participantStatusTypes[0]['id'],
                ],
                'languages' => [],
                'roles' => [],
                'attachments' => NULL,
            ],
          ];

          //print_r($to_create);

        } else {
          $msg="         Message template, statut, ou évenement manquent".PHP_EOL;
          fwrite($fp, $msg);
          echo $msg;
        }
        fclose($fp);
        create_entity($to_create);
     // Fin modification message liés aux ceremonies

  }   // fin Implements hook_civicrm_postInstall().

#IMPLEMENTS hook_civicrm_container().
/* function don_corps_civicrm_container(ContainerBuilder $container): void {
  ## Ajoute l'action imprimer document civioffice dans les utilisations du corps
  $container->autowire(\Civi\EventSubscriber\CiviOfficeSearchKitTaskSubscriber::class)->addTag('kernel.event_subscriber');

  ## Remonte tous les tokens d'utilisation du corps pour les rendre disponibles
  $container->autowire(\Civi\EventSubscriber\CiviOfficeTokenSubscriber::class)->addTag('kernel.event_subscriber');

} */






  # IMPLEMENTS hook_civicrm_uninstall().
 
  function don_corps_civicrm_uninstall() {
    $msg="  - Activation des menus par défaut".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;
      fclose($fp);

      activate_menu('Contacts');
      activate_menu('Search');
      activate_menu('Contributions');
      activate_menu('Events');
      activate_menu('Mailings');
      activate_menu('Reports');
      activate_menu('Support');

  
  }
