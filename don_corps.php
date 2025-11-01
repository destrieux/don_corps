<?php

// verifier que la purge vire les contributions
// vérifier requetes ; ex tous donneurs
//mysql://root:root@localhost:8888/civicrm37test//

/*VERIFICATION
- Contacts Layouts
- Création des contacts en utilisanst Profils 
- Menus navigation
- Tableau de bord corps

SEARCHES
search contacts ano ville (suppression code correction requete) OK
search contact annulés (suppression code correction requete) OK

RULES :
- copie code barre vers piece ppale si modification 


A VERIFIER
affichage groupes dynamiques dans CONTACT LAYOUT DB error

*/

require_once 'don_corps.civix.php';

use CRM_DonCorps_ExtensionUtil as E;

function deactivate_menu(){
  //////////// function deactivate_menu /////////
  // Cette fonction est invoquée à l'installation pour desactiver un menu original
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

    echo "Menu ".$menu." désactivé"."\n";
    CRM_Core_Session::setStatus('Menu '.$menu.' désactivé', 'Succès', 'success');

  } catch (API_Exception $e) {                  // si l'update échoue
     echo "Pas de Menu ".$menu."\n";
     CRM_Core_Session::setStatus('Pas de Menu '.$menu, 'Info', 'info');
  }

}// Fin de définition de la fonction : deactivate_menu()


function activate_menu(){
  //////////// function activate_menu /////////
  // Cette fonction est invoquée à la désinstallation pour activer les menus originaux les sous rubriques inutiles des menus qui sont definis par un fichier mgd
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

    echo "Menu ".$menu." activé"."\n";
    CRM_Core_Session::setStatus('Menu '.$menu.' activé', 'Succès', 'success');

  } catch (API_Exception $e) {    // si l'update échoue
     echo "Pas de Menu ".$menu."\n";
     CRM_Core_Session::setStatus('Pas de Menu '.$menu, 'Info', 'info');
  }

}// Fin de définition de la fonction : activate_menu()


function change_icon (){
  //////////// function change_icon /////////
  // Cette fonction est invoquée en post installation installation pour remplacer aussi l'icone du menu
  //
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
      echo "icone du menu ".$menu." changée pour : ".$icon."\n";
      CRM_Core_Session::setStatus('Icone de la rubrique de menu '.$menu.' changée', 'Succès', 'success');
  } else {
     echo "Pas de sous rubrique pour ".$menu."\n";
     CRM_Core_Session::setStatus('Pas de sous rubrique pour '.$menu, 'Info', 'info');
  }
}// Fin de définition de la fonction : change_icon()

function install_layouts () {
  /// LLa variable $layout QUI COMPREND LES PARAMETRES DE TOUS LES LAYOUTS doit etre définie
  /// rechercher "DEFINITION DE LA VARIABLE" dans ce script et suivez les instructions
  // Définition de function install_layouts ()
  //
  // Cette fonction installe le ContactsLaoyouts depuis un site maitre vers un nouveau site
  //
  // Elle est lancée en post installation, une fois que toutes les entités sont installées
  //
  // 1- définir la variable  $layout depuis l'api du site maitre  ; comprend les parametres de tous les layouts
  // 2- Les customgroups/fields ont normalement été instalés lors des étables porrécédentes d'installation de l'appli
  // 3- Depuis le site maitre, il faut générer des fichiers mgd pour chaque profil utilisé :
  //    Ces profils sont utilisés pour l'affichage des blocs par ContactLayout
  //          récuperer l'id des profils suivants
  //            CESP_29
  //            Dates_naissance_et_d_c_s
  //            Fonction
  //            Op_rations_fun_raires_r_alis_es
  //            Profil_sans_nom
  //            Restitution
  //            Type_de_contact
  //            name_and_address (creation de donneur)
  //
  //          le chiffre correspond à l'id mais peut varier selon le site maitre
  //          il faut récupérer l'id sur le site maitre avec l'api
  //              entité : UFGroup
  //              select : id et name
  //              where names contains : CESP par exemple
  //          l'id est retournée
  //
  //          cd /Applications/MAMP/htdocs/wordpress/wp-content/plugins/civicrm/civicrm/ext/don_corps (extension don du corps du site maitre)
  //          med-2019005062:destri_c[27] civix export UFGroup 29
  //            Enable mixin mgd-php@1.0
  //            Write info.xml
  //            Write managed/UFGroup_CESP_29.mgd.php
  //
  //          vous devez avoir à la fin les fichiers suivants dans le repertoire ext/don_corps/managed
  //              UFGroup_CESP_29.mgd.php
  //              UFGroup_Dates_naissance_et_d_c_s_17.mgd.php
  //              UFGroup_Fonction_18.mgd.php
  //              UFGroup_Op_rations_fun_raires_r_alis_es_30.mgd.php
  //              UFGroup_Profil_sans_nom_20.mgd.php
  //              UFGroup_Restitution_28.mgd.php
  //              UFGroup_Type_de_contact_23.mgd.php
  //              UFGroup_name_and_address.mgd.php
  //
  //              Les copier dans le repertoire ext/don_corps/managed du site cible


  $layouts = func_get_arg(0);

  foreach ($layouts as $params) {           // pour chacun des Layouts

    // Vérification que tous les profils ont bien été installés

    unset($params['id']);                   // supprime la clé id qui est générée par l'API
    $profs=$params['blocks'][0];            // array qui definit le blc de profil

    foreach ($profs as $prof) {             // Pour chacun des blocs de profils
    $blks=$prof;
      foreach ($blks as $blk) {
        $name = $blk['name'];                        // nom des profils utilisés qui contient un préfixe custom. (pour les groupes de champs perso), core. ou profile.

        $position = strpos($name, '.');             // retrouve la position du point dans le nom
        if ($position !== false) {
          $prefix = substr($name, 0, $position);    // ne garde que ce qui est à gauche du point, donc le prefixe
          $short_name = substr($name, $position + 1);// ne garde que ce qui est à droite du point, donc le nom du custom group ou du profile
    //      echo $name."  ".$prefix."\n";

    $error=0; // flag erreur - le layout ne sera créé que si pas d'erreur


          switch ($prefix){       // traite chacun des blocs de profils
            case 'custom':        // cas d'un bloc constitué d'un groupe de chmaps personalisés
              $customGroups = civicrm_api4('CustomGroup', 'get', [   // on vérifie si le custom group $short_name existe bien
                'where' => [
                  ['name', '=', $short_name],
                ],
                'checkPermissions' => FALSE,
                ]);

              if ($customGroups[0]['id']!=0){
  //              echo $name."  le CustomGroupe ".$short_name." existe : OK !"."\n";
              } else {
  //              echo "\n". "#####################".$name."  le CustomGroupe ".$short_name." n existe pas : Importer fichier mgd en premier #####################"."\n";
                CRM_Core_Session::setStatus('CustomGroup '.$short_name.' manque - importer fichier mgd', 'Erreur', 'error');

                $error=1;           // le layout ne sera pas créé
              }
            break;

            case 'profile':        // cas d'un bloc constitué par l'utilisatues de champs non retroupés dans un groupe de champs personalisés
    //          echo "\n".$name."  ".$prefix."  ".$short_name."\n";;
    //          echo "Il faut vérifier si le profile (UFgroup) existe bien et l'importer"."\n";
              $profiles = civicrm_api4('UFGroup', 'get', [   // on vérifie si le custom group $short_name existe bien
                'where' => [
                  ['name', '=', $short_name],
                ],
                'checkPermissions' => FALSE,
                ]);
                if (isset($profiles[0]['id'])){
  //               echo $name."  le Profil ".$short_name." existe : OK !"."\n";

                    // il faut le relier à l'extension CustomSummary sinon les champs ne s'affichent pas
                    // la correspondance entre profils et extension se fait dans UFJoin

                  $uFJoins = civicrm_api4('UFJoin', 'get', [
                    'where' => [
                      ['module', '=', 'Contact Summary'],
                      ['uf_group_id:name', '=', $short_name],
                    ],
                    'checkPermissions' => FALSE,
                    ]);


      //            echo $uFJoins[0]['id'];

                  if (isset($uFJoins[0]['id'])){  // UFJoin existe : on l'update
                  $results = civicrm_api4('UFJoin', 'update', [
                    'values' => [
                      'is_active' => TRUE,
                      'module' => 'Contact Summary',
                      'weight' => 1,
                      'uf_group_id.name' => $short_name,
                    ],
                    'where' => [
                      ['id', '=', $uFJoins[0]['id']],
                    ],
                    'checkPermissions' => FALSE,
                  ]);
  //               echo "update UF ".$short_name." / Contact Summary"."\n";

                  } else {                    // UFJoin n'existe pas : on la crée
                    $results = civicrm_api4('UFJoin', 'create', [
                      'values' => [
                        'is_active' => TRUE,
                        'module' => 'Contact Summary',
                        'weight' => 1,
                        'uf_group_id.name' => $short_name,
                      ],
                      'checkPermissions' => FALSE,
                    ]);
  //                echo "create UF ".$short_name." / Contact Summary"."\n";

                  }

                  ////////
                                $uFJoins = civicrm_api4('UFJoin', 'get', [
                    'where' => [
                      ['module', '=', 'Profile'],
                      ['uf_group_id:name', '=', $short_name],
                    ],
                    'checkPermissions' => FALSE,
                    ]);


      //            echo $uFJoins[0]['id'];

                  if (isset($uFJoins[0]['id'])){  // UFJoin existe : on l'update
                  $results = civicrm_api4('UFJoin', 'update', [
                    'values' => [
                      'is_active' => TRUE,
                      'module' => 'Profile',
                      'weight' => 1,
                      'uf_group_id.name' => $short_name,
                    ],
                    'where' => [
                      ['id', '=', $uFJoins[0]['id']],
                    ],
                    'checkPermissions' => FALSE,
                  ]);
  //                echo "update UF ".$short_name." / Profile"."\n";

                  } else {                    // UFJoin n'existe pas : on la crée
                    $results = civicrm_api4('UFJoin', 'create', [
                      'values' => [
                        'is_active' => TRUE,
                        'module' => 'Profile',
                        'weight' => 1,
                        'uf_group_id.name' => $short_name,
                      ],
                      'checkPermissions' => FALSE,
                    ]);
  //                echo "create UF ".$short_name." / Profile"."\n";

                  }
                  /////////

                } else {
  //               echo "\n". "#####################".$name."  le Profil ".$short_name." n existe pas : Importer fichier mgd en premier #####################"."\n";
                  CRM_Core_Session::setStatus('Profil '.$short_name.' manque - importer fichier mgd', 'Erreur', 'error');

                  $error=1;
                }
            break;

            case 'core':
  //           echo "\n".$name." rien à faire"."\n";
            break;

          }

        } else {
          echo "Caractère non trouvé!";
        }


      }

    }              // Fin de la boucle Pour chacun des blocs de profils

    if ($error==0){           // tous les customGroups et Profiles sont présents
  //    echo "\n"."Les Profils et CustomGroups necessaires sont présents"."\n";


    $contactLayouts = civicrm_api4('ContactLayout', 'get', [    // on vérifie si un layout ayant un label identique à celui à créer existe
      'where' => [
        ['label', '=', $params['label']],
      ],
      'checkPermissions' => FALSE,
    ]);


    //print_r($contactLayouts);

    //echo "toto".$contactLayouts[0]['id'];



    if (!isset($contactLayouts[0]['id'])){         // si le layout n'existe pas on le crée
      $results = civicrm_api4('ContactLayout', 'create', [
      'values' => $params,
      'checkPermissions' => FALSE,
      ]);

  //    echo "\n"."############# Creation du Layout ".$contactLayouts[0]['label']."\n";
      CRM_Core_Session::setStatus('Creation du Layout '.$results[0]['label'], 'Succès', 'success');

    } else {                                 // si le layout existe on l'update (le premier trouvé avec ce label)
      $results = civicrm_api4('ContactLayout', 'update', [
      'values' => $params,
      'where' => [
        ['id', '=', $contactLayouts[0]['id']],
      ],
      'checkPermissions' => FALSE,
    ]);

  //    echo "\n"."############# Update du Layout : ".$contactLayouts[0]['label']."\n";
      CRM_Core_Session::setStatus('MAJ du Layout '.$results[0]['label'], 'Succès', 'success');

    }

    }else{
  //    echo "\n"."Installez les composants qui manquent puis relancez la commande"."\n";
      CRM_Core_Session::setStatus('Installez les composants qui manquent puis relancez la commande : cv -vvv ext:enable don_corps', 'Erreur', 'error');

      exit;
    }
  }     // fin de la boucle pour chacun des Layouts

}// Fin de la définition de la fonction : install_layouts()

