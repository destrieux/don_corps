<?php
eval(`cv php:boot`);

// 

   // $contactId = $triggerData->getContactId();
       
function create_entity2(){
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
        break;
  
        case 'CiviRulesRuleCondition':                        // CiviRulesRuleCondition
            $check_entity = civicrm_api4($entity, 'get', [    
              'where' => [
                ['rule_id.name', '=', $values['rule_id.name']],
                ['condition_id.name', '=', $values['condition_id.name']],
              ],
              'checkPermissions' => FALSE,
            ]);
        break;
  
        case 'MessageTemplate':                                  // rmessage template
            $check_entity = civicrm_api4($entity, 'get', [   
              'where' => [
                ['msg_title', '=', $values['msg_title']],
              ],
              'checkPermissions' => FALSE,
            ]);
        break;

        case 'UFJoin':                                             // UF join (utilisation des profiles)
            $check_entity = civicrm_api4($entity, 'get', [    
                'where' => [
                    ['uf_group_id:name', "=", $values['uf_group_id:name']],
                    [ 'module', "=", $values['module']],
                ],
                'checkPermissions' => FALSE,
            ]);
        break;

        case 'Navigation':                                             // Menus e navigation
            $check_entity = civicrm_api4($entity, 'get', [    
                'where' => [
                    ['parent_id:name', "=", $values['parent_id:name']],
                    [ 'name', "=", $values['name']],
                ],
                'checkPermissions' => FALSE,
            ]);
        break;

        default:
          $check_entity = civicrm_api4($entity, 'get', [    // rule, action, condition,OptionValue
            'where' => [
            ['name', '=', $values['name']],
            ],
            'checkPermissions' => FALSE,
          ]);
        break;
    }
  
    if(isset($check_entity[0])){            // si l'entité existe on l'update
      echo "entité ".$entity." existe - update".PHP_EOL;
      
      $results = civicrm_api4($entity, 'update', [
        'values' => $values,
        'where' => [
          ['id', '=', $check_entity[0]['id']],
        ],
        'checkPermissions' => FALSE,
      ]);
  
    }else{                                  // si l'entité n'existe pas, on la crée
      echo "entité ".$entity." n'existe pas - creation".PHP_EOL;
      $results = civicrm_api4($entity, 'create', [
        'values' => $values,
        'checkPermissions' => FALSE,
      ]);
  
    }
   return $results[0]['id']; // retourne l'id de l'entité créée
  }
  

// Modifie l'utilisation des profils créées par le mgd files

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
$profiles_to_update = ['Mairie','Lieu_de_stockage','Centre_d_accueil_des_corps','Personnel_de_centre_de_don_de_corps','Inscription_proche_donateur_27', 'Demandeur_information_22', 'Inscription_proche_donateur_14', 'name_and_address'];
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
          create_entity2($to_create);  // create ou update UFJOIN

        $to_create =  [                                                 // ajoute à chacun de ces profils l'utilisaiton "Profile" = "Formulaire ou Liste à afficher"
          'entity' => 'UFJoin',
          'values' => [
              'uf_group_id:name' => $profile_to_update,
              'module' => 'Profile',
              'is_active' => TRUE,
              'module_data' => NULL,
          ],
        ];
        create_entity2($to_create);                                     // create ou update UFJOIN   
        
        
      
      } else {
          echo $profile_to_update." : Profil non trouvé non trouvé ////////.".PHP_EOL;
      }

}

/// Modification des menus de nevaigation liés aux profil de création de contacts

    $url_menus_to_change =[                             // Profil name, parent_id:name, name du menu navigation
        ['name_and_address', 'Contacts','New Donateur'],
        ['Inscription_proche_donateur_14', 'Contacts','Ajouter proche donateur'],
        ['Demandeur_information_22', 'Contacts','New Demandeur_d_information'],
        ['Inscription_proche_donateur_27', 'Pompes funebres','New Pompes'],  // 'Inscription_proche_donateur_27' correpond au profil pompes
        ['Mairie', 'Mairies','New Mairies'],
        ['Personnel_de_centre_de_don_de_corps', 'Centres de don du corps','New Personnel'],
        ['Centre_d_accueil_des_corps', 'Centres de don du corps','New CDC'],
        ['Lieu_de_stockage', 'Pièces anatomiques','New Emprunteur'],
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
            print_r($to_create);
            create_entity2($to_create);                                     // create ou update navigation menu  


        }else {

            echo "***** Le profil ".$url_menu_to_change[0]." n'existe pas *****".PHP_EOL;
        }
    }
/// Fin de Modification des menus de nevaigation liés aux profil de création de contacts


