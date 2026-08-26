<?php



eval(`cv php:boot`);

function after ($thisv, $inthatv)
  {
      if (!is_bool(strpos($inthatv, $thisv)))
      return substr($inthatv, strpos($inthatv,$thisv)+strlen($thisv));
};
function before ($thisv, $inthatv)
  {
      return substr($inthatv, 0, strpos($inthatv, $thisv));
};

function install_UFGroup(){
    // Cette fonction est utilisée pour installer un profil specifié par un mgd file car l'imprt natif ne marche pas bien
    // Elle lit le managed file spécifié dans l'argument puis : 
    // - crée l'UFGroup (spécifié en premier dans le mgd file)
    // - modifie les url appelées en cas de creation de contact ou d'annulation de cette creation
    // - modifie les url de navigation qui appellent les profils de creation de contact
    // - crée les UFjoins liés à l'UFGroup si celui ci est utilisé par un custom layout
    // - supprime les UFfields de ce groupe
    // - importe les UFfields depuis le mgd file
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log

    // lit le fichier mgd specifié dans l'argument et met la valeur dans la variable $entities
    $entities_to_process = func_get_arg(0);
    $entities_to_process_file = Civi::paths()->getPath("[civicrm.root]/ext/don_corps/managed/")."UFGroup_".$entities_to_process.".mgd.php";
    $entities = require $entities_to_process_file;

    if(func_num_args()==3){
        $nav_parent=func_get_arg(1); // nom du menu parent de navigation si le profil est appelé par un menu
        $nav=func_get_arg(2);        // nom du menu de navigation si le profil est appelé par un menu
    }

    foreach($entities as $entitie){
        $entity = $entitie['entity'];
        switch ($entity){

            case 'UFGroup':     // Création UF Group

                $msg= PHP_EOL.'Traitement de '.$entity." ".$entitie['params']['values']['name'].PHP_EOL;
                fwrite($fp, $msg);
                echo $msg;

                // Si l'UF group comporte une  url à charger apres creation du contact en utilisant le profil on la maj
                if(isset($entitie['params']['values']['post_url'])){
                    $entitie['params']['values']['post_url'] = admin_url("admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid=")."{contact.id}";  
                    $msg= "         Réécriture url à afficher apres utilisation du profil".PHP_EOL;
                    fwrite($fp, $msg);
                    echo $msg;
                        
                }

                // Si l'UF group comporte une  url à charger en cas d'anulation en utilisant le profil on la maj
                if(isset($entitie['params']['values']['cancel_url'])){
                    $entitie['params']['values']['cancel_url'] = admin_url("admin.php?page=CiviCRM");  
                    $msg= "         Réécriture url à afficher en cas d'annulation".PHP_EOL;
                    fwrite($fp, $msg);
                    echo $msg;
                }

                $to_create =  [                                                 // modifie l'URL à afficher apres la creation (post url) par un profil
                  'entity' => 'UFGroup',
                  'values' => $entitie['params']['values'],
                ];

                $UFGroup_id=create_entity($to_create);  // create ou update UFGROUP

                // on met à jour le menu de navigation éventuel (2 et 3 eme arguments passés à la fonction) qui appelle ce profil
                // le champ URL doit être modifié en utilisant l'id de l'UF group qui est crée ($results[0]['id])

                if (isset($nav)){
                $msg= "         Menu de navigation : ".$nav_parent." / ".$nav;
                fwrite($fp, $msg);
                echo $msg;
                
                $to_create =  [                                                 // modifie l'URL pour le menu
                    'entity' => 'Navigation',
                    'values' => [
                        'parent_id:name' => $nav_parent,
                        'name' => $nav,
                        'url' => 'civicrm/profile/create/?gid='.$UFGroup_id.'&reset=1',
                        'permission' => 'add contacts',
                        'is_active' =>true,
                        ],
                    ];
                create_entity($to_create); 
                }
                
                // Suppression des UFFields liés à cet UFGroup
                    $results = civicrm_api4('UFField', 'delete', [
                        'where' => [
                            ['uf_group_id:name', '=', $entitie['params']['values']['name']],
                        ],
                        'checkPermissions' => FALSE,
                        ]);

                    $msg = "         Suppression des UF Fields liés à cet UFGroup".PHP_EOL;
                    fwrite($fp, $msg);
                    echo $msg;

                // Si ce customgroup est utilisé par un contact layout on crée l'UFJoin
                    $contactLayouts = civicrm_api4('ContactLayout', 'get', [  
                        'select' => [
                            'blocks',
                        ],
                        'checkPermissions' => FALSE,
                    ]);

                    foreach($contactLayouts as $contactLayout){
                    $block_cols=$contactLayout['blocks'];
                    
                        foreach($block_cols as $block_col){
                            $blocks=$block_col;
                            foreach($blocks as $block){
                                $profiles=$block;
                                foreach ($profiles as $profile){
                                        if ($profile['name']=='profile.'.$entitie['params']['values']['name']){         // Profil utilisé par un CaontactLayout
                                            $msg = '         UFGroup attaché à un Contact Layout : Traitement des UFJoins'.PHP_EOL;
                                            fwrite($fp, $msg);
                                            if (VERBOSE==1){
                                            echo $msg;
                                            }

                                            $to_create =  [                                                 // modifie l'URL pour le menu
                                            'entity' => 'UFJoin',
                                            'values' => [
                                                'module' => 'Contact Summary',
                                                'is_active' => TRUE,
                                                'uf_group_id:name' => $entitie['params']['values']['name'],
                                                ],
                                            ];
                                            create_entity($to_create); 

                                            $to_create =  [                                                 // modifie l'URL pour le menu
                                            'entity' => 'UFJoin',
                                            'values' => [
                                                'module' => 'Profile',
                                                'is_active' => TRUE,
                                                'uf_group_id:name' => $entitie['params']['values']['name'],
                                                ],
                                            ];
                                            create_entity($to_create); 
                                        break 5;
                                        }
                                    }
                                }
                        }
                    }

                //
                    break;

            case 'UFField':
                // création du nom de l'UFfield 

                if(isset($entitie['params']['values']['field_name:name'])){
                    // si le champ est identifié sous la forme field_name:name
                    // (ex. custom_group_name.custom_field_name)
                    // c à d que c'est un champ cré par l'utilisateur
                    // il faut 
                    //  1/ extraire le custom_GROUP_name et le custom_field_name 
                    //  2/ récupérer l'id du custom field correspondant
                    //  3/ creer le filed name custom_idDuCustomField
                    //
                    // si le champ est identifié sous la forme field_name
                    // (ex. birth_date)
                    // c à d que c'est un champ natif
                    // on utilise directement le custom_field_name pour creer l'UFField

                    $field=$entitie['params']['values']['field_name:name'];

                    $custom_group=before('.', $field);
                    $custom_field=after('.', $field);

                    // On récupère l'id du custom field correspondant au custom_field_name
                    $customFields = civicrm_api4('CustomField', 'get', [
                    'select' => [
                        'id',
                    ],
                    'where' => [
                        ['custom_group_id:name', '=', $custom_group],
                        ['name', '=', $custom_field],
                    ],
                    'checkPermissions' => FALSE,
                    ]);

                    $field_name='custom_'.$customFields[0]['id'];
                    unset ($entitie['params']['values']['field_name:name']);  // sinon erreur
                    $entitie['params']['values']['field_name']=$field_name;

                } else {

                    $field=$entitie['params']['values']['field_name'];

                }

                $entitie['params']['values']['is_active']=true;

                // création de l'UFField

                $to_create =  [                                                 // modifie l'URL pour le menu
                'entity' => 'UFField',
                'values' => $entitie['params']['values'],
                ];

                create_entity($to_create); 
            break;
        }          
    }
}