function change_tabs(){
  ##### Recuperation de la liste actuelle des tabs pour ce layout
    $icons_default= func_get_arg(0);         // array contenant les icones par defaut
    $inactive_tabs= func_get_arg(1);         // array contenant les tabs à inactiver par profil
  
    $i=0;

  /*     $icons_default = [                      /// modifier ici les icones à afficher par tab
      ["name" => "Arriv_e_du_corps_new",
      "id" => "",
      "icon" => "crm-i fa-ambulance"],

      ["name" => "Utilisation_du_corps",
      "id" => "",
      "icon" => "crm-i fa-sign-language"],

      ["name" => "Protocoles_in_vivo",
      "id" => "",
      "icon" => "crm-i fa-flask"],

      ['id' => 'contribute',
      "icon" => "crm-i fa-money"],

      ['id' => 'participant',
      "icon" => "crm-i fa-users"],
    ]; */



    foreach($icons_default as $icon_default){
        ##### recupération des codes correspondant aux group/tabs
        #print_r($icon_default);
        $customGroups = civicrm_api4('CustomGroup', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['name', '=', $icon_default['name']],
          ],
          'checkPermissions' => FALSE,
          ]);
                 
          if (isset($customGroups[0]['id'])){      // si un custom group existe avec ce nom
            $group_id=$customGroups[0]['id'];  // $icons_default contient les tabs liés à des groupes de champs customisés et les tabs par defaut dont l'icone doit être changée
            $icons_default[$i]['id']="custom_".$group_id; // assigne le nom du tab codé avec le num de groupe
            $icons_default[$i]['is_active']=1;
            unset(($icons_default)[$i]['name']);
          }else{
            echo "Le groupe de custom options ".$icon_default['name']."n'existe pas".PHP_EOL;
            unset(($icons_default)[$i]['name']);
          }
    $i++;
     }
   
     $icons_new = $icons_default; // on ne garde dans $icons_new que les tabs correspondant à des groupes de champs customisés
     $i=0;
     foreach ($icons_new as $icon_new){
  
      if(str_contains($icon_new['id'], 'custom_')){
        
      }else{
        unset ($icons_new[$i]);
      }
      $i++;
     }
  
    $icon_id= array_column($icons_default, 'id');   // ne garde que la colonne des id
  
    $layouts = civicrm_api4('ContactLayout', 'get', [  // recupere la liste des layouts
      'checkPermissions' => FALSE,
      ]);
  
    /// on commence par supprimer tous les tabs dont le nom commence par custom ///
    $l=0;
    foreach($layouts as $layout){
    
      $t=0;
      foreach($layout['tabs'] as $tab){
    
      
    
        // var_dump($tab['id']);
      if(str_contains($tab['id'], 'custom_')){
        //echo $l."  ".$t."  ".$tab['id']."  ".$tab['is_active']."  ".$tab['icon'].PHP_EOL;
        // print_r($layouts[$l]['tabs'][$t]);
        unset($layouts[$l]['tabs'][$t]);
    
        }else {
          //echo 'id : '.$layouts[$l]['tabs'][$t]['id'].PHP_EOL;
        $layouts[$l]['tabs'][$t]['is_active']=1;
        foreach ($icons_default as $icon_default){
          
          if ($tab['id']==$icon_default['id']){
            //var_dump($icon_default['id']);
            $layouts[$l]['tabs'][$t]['icon']=$icon_default['icon'];
            }
        }
      
        }
      $t++;
      }
      $l++;
    }
  
    /// on ajoute les tabs correspondant aux groupes de champs custom
    $l=0;
    foreach($layouts as $layout){
        $layouts[$l]['tabs'] = array_merge($layout['tabs'], $icons_new);
        $l++;
    }
  
    /// il faut desactiver les champs inutiles
  
  
    $t=0;     /// on traduit le nom des tabs de champs personnalisés en custom_XXX
  foreach($inactive_tabs as $inactive_tab){
  
    $label= key($inactive_tab);
    //echo $label.PHP_EOL;
  
    $tabs_to_inactivate = $inactive_tab[$label];
    $t2=0;

    foreach ($tabs_to_inactivate as $tab_to_inactivate){
      //echo "   - traitement du tab : ".$t." ".$tab_to_inactivate.PHP_EOL;
     
      $customGroups = civicrm_api4('CustomGroup', 'get', [
        'select' => [
          'id',
        ],
        'where' => [
          ['name', '=', $tab_to_inactivate
        ],
        ],
        'checkPermissions' => FALSE,
      ]);
  
      //echo "old :".$inactive_tabs[$t][$label][$t2].PHP_EOL;
  
      if (isset($customGroups[0]['id'])){
        $new_name="custom_".$customGroups[0]['id'];
        $inactive_tabs[$t][$label][$t2]=$new_name;
      }else {
        $new_name="unchanged";
  
      }
      //echo "new : ".$new_name.PHP_EOL;
      $t2++;
    }
  $t++;
  }
  
  
  
  foreach($inactive_tabs as $inactive_tab){
  
    $label= key($inactive_tab);
  
    
      $l=0;
      foreach ($layouts as $layout){
        if ($layout['label']==$label){
          $orig_tabs = $layout['tabs'];
          //print_r($orig_tabs);
          //echo PHP_EOL."Traitement du layout : ".$l." ".$layout['label'].PHP_EOL;
            $tabs_to_inactivate = $inactive_tab[$label];
            //print_r($tabs_to_inactivate);
            foreach ($tabs_to_inactivate as $tab_to_inactivate){
              //echo "   - traitement du tab : ".$tab_to_inactivate.PHP_EOL;
              
              
              $tab_id=array_column($layout['tabs'],'id');
              //echo "      -searching for : ".$tab_to_inactivate.PHP_EOL;
              //print_r($tab_id);
              //print_r($layout['tabs']);
              $keytab=array_search($tab_to_inactivate,$tab_id);
             // echo "Key : ".$keytab.PHP_EOL;
  
             // echo $layouts[$l]['tabs'][$keytab]['id'].PHP_EOL;
             // echo $layouts[$l]['tabs'][$keytab]['is_active'].PHP_EOL;
             // echo $layouts[$l]['tabs'][$keytab]['icon'].PHP_EOL;
             $layouts[$l]['tabs'][$keytab]['is_active']=0;
              
            }
        }
        $l++;
      }
  }
  
  foreach ($layouts as $layout){
    $layout_to_update=$layout['label'];
    $tabs_to_update=$layout['tabs'];
  
    try {
    $results = civicrm_api4('ContactLayout', 'update', [
      'values' => [
        'tabs' => $tabs_to_update,
      ],
      'where' => [
        ['label', '=', $layout_to_update],
      ],
      'checkPermissions' => FALSE,
    ]);

    echo "Tabs inutiles désactivés pour le  layout : ".$layout_to_update."\n";
    CRM_Core_Session::setStatus('Tabs inutiles désactivés pour le  layout '.$layout_to_update, 'Succès', 'success');

    } catch (API_Exception $e) {
      echo "Erreur lors de l'inactivation des tabs inutilisés pour le layout : ".$layout_to_update."\n";
      CRM_Core_Session::setStatus('Erreur lors de inactivation des tabs inutilisés pour le layout : '.$layout_to_update, 'Erreur', 'error');
    }
  
  }
  
}// Fin de la définition de la fonction : change_tabs() 
  
function create_entity(){
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
          echo "entité ".$entity." ".$descr." existe - update ".PHP_EOL;
          
          $results = civicrm_api4($entity, 'update', [
            'values' => $values,
            'where' => [
              ['name', '=', $check_entity[0]['name']],
            ],
            'checkPermissions' => FALSE,
          ]);
      
        }else{                                  // si l'entité n'existe pas, on la crée
          echo "entité ".$entity." ".$descr." n'existe pas - creation".PHP_EOL;
          $results = civicrm_api4($entity, 'create', [
            'values' => $values,
            'checkPermissions' => FALSE,
          ]);
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

  if(isset($check_entity[0])){            // si l'entité existe on l'update
    echo "entité ".$entity." ".$descr." existe - update (".$check_entity[0]['id'].")".PHP_EOL;
    #print_r($check_entity[0]);
    
    $results = civicrm_api4($entity, 'update', [
      'values' => $values,
      'where' => [
        ['id', '=', $check_entity[0]['id']],
      ],
      'checkPermissions' => FALSE,
    ]);

  }else{                                  // si l'entité n'existe pas, on la crée
    if($values['is_active']==TRUE){       // on verifie qu'elle est bien active sinon pas de création
      echo "entité ".$entity." ".$descr." n'existe pas - creation".PHP_EOL;
      $results = civicrm_api4($entity, 'create', [
        'values' => $values,
        'checkPermissions' => FALSE,
      ]);
    } else {
      echo "entité ".$entity." ".$descr." n'existe pas mais inactive - ignorée".PHP_EOL;
      return;
    }
  }
 return $results[0]['id']; // retourne l'id de l'entité créée
}// Fin de la définition de la fonction : create entity()

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
  
  // Si des custom filds ne sont pas passés à la fonction : return
  if (count($args)==0){
    echo "Pas de custom field passé à la fonction ".PHP_EOL;
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

}// Fin de la définition de la fonction : serialize_custom_fields()

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
          CRM_Core_Session::setStatus('Le type de contact '.$name.' a été supprimé.', 'Succès', 'success');
          //echo 'Le type de contact '.$name.' a été supprimé.'."\n";

          } catch (API_Exception $e) {
            CRM_Core_Session::setStatus('Erreur lors de la suppression du type de contact '.$name, 'Erreur', 'error');
            //echo 'Erreur lors de la suppression du type de contact '.$name."\n";



          }

      } else {                      // le sous type de contact est utilisé par au moins un contact, alors on le conserve
        CRM_Core_Session::setStatus('Le type de contact '.$name.' est utilisé, on le conserve.', 'Info', 'info');
        //echo 'Le type de contact '.$name.' est utilisé, on le conserve.'."\n";
      }

    }


}// Fin de la définition de la fonction : delete_contact_type()

function modif_filtre(){
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
        
        echo "Le filtre de groupe ".$Group_name." a été modifié dans le champ ".$Custom_name."\n";
        CRM_Core_Session::setStatus('Le filtre de groupe '.$Group_name.' a été modifié dans le champ '.$Custom_name, 'Succès', 'success');

      } catch  (API_Exception $e) {
        echo "Erreur lors de l'injection du filtre de groupe ".$Group_name." dans le champ ".$Custom_name."\n";
        CRM_Core_Session::setStatus('Erreur lors de l injection du filtre de groupe '.$Group_name.' dans le champ '.$Custom_name, 'Erreur', 'error');
      }


      }else{
  //      echo "Le groupe ".$Group_name." n'existe pas"."\n";
        CRM_Core_Session::setStatus('Le groupe '.$Group_name.' n existe pas', 'Erreur', 'error');
      }

    }else{
  //  echo "Le champ personalisé ".$Custom_name." n'existe pas"."\n";
    CRM_Core_Session::setStatus('Le champ personalisé '.$Custom_name.' n existe pas', 'Erreur', 'error');

    }
}// Fin de la définition de la fonction : modif_filtre()

function deactivate_relation_type(){
  //////////// function deactivate_relation_type /////////
  // Cette fonction est invoquée à l'activation pour désactiver les types de relation instalés par défauts et inutiles
  //
  // syntaxe : deactivate_relation_type ('name_a_b_de_la_relation_a_desactiver');
  //
  // elle est appelée par : function don_corps_civicrm_install()
  //////////////
  $relation_type = func_get_arg(0);

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

      echo "Type de relation ".$relation_type." désactivé"."\n";
      CRM_Core_Session::setStatus('Type de relation '.$relation_type.' désactivé', 'Succès', 'success');
    

  } else {
     echo "Type de relation ".$relation_type." inexistant"."\n";
     CRM_Core_Session::setStatus('Type de relation '.$relation_type.' inexistant', 'Info', 'info');
  }

}// Fin de la définition de la fonction : deactivate_relation_type()

function update_search(){
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

          echo "     - Requete ".$searchname." MAJ".PHP_EOL;
          CRM_Core_Session::setStatus('MAJ réussie de la requete '.$searchname, 'Succès', 'success');
          
          } catch (API_Exception $e) {
            echo "    Erreur lors de la MAJ de la requete ".$searchname.": ".$e->getMessage().PHP_EOL;
            CRM_Core_Session::setStatus('Erreur lors de la MAJ de la requete '.$searchname, 'Erreur', 'error');
          }
        }else{
          echo "     - Requete ".$searchname." MAJ du seul tag".PHP_EOL;
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
        echo "      & Tag ajouté".PHP_EOL;
        }          
  
      }
    } else {                                              // si la requete n'exste pas
      echo "La requete ".$searchname." n'existe pas".PHP_EOL;
    }
}// Fin de la définition de la fonction : update_search() 


//###########################################################
#
#   FIN DE LA DECLARATION DES FONCTIONS
#
//###########################################################


/*****************************************************************************
 * Implements hook_civicrm_managed().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 ******************************************************************************/

