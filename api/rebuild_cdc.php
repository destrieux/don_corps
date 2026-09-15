<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;

$exp_dir = '../managed/';    // racine du répertoire d'import export
$contact_default = 2;       // id du contact par defaut lorsque le contact origine a disparu

## Définition des fonctions
    function import_stuffCDC(){
        $count=1;
        $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
        $values = func_get_arg(1);     // parametres de cette entité
        $check=array();

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
            echo "employer_id : ".$value['employer_id'].PHP_EOL;   // on en modifie la valeur par l'id de l'institution crée au préalable
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
                    echo "employer_id new: ".$value['employer_id'].PHP_EOL;
                } else {
                    echo "employer_id  externeal identifier n'existe pas : ".$value['employer_id '].PHP_EOL;
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
                echo "         ".$count." CREATION : ".$value['sort_name'].PHP_EOL;
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
                echo "         ".$count." MAJ : ".$value['sort_name']." | id : ".$id_to_update.PHP_EOL;

                ++$count;
            }

            array_push($check, $results[0]['id']);
        }
        
        echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);

        if (count($check)==count($values)) {    // le bon nombre de lignes a été importées
            echo " ---> OK".PHP_EOL;
        }else {
        echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
        }
        return ($check);
    }

    function import_addressCDC(){
        $count=1;
        $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
        $values = func_get_arg(1);     // parametres de cette entité
        $check=array();

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
                echo "         ".$count." CREATION adresse : ".$value['street_address']." pour CDC id ".$value['contact_id'].PHP_EOL;
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
                echo "         ".$count." MAJ adresse : ".$value['street_address']." pour CDC id ".$value['contact_id'].PHP_EOL;

                ++$count;
            }
            }
            array_push($check, $old_contact_id); // crée un tableau avec les n° originaux des contacts (external id dans la nouvelle base)
                                                // à utiliser avec check_address.php
        }
        echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);

            if (count($check)==count($values)) {// le bon nombre de lignes a été importées
                echo " ---> OK".PHP_EOL;
            }else {
            echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
            }
            return ($check);
    }

    function import_phoneCDC(){
        $count=1;
        $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
        $values = func_get_arg(1);     // parametres de cette entité
        $check=array();

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
                echo "         ".$count." CREATION téléphone: ".$value['phone']." pour CDC id ".$value['contact_id'].PHP_EOL;
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
                echo "         ".$count." MAJ téléphone : ".$value['phone']." pour CDC id ".$value['contact_id'].PHP_EOL;

                ++$count;
            }
            }
            array_push($check, $old_contact_id); // crée un tableau avec les n° originaux des contacts (external id dans la nouvelle base)
                                                // à utiliser avec check_address.php
        }

        echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);

            if (count($check)==count($values)) {// le bon nombre de lignes a été importées
                echo " ---> OK".PHP_EOL;
            }else {
            echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
            }
            return ($check);
    }

    function import_emailCDC(){
        $count=1;
        $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
        $values = func_get_arg(1);     // parametres de cette entité
        $check=array();
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
                echo "         ".$count." CREATION email: ".$value['email']." pour CDC id ".$value['contact_id'].PHP_EOL;
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
                echo "         ".$count." MAJ email : ".$value['email']." pour CDC id ".$value['contact_id'].PHP_EOL;

                ++$count;

            }
            }
            array_push($check, $old_contact_id); // crée un tableau avec les n° originaux des contacts (external id dans la nouvelle base)
                                                // à utiliser avec check_address.php

        }


        echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);

            if (count($check)==count($values)) {// le bon nombre de lignes a été importées
                echo " ---> OK".PHP_EOL;
            }else {
            echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
            }
            return ($check);
    }

    

## Fin de la définition des fonctions


echo "  - Ajout des centres de don du corps".PHP_EOL;

## On vérifie qu'il existe bien une location_type existe bien pour le cesp - Sinon on la crée

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

            echo "      -> MAJ ";

        }else{
            $results = civicrm_api4('LocationType', 'create', [
            'values' => [
            'name' => 'CESP',
            'display_name' => 'CESP',
            'is_active' => TRUE,
            ],
            'checkPermissions' => FALSE,
            ]);
            
            echo "      -> Création ";
        }
        echo "de la location type : ".$results[0]['name']." (".$results[0]['id'].")".PHP_EOL;

## Fin verification location_type pour CESP

// importe organisations
    $name =  "05_organisations";
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);
    echo "      -> ".count($toimport)." Organisations à importer".PHP_EOL;
    $check=import_stuffCDC('Contact',$toimport);
    $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;


// importe adresses
    $name =  "15_adresses";
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);
    echo "      -> ".count($toimport)." Adresses à importer".PHP_EOL;
    $check=import_addressCDC('Address',$toimport); // appelle la fonction import  et assigne à check la liste des anciennes id de contact
    $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

// importe telephones
    $name = '20_telephone';                     // nom du fichier à importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);
    echo "      -> ".count($toimport)." Téléphones à importer".PHP_EOL;
    $check=import_phoneCDC('Phone',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact
    $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

// importe email
    $name = '25_Email';                     // nom du fichier à importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);

    echo "      -> ".count($toimport)." Emails à importer".PHP_EOL;
        $check=import_emailCDC('Email',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact
    $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