install_UFGroup ('Centre_d_accueil_des_corps','Centres de don du corpsDDC','New CDC'); 
install_UFGroup ('CESP_29');
install_UFGroup ('D_mographie_animal'); 
install_UFGroup ('Dates_naissance_et_d_c_s_17'); 
install_UFGroup ('Demandeur_information_22','ContactsDDC','New Demandeur_d_informationDDC'); 
install_UFGroup ('Employeur'); 
install_UFGroup ('Inscription_anat_compar_e', 'ContactsDDC','Nouvelle pièce anat comparée'); 
install_UFGroup ('Inscription_donateur','ContactsDDC','New DonateurDDC'); 
install_UFGroup ('inscription_pompes', 'Pompes funebresDDC','New Pompes'); 
install_UFGroup ('Inscription_proche_donateur_14','ContactsDDC','Ajouter proche donateurDDC');
install_UFGroup ('Lieu_de_stockage', 'Pièces anatomiquesDDC','New Emprunteur'); 
install_UFGroup ('Mairie', 'MairiesDDC','New Mairies'); 
install_UFGroup ('Op_rations_fun_raires_r_alis_es_30');
install_UFGroup ('Personnel_de_centre_de_don_de_corps', 'Centres de don du corpsDDC','New Personnel'); 
install_UFGroup ('Profil_sans_nom_20'); // Adresse incorrecte OK
install_UFGroup ('Restitution_28'); 
install_UFGroup ('Type_de_contact_23'); 

//install_UFGroup ();