function don_corps_civicrm_managed(&$entities) {
  //_don_corps_civix_civicrm_managed($entities);

  // Load the triggers when civirules is installed.
  if (_don_corps_is_civirules_installed()) {
     CRM_Civirules_Utils_Upgrader::insertTriggersFromJson(E::path('civirules/triggers.json'));
  }
  
  
  
  
  
  // modification des parametres de localisation, date....
    // utilise apiv3 car non porté en V4 pour le moment
  echo PHP_EOL."@@@  hook_civicrm_managed ".PHP_EOL;

  echo "  -modification des parametres de localisation, date...".PHP_EOL;
    $result = civicrm_api3('Setting', 'create', [
      'dateformatDatetime'=> "%e %B %Y",
      // 'dateformatDatetime'=> "%e %B %Y %H:%M",
      'theme_backend'  => "greenwich",
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
  // fin de la modification des parametres de localisation, date....
  

  echo PHP_EOL."   - Tags".PHP_EOL;

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

    

}

/**
 * Function to check whether civirules is installed.
 *
 * @return bool
 */
function _don_corps_is_civirules_installed() {
  if (civicrm_api3('Extension', 'get', ['key' => 'civirules', 'status' => 'installed'])['count']) {
    return true;
  } elseif (civicrm_api3('Extension', 'get', ['key' => 'org.civicoop.civirules', 'status' => 'installed'])['count']) {
    return true;
  }
  return false;
}

/*****************************************************************************
 * Implements hook_civicrm_config()
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 ******************************************************************************/
function don_corps_civicrm_config(&$config): void {
  //function don_corps_civicrm_config(&$config) {
  // echo PHP_EOL."@@@  hook_civicrm_config ".PHP_EOL;
  _don_corps_civix_civicrm_config($config);
}

/*****************************************************************************
 * Implements hook_civicrm_install()
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install/
******************************************************************************/

function don_corps_civicrm_install(): void {
//function don_corps_civicrm_install() {
  echo PHP_EOL."@@@  hook_civicrm_install ".PHP_EOL;

  echo "  -Création des intervales de date personnalisés".PHP_EOL;
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
    echo "relative_date_filters : ";
    create_entity($to_create);
  // fin de la création des intervales de date personnaliés (dans l'interface : administrer > Param systeme > Liqstes de choix > Realtive Date Filters)

  echo "  -Création des prefixes de contact".PHP_EOL;
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
      echo $to_create['values']['label']." : ";
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
      echo $to_create['values']['label']." : ";
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
      echo $to_create['values']['label']." : ";
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
      echo $to_create['values']['label']." : ";
      create_entity($to_create);
  
  
  
  echo "  -Création des types de contact".PHP_EOL;
    
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
      echo $to_create['values']['name']." : ";
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
      echo $to_create['values']['name']." : ";
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
      echo $to_create['values']['name']." : ";
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
        echo $to_create['values']['name']." : ";
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
          echo $to_create['values']['name']." : ";
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
      echo $to_create['values']['name']." : ";
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
      echo $to_create['values']['name']." : ";
      create_entity($to_create);

      $to_create =  [       // Création des types de contact : 
        'entity' => 'ContactType',
        'values' => [
          'name' => 'Emprunteur',
          'label' => E::ts('Localisation pièces'),
          'description' => E::ts('Localisation des pièces dans le CDC ou à l extérieur'),
          'parent_id.name' => 'Organization',
          'is_active' => TRUE,
        ],
      ];
      echo $to_create['values']['name']." : ";
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
      echo $to_create['values']['name']." : ";
      create_entity($to_create);
    // fin de la création des types de contact

  echo "  -Création des types de contributions financieres".PHP_EOL;
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

  echo "  -Création des status de contribution ".PHP_EOL;
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

  echo "  -Création des modes de paiement".PHP_EOL;
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

  echo "  -Création des types d'evenements".PHP_EOL;
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

  echo "  -Création des types de participants".PHP_EOL;
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

  echo "  -Création des Statuts de participants".PHP_EOL;

    /* $to_create =  [   // On waitlist / invité
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
      create_entity($to_create);    */  
    
    $to_create =  [   // Transferred
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Transferred',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);    
    
    $to_create =  [   // Pending refund
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Pending refund',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);    
    
    $to_create =  [   // Pending in cart  
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Pending in cart',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);    
    
    $to_create =  [   // Partially paid'
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Partially paid',
        'is_active' => FALSE,
      ],
     ];
      create_entity($to_create);    
    
    $to_create =  [   // Expired 
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Expired',
        'is_counted' => FALSE,
       ],
      ];
      create_entity($to_create);    
    
    $to_create =  [   // Rejected 
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Rejected',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);    
    
    $to_create =  [   // Pending from approval
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Pending from approval',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);    

    $to_create =  [   // On waitlist
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'On waitlist',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);    
    
    $to_create =  [   // Pending from waitlist
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Pending from waitlist',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);    
    
    $to_create =  [   // Awaiting approval
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Awaiting approval',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);    
    
    $to_create =  [   // Pending from incomplete transaction
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Pending from incomplete transaction',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);    
    
    $to_create =  [   // Pending from pay later
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Pending from pay later',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);    

    $to_create =  [   // Attended  
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'Attended',
        'is_active' => FALSE,
      ],
      ];
     create_entity($to_create);    

    $to_create =  [   // No-show
      'entity' => 'ParticipantStatusType',
      'values' => [
        'name' => 'No-show',
        'is_active' => FALSE,
      ],
      ];
      create_entity($to_create);  
    
  

      // Fin de création des statuts de participants

  
  
  echo "  -Création des relations".PHP_EOL;
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
          'name_a_b' => 'en attente',
          'label_a_b' => 'en attente',
          'name_b_a' => 'en attente',
          'label_b_a' => 'en attente',
          'description' => 'en attente de creation',
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
  
  echo "  -Déactivation des contacts par défaut".PHP_EOL;
    deactivate_relation_type ('Spouse of');
    deactivate_relation_type ('Volunteer for');
    deactivate_relation_type ('Head of Household for');
    deactivate_relation_type ('Household Member of');
    deactivate_relation_type ('Case Coordinator is');
    deactivate_relation_type ('Supervised by');
    // Fin de la Déactivation des types de relation par défaut  

    
  echo "  -Déactivation des champs du profil par defaut ".PHP_EOL; // sans cela le profil contient de champs suppléntaiers
    $results = civicrm_api4('UFField', 'update', [
      'values' => [
        'is_active' => FALSE,
      ],
      'where' => [
        ['uf_group_id:name', '=', 'name_and_address'],
      ],
      'checkPermissions' => FALSE,
    ]);

   // Fin de Déactivation des champs du profil par defaut

  _don_corps_civix_civicrm_install();
}

/*****************************************************************************
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 ******************************************************************************/
function don_corps_civicrm_enable(): void {   // pas d'affcicahge des messages consoles lors installation
//function don_corps_civicrm_enable() {     // affiche les messages console lors de l'installation
  echo PHP_EOL."@@@  hook_civicrm_enable ".PHP_EOL;
  _don_corps_civix_civicrm_enable();

  /// Activation des groupes de champs personalisés crées par l'extension
    echo "  -Activation des groupes de champs personalisés crées par l'extension".PHP_EOL;
    $results = civicrm_api4('CustomGroup', 'update', [
      'values' => [
        'is_active' => TRUE,
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
        ],
      'checkPermissions' => FALSE,
    ]);
    CRM_Core_Session::setStatus('Activation réussie des groupes de champs personalisés.', 'Succès', 'success');
   /// FIN Activation des groupes de champs personalisés crées par l'extension
  /// Activation des champs personalisés crées par l'extension
    echo "  -Activation des champs personalisés".PHP_EOL;
    $results = civicrm_api4('CustomField', 'update', [
      'values' => [
        'is_active' => TRUE,
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
      ],
      'checkPermissions' => FALSE,
    ]);
    CRM_Core_Session::setStatus('Activation réussie des champs personalisés.', 'Succès', 'success');
    /// FIN Activation des champs personalisés crées par l'extension
  /// Activation des groupes de contacts personalisés crées par l'extension
    echo "  -Activation des groupes de contact crées par l'extension".PHP_EOL;
    $results = civicrm_api4('Group', 'update', [
      'values' => [
        'is_active' => TRUE,
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
      ],
      'checkPermissions' => FALSE,
    ]);
    CRM_Core_Session::setStatus('Activation réussie des groupes de contacts personalisés.', 'Succès', 'success');
    /// FIN Activation des groupes de contacts personalisés crées par l'extension
  /// Activation des relations personalisés crées par l'extension
    echo "  -Activation des relations personnalisés crées par l'extension".PHP_EOL;
    $results = civicrm_api4('RelationshipType', 'update', [
      'values' => [
        'is_active' => TRUE,
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
      ],
      'checkPermissions' => FALSE,
    ]);

    CRM_Core_Session::setStatus('Activation réussie des relations personalisés.', 'Succès', 'success');

  /// Activation des profils crées par l'extension
    echo "  -Activation des profils créés par l'extension".PHP_EOL;
    $results = civicrm_api4('UFGroup', 'update', [
      'values' => [
        'is_active' => TRUE,
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
      ],
      'checkPermissions' => FALSE,
    ]);

    CRM_Core_Session::setStatus('Activation réussie des Groupes de Profils.', 'Succès', 'success');
    /// Fin Activation des profils crées par l'extension

  /// Activation des champs de profils crées par l'extension
    echo "  -Activation des champs de profils créés par l'extension".PHP_EOL;
    $results = civicrm_api4('UFField', 'update', [
      'values' => [
        'is_active' => TRUE,
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
      ],
      'checkPermissions' => FALSE,
    ]);

    CRM_Core_Session::setStatus('Activation réussie des Profils.', 'Succès', 'success');
    /// Fin Activation des champs de profils crées par l'extension

  /// Déactivation des Tags par défaut
    echo "  -Déactivation des tags par défaut".PHP_EOL;
    $tags = civicrm_api4('Tag', 'delete', [
      'where' => [
        ['base_module', 'NOT CONTAINS', 'don_corps'],
      ],
      'checkPermissions' => FALSE,
    ]);
    CRM_Core_Session::setStatus('Déactivation réussie des Tags.', 'Succès', 'success');
    // Fin Déactivation des Tags par défaut

  /// Création des correspondances de mots
    echo "  -Création des correspondances de mots".PHP_EOL;

    $translats = [
    [
        //'id' => 1,
        'find_word' => 'Contribution',
        'replace_word' => 'Don financier',
        'is_active' => TRUE,
        'match_type' => 'wildcardMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 2,
        'find_word' => 'add',
        'replace_word' => 'ajouter',
        'is_active' => TRUE,
        'match_type' => 'exactMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 4,
        'find_word' => 'amount',
        'replace_word' => 'montant',
        'is_active' => TRUE,
        'match_type' => 'wildcardMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 5,
        'find_word' => 'Surnom',
        'replace_word' => 'Nom de naissance',
        'is_active' => TRUE,
        'match_type' => 'wildcardMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 7,
        'find_word' => 'Attendee List',
        'replace_word' => 'liste des participants',
        'is_active' => TRUE,
        'match_type' => 'wildcardMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 11,
        'find_word' => 'Événement',
        'replace_word' => 'Cérémonie',
        'is_active' => TRUE,
        'match_type' => 'wildcardMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 12,
        'find_word' => 'Event',
        'replace_word' => 'Cérémonie',
        'is_active' => TRUE,
        'match_type' => 'wildcardMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 13,
        'find_word' => 'New',
        'replace_word' => 'Nouveau/Nouvelle',
        'is_active' => TRUE,
        'match_type' => 'wildcardMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 14,
        'find_word' => 'Activity',
        'replace_word' => 'Activité',
        'is_active' => TRUE,
        'match_type' => 'wildcardMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 15,
        'find_word' => 'Find',
        'replace_word' => 'Cherche',
        'is_active' => TRUE,
        'match_type' => 'wildcardMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 16,
        'find_word' => 'Report',
        'replace_word' => 'Rapport',
        'is_active' => TRUE,
        'match_type' => 'wildcardMatch',
        'domain_id' => 1,
      ],
      [
        //'id' => 16,
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
      //echo $translat['find_word']."\n";

      $wordReplacements = civicrm_api4('WordReplacement', 'get', [
      'where' => [
        ['find_word', '=', $translat['find_word']],
      ],
      'checkPermissions' => FALSE,
      ]);

      //echo "\n".$wordReplacements[0]['id']."\n";

      if (isset($wordReplacements[0]['id']))  {  // si le Wordreplacement existe
          //echo "update"."\n";

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

}

/*****************************************************************************
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 *****************************************************************************/


function don_corps_civicrm_postInstall() {
  echo PHP_EOL."@@@  hook_civicrm_postinstall ".PHP_EOL;

    //echo "  -Déactivation des menus par défaut".PHP_EOL;
    deactivate_menu('Contacts');
    deactivate_menu('Search');
    deactivate_menu('Contributions');
    deactivate_menu('Events');
    deactivate_menu('Mailings');
    deactivate_menu('Reports');
    deactivate_menu('Support');

  // modification de l'usage des regles de dédoublonage : ne garde que les numeros_don_annulation_dc_2 et num_don_annulation_deces en non géneral (supervise et auto)
   echo "  -modification des regles de déboublonage".PHP_EOL;
    $results = civicrm_api4('DedupeRuleGroup', 'update', [
      'values' => [
        'used' => 'General',
      ],
      'where' => [
        ['name', '<>', 'numeros_don_annulation_dc_2'],
        ['name', '<>', 'num_don_annulation_deces'],
        ['contact_type', '=', 'Individual']
      ],
      'checkPermissions' => FALSE,
    ]);
   // fin de la modification de l'usage des regles de dédoublonage

  // modification du filtre du champ perso Preparé par pour ne garder que les contacts du groupe personnel des cdc
    echo "  - Modification du filtre du champ perso Preparé par".PHP_EOL;
    modif_filtre('Pr_par_par','Personnel_centre_de_don_77');

    echo "  - Modification du filtre du champ perso Localisation".PHP_EOL;
    modif_filtre('Lacalisation','Emprunteurs_44');

   // fin de modification du filtre du champ perso Preparé par 



  // création des rules
   echo "  -Création des Rules".PHP_EOL;

    echo PHP_EOL."   - Civirule : MAJ civilités ".PHP_EOL;
          
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
    $condition_params=serialize_custom_fields('Civilit_user');

    $to_create =  [      
      'entity' => 'CiviRulesRuleCondition',
      'values' => [
        'condition_link' => NULL,
        'condition_params' => $condition_params,
        'is_active' => TRUE,
        'rule_id.name' => 'maj_genre_',
        'condition_id.name' => 'contact_custom_field_changed',
      ],
    ];
    create_entity($to_create);
                  

  
    echo PHP_EOL."   - Civirule : Compile pieces et utilisations".PHP_EOL;
          
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
    $condition_params=serialize_custom_fields('Type_de_poi_ce_3', 'Utilisation2');

    $to_create =  [      
      'entity' => 'CiviRulesRuleCondition',
      'values' => [
        'rule_id.name' => 'update_pieces_et_utilisations',
        'condition_id.name' => 'contact_custom_field_changed',
        'is_active' => TRUE,
        'condition_params' => $condition_params, 
      ],
    ];
    create_entity($to_create);



    // Copie code barre corps
    echo PHP_EOL."   - Civirule : Copie code barre corps".PHP_EOL;

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

    $condition_params=serialize_custom_fields('N_de_pi_ce_ou_de_corps');

    $to_create =  [       // Copie code barre corps : Rule Condition
      'entity' => 'CiviRulesRuleCondition',
      'values' => [
        'rule_id.name' => 'copie_code_barre_corps',
        'condition_id.name' => 'contact_custom_field_changed',
        'is_active' => TRUE,
        'condition_params' => $condition_params,
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


    //envoyer_mail_si_demande_cremation
    echo PHP_EOL."   - Civirule : Envoyer_mail_si_demande_cremation".PHP_EOL;



    $to_create =  [     //envoyer_mail_si_demande_cremation : Message template à envoyer 
      'entity' => 'MessageTemplate',
      'values' => [
        'msg_title' => 'Demander crémation au secrétariat',
        'msg_subject' => 'Merci de demander crémation pour : {contact.display_name}',
        'msg_text' => NULL,
        'msg_html' => '<p>Bonjour</p>
    <p>Merci de prévoir le transfert et de nous communiquer la date de crémation de :</p>
    <p>{contact.display_name}</p>
    <p>né(e)&nbsp;{contact.nick_name} le {contact.birth_date} à </p>
    <p>décédé(e) le {contact.deceased_date} à </p>
    <p><br />
    Nous restons à votre disposition pour tout renseignement complémentaire</p>
    <p>Les techniciens du laboratoire de {domain.city}</p>
    <p>&nbsp;</p>',
        'is_active' => TRUE,
        'workflow_id' => NULL,
        'workflow_name' => NULL,
        'is_default' => TRUE,
        'is_reserved' => FALSE,
        'is_sms' => FALSE,
        'pdf_format_id' => 0,
      ],
    ];

    $msg_id = serialize(create_entity($to_create));  // récupère l'id du message qui vient d'être créé et sera utilisé dans CiviRulesRuleAction (1)



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
    $condition_params=serialize_custom_fields('Mode_limination_hors_corps_2');
    $to_create =  [
      'entity' => 'CiviRulesRuleCondition',
      'values' => [
        'condition_link' => NULL,
        'rule_id.name' => 'envoyer_mail_si_demande_cremation',
        'condition_id.name' => 'contact_custom_field_changed',
        'is_active' => TRUE,
        'condition_params' => $condition_params,  
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
        'action_params' => 'a:10:{s:9:"from_name";s:25:"Techniciens labo anatomie";s:10:"from_email";s:28:"dons.corps@med.univ-tours.fr";s:11:"template_id";'.$msg_id.'s:14:"disable_smarty";b:0;s:16:"location_type_id";s:0:"";s:17:"from_email_option";s:0:"";s:28:"alternative_receiver_address";s:23:"destrieux@univ-tours.fr";s:2:"cc";s:0:"";s:3:"bcc";s:0:"";s:12:"file_on_case";b:0;}',    
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

    // Rule Neutralise adresse postale en cas de retour de courrier
    echo PHP_EOL."   - Civirule : Neutralise adresse postale en cas de retour de courrier".PHP_EOL;

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
    //echo $id_activité_modif_coord.PHP_EOL;

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


      $id_activité_modif_coord_ser = serialize($id_activité_modif_coord); // passer l'adresse en erroné : Rule Condition 1
      $to_create =  [
        'entity' => 'CiviRulesRuleCondition',
        'values' => [
          'condition_link' => NULL,
          'condition_params' => 'a:2:{s:8:"operator";s:1:"0";s:16:"activity_type_id";a:1:{i:0;'.$id_activité_modif_coord_ser.'}}',
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
            'condition_params' => 'a:2:{s:8:"operator";s:8:"contains";s:4:"text";'.$mail_content_triger.'}',
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

        $custom_adresse_incorrecte = serialize ($customFields[0]['id']);

      $to_create =  [                                  // passer l'adresse en erroné : Rule Action 1
        'entity' => 'CiviRulesRuleAction',
        'values' => [    
          'action_params' => 'a:2:{s:8:"field_id";'.$custom_adresse_incorrecte.'s:5:"value";s:1:"1";}',
          'delay' => NULL,
          'ignore_condition_with_delay' => 0,
          'is_active' => TRUE,
          'rule_id.name' => 'neutralise_adresse_postale',
          'action_id.name' => 'set_custom_field',
        ],
      ];
      create_entity($to_create);
   // fin de la création des Rules
    
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


    echo "  - Modification des requetes".PHP_EOL;
      $api_params="onlytag";  // ne modifie que le tag

      // Requetes de tokens
      $searchname = 'tokens_lastDons';            // requete search creation tokens dernier don  //
      //$api_params = ['version' => 4, 'select' => [ 'sort_name', 'GROUP_FIRST(Contact_Contribution_contact_id_01.total_amount ORDER BY Contact_Contribution_contact_id_01.receive_date DESC) AS GROUP_FIRST_Contact_Contribution_contact_id_01_total_amount_Contact_Contribution_contact_id_01_receive_date', 'GROUP_FIRST(Contact_Contribution_contact_id_01.receive_date ORDER BY Contact_Contribution_contact_id_01.receive_date DESC) AS GROUP_FIRST_Contact_Contribution_contact_id_01_receive_date_Contact_Contribution_contact_id_01_receive_date', 'id', ], 'orderBy' => [], 'where' => [ [ 'Contact_Contribution_contact_id_01.total_amount', '!=', '0', ], ], 'groupBy' => [ 'id', ], 'join' => [ [ 'Contribution AS Contact_Contribution_contact_id_01', 'LEFT', [ 'id', '=', 'Contact_Contribution_contact_id_01.contact_id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'tokens');

      $searchname = 'Tokens_PAQPF';            // requete search creation tokens PAQPF  //
      //$api_params = ['version' => 4, 'select' => [ 'display_name', 'Contact_RelationshipCache_Contact_01.near_contact_id.display_name', 'id', 'Contact_RelationshipCache_Contact_01.address_primary.supplemental_address_1', 'Contact_RelationshipCache_Contact_01.address_primary.postal_code', 'Contact_RelationshipCache_Contact_01.address_primary.city', 'Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01.email', 'Contact_RelationshipCache_Contact_01.phone_primary.phone', 'Contact_RelationshipCache_Contact_01.address_primary.street_address', 'Contact_RelationshipCache_Contact_01.email_greeting_display', 'Contact_RelationshipCache_Contact_01.postal_greeting_display', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'Contact_RelationshipCache_Contact_01.far_contact_id.display_name', 'IS NOT EMPTY', ], ], 'groupBy' => [], 'join' => [ [ 'Contact AS Contact_RelationshipCache_Contact_01', 'LEFT', 'RelationshipCache', [ 'id', '=', 'Contact_RelationshipCache_Contact_01.far_contact_id', ], [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"a pour PAQPF"', ], [ 'Contact_RelationshipCache_Contact_01.is_active', '=', TRUE, ], ], [ 'Email AS Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01', 'LEFT', [ 'Contact_RelationshipCache_Contact_01.id', '=', 'Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01.contact_id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'tokens');

      $searchname = 'Tokens_for_contact';            // requete search creation Tokens_for_contact  //
      //$api_params = ['version' => 4, 'select' => [ 'Promesse_de_don.N_de_don', 'deceased_date', 'Promesse_de_don.Centre_de_don:label', 'Promesse_de_don.Date_du_don', 'Compl_m_nt_tat_civil.Ville_de_naissance', 'Promesse_de_don.Devenir_souhait_:label', 'Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:label', 'Promesse_de_don.Souhait_lecture_nom:label', 'Promesse_de_don.Souhiat_affichage_st_le:label', 'Promesse_de_don.Refus_personne_referente', 'id', 'Annulation.Date_d_annulation', 'Annulation.N_annulation', 'Arriv_e_du_corps_new.Effets_personnels_retir_s', 'Prise_en_charge_au_d_c_s.Ville_de_d_c_s', 'Devenir_du_corps.Date_de_sortie_d_finitive', 'Devenir_du_corps.Date_op_rations_fun_raires', 'Prise_en_charge_au_d_c_s.N_de_d_c_s', 'Devenir_du_corps.Souhait_funeraire_personne_ref_rente:label', 'champs_caches.piece_prinicpale', ], 'orderBy' => [], 'where' => [], 'groupBy' => [], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'tokens');

      $searchname = 'Tokens_pour_personne_de_confinace_1';            // requete search creation Tokens_pour_personne_de_confinace_1  //
      //$api_params = ['version' => 4, 'select' => [ 'display_name', 'Contact_RelationshipCache_Contact_01.near_contact_id.display_name', 'id', 'Contact_RelationshipCache_Contact_01.address_primary.supplemental_address_1', 'Contact_RelationshipCache_Contact_01.address_primary.postal_code', 'Contact_RelationshipCache_Contact_01.address_primary.city', 'Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01.email', 'Contact_RelationshipCache_Contact_01.phone_primary.phone', 'Contact_RelationshipCache_Contact_01.address_primary.street_address', 'Contact_RelationshipCache_Contact_01.email_greeting_display', 'Contact_RelationshipCache_Contact_01.postal_greeting_display', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'Contact_RelationshipCache_Contact_01.far_contact_id.display_name', 'IS NOT EMPTY', ], ], 'groupBy' => [], 'join' => [ [ 'Contact AS Contact_RelationshipCache_Contact_01', 'LEFT', 'RelationshipCache', [ 'id', '=', 'Contact_RelationshipCache_Contact_01.far_contact_id', ], [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"a pour personne de confiance"', ], [ 'Contact_RelationshipCache_Contact_01.is_active', '=', TRUE, ], ], [ 'Email AS Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01', 'LEFT', [ 'Contact_RelationshipCache_Contact_01.id', '=', 'Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01.contact_id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'tokens');
      
      $searchname = 'tokens_for_a_pour_personne_de_confiance_1';            // requete search creation tokens_for_a_pour_personne_de_confiance_2 (et pas 1)  //
      //$api_params = ['version' => 4, 'select' => [ 'display_name', 'Contact_RelationshipCache_Contact_01.near_contact_id.display_name', 'id', 'Contact_RelationshipCache_Contact_01.address_primary.supplemental_address_1', 'Contact_RelationshipCache_Contact_01.address_primary.postal_code', 'Contact_RelationshipCache_Contact_01.address_primary.city', 'Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01.email', 'Contact_RelationshipCache_Contact_01.phone_primary.phone', 'Contact_RelationshipCache_Contact_01.address_primary.street_address', 'Contact_RelationshipCache_Contact_01.email_greeting_display', 'Contact_RelationshipCache_Contact_01.postal_greeting_display', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'Contact_RelationshipCache_Contact_01.far_contact_id.display_name', 'IS NOT EMPTY', ], ], 'groupBy' => [], 'join' => [ [ 'Contact AS Contact_RelationshipCache_Contact_01', 'LEFT', 'RelationshipCache', [ 'id', '=', 'Contact_RelationshipCache_Contact_01.far_contact_id', ], [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"a pour personne de confiance 2"', ], [ 'Contact_RelationshipCache_Contact_01.is_active', '=', TRUE, ], ], [ 'Email AS Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01', 'LEFT', [ 'Contact_RelationshipCache_Contact_01.id', '=', 'Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01.contact_id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'tokens');

  
      // Requetes d'affichage de listes et de groupes

      $searchname = 'Donneurs_sans_PAQPF';            // requete liste donneurs sans PAQPF et avec pers referente  //
      update_search($searchname, $api_params, 'civi_ddc');

      $searchname = 'A_PAQPF';            // groupe dynamique donneurs avec PAQPF  //
      update_search($searchname, $api_params, 'civi_ddc');

      $searchname = 'Personnes_r_f_rentes_dont_le_donneur_est_d_c_d_sans_les_deux_mois_pr_c_dents';            // requete PAQF //
      //$api_params = ['version' => 4, 'select' => [ 'sort_name', 'Contact_RelationshipCache_Contact_01.far_relation:label', 'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.N_de_d_c_s', 'Contact_RelationshipCache_Contact_01.display_name', 'Contact_RelationshipCache_Contact_01.deceased_date', 'Contact_RelationshipCache_Contact_01.Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:label', 'Contact_Participant_contact_id_01.event_id.title', 'Contact_Participant_contact_id_01.status_id:label', 'Contact_Participant_contact_id_01_Participant_Event_event_id_01.title', 'Contact_Participant_contact_id_01_Participant_Event_event_id_01.start_date', 'id', ], 'orderBy' => [], 'where' => [ [ 'OR', [ [ 'contact_type:name', '=', 'Individual', ], ], ], [ 'Contact_RelationshipCache_Contact_01.deceased_date', '=', 'ending_2.year', ], [ 'OR', [ [ 'Contact_RelationshipCache_Contact_01.Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:name', 'IS EMPTY', ], [ 'Contact_RelationshipCache_Contact_01.Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:name', '!=', 'Non', ], ], ], [ 'OR', [ [ 'Contact_Participant_contact_id_01_Participant_Event_event_id_01.start_date', 'IS EMPTY', ], [ 'Contact_Participant_contact_id_01_Participant_Event_event_id_01.start_date', '>', 'now', ], ], ], ], 'groupBy' => [], 'join' => [ [ 'Contact AS Contact_RelationshipCache_Contact_01', 'INNER', 'RelationshipCache', [ 'id', '=', 'Contact_RelationshipCache_Contact_01.far_contact_id', ], [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"est la PAQPF"', ], ], [ 'Participant AS Contact_Participant_contact_id_01', 'LEFT', [ 'id', '=', 'Contact_Participant_contact_id_01.contact_id', ], ], [ 'Event AS Contact_Participant_contact_id_01_Participant_Event_event_id_01', 'LEFT', [ 'Contact_Participant_contact_id_01.event_id', '=', 'Contact_Participant_contact_id_01_Participant_Event_event_id_01.id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'civi_ddc');
      
      $searchname = 'Tous_les_contacts';            // requete Tous les contacts //
      //$api_params = ['version' => 4, 'select' => ['last_name', 'first_name', 'contact_sub_type:label', 'birth_date', 'deceased_date', 'Promesse_de_don.Centre_de_don:label', 'Promesse_de_don.N_de_don', 'Annulation.N_annulation', 'Prise_en_charge_au_d_c_s.N_de_d_c_s', 'Devenir_du_corps.Date_op_rations_fun_raires', 'Contact_Custom_Utilisation_du_corps_entity_id_01.N_de_pi_ce_ou_de_corps', 'display_name', 'sort_name'], 'orderBy' => [], 'where' => [['OR', [['Contact_Custom_Utilisation_du_corps_entity_id_01.Type_de_poi_ce_3:name', 'IS EMPTY'], ['Contact_Custom_Utilisation_du_corps_entity_id_01.Type_de_poi_ce_3:name', 'CONTAINS', 'Corps_entier_tronc']]]], 'groupBy' => [], 'join' => [['Custom_Utilisation_du_corps AS Contact_Custom_Utilisation_du_corps_entity_id_01', 'LEFT', ['id', '=', 'Contact_Custom_Utilisation_du_corps_entity_id_01.entity_id']]], 'having' => [],];
      update_search($searchname, $api_params, 'civi_ddc');

      $searchname = 'Donneurs_vivants';            // requete Tous les contacts //
      //$api_params = ['version' => 4, 'select' => ['Promesse_de_don.N_de_don', 'display_name', 'birth_date', 'age_years', 'Annulation.N_annulation'], 'orderBy' => [], 'where' => [['contact_sub_type:name', '=', 'Donateur'], ['NOT', [['OR', [['Prise_en_charge_au_d_c_s.N_de_d_c_s', 'IS NOT EMPTY'], ['is_deceased', '=', TRUE], ['deceased_date', 'IS NOT EMPTY']]]]]], 'groupBy' => [], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'civi_ddc');

      $searchname = 'Donneurs_DCD';                // requete Donneurs_décédés
      //$api_params = ['version' => 4, 'select' => ['Promesse_de_don.N_de_don', 'display_name', 'birth_date', 'age_years', 'deceased_date', 'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC', 'Prise_en_charge_au_d_c_s.N_de_d_c_s', 'Devenir_du_corps.devenir_effectif_du_corps:label', 'Devenir_du_corps.Date_op_rations_fun_raires', 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires:label'], 'orderBy' => [], 'where' => [['contact_sub_type:name', 'CONTAINS', 'Donateur'], ['OR', [['is_deceased', '=', TRUE], ['deceased_date', 'IS NOT EMPTY'], ['Prise_en_charge_au_d_c_s.N_de_d_c_s', 'IS NOT EMPTY']]]], 'groupBy' => [], 'join' => [], 'having' => []];
      update_search($searchname, $api_params, 'civi_ddc');

      $searchname = 'Annulation';                 // requete Donneurs_annulés
      //$api_params =[ 'version' => 4, 'select' => [ 'id', 'Promesse_de_don.N_de_don', 'contact_sub_type:label', 'Compl_m_nt_tat_civil.Civilit_user:label', 'first_name', 'last_name', 'Annulation.N_annulation', 'Annulation.Date_d_annulation', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'OR', [ [ 'Annulation.Date_d_annulation', 'IS NOT EMPTY', ], [ 'Annulation.N_annulation', 'IS NOT EMPTY', ], ], ], ], 'groupBy' => [], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params,'civi_ddc');

      $searchname = 'Mairies';                   // requete mairies
      //$api_params = ['version' => 4, 'select' => ['id', 'sort_name', 'email_primary', 'address_primary.street_address', 'address_primary.supplemental_address_1', 'address_primary.postal_code', 'address_primary.city'], 'orderBy' => [], 'where' => [['contact_sub_type:name', 'CONTAINS', 'Mairies']], 'groupBy' => [], 'join' => [['Address AS Contact_Address_contact_id_01', 'LEFT', ['id', '=', 'Contact_Address_contact_id_01.contact_id']]], 'having' => [],];
      update_search($searchname, $api_params,'civi_ddc');

      $searchname = 'tableau_bord_2';            // requete tableau de bord   
      ////$api_params = ['api_params' => [ 'version' => 4, 'select' => [ 'sort_name', 'contact_sub_type:label', 'Contact_Custom_Utilisation_du_corps_entity_id_01.Type_de_poi_ce_3:label', 'deceased_date', 'Prise_en_charge_au_d_c_s.N_de_d_c_s', 'Arriv_e_du_corps_new.Retrait_Stimulateur_piles:label', 'Promesse_de_don.Devenir_souhait_:label', 'Contact_Custom_Utilisation_du_corps_entity_id_01.Lacalisation.display_name', 'Contact_Custom_Utilisation_du_corps_entity_id_01.N_de_pi_ce_ou_de_corps', 'Contact_Custom_Utilisation_du_corps_entity_id_01.Utilisation2:label', 'champs_caches.toutes_utilisations:label', 'champs_caches.toutes_pieces:label', 'Contact_Custom_Utilisation_du_corps_entity_id_01.Compl_ment', 'Devenir_du_corps.Date_de_sortie_d_finitive', 'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:label', 'Devenir_du_corps.Date_op_rations_fun_raires', 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires:label', 'id', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'OR', [ [ 'Contact_Custom_Utilisation_du_corps_entity_id_01.Type_de_poi_ce_3:name', 'CONTAINS', 'Corps_entier_tronc', ], [ 'Contact_Custom_Utilisation_du_corps_entity_id_01.N_de_pi_ce_ou_de_corps', 'IS EMPTY', ], ], ], [ 'OR', [ [ 'is_deceased', '=', TRUE, ], [ 'Prise_en_charge_au_d_c_s.N_de_d_c_s', 'IS NOT EMPTY', ], [ 'deceased_date', 'IS NOT EMPTY', ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '!=', 'Pas_de_refus', ], ], ], [ 'OR', [ [ 'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:name', '=', 'Non_limin_e', ], [ 'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:name', '=', 'Conservation_illimit_e', ], [ 'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:name', '=', 'Demander_cr_mation', ], [ 'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:name', '=', 'Cr_mation_demand_e', ], ], ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Pas_de_refus', ], ], 'groupBy' => [], 'join' => [ [ 'Custom_Utilisation_du_corps AS Contact_Custom_Utilisation_du_corps_entity_id_01', 'LEFT', [ 'id', '=', 'Contact_Custom_Utilisation_du_corps_entity_id_01.entity_id', ], ], ], 'having' => [], ];
      update_search($searchname, $api_params,'civi_ddc');

      $searchname = 'Donneurs_vivants_ano_ville_CP';        // requete donneurs vivants avec anomalie de ville d'adresse ou CP  
      //$api_params = [ 'version' => 4, 'select' => ['Promesse_de_don.N_de_don', 'display_name', 'Contact_Address_contact_id_01.street_address', 'Contact_Address_contact_id_01.postal_code', 'Contact_Address_contact_id_01.city', 'Compl_m_nt_tat_civil.Adresse_incorrecte:label', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'OR', [ [ 'Contact_Address_contact_id_01.postal_code', 'IS EMPTY', ], [ 'Contact_Address_contact_id_01.city', 'IS EMPTY', ], [ 'NOT', [ [ 'Contact_Address_contact_id_01.postal_code', 'LIKE', '_____', ], ], ], [ 'address_primary.street_address', 'IS EMPTY', ], ], ], [ 'Compl_m_nt_tat_civil.Adresse_incorrecte', '=', FALSE, ], [ 'is_deceased', '=', FALSE, ], [ 'deceased_date', 'IS EMPTY', ], [ 'Prise_en_charge_au_d_c_s.N_de_d_c_s', 'IS EMPTY', ], [ 'groups:name', 'IN', [ 'Annulation_32', ], ], ], 'groupBy' => [], 'join' => [ [ 'Address AS Contact_Address_contact_id_01', 'LEFT', [ 'id', '=', 'Contact_Address_contact_id_01.contact_id', ], ], ], 'having' => [], ];
      update_search($searchname, $api_params,'civi_ddc');

      $searchname = 'Emprunteurs';                // requete emprunteurs et lieux de stockage 
      //$api_params = ['version' => 4, 'select' => [ 'id', 'contact_sub_type:label', 'display_name', 'Contact_Address_contact_id_01.street_address', 'Contact_Address_contact_id_01.supplemental_address_1', 'Contact_Address_contact_id_01.postal_code', 'Contact_Address_contact_id_01.city', 'phone_primary.phone', ], 'orderBy' => [], 'where' => [ [ 'OR', [ [ 'contact_sub_type:name', 'CONTAINS', 'Emprunteur', ], [ 'contact_sub_type:name', 'CONTAINS', 'CDC', ], ], ], ], 'groupBy' => [], 'join' => [ [ 'Address AS Contact_Address_contact_id_01', 'LEFT', [ 'id', '=', 'Contact_Address_contact_id_01.contact_id', ], [ 'Contact_Address_contact_id_01.is_primary', '=', TRUE, ], ], ], 'having' => [],];
      update_search($searchname, $api_params,'civi_ddc');

      $searchname = 'Personnel_centre_de_don';     // requete personnels centre de don (crée groupe dyn)
      //$api_params = ['version' => 4, 'select' => [ 'id', 'sort_name', 'contact_type:label', 'contact_sub_type:label', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Personnel', ], ], 'groupBy' => [], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params,'civi_ddc');

      $searchname = 'Centres_d_accueil_des_corps';     // requete centre de don 
      //$api_params = ['version' => 4, 'select' => [ 'sort_name', 'Contact_Address_contact_id_01.street_address', 'Contact_Address_contact_id_01.supplemental_address_1', 'Contact_Address_contact_id_01.postal_code', 'Contact_Address_contact_id_01.city', 'Contact_Address_contact_id_01.supplemental_address_2', 'Contact_Address_contact_id_01.supplemental_address_3', 'phone_primary.phone', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'CDC', ], ], 'groupBy' => [], 'join' => [ [ 'Address AS Contact_Address_contact_id_01', 'LEFT', [ 'id', '=', 'Contact_Address_contact_id_01.contact_id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params,'civi_ddc');

      $searchname = 'Pompes_funebres';            // requete pompes funebres (crée groupe dyn)
      //$api_params = ['version' => 4, 'select' => [ 'id', 'sort_name', 'Contact_Address_contact_id_01.street_address', 'Contact_Address_contact_id_01.supplemental_address_1', 'Contact_Address_contact_id_01.supplemental_address_2', 'Contact_Address_contact_id_01.city', 'Contact_Address_contact_id_01.postal_code', 'email_primary.email', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Pompes', ], ], 'groupBy' => [], 'join' => [ [ 'Address AS Contact_Address_contact_id_01', 'LEFT', [ 'id', '=', 'Contact_Address_contact_id_01.contact_id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params,'civi_ddc');

      $searchname = 'Inventaires';                // requete inventaires
      //$api_params = ['version' => 4, 'select' => [ 'entity_id.sort_name', 'Custom_Utilisation_du_corps_Contact_entity_id_01.Prise_en_charge_au_d_c_s.N_de_d_c_s', 'N_de_pi_ce_ou_de_corps', 'Type_de_poi_ce_3:label', 'Sortie', 'Date_de_retour', 'Lacalisation.display_name', 'Custom_Utilisation_du_corps_Contact_entity_id_01.Devenir_du_corps.devenir_effectif_du_corps:label', 'Inventaires:label', ], 'orderBy' => [], 'where' => [ [ 'OR', [ [ 'Inventaires:name', 'IS NOT EMPTY', ], [ 'Lacalisation', 'IS NOT EMPTY', ], ], ], ], 'groupBy' => [], 'join' => [ [ 'Contact AS Custom_Utilisation_du_corps_Contact_entity_id_01', 'LEFT', [ 'entity_id', '=', 'Custom_Utilisation_du_corps_Contact_entity_id_01.id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params,'civi_ddc');

      $searchname = 'Toutes_pi_ces_corps';        // requete utilisée pour lister corps et pieces presents     
      //$api_params = ['version' => 4, 'select' => [ 'entity_id.display_name', 'N_de_pi_ce_ou_de_corps', 'Custom_Utilisation_du_corps_Contact_entity_id_01.Prise_en_charge_au_d_c_s.N_de_d_c_s', 'type_de_pi_ce:label', 'Type_de_poi_ce_3:label', 'Utilisation2:label', 'Protocole_de_recherche_ex_vivo2:label', 'Compl_ment', 'Lacalisation.display_name', 'Sortie', 'Mode_limination_hors_corps_2:label', 'D_lai_en_heure_entre_d_c_s_h_0_et_injection', ], 'orderBy' => [], 'where' => [ [ 'Type_de_poi_ce_3:name', 'IS NOT EMPTY', ], ], 'groupBy' => [], 'join' => [ [ 'Contact AS Custom_Utilisation_du_corps_Contact_entity_id_01', 'LEFT', [ 'entity_id', '=', 'Custom_Utilisation_du_corps_Contact_entity_id_01.id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'civi_ddc');

      $searchname = 'participants';        // requete utilisée pour lister participants aux ceremonies    
      //$api_params = ['version' => 4, 'select' => [ 'GROUP_CONCAT(DISTINCT event_id.title) AS GROUP_CONCAT_event_id_title', 'GROUP_CONCAT(DISTINCT Participant_Event_event_id_01.start_date) AS GROUP_CONCAT_Participant_Event_event_id_01_start_date', 'GROUP_CONCAT(DISTINCT role_id:label) AS GROUP_CONCAT_role_id_label', 'GROUP_CONCAT(DISTINCT status_id:label) AS GROUP_CONCAT_status_id_label', 'GROUP_CONCAT(DISTINCT contact_id.sort_name) AS GROUP_CONCAT_contact_id_sort_name', 'GROUP_CONCAT(DISTINCT Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.far_relation:label) AS GROUP_CONCAT_Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01_far_relation_label', 'GROUP_FIRST(Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.near_contact_id.sort_name ORDER BY Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.deceased_date DESC) AS GROUP_FIRST_Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01_near_contact_id_sort_name_Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01_deceased_date', 'GROUP_CONCAT(DISTINCT Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.deceased_date) AS GROUP_CONCAT_Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01_deceased_date', ], 'orderBy' => [], 'where' => [], 'groupBy' => [ 'contact_id', ], 'join' => [ [ 'Contact AS Participant_Contact_contact_id_01', 'LEFT', [ 'contact_id', '=', 'Participant_Contact_contact_id_01.id', ], ], [ 'Contact AS Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01', 'LEFT', 'RelationshipCache', [ 'Participant_Contact_contact_id_01.id', '=', 'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.far_contact_id', ], [ 'OR', [ [ 'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"est la PAQPF"', ], [ 'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"est la personne de confiance de"', ], [ 'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"est la personne de confiance 2"', ], ], ], ], [ 'Event AS Participant_Event_event_id_01', 'LEFT', [ 'event_id', '=', 'Participant_Event_event_id_01.id', ], ], ], 'having' => [], ];
      update_search($searchname, $api_params, 'civi_ddc');

      // Requetes utilisées par les purges

      $searchname = 'Donneurs_annul_s';          // requete donneurs annulés sans ceux placés en archive, ie deja purgé       
      //$api_params = [ 'version' => 4, 'select' => [ 'id', 'Promesse_de_don.N_de_don', 'display_name', 'Annulation.N_annulation', 'Annulation.Date_d_annulation', 'Prise_en_charge_au_d_c_s.N_de_d_c_s', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'OR', [ [ 'Annulation.Date_d_annulation', 'IS NOT EMPTY', ], [ 'Annulation.N_annulation', 'IS NOT EMPTY', ], ], ], [ 'groups:name', 'NOT IN', [ 'Archives_61', ], ], ], 'groupBy' => [], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Anonymis_s_par_la_purge';   // requete contacts ayant été anonymisés par les purges
      //$api_params = [ 'version' => 4, 'select' => [ 'id', 'last_name', 'first_name', 'contact_sub_type:label', 'Promesse_de_don.N_de_don', 'Annulation.N_annulation', 'Prise_en_charge_au_d_c_s.N_de_d_c_s', 'Ant_c_dents_m_dicaux.Ant_c_dents_m_dico_chirurgicaux', ], 'orderBy' => [], 'where' => [ ['last_name', '=', 'ANONYMISE'], ], 'groupBy' => [], 'join' => [], 'having' => [], ];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Anonymises_sans_protocole';      // requete Donneurs anonymisés dont les ATCD n'ont pas été purgés et qui ne sont pas inclus dans un protocole.
                                                // Utilisé pour préserver les ATCD des donneurs inclus dans un ptotocole
      //$api_params = ['version' => 4, 'select' => [ 'id', 'sort_name', 'Contact_Custom_Utilisation_du_corps_entity_id_01.Protocole_de_recherche_ex_vivo2:label', ], 'orderBy' => [], 'where' => [ ['last_name', '=', 'ANONYMISE'], ['first_name', '=', 'Anonymisé'], [ 'OR', [ [ 'Contact_Custom_Utilisation_du_corps_entity_id_01.Protocole_de_recherche_ex_vivo2:name', 'CONTAINS', 'Pas_de_protocole', ], [ 'Contact_Custom_Utilisation_du_corps_entity_id_01.Protocole_de_recherche_ex_vivo2:name', 'IS EMPTY', ], ], ], [ 'tags:name', 'NOT IN', ['ATCD Purges'], ], ], 'groupBy' => [], 'join' => [ [ 'Custom_Utilisation_du_corps AS Contact_Custom_Utilisation_du_corps_entity_id_01', 'LEFT', [ 'id', '=', 'Contact_Custom_Utilisation_du_corps_entity_id_01.entity_id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Proches_sans_relation_1';                      // requete Proches sans relation    
      //api_params = [ 'version' => 4, 'select' => [ 'id', 'contact_sub_type:label', 'display_name', 'Contact_RelationshipCache_Contact_01.far_relation:label', 'Contact_RelationshipCache_Contact_01.near_relation:label', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Proches', ], [ 'Contact_RelationshipCache_Contact_01.is_current', '=', FALSE, ], [ 'contact_sub_type:name', 'NOT CONTAINS', 'Personnel', ], [ 'contact_sub_type:name', 'NOT CONTAINS', 'Donateur', ], ], 'groupBy' => [], 'join' => [ [ 'Contact AS Contact_RelationshipCache_Contact_01', 'LEFT', 'RelationshipCache', [ 'id', '=', 'Contact_RelationshipCache_Contact_01.far_contact_id', ], ], ], 'having' => [], ];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Personnels';                                   // requete Personnels partis depuis plus de 5 ans      
      //$api_params = [ 'version' => 4, 'select' => [ 'id', 'display_name', 'employer_id.display_name', 'job_title', 'infos_personnel.Date_debut_fonctions', 'infos_personnel.Date_fin_fonctions', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Personnel', ], [ 'NOT', [ [ 'infos_personnel.Date_fin_fonctions', '=', 'ending_5.year', ], ], ], ], 'groupBy' => [], 'join' => [], 'having' => [], ];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Demandeurs_d_informations_plus_d_un_an';      // requete demandeurs d'informations de plus d'un an      
      //$api_params =['version' => 4, 'select' => [ 'id', 'contact_sub_type:label', 'more_greetings_group.greeting_field_1', 'first_name', 'last_name', 'Demandeur_information.Date_d_envoi_d_informations', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', '=', 'Demandeur_d_information', ], [ 'contact_sub_type:name', '!=', 'Donateur', ], [ 'contact_sub_type:name', '!=', 'Personnel', ], [ 'contact_sub_type:name', '!=', 'Proches', ], [ 'NOT', [ [ 'Demandeur_information.Date_d_envoi_d_informations', '=', 'ending.year', ], ], ], ], 'groupBy' => [], 'join' => [], 'having' => [], ];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Donneurs_120_ans';                        // requete Donneurs âgés de plus de 120 ans et sans numéro de DC  : anonymisation 
      //$api_params = [ 'version' => 4, 'select' => [ 'id', 'display_name', 'birth_date', 'age_years', 'Promesse_de_don.N_de_don', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'age_years', '>=', 120, ], [ 'Prise_en_charge_au_d_c_s.N_de_d_c_s', 'IS EMPTY', ], [ 'deceased_date', 'IS EMPTY', ], [ 'is_deceased', '=', FALSE, ], [ 'last_name', '!=', 'ANONYMISE', ], ], 'groupBy' => [], 'join' => [], 'having' => [], ];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'donneurs_DC_un_an_et_refus';              // requete  Donneurs décédés depuis plus d'un an et refusés en raison du motif de décès ou de l'état de conservation : suppression"),     
      //$api_params = [ 'version' => 4, 'select' => [ 'id', 'display_name', 'deceased_date', 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:label', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', '=', 'Donateur', ], [ 'OR', [ [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'D_lai_d_pass_', ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Maladie_infectieuse', ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Obstacle_m_dico_l_gal', ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'D_c_s_l_tranger', ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Transfert_vers_autre_centre', ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Etat_de_conservation_du_corps', ], ], ], [ 'NOT', [ [ 'deceased_date', '=', 'ending.year', ], ], ], ], 'groupBy' => [], 'join' => [], 'having' => [], ];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Donneurs_dont_op_fun_raires_1_an';        // requeteDonneurs dont les opérations funéraires ont été achevées il y a plus d'un an : suppression des relations (avant purge des proches sans relations)\nLes donneurs dont les relations sont déja purgés (tag \"relations purgées\" sont exclus"),
      //$api_params = [ 'version' => 4, 'select' => [ 'id', 'display_name', 'Devenir_du_corps.Date_op_rations_fun_raires', 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires:label', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'Devenir_du_corps.Date_op_rations_fun_raires', '<', 'now - 1 year', ], [ 'tags:name', 'NOT IN', [ 'Relations Purgees', ], ], ], 'groupBy' => [], 'join' => [], 'having' => [], ];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Op_rations_fun_raires_de_plus_de_5_ans'; // requete Op_rations_fun_raires_de_plus_de_5_ans  
      //$api_params = [ 'version' => 4, 'select' => [ 'id', 'Promesse_de_don.N_de_don', 'more_greetings_group.greeting_field_1', 'first_name', 'UPPER(last_name) AS UPPER_last_name', 'Prise_en_charge_au_d_c_s.N_de_d_c_s', 'deceased_date', 'Devenir_du_corps.Date_op_rations_fun_raires', 'Devenir_du_corps.devenir_effectif_du_corps:label', 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires:label', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', '=', 'Donateur', ], [ 'NOT', [ [ 'Devenir_du_corps.Date_op_rations_fun_raires', '=', 'ending_5.year', ], ], ], [ 'Devenir_du_corps.Date_op_rations_fun_raires', '<', 'now', ], ], 'groupBy' => [], 'join' => [], 'having' => [], ];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Proches_dont_donneur_DC_1_an_et_refuses'; // requete Proches_dont_donneur_DC_1_an_et_refuses     
      //$api_params = [ 'version' => 4, 'select' => [ 'id', 'display_name', 'contact_sub_type:label', 'Contact_RelationshipCache_Contact_01.near_relation:label', 'Contact_RelationshipCache_Contact_01.far_relation:label', 'Contact_RelationshipCache_Contact_01.contact_sub_type:label', 'Contact_RelationshipCache_Contact_01.display_name', 'Contact_RelationshipCache_Contact_01.deceased_date', ], 'orderBy' => [], 'where' => [ [ 'OR', [ [ 'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'D_lai_d_pass_', ], [ 'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Maladie_infectieuse', ], [ 'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Obstacle_m_dico_l_gal', ], [ 'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'D_c_s_l_tranger', ], [ 'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Transfert_vers_autre_centre', ], [ 'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Etat_de_conservation_du_corps', ], ], ], [ 'contact_sub_type:name', '=', 'Proches', ], [ 'NOT', [ [ 'Contact_RelationshipCache_Contact_01.deceased_date', '=', 'ending.year', ], ], ], ], 'groupBy' => [], 'join' => [ [ 'Contact AS Contact_RelationshipCache_Contact_01', 'LEFT', 'RelationshipCache', [ 'id', '=', 'Contact_RelationshipCache_Contact_01.far_contact_id', ], [ 'OR', [ [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"est la personne de confiance de"', ], [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"Child of"', ], [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"Parent of"', ], [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"Spouse of"', ], [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"Sibling of"', ], [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"est la personne de confiance 2"', ], [ 'Contact_RelationshipCache_Contact_01.far_relation:name', '=', '"est la PAQPF"', ], ], ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Contacts_impliqu_s_dans_un_protocole_ex_vivo';                   //    %    Contacts ayant au moins une piece impliquée dans un protocole ex vivo
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Archives_dans_protocole_in_ni_ex_vivo';                   //    %    Contacts impliques dans aucun protocole
      update_search($searchname, $api_params, 'purge');

      $searchname = 'Personnels_tous';                   //    %    Liste les personnels des CDC ; utilisé pour crer un groupe pour filtrer les contacs dans utilisaito ncorps
      update_search($searchname, $api_params, 'purge');

      // Requetes utilisées pour le bilan annuel (aform activité)
      $searchname = 'corps_pr_sents_au_1_1_ann_e_en_cours';                   // % requete Bilan : corps présents au 1/1 année en cours      
      //$api_params = ['version' => 4, 'select' => [ 'contact_type:label', 'COUNT(contact_sub_type:label) AS COUNT_contact_sub_type_label', ], 'orderBy' => [], 'where' => [ [ 'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC', '<', 'this.year', ], [ 'OR', [ [ 'Devenir_du_corps.Date_de_sortie_d_finitive', 'IS EMPTY', ], [ 'Devenir_du_corps.Date_de_sortie_d_finitive', '>', 'previous.year', ], ], ], [ 'OR', [ [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', '=', FALSE, ], [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', 'IS EMPTY', ], ], ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'bilan_corps_presents_au_31_12_ann_e_A_1';                // % requete bilan : corps presents au 31/12 année A -1         
      //$api_params = [ 'version' => 4, 'select' => [ 'COUNT(last_name) AS COUNT_last_name', ], 'orderBy' => [], 'where' => [ [ 'deceased_date', 'IS NOT EMPTY', ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Pas_de_refus', ], [ 'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC', '<', 'this.year', ], [ 'OR', [ [ 'Devenir_du_corps.Date_de_sortie_d_finitive', 'IS EMPTY', ], [ 'Devenir_du_corps.Date_de_sortie_d_finitive', '>', 'previous.year', ], ], ], [ 'OR', [ [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', 'IS EMPTY', ], [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', '=', FALSE, ], ], ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [], ];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'bilan_corps_presents_31_12_ann_e_en_cours';             // bilan : nombre de corps presents 31/12 année en cours          
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(id) AS COUNT_id', ], 'orderBy' => [], 'where' => [ [ 'deceased_date', 'IS NOT EMPTY', ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Pas_de_refus', ], [ 'OR', [ [ 'Devenir_du_corps.Date_de_sortie_d_finitive', 'IS EMPTY', ], [ 'Devenir_du_corps.Date_de_sortie_d_finitive', '>', 'now', ], ], ], [ 'OR', [ [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', 'IS EMPTY', ], [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', '=', FALSE, ], ], ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'bilan_nombre_de_corps_pr_sents_1_1_ann_e_A_1';         // % bilan : nombre de corps présents 1/1 année A-1        
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(deceased_date) AS COUNT_deceased_date', ], 'orderBy' => [], 'where' => [ [ 'deceased_date', 'IS NOT EMPTY', ], [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', '!=', TRUE, ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '=', 'Pas_de_refus', ], [ 'OR', [ [ 'Devenir_du_corps.Date_de_sortie_d_finitive', 'IS EMPTY', ], [ 'Devenir_du_corps.Date_de_sortie_d_finitive', '=', 'this.year', ], [ 'Devenir_du_corps.Date_de_sortie_d_finitive', '=', 'previous.year', ], ], ], [ 'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC', '<', 'previous.year', ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'bilan_nombre_de_corps_recus_dans_l_ann_e_pr_cdente';   //  bilan : corps recus année A-1'         
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC) AS COUNT_Prise_en_charge_au_d_c_s_Date_d_arriv_e_au_CDC', ], 'orderBy' => [], 'where' => [ [ 'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC', '=', 'previous.year', ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Dons_ann_e_en_cours';                                  //  % Bilan : dons année en cours
      //$api_params = ['version' => 4, 'select' => [ 'receive_date', 'contact_id.sort_name', 'total_amount', 'financial_type_id:label', ], 'orderBy' => [], 'where' => [ [ 'receive_date', '=', 'this.year', ], ], 'groupBy' => [], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Inscriptions_annulations_ann_e_en_cours';              // Bilan : Inscriptions année en cours     
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(Promesse_de_don.Date_du_don) AS COUNT_Promesse_de_don_Date_du_don', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'OR', [ [ 'Promesse_de_don.Date_du_don', '=', 'this.year', ], ], ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_annulations_ann_e_A_1';                          // Bilan : annulations année A-1      
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(Annulation.Date_d_annulation) AS COUNT_Annulation_Date_d_annulation', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'OR', [ [ 'Annulation.Date_d_annulation', '=', 'previous.year', ], ], ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_c_r_monies_ann_e_en_cours';                      // % Bilan : cérémonies année en cours      
      //$api_params = ['version' => 4, 'select' => [ 'start_date', 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.street_address', 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.supplemental_address_1', 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.postal_code', 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.city', ], 'orderBy' => [], 'where' => [ [ 'start_date', '=', 'this.year', ], [ 'is_active', '=', TRUE, ], ], 'groupBy' => [], 'join' => [ [ 'LocBlock AS Event_LocBlock_loc_block_id_01', 'LEFT', [ 'loc_block_id', '=', 'Event_LocBlock_loc_block_id_01.id', ], ], [ 'Address AS Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01', 'LEFT', [ 'Event_LocBlock_loc_block_id_01.address_id', '=', 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.id', ], [ 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.is_primary', '=', TRUE, ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_c_r_monies_ann_e_A_1';                           // % Bilan : cérémonies année en cours      
      //$api_params = ['version' => 4, 'select' => [ 'start_date', 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.street_address', 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.supplemental_address_1', 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.postal_code', 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.city', ], 'orderBy' => [], 'where' => [ [ 'start_date', '=', 'previous.year', ], [ 'is_active', '=', TRUE, ], ], 'groupBy' => [], 'join' => [ [ 'LocBlock AS Event_LocBlock_loc_block_id_01', 'LEFT', [ 'loc_block_id', '=', 'Event_LocBlock_loc_block_id_01.id', ], ], [ 'Address AS Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01', 'LEFT', [ 'Event_LocBlock_loc_block_id_01.address_id', '=', 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.id', ], [ 'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.is_primary', '=', TRUE, ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_corps_recus_et_devenir_ann_e_en_cours';          // Bilan : corps sortis année en cours      
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(id) AS COUNT_id', 'Devenir_du_corps.devenir_effectif_du_corps:label', ], 'orderBy' => [], 'where' => [ [ 'Devenir_du_corps.Date_de_sortie_d_finitive', '=', 'this.year', ], [ 'Devenir_du_corps.Date_de_sortie_d_finitive', '<=', 'now', ], [ 'OR', [ [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', 'IS EMPTY', ], [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', '=', FALSE, ], ], ], ], 'groupBy' => [ 'Devenir_du_corps.devenir_effectif_du_corps', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_corps_sortis_ann_e_A_1';                         // % Bilan : corps sortis année A -1      
      //$api_params = ['version' => 4, 'select' => [ 'Devenir_du_corps.devenir_effectif_du_corps:label', 'COUNT(Devenir_du_corps.Date_op_rations_fun_raires) AS COUNT_Devenir_du_corps_Date_op_rations_fun_raires', ], 'orderBy' => [], 'where' => [ [ 'Devenir_du_corps.Date_de_sortie_d_finitive', '=', 'previous.year', ], [ 'OR', [ [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', 'IS EMPTY', ], [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', '=', FALSE, ], ], ], ], 'groupBy' => [ 'Devenir_du_corps.devenir_effectif_du_corps', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_demandeurs_information_ann_e_en_cours';          // Bilan : demandeurs information année en cours      
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(Demandeur_information.Date_d_envoi_d_informations) AS COUNT_Demandeur_information_Date_d_envoi_d_informations', ], 'orderBy' => [], 'where' => [ [ 'Demandeur_information.Date_d_envoi_d_informations', '=', 'this.year', ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_dons_ann_e_A_1';                                 // Bilan : dons année A -1       
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(receive_date) AS COUNT_receive_date', 'SUM(total_amount) AS SUM_total_amount', ], 'orderBy' => [], 'where' => [ [ 'receive_date', '=', 'previous.year', ], ], 'groupBy' => [ 'Contribution_Contact_contact_id_01.contact_type', ], 'join' => [ [ 'Contact AS Contribution_Contact_contact_id_01', 'LEFT', [ 'contact_id', '=', 'Contribution_Contact_contact_id_01.id', ], ], ], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_inscription_ann_e_A_1';                          //  Bilan : inscription année A-1     
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(Promesse_de_don.Date_du_don) AS COUNT_Promesse_de_don_Date_du_don', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'OR', [ [ 'Promesse_de_don.Date_du_don', '=', 'previous.year', ], ], ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_nombre_d_annualtion_ann_e_en_cours';             //  Bilan : nombre d'annulations année en cours     
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(Annulation.Date_d_annulation) AS COUNT_Annulation_Date_d_annulation', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'OR', [ [ 'Annulation.Date_d_annulation', '=', 'this.year', ], ], ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_nombre_de_corps_rec_us_ann_e_en_cours';          //  Bilan : nombre de corps reçus année en cours     
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(contact_sub_type:label) AS COUNT_contact_sub_type_label', ], 'orderBy' => [], 'where' => [ [ 'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC', '=', 'this.year', ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'corps_pr_sents_au_1_1_ann_e_en_cours';             // bilan : nombre de corps presents 1/1 année en cours          
      //$api_params = [ 'version' => 4, 'select' => [ 'contact_type:label', 'COUNT(contact_sub_type:label) AS COUNT_contact_sub_type_label', 'id', ], 'orderBy' => [], 'where' => [ [ 'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC', '<', 'this.year', ], [ 'OR', [ [ 'Devenir_du_corps.Date_de_sortie_d_finitive', 'IS EMPTY', ], [ 'Devenir_du_corps.Date_de_sortie_d_finitive', '>', 'previous.year', ], ], ], [ 'OR', [ [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', '=', FALSE, ], [ 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires', 'IS EMPTY', ], ], ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [], ];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_nombre_de_demandeurs_d_information';             // Bilan : nombre de demandeurs d'information année A-1      
      //$api_params = ['version' => 4, 'select' => [ 'COUNT(Demandeur_information.Date_d_envoi_d_informations) AS COUNT_Demandeur_information_Date_d_envoi_d_informations', ], 'orderBy' => [], 'where' => [ [ 'Demandeur_information.Date_d_envoi_d_informations', '=', 'previous.year', ], ], 'groupBy' => [ 'contact_type', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Personnels_id3q';                                           //  Personnels     
      //$api_params = ['version' => 4, 'select' => [ 'id', 'display_name', 'employer_id.display_name', 'job_title', 'infos_personnel.Date_debut_fonctions', 'infos_personnel.Date_fin_fonctions', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Personnel', ], [ 'NOT', [ [ 'infos_personnel.Date_fin_fonctions', '=', 'ending_5.year', ], ], ], ], 'groupBy' => [], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');

      $searchname = 'Bilan_refus_ou_non_reception_corps_A_1';               // % Corps refusés ou non recus année A -1 
      //$api_params = ['version' => 4, 'select' => [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:label', 'COUNT(last_name) AS COUNT_last_name', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'deceased_date', '=', 'previous.year', ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '!=', 'Pas_de_refus', ], ], 'groupBy' => [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');
    
      $searchname = 'Bilan_refus_ou_non_reception_corps';                   //    %    Corps refusés ou non recus année en cours
      //$api_params = ['version' => 4, 'select' => [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:label', 'COUNT(last_name) AS COUNT_last_name', ], 'orderBy' => [], 'where' => [ [ 'contact_sub_type:name', 'CONTAINS', 'Donateur', ], [ 'deceased_date', '=', 'this.year', ], [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name', '!=', 'Pas_de_refus', ], ], 'groupBy' => [ 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps', ], 'join' => [], 'having' => [],];
      update_search($searchname, $api_params, 'bilan');


    
    // fin de Modifie les requetes qui ne sont pas correctement importées

  // création des layouts

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
                  'showTitle' => FALSE,
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
              'id' => 'custom_3',
              'is_active' => 1,
              'icon' => 'crm-i fa-ambulance',
            ],
            [
              'id' => 'custom_12',
              'is_active' => 1,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'custom_10',
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
              'id' => 'custom_3',
              'is_active' => 0,
              'icon' => 'crm-i fa-ambulance',
            ],
            [
              'id' => 'custom_12',
              'is_active' => 0,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'custom_10',
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
                  'name' => 'profile.Fonction_18',
                  'title' => E::ts('Fonction'),
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
              'id' => 'custom_3',
              'is_active' => 0,
              'icon' => 'crm-i fa-ambulance',
            ],
            [
              'id' => 'custom_12',
              'is_active' => 0,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'custom_10',
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
              'id' => 'custom_3',
              'is_active' => 0,
              'icon' => 'crm-i fa-ambulance',
            ],
            [
              'id' => 'custom_12',
              'is_active' => 0,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'custom_10',
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
              'id' => 'custom_3',
              'is_active' => 0,
              'icon' => 'crm-i fa-ambulance',
            ],
            [
              'id' => 'custom_12',
              'is_active' => 0,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'custom_10',
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
              'id' => 'custom_3',
              'is_active' => 0,
              'icon' => 'crm-i fa-ambulance',
            ],
            [
              'id' => 'custom_12',
              'is_active' => 0,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'custom_10',
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
                  'name' => 'custom.CDC_Administration',
                  'title' => E::ts('CDC Administration'),
                  'collapsible' => FALSE,
                  'collapsed' => FALSE,
                  'showTitle' => FALSE,
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
              'id' => 'custom_3',
              'is_active' => 0,
              'icon' => 'crm-i fa-ambulance',
            ],
            [
              'id' => 'custom_12',
              'is_active' => 0,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'custom_10',
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
          'contact_sub_type' => [
            'Mairies',
          ],
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
              'id' => 'custom_3',
              'is_active' => 0,
              'icon' => 'crm-i fa-ambulance',
            ],
            [
              'id' => 'custom_12',
              'is_active' => 0,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'custom_10',
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
          'contact_sub_type' => NULL,
          'groups' => NULL,
          'weight' => 8,
          'blocks' => [
            [
              [
                [
                  'name' => 'profile.Animal',
                  'title' => E::ts('Animal'),
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
              'id' => 'custom_3',
              'is_active' => 0,
              'icon' => 'crm-i fa-ambulance',
            ],
            [
              'id' => 'custom_12',
              'is_active' => 1,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'custom_10',
              'is_active' => 1,
              'icon' => 'crm-i fa-flask',
            ],
          ],
          'settings' => [
            'sub_type_operator' => 'OR',
          ],
        ],
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

  // Changement des icones de menus
    echo PHP_EOL."  -Changement des icones de menus".PHP_EOL;
      //change_icon('Contacts', 'crm-i fa-address-book-o');
      //change_icon('Search', 'crm-i fa-search');
      change_icon('Contributions','crm-i fa-money-bill-1');
      change_icon('Events','crm-i fa-users');
      //change_icon('Mailings','crm-i fa-envelope-o');
      //change_icon('Report','crm-i fa-bar-chart');
      //change_icon('Support','crm-i fa-life-ring');

   // Fin du Changement des icones de menus


  // Modifie l'utilisation des profils créées par le mgd files et de menus qui permettent d'y accéder
    echo "  -modification de l'utilisation des profils".PHP_EOL;
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
      
      $url = admin_url("admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid=")."{contact.id}";  // url à charger apres creation du contact utilisant le profil 
      $profiles_to_update = ['Mairie','Lieu_de_stockage','Centre_d_accueil_des_corps','Personnel_de_centre_de_don_de_corps','Inscription_proche_donateur_27', 'Demandeur_information_22', 'Inscription_proche_donateur_14', 'name_and_address','Inscription_anat_compar_e'];
      // Liste de profils à associer à un role (ceux utilisés pour creation contacts) name_and_address = ionscription donneur ; Inscription_proche_donateur_27 : pompes
      
      foreach ($profiles_to_update as $profile_to_update) {
          $position = array_search($profile_to_update, $profile_names);
          if ($position !== false) {                                          // Si le profil est déja créé 
              echo $profile_to_update." : ";
      
              $to_create =  [                                                 // modifie l'URL à afficher apres la creation (post url) par un profil
                  'entity' => 'UFGroup',
                  'values' => [
                      'post_url' => $url,
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
                echo $profile_to_update." : Profil non trouvé non trouvé ////////.".PHP_EOL;
            }
      }
   // fin de Modifie l'utilisation des profils créées par le mgd files

  /// Modification des menus de navigation liés aux profil de création de contacts
    echo "  -modification des rmenus de navigation liés aux proils".PHP_EOL;
    $url_menus_to_change =[                             // Profil name, parent_id:name, name du menu navigation
      ['name_and_address', 'ContactsDDC','New DonateurDDC'],  //// MODIFIE
      ['Inscription_proche_donateur_14', 'ContactsDDC','Ajouter proche donateurDDC'],///MODIFIE
      ['Demandeur_information_22', 'ContactsDDC','New Demandeur_d_informationDDC'],///MODIFIE
      ['Inscription_proche_donateur_27', 'Pompes funebresDDC','New Pompes'],  // 'Inscription_proche_donateur_27' correpond au profil pompes
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

          echo $url_menu_to_change[1]." / ".$url_menu_to_change[2]." / ".$url_menu_to_change[0]." : ";
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

          echo "***** Le profil ".$url_menu_to_change[0]." n'existe pas *****".PHP_EOL;
      }
    }
   /// Fin de Modification des menus de navigation liés aux profil de création de contacts

  /// Création des messages templates (hors cenx crees par les rules)
   echo "  -Création des templates emails".PHP_EOL;
      $to_create =  [       
        'entity' => 'MessageTemplate',
        'values' => [
          'msg_title' => '01 - Préinscription: envoi informations MAIL',
          'msg_subject' => 'Envoi information inscription par mail',
          'msg_text' => NULL,
          'msg_html' => "<p>{contact.postal_greeting},</p>\r\n\r\n<p>Je vous remercie de votre volonté de donner votre corps à des fins d’enseignement et de recherche et vous engage à consulter notre site web : <strong><a href=\"https://dons-corps.univ-tours.fr/medias/fichier/guide-information-dec2023-avectours_1702540511292-pdf?ID_FICHE=402072&amp;INLINE=FALSE\">{domain.description}</a>.</strong></p>\r\n\r\n<p>Vous y trouverez de nombreuses informations et des réponses aux principales questions que vous pourriez vous poser.</p>\r\n\r\n<p>Vous pourrez télécharger les documents nécessaires à votre inscription :<br />\r\n<strong>1) le guide d’information officiel : <a href=\"https://dons-corps.univ-tours.fr/medias/fichier/guide-information-juil2024-avectours_1733124203133-pdf?ID_FICHE=402072&amp;INLINE=FALSE\">Téléchargez le guide</a></strong></p>\r\n\r\n<p><strong>2)&nbsp; le formulaire de promesse de don à nous retourner par courrier si vous poursuivez votre démarche : <a href=\"https://dons-corps.univ-tours.fr/medias/fichier/declaration-consentement-don-corps-avecrgpd-2023-09-22_1695360636047-pdf?ID_FICHE=402072&amp;INLINE=FALSE\">Téléchargez le dossier d'inscription</a></strong></p>\r\n\r\n<p>N’hésitez pas à nous contacter si vous avez besoin d’information complémentaire</p>\r\n\r\n<p>Je vous remercie à nouveau de votre intérêt pour le don du corps et vous prie d'agréer, {contact.postal_greeting}, l'expression de ma parfaite considération.</p>",
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
          'msg_title' => '10 - Inscription : Mail accusé réception demande inscription',
          'msg_subject' => 'Confirmation inscription Centre de don du corps',
          'msg_text' => NULL,
          'msg_html' => "<p>{contact.postal_greeting},</p>\r\n<!-- --CIVILITE AUTOMATIQUE DETERMINEE A PARTIR DU GENRE --->\r\n\r\n<p>Nous vous remercions de votre demande d'inscription à notre centre de don du corps, dont nous accusons réception.<br />\r\nVotre numéro d'inscription est le {Tokens_for_contact_Champs_de_fu.Promesse_de_don.N_de_don}.<br />\r\nVotre carte définitive et les documents relatifs à votre inscription vont vous parvenir par courrier dans les semaines qui viennent.<br />\r\nNous vous prions de bien vouloir excuser ce délai lié à un grand nombre de demande. Votre inscription est effective à partir d'aujourd'hui.</p>\r\n\r\n<p>Nous restons à votre disposition pour tout renseignement complémentaire et vous prions d'agréer, {contact.postal_greeting}, l'expression de notre parfaite considération.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>{domain.supplemental_address_3}<br />\r\nCentre de don du corps de {domain.city}</p>",
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
          'msg_title' => '300 Cérémonie invitation (email)',
          'msg_subject' => "{domain.name} - cérémonie d'hommage {event.start_date} à {event.start_date|crmDate:\"Time\"}",
          'msg_text' => NULL,
          'msg_html' => "<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">{contact.postal_greeting_display}</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous avons l'honneur de vous inviter à la cérémonie organisée par le Centre d'accueil des corps de {domain.city} en l'honneur des donneurs et de leurs proches.<br />\r\nElle aura lieu le<strong> {event.start_date} à {event.start_date|crmDate:\"Time\"} au&nbsp;</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><strong>{event.location}</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Cette manifestation, d'une durée d'environ deux heures, sera l'occasion de nous recueillir en mémoire des personnes qui, comme votre proche, ont donné récemment leur corps à des fins d'enseignement médical et de recherche.<br />\r\nMerci de nous indiquer par retour de mail si vous souhaitez y participer et de nous communiquer le nombre de personnes présentes.</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous restons à votre disposition pour tout renseignement complémentaire et vous prions d'agréer, {contact.postal_greeting_display}, l'expression de notre parfaite considération.</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"2\">{domain.supplemental_address_3}<br />\r\n{domain.supplemental_address_2}<br />\r\ndu Centre de Don du Corps de {domain.city}</font></p>",
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
          'msg_title' => '310 Cérémonie confirmation  (email)',
          'msg_subject' => "{domain.name} - cérémonie d'hommage {event.start_date} à {event.start_date|crmDate:\"Time\"}",
          'msg_text' => NULL,
          'msg_html' => "<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">{contact.postal_greeting_display}</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous vous confirmons votre inscription à la cérémonie d'hommage aux donneurs et à leurs proches qui débutera le<strong> {event.start_date} à {event.start_date|crmDate:\"Time\"} au&nbsp;</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><strong>{event.location}</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Merci de vous présenter au funérarium 15 minutes avant le début de la cérémonie et de respecter le nombre de personnes que vous nous avez communiqué.</font></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous restons à votre disposition pour tout renseignement complémentaire et vous prions d'agréer, {contact.postal_greeting_display}, l'expression de notre parfaite considération.</font></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"2\">{domain.supplemental_address_3}<br />\r\n{domain.supplemental_address_2}<br />\r\ndu Centre de Don du Corps de {domain.city}</font></font></p>",
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
          'msg_title' => '320 Cérémonie non inscription  (email)',
          'msg_subject' => "{domain.name} - cérémonie d'hommage {event.start_date} à {event.start_date|crmDate:\"Time\"}",
          'msg_text' => NULL,
          'msg_html' => "<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">{contact.postal_greeting_display}</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous avons bien noté que vous ne participerez pas à la cérémonie d'hommage aux donneurs et à leurs proches du<strong> {event.start_date}.</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous restons à votre disposition pour tout renseignement complémentaire et vous prions d'agréer, {contact.postal_greeting_display}, l'expression de notre parfaite considération.</font></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"2\">{domain.supplemental_address_3}<br />\r\n{domain.supplemental_address_2}<br />\r\ndu Centre de Don du Corps de {domain.city}</font></font></p>",
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


  // création des templates de cérmonies ; les statuts et les roles des participants doivent etre crées en amont
  echo "  -Création des Templates de cérémonie".PHP_EOL;
        
  $to_create =  [        //Corriger_civililite : Déclaration de l'Action
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

// Inactivation du message template de confirmation d'inscription ; si non supprimé un message est envoyé à l'inscription prevenant d'une liste d attente
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

// installation des regles de message pour les cérémonies
  // message '300 Cérémonie invitation (email)' pour statut 'On waitlist' (Invité)
  $messageTemplates = civicrm_api4('MessageTemplate', 'get', [                /// récupère l'id du MessageTemplate
    'select' => [
      'id',
    ],
    'where' => [
      ['msg_title', '=', '300 Cérémonie invitation (email)'],
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
    echo "Message template, statut, ou évenement manquent".PHP_EOL;
  }
  create_entity($to_create);


  // message '310 Cérémonie confirmation  (email)' pour statut Registered (Confirmé)
   $messageTemplates = civicrm_api4('MessageTemplate', 'get', [                /// récupère l'id du MessageTemplate
    'select' => [
      'id',
    ],
    'where' => [
      ['msg_title', '=', '310 Cérémonie confirmation  (email)'],
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
    echo "Message template, statut, ou évenement manquent".PHP_EOL;
  }
  create_entity($to_create);


// message '320 Cérémonie non inscription  (email)' pour statut Cancelled (Annulé)
$messageTemplates = civicrm_api4('MessageTemplate', 'get', [                /// récupère l'id du MessageTemplate
  'select' => [
    'id',
  ],
  'where' => [
    ['msg_title', '=', '320 Cérémonie non inscription  (email)'],
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
  echo "Message template, statut, ou évenement manquent".PHP_EOL;
}
create_entity($to_create);






  // Modification des filtres de profil

}   // fin Implements hook_civicrm_postInstall().

/*****************************************************************************
 * Implements hook_civicrm_uninstall().
 *
 * @link
 *****************************************************************************/

function don_corps_civicrm_uninstall() {


  //CRM_Core_Session::setStatus('Desinstallation don corps', 'Info', 'info');
  //_don_corps_civix_civicrm_uninstall();



  echo "  -Activation des menus par défaut".PHP_EOL;
    activate_menu('Contacts');
    activate_menu('Search');
    activate_menu('Contributions');
    activate_menu('Events');
    activate_menu('Mailings');
    activate_menu('Reports');
    activate_menu('Support');

 
}

