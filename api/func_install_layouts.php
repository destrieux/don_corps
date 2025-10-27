<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;



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

function install_layouts2 () {

    $layouts = func_get_arg(0);


  
    foreach ($layouts as $params) {           // pour chacun des Layouts
  
      // Vérification que tous les profils ont bien été installés
  
      unset($params['id']);                   // supprime la clé id qui est générée par l'API



      $profs=$params['blocks'][0];            // array qui definit le blc de profil
     // print_r($profs);
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
                  if ($profiles[0]['id']!=0){
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
  
                    if ($uFJoins[0]['id']!=0){  // UFJoin existe : on l'update
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
  
                    if ($uFJoins[0]['id']!=0){  // UFJoin existe : on l'update
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
  
  
  
      if ($contactLayouts[0]['id']==0){         // si le layout n'existe pas on le crée
        $results = civicrm_api4('ContactLayout', 'create', [
        'values' => $params,
        'checkPermissions' => FALSE,
        ]);
    //    echo "\n"."############# Creation du Layout ".$contactLayouts[0]['label']."\n";
        CRM_Core_Session::setStatus('Creation du Layout '.$contactLayouts[0]['label'], 'Succès', 'success');
  
      } else {                                 // si le layout existe on l'update (le premier trouvé avec ce label)
        $results = civicrm_api4('ContactLayout', 'update', [
        'values' => $params,
        'where' => [
          ['id', '=', $contactLayouts[0]['id']],
        ],
        'checkPermissions' => FALSE,
      ]);
  
    //    echo "\n"."############# Update du Layout : ".$contactLayouts[0]['label']."\n";
        CRM_Core_Session::setStatus('MAJ du Layout '.$contactLayouts[0]['label'], 'Succès', 'success');
  
      }
  
      }else{
    //    echo "\n"."Installez les composants qui manquent puis relancez la commande"."\n";
        CRM_Core_Session::setStatus('Installez les composants qui manquent puis relancez la commande : cv -vvv ext:enable don_corps', 'Erreur', 'error');
  
        exit;
      }
    }     // fin de la boucle pour chacun des Layouts
  
  } // fin de la définition de la fonction  install_layouts





// création des layouts

    /// DEFINITION DE LA VARIABLE $layout QUI COMPREND LES PARAMETRES DE TOUS LES LAYOUTS
    ///
    /// la variable layout doit être récupérée depuis un site maitre via Support>Developperu>explorateur APIv4
    /// Entité : ContactLayout
    /// Action : get
    /// Récuperer la variable dans la boite résltats, au format php (cliquer sur voir en JSON -> voir en PHP)
    /// copier coller la sortie et ramplacer return par $layouts
    ///

    echo "  - INstallation des layouts".PHP_EOL;

    $layouts =  [
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
                  'collapsible' => TRUE,
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
                [
                  'name' => 'custom.Promesse_de_don',
                  'title' => E::ts('Promesse de don'),
                  'collapsible' => TRUE,
                  'collapsed' => FALSE,
                  'showTitle' => FALSE,
                ],
                [
                  'name' => 'custom.Annulation',
                  'title' => E::ts('Annulation'),
                  'collapsible' => TRUE,
                  'collapsed' => FALSE,
                ],
              ],
              [
                [
                  'name' => 'custom.Prise_en_charge_au_d_c_s',
                  'title' => E::ts('Prise en charge au décès'),
                  'collapsible' => TRUE,
                  'collapsed' => FALSE,
                  'showTitle' => FALSE,
                ],
                [
                  'name' => 'custom.Transfert_vers_autre_centre',
                  'title' => E::ts('En cas de transfert vers un autre centre'),
                  'collapsible' => TRUE,
                  'collapsed' => FALSE,
                  'showTitle' => FALSE,
                ],
                [
                  'name' => 'profile.CESP_29',
                  'title' => E::ts('CESP'),
                  'collapsible' => TRUE,
                ],
                [
                  'name' => 'profile.Op_rations_fun_raires_r_alis_es_30',
                  'title' => E::ts('Opérations funéraires réalisées'),
                  'collapsible' => TRUE,
                ],
                [
                  'name' => 'profile.Restitution_28',
                  'title' => E::ts('Restitution'),
                  'collapsible' => TRUE,
                ],
              ],
            ],
          ],
          'tabs' => [
            [
              'id' => 'summary',
              'is_active' => TRUE,
            ],
            [
              'id' => 'contact_documents',
              'is_active' => TRUE,
            ],
            [
              'id' => 'contribute',
              'is_active' => TRUE,
            ],
            [
              'id' => 'participant',
              'is_active' => TRUE,
            ],
            [
              'id' => 'mailing',
              'is_active' => TRUE,
            ],
            [
              'id' => 'activity',
              'is_active' => TRUE,
            ],
            [
              'id' => 'rel',
              'is_active' => TRUE,
            ],
            [
              'id' => 'custom_35',
              'is_active' => TRUE,
              'icon' => 'crm-i fa-flask',
            ],
            [
              'id' => 'custom_37',
              'is_active' => TRUE,
              'icon' => 'crm-i fa-ambulance',
            ],
            [
              'id' => 'custom_33',
              'is_active' => TRUE,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'note',
              'is_active' => TRUE,
            ],
            [
              'id' => 'tag',
              'is_active' => TRUE,
            ],
            [
              'id' => 'group',
              'is_active' => TRUE,
            ],
            [
              'id' => 'log',
              'is_active' => TRUE,
            ],
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
              'is_active' => TRUE,
            ],
            [
              'id' => 'contact_documents',
              'is_active' => TRUE,
            ],
            [
              'id' => 'contribute',
              'is_active' => TRUE,
            ],
            [
              'id' => 'participant',
              'is_active' => TRUE,
            ],
            [
              'id' => 'mailing',
              'is_active' => TRUE,
            ],
            [
              'id' => 'activity',
              'is_active' => TRUE,
            ],
            [
              'id' => 'rel',
              'is_active' => TRUE,
            ],
            [
              'id' => 'group',
              'is_active' => TRUE,
            ],
            [
              'id' => 'note',
              'is_active' => TRUE,
            ],
            [
              'id' => 'tag',
              'is_active' => TRUE,
            ],
            [
              'id' => 'log',
              'is_active' => TRUE,
            ],
            [
              'id' => 'custom_35',
              'is_active' => FALSE,
              'icon' => 'crm-i fa-linkedin-square',
            ],
            [
              'id' => 'custom_33',
              'is_active' => FALSE,
              'icon' => 'crm-i fa-sign-language',
            ],
            [
              'id' => 'custom_37',
              'is_active' => FALSE,
            ],
          ],
        ],
        [
          'id' => 3,
          'label' => E::ts('Personnel'),
          'contact_type' => 'Individual',
          'contact_sub_type' => NULL,
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
              'is_active' => TRUE,
            ],
            [
              'id' => 'contribute',
              'is_active' => FALSE,
            ],
            [
              'id' => 'participant',
              'is_active' => TRUE,
            ],
            [
              'id' => 'mailing',
              'is_active' => TRUE,
            ],
            [
              'id' => 'activity',
              'is_active' => TRUE,
            ],
            [
              'id' => 'rel',
              'is_active' => TRUE,
            ],
            [
              'id' => 'group',
              'is_active' => TRUE,
            ],
            [
              'id' => 'note',
              'is_active' => TRUE,
            ],
            [
              'id' => 'tag',
              'is_active' => TRUE,
            ],
            [
              'id' => 'log',
              'is_active' => TRUE,
            ],
            [
              'id' => 'contact_documents',
              'is_active' => TRUE,
            ],
            [
              'id' => 'custom_33',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_35',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_37',
              'is_active' => FALSE,
            ],
          ],
        ],
        [
          'id' => 4,
          'label' => E::ts('Organisation'),
          'contact_type' => 'Organization',
          'contact_sub_type' => NULL,
          'groups' => NULL,
          'weight' => 8,
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
              'is_active' => TRUE,
            ],
            [
              'id' => 'contact_documents',
              'is_active' => TRUE,
            ],
            [
              'id' => 'contribute',
              'is_active' => FALSE,
            ],
            [
              'id' => 'participant',
              'is_active' => TRUE,
            ],
            [
              'id' => 'mailing',
              'is_active' => FALSE,
            ],
            [
              'id' => 'activity',
              'is_active' => FALSE,
            ],
            [
              'id' => 'rel',
              'is_active' => TRUE,
            ],
            [
              'id' => 'group',
              'is_active' => TRUE,
            ],
            [
              'id' => 'note',
              'is_active' => TRUE,
            ],
            [
              'id' => 'tag',
              'is_active' => TRUE,
            ],
            [
              'id' => 'log',
              'is_active' => TRUE,
            ],
            [
              'id' => 'custom_33',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_35',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_37',
              'is_active' => FALSE,
            ],
          ],
        ],
        [
          'id' => 6,
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
              'is_active' => TRUE,
            ],
            [
              'id' => 'contact_documents',
              'is_active' => FALSE,
            ],
            [
              'id' => 'contribute',
              'is_active' => FALSE,
            ],
            [
              'id' => 'participant',
              'is_active' => FALSE,
            ],
            [
              'id' => 'mailing',
              'is_active' => TRUE,
            ],
            [
              'id' => 'activity',
              'is_active' => TRUE,
            ],
            [
              'id' => 'rel',
              'is_active' => FALSE,
            ],
            [
              'id' => 'group',
              'is_active' => TRUE,
            ],
            [
              'id' => 'note',
              'is_active' => TRUE,
            ],
            [
              'id' => 'tag',
              'is_active' => TRUE,
            ],
            [
              'id' => 'log',
              'is_active' => TRUE,
            ],
            [
              'id' => 'custom_33',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_35',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_37',
              'is_active' => FALSE,
            ],
          ],
        ],
        [
          'id' => 7,
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
                  'name' => 'profile.Type_de_contact_23',
                  'title' => E::ts('Type de contact'),
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
              'is_active' => TRUE,
            ],
            [
              'id' => 'contact_documents',
              'is_active' => TRUE,
            ],
            [
              'id' => 'contribute',
              'is_active' => FALSE,
            ],
            [
              'id' => 'participant',
              'is_active' => FALSE,
            ],
            [
              'id' => 'mailing',
              'is_active' => FALSE,
            ],
            [
              'id' => 'activity',
              'is_active' => FALSE,
            ],
            [
              'id' => 'rel',
              'is_active' => FALSE,
            ],
            [
              'id' => 'group',
              'is_active' => TRUE,
            ],
            [
              'id' => 'note',
              'is_active' => TRUE,
            ],
            [
              'id' => 'tag',
              'is_active' => FALSE,
            ],
            [
              'id' => 'log',
              'is_active' => TRUE,
            ],
            [
              'id' => 'custom_33',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_35',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_37',
              'is_active' => FALSE,
            ],
          ],
        ],
        [
          'id' => 8,
          'label' => E::ts('Centre de don<br>'),
          'contact_type' => 'Organization',
          'contact_sub_type' => [
            'CDC',
          ],
          'groups' => NULL,
          'weight' => 7,
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
              'is_active' => TRUE,
            ],
            [
              'id' => 'contact_documents',
              'is_active' => TRUE,
            ],
            [
              'id' => 'contribute',
              'is_active' => FALSE,
            ],
            [
              'id' => 'participant',
              'is_active' => TRUE,
            ],
            [
              'id' => 'mailing',
              'is_active' => FALSE,
            ],
            [
              'id' => 'activity',
              'is_active' => FALSE,
            ],
            [
              'id' => 'rel',
              'is_active' => FALSE,
            ],
            [
              'id' => 'group',
              'is_active' => TRUE,
            ],
            [
              'id' => 'note',
              'is_active' => TRUE,
            ],
            [
              'id' => 'tag',
              'is_active' => TRUE,
            ],
            [
              'id' => 'log',
              'is_active' => TRUE,
            ],
            [
              'id' => 'custom_33',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_35',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_37',
              'is_active' => FALSE,
            ],
          ],
        ],
        [
          'id' => 12,
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
              'is_active' => TRUE,
            ],
            [
              'id' => 'contact_documents',
              'is_active' => TRUE,
            ],
            [
              'id' => 'contribute',
              'is_active' => FALSE,
            ],
            [
              'id' => 'participant',
              'is_active' => FALSE,
            ],
            [
              'id' => 'mailing',
              'is_active' => FALSE,
            ],
            [
              'id' => 'activity',
              'is_active' => FALSE,
            ],
            [
              'id' => 'rel',
              'is_active' => FALSE,
            ],
            [
              'id' => 'group',
              'is_active' => TRUE,
            ],
            [
              'id' => 'note',
              'is_active' => TRUE,
            ],
            [
              'id' => 'tag',
              'is_active' => TRUE,
            ],
            [
              'id' => 'log',
              'is_active' => TRUE,
            ],
            [
              'id' => 'custom_33',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_35',
              'is_active' => FALSE,
            ],
            [
              'id' => 'custom_37',
              'is_active' => FALSE,
            ],
          ],
        ],
      ];
  
    /// FIN DE LA DEFINITION DE LA VARIABLE LAYOUT QUI COMPRENT LES PARAMETRES DE TOUS LES LAYOUTS


    install_layouts2 ($layouts);