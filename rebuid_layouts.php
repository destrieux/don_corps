<?php

eval(`cv php:boot`);


echo "Modification des profils personnalisés".PHP_EOL;
    #####
    # Lors de la création de profils de formulaires ou de custom layouts, des profils personnalisés sont générés
    # ils regroupent des champs personnalisés qui sont identifiés par custom_XX avec XX l'id du customfield correspondant
    # Lors d'une nouvelle installation les id des custom fields peuvent varier ce qui induit une incohérence
    # Ici on utilise un tableau donnant la correspondance entre le nom original du champ personnlisé (uf id) 
    # et son nom ; cela permt de modifier celui-ci dans la nouvelle installation
    $toimport_file = 'managed/ufnameconversion.txt';                     // nom du fichier à importer sans le suffixe
    $json = file_get_contents($toimport_file);
    $convert = json_decode($json, true);

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
    //print_r ($convert);

    $labels_table = array_column($convert, 'label');
    $names_table = array_column($convert, 'field_name:name');
    $customs_table = array_column($convert, 'field_name');

    print_r ($labels_table);

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

      //echo "il y a des uffields ".PHP_EOL;
      
      foreach ($uFFields as $uFField){
        //echo PHP_EOL.PHP_EOL;
        //print_r($uFField);
        if (isset($uFField['label'])) {           // si un label existe, on récupère la valeur de field_name_name 
          //echo 'un label existe'.PHP_EOL;         // depuis la table de conversion en utilisant le label comme critere de concordance
          $label=$uFField['label'];
          $key = array_search($label, $labels_table); 
          //var_dump($key);
          if ($key){
                $name=$convert[$key]['field_name:name'];
          //echo "on recupere dans convert la valeur field_name : ".$name.' pour label :'.$label.PHP_EOL;
          } else {
            echo "ERREUR : pas de label ".$label.' dans le fichier de correspondance : '.$toimport_file.PHP_EOL;
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
                echo "ERREUR : pas de name ".$name.'dans le fichier de correspondance : '.$toimport_file.PHP_EOL;
                exit;
              }
            } else {                                          // pas de field _name:name de type customgroup.customfield 
              $key = array_search($name, $customs_table);

              if ($key){
                $label=$convert[$key]['field_name:label'];      // on récupère label et name depuis table correspondance
                $name=$convert[$key]['field_name:name'];        // en utilisant le custom_name comme critere de concordance
              } else {
                echo "ERREUR : pas de name ".$name.'dans le fichier de correspondance : '.$toimport_file.PHP_EOL;
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

          echo "UFFIELF name : ".$name." | label : ".$label." | field name : ".$field_name;

          if($field_name!=$uFField['field_name']){
            echo " - MAJ";         
            $results = civicrm_api4('UFField', 'update', [        // on inject les nouvelles valeurs dans
              'values' => [
                'label' => $label,
                'field_name' => $field_name,
              ],

              'where' => [
                ['field_name', '=', $uFField['field_name']],
              ],

              'checkPermissions' => FALSE,
            ]); 

            }else {
              echo " - Inchangé".PHP_EOL;
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
          //echo $profile['name'].PHP_EOL;

            $position = strpos($profile['name'], '.');             // retrouve la position du point dans le nom
            if ($position !== false) {
              $prefix = substr($profile['name'], 0, $position);    // ne garde que ce qui est à gauche du point, donc le prefixe
              $postfix = substr($profile['name'], $position + 1);// ne garde que ce qui est à droite du point, donc le nom du custom group ou du profile
              //print_r($profile).PHP_EOL;
              if ($prefix=='profile'){
                array_push($summary_profile_list, $postfix);
              }
            }
          }
        }
        //echo $block['name'].PHP_EOL;
      }
    }

    //print_r($summary_profile_list);


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

        echo "UFJoin pour Contact Summary et profil : ".$profile;

      if(!isset($uFJoins[0]['id'])){
          $results = civicrm_api4('UFJoin', 'create', [
            'values' => [
              'module' => 'Contact Summary',
              'uf_group_id.name' => $profile,
            ],
            'checkPermissions' => FALSE,
          ]);
          echo " - Créé".PHP_EOL;
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
              echo " - MAJ".PHP_EOL;
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

        echo "UFJoin pour Profile (standalone form) et profil : ".$profile;

      if(!isset($uFJoins[0]['id'])){
          $results = civicrm_api4('UFJoin', 'create', [
            'values' => [
              'module' => 'Profile',
              'uf_group_id.name' => $profile,
            ],
            'checkPermissions' => FALSE,
          ]);
          echo " - Créé".PHP_EOL;
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
              echo " - MAJ".PHP_EOL;
      }

    }

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
        $cancel_url = admin_url("admin.php?page=CiviCRM");  // url à charger si annulation 
        $url = admin_url("admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid=")."{contact.id}";  // url à charger apres creation du contact utilisant le profil 
        
        $profiles_to_update = ['Mairie','Lieu_de_stockage','Centre_d_accueil_des_corps','Personnel_de_centre_de_don_de_corps','Inscription_proche_donateur_14', 'Demandeur_information_22', 'inscription_pompes', 'Inscription_donateur','Inscription_anat_compar_e'];
  
        // Liste de profils à associer à un role (ceux utilisés pour creation contacts) name_and_address = ionscription donneur ; Inscription_proche_donateur_27 : pompes
        
        foreach ($profiles_to_update as $profile_to_update) {
            $position = array_search($profile_to_update, $profile_names);
            if ($position !== false) {                                          // Si le profil est déja créé 
                echo $profile_to_update." : ";
        
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
                  echo $profile_to_update." : Profil non trouvé ////////.".PHP_EOL;
              }
        }
    // fin de Modifie l'utilisation des profils créées par le mgd files

    /// Modification des menus de navigation liés aux profil de création de contacts
      echo "  -modification des rmenus de navigation liés aux proils".PHP_EOL;
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
