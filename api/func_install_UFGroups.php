<?php

// Cette fonction est utilisée pour installer un profil specifié par un mgd file car l'imprt natif ne marche pas bien
// Elle lit le managed file spécifié dans l'argument puis : 
// - crée l'UFGroup (spécifié en premier dans le mgd file)
// - supprime les UFfields de ce groupe
// - importe les UFfields depuis le mgd file

eval(`cv php:boot`);

//$convert_file = Civi::paths()->getPath("[civicrm.root]/ext/don_corps/managed/")."ufnameconversion.txt";                           // fichier de conversion  
//$json = file_get_contents($convert_file);
//$convert = json_decode($json, true);
//print_r ($convert);
//$convert = array_column($convert, 'field_name', 'field_name:name');

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
    $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log

    //global $convert;
    //print_r ($convert);

    // lit le fichier mgd specifié dans l'argument et met la valeur dans la variable $entities
    $entities_to_process = func_get_arg(0);
    $entities_to_process_file = Civi::paths()->getPath("[civicrm.root]/ext/don_corps/managed/")."UFGroup_".$entities_to_process.".mgd.php";
    $entities = require $entities_to_process_file;
    //print_r($entities);

    foreach($entities as $entitie){
        $entity = $entitie['entity'];
        echo $entity.PHP_EOL;

        switch ($entity){

            case 'UFGroup':     // Création UF Group
                $check_entity = civicrm_api4($entity, 'get', [   
                    'where' => [
                    ['name', '=', $entitie['params']['values']['name']],
                    ],
                    'checkPermissions' => FALSE,
                ]);

                if (isset($check_entity[0])){       // si l'UF Group existe on le MAJ
                    $results = civicrm_api4('UFGroup', 'update', [
                        'values' => $entitie['params']['values'],
                        'where' => [
                            ['id', '=', $check_entity[0]['id']],
                        ],
                        'checkPermissions' => FALSE,
                        ]);
                    $msg= $entity." ".$entitie['params']['values']['name']." mis à jour".PHP_EOL;
                    fwrite($fp, $msg);
                    echo $msg;
                } else {
                    $results = civicrm_api4('UFGroup', 'create', [
                    'values' => $entitie['params']['values'],
                    'checkPermissions' => FALSE,
                    ]);

                    $msg = $entity." ".$entitie['params']['values']['name']." crée".PHP_EOL;
                    fwrite($fp, $msg);
                    echo $msg;
                }

                $results = civicrm_api4('UFField', 'delete', [
                    'where' => [
                        ['uf_group_id:name', '=', $entitie['params']['values']['name']],
                    ],
                    'checkPermissions' => FALSE,
                    ]);

                $msg = "Suppression des UF Fields pour l'UFGroup ".$entitie['params']['values']['name'].PHP_EOL;
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
                //print_r($contactLayout);
                $block_cols=$contactLayout['blocks'];
                
                    foreach($block_cols as $block_col){
                        $blocks=$block_col;
                        foreach($blocks as $block){
                            $profiles=$block;
                            foreach ($profiles as $profile){
                                    if ($profile['name']=='profile.'.$entitie['params']['values']['name']){         // Profil utilisé par un CaontactLayout
                                        $msg = 'UFGroup '.$entitie['params']['values']['name'].' attaché à un Contact Layout --> UF Join pour Contact Layout ';
                                        fwrite($fp, $msg);
                                        if (VERBOSE==1){
                                        echo $msg;
                                        }

                                        $uFJoins = civicrm_api4('UFJoin', 'get', [
                                            'select' => [
                                                'id',
                                            ],
                                            'where' => [
                                                ['uf_group_id:name', '=', $entitie['params']['values']['name']],
                                                ['module', '=', 'Contact Summary'],
                                            ],
                                            'checkPermissions' => FALSE,
                                        ]);

                                        if(!isset($uFJoins[0]['id'])){
                                            $results = civicrm_api4('UFJoin', 'create', [
                                                'values' => [
                                                'module' => 'Contact Summary',
                                                'uf_group_id.name' => $entitie['params']['values']['name'],
                                                ],
                                                'checkPermissions' => FALSE,
                                            ]);
                                            $msg=" - Créé";
                                        }else{
                                            $results = civicrm_api4('UFJoin', 'update', [
                                            'values' => [
                                                'uf_group_id.name' =>  $entitie['params']['values']['name'],
                                            ],
                                            'where' => [
                                                ['id', '=', $uFJoins[0]['id']],
                                            ],
                                            'checkPermissions' => FALSE,
                                            ]);
                                                $msg=" - MAJ";
                                        }
                                        fwrite($fp, $msg);
                                        if (VERBOSE==1){
                                            echo $msg;
                                        }

                                        $uFJoins = civicrm_api4('UFJoin', 'get', [
                                            'select' => [
                                                'id',
                                            ],
                                            'where' => [
                                                ['uf_group_id:name', '=', $entitie['params']['values']['name']],
                                                ['module', '=', 'Profile'],
                                            ],
                                            'checkPermissions' => FALSE,
                                            ]);

                                            $msg=" - UFJoin pour Profile (standalone form) et profil ";
                                            fwrite($fp, $msg);
                                            if (VERBOSE==1){
                                            echo $msg;
                                            }

                                        if(!isset($uFJoins[0]['id'])){
                                            $results = civicrm_api4('UFJoin', 'create', [
                                                'values' => [
                                                'module' => 'Profile',
                                                'uf_group_id.name' => $entitie['params']['values']['name'],
                                                ],
                                                'checkPermissions' => FALSE,
                                            ]);
                                            $msg= " - Créé".PHP_EOL;
                                        }else{
                                            $results = civicrm_api4('UFJoin', 'update', [
                                            'values' => [
                                                'uf_group_id.name' => $entitie['params']['values']['name'],
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
                                    break 5;
                                    }
                                }
                            }
                    }
                }
            break;

            case 'UFField':
                // création du nom de l'UFfield 
                $custom_group=before('.', $entitie['params']['values']['field_name:name']);
                $custom_field=after('.', $entitie['params']['values']['field_name:name']);

                    // On récupère l'id custom field correspondant
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
                echo $field_name.PHP_EOL;
                $entitie['params']['values']['field_name']=$field_name;
                unset ($entitie['params']['values']['field_name:name']);  // sinon erreur

                $results = civicrm_api4('UFField', 'create', [ // pas de verification d'existence car les UFFields ont été supprimés plus haut
                'values' => $entitie['params']['values'],
                'checkPermissions' => FALSE,
                ]);




            break;
        }
 
          //print_r($results);
          

    }

   


   
    }


install_UFGroup ('Op_rations_fun_raires_r_alis_es_30');


