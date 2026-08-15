<?php
eval(`cv php:boot`);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



$exp_dir = '/Users/destri_c/Desktop/importLyon/';       // racine du répertoire d'import export
$contact_default = 2; // id du contact par defaut lorsque le contact origine a disparu

$custom = '/Applications/MAMP/htdocs/preprod/wp-content/uploads/civicrm/custom';   // repertoire contenant les pdf
$custom_orig = $custom."/custom_orig/";                                             // repertoire contenant les pdf de la base originale (cux qui sont utilisés sont  déplacés vers $custom)

$check_custom_field = 0;
$check_option_values = 1 ;
$import_organisations =0;
$import_individus =0 ;
$import_groups = 0 ;
$import_adresses = 0 ;
$import_telephones = 0 ;
$import_email = 0 ;
$import_relationships = 0 ;
$import_utilisations =1;
$import_protinvivo =0 ;
$import_FinancialType =0;
$import_contributions =0 ;
$import_events =0 ;
$import_participants =0;
$import_activites =0 ;
$import_notes =0 ;
$import_documents =0 ;   // a faire avant files
$import_files =0 ;
$mv_files = 0 ;
$import_tags = 0 ;


function check_custom(){
  $options = func_get_arg(0);     //  tableau des custom values importées
  //print_r($options);
  foreach ($options as $custom){
    $customFields = civicrm_api4('CustomField', 'get', [
      'select' => [
        'id',
      ],
      'where' => [
        ['name', '=', $custom['name']],
        ['custom_group_id:name', '=', $custom['custom_group_id:name']],
      ],
      'limit' => 25,
      'checkPermissions' => FALSE,
    ]);

    if (!isset($customFields[0]['id'])){
      echo "Custom Field name : ".$custom['name']." | group :".$custom['custom_group_id:name']." n'existe pas".PHP_EOL;
      $err=1;
    }

  }

  if ($err=1){
    echo PHP_EOL."##### Les ignorer (I) ou les créer à la main (M) sur le site d'import".PHP_EOL;
  }

   $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles
  // echo "entrée : ".$kb.PHP-EOL;

  if ($kb=='I' OR $kb=='i'){
    echo "Custom Champs manquant ignorés".PHP_EOL;
  } else {
    echo "Créez manuelement les champs custom manquants sur le site d'import".PHP_EOL;
    exit;
  }


}

function check_option_values(){
  $options = func_get_arg(0);     //  tableau des valeurs d'option importées

  foreach ($options as $option_group){

    foreach($option_group as $val){
      //echo "val : ".PHP_EOL;
      //print_r($val);
        $optionValues = civicrm_api4('OptionValue', 'get', [
          'where' => [
            ['option_group_id:name', '=', $val['option_group_id.name']],
            ['value', '=', $val['value']]
            //['label', '=', $val['label']],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);


        //var_dump($option);

        if (!isset($optionValues[0])){   /// aucune option avec cette valeur numérique : on la crée
          echo "###### Valeur d'option n'existe pas : Group : ".$val['option_group_id.name']." ; label : ".$val['label'].PHP_EOL;
          echo "       Voulez vous la créer (O / N) ?".PHP_EOL;
          $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles
          // echo "entrée : ".$kb.PHP-EOL;

          if ($kb=='O' OR $kb=='o'){
            echo "creation".PHP_EOL;
            $creation = civicrm_api4('OptionValue', 'create', [
              'values' => [
                'option_group_id.name' => $val['option_group_id.name'],
                'name' => $val['name'],
                'label' => $val['label'],
                'value' => $val['value'],
                'is_active' => $val['is_active'],
              ],
              'checkPermissions' => FALSE,
            ]);

          }

        } else{           // dans le cas ou la valeur numerique existe pour cette option
          //print_r($option);
          //echo $val['label'].PHP_EOL;
          //echo $option['label'].PHP_EOL;
          $option=$optionValues[0];

            if ($option['label']==$val['label']){  // si le label est concordant on ne fait rien
              echo "Option non modifiée : group : ".$val['option_group_id.name']." ; value :".$val['value']." ; label : ".$val['label']." | ACTUAL value :".$option['value'].PHP_EOL;
              $results = civicrm_api4('OptionValue', 'update', [
                'values' => [
                  'is_active' => $val['is_active'],
                ],
                'where' => [
                  ['value', '=', $val['value']],
                  ['option_group_id:name', '=', $val['option_group_id.name']],
                ],
                'checkPermissions' => FALSE,
              ]);

            } else {                                        // SI LE LABEL N'EST PAS CONCORDANT
              enterkb:      // point entrée pour goto saisie clavier
              echo "###### group : ".$val['option_group_id.name']." ; value :".$val['value']." ; label A IMPORTER : ".$val['label']." | label EXISTANT : ".$option['label'].PHP_EOL;
              echo "       Voulez vous conserver la valeur existante (C) ou importer la nouvelle (I) ?".PHP_EOL;
              $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

              switch ($kb) {

                case 'I':
                case 'i':
                  echo "Import ".$val['label']." à la place de : ".$option['label'].PHP_EOL;
                  $results = civicrm_api4('OptionValue', 'update', [
                    'values' => [
                      'label' => $val['label'],
                      'is_active' => $val['is_active'],
                    ],
                    'where' => [
                      ['value', '=', $val['value']],
                      ['option_group_id:name', '=', $val['option_group_id.name']],
                    ],
                    'checkPermissions' => FALSE,
                  ]);
                  break;

                case 'C':
                case 'c':
                    echo "label  ".$option['label']." inchangé".PHP_EOL;
                    $results = civicrm_api4('OptionValue', 'update', [
                      'values' => [
                        'is_active' => $val['is_active'],
                      ],
                      'where' => [
                        ['value', '=', $val['value']],
                        ['option_group_id:name', '=', $val['option_group_id.name']],
                      ],
                      'checkPermissions' => FALSE,
                    ]);
                    break;

                default :
                  goto enterkb;
              }
            }
        }
      }
    }
}

function import_FinancialType (){
    $count=1;
    $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
    $values = func_get_arg(1);     // parametres de cette entité



  foreach ($values as $value){    // Vérifie si ce financial type existe



    $financialTypes = civicrm_api4('FinancialType', 'get', [
      'where' => [
      ['name', '=', $value['name']],
      ],
    'checkPermissions' => FALSE,
    ]);

    unset($value['id']);



    if(isset($financialTypes[0])){    // si le type finanier existe = update
      $results = civicrm_api4('FinancialType', 'update', [
        'values' => $value,
        'where' => [
          ['id', '=', $financialTypes[0]['id']],
        ],
        'checkPermissions' => FALSE,
      ]);
      echo $count." Updated : ".$value['name'].PHP_EOL;



    }else{                            // si le type finanier n'eiste pas = creation
      $results = civicrm_api4('FinancialType', 'create', [
        'values' => $value,
        'checkPermissions' => FALSE,
      ]);
      echo $count." Created : ".$value['name'].PHP_EOL;

    }

  }

}


function import_stuff(){
    $count=1;
    $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
    $values = func_get_arg(1);     // parametres de cette entité
    $check=array();
      //print_r($values);



    foreach ($values as $value) {

        $value['external_identifier']=$value['id'];

        unset ($value['id']);
        unset ($value['hash']);
        //unset ($value['display_name']);
        //unset ($value['sort_name']);
        unset ($value['email_greeting_id']);
        unset ($value['email_greeting_custom']);
        unset ($value['email_greeting_display']);

        unset ($value['postal_greeting_id']);
        unset ($value['postal_greeting_custom']);
        unset ($value['postal_greeting_display']);

        //unset ($value['addressee_display']);
        unset ($value['more_greetings_group.greeting_field_1']);
        unset ($value['more_greetings_group.greeting_field_2']);
        unset ($value['more_greetings_group.greeting_field_3']);
        unset ($value['more_greetings_group.greeting_field_4']);
        unset ($value['more_greetings_group.greeting_field_5']);
        unset ($value['more_greetings_group.greeting_field_6']);
        unset ($value['more_greetings_group.greeting_field_7']);
        unset ($value['more_greetings_group.greeting_field_8']);
        unset ($value['more_greetings_group.greeting_field_9']);

        unset ($value['more_greetings_group.greeting_field_1_protected']);
        unset ($value['more_greetings_group.greeting_field_2_protected']);
        unset ($value['more_greetings_group.greeting_field_3_protected']);
        unset ($value['more_greetings_group.greeting_field_4_protected']);
        unset ($value['more_greetings_group.greeting_field_5_protected']);
        unset ($value['more_greetings_group.greeting_field_6_protected']);
        unset ($value['more_greetings_group.greeting_field_7_protected']);
        unset ($value['more_greetings_group.greeting_field_8_protected']);
        unset ($value['more_greetings_group.greeting_field_9_protected']);

        //unset ($value['prefix_id']);

        unset ($value['suffix_id']);
        unset ($value['communication_style_id']);



       /// dans les anciennses versions le genre de l'animal était ocnservé dans un custom field
       /// du groupe animal
       /// ce champ a été supprimé au profit de Gender qu'il écrase s'il est défini.

       if (isset($value['contact_sub_type']) && in_array('Animal',$value['contact_sub_type']) && isset($value['animal.Sexe'])){

            $value['gender_id']=$value['animal.Sexe'];
            unset($value['animal.Sexe']);
            //echo "Animal : Champ Sexe du custom group animal non importé -> Gender mis à : ".$value['gender_id'].PHP_EOL;
       }

       //echo $value['gender_id'].PHP_EOL;

        if (isset($value['Devenir_du_corps.Pompes_fun_bres_mandat_es_par_proches'])){                    // si le contact a un champ Pompes_fun_bres_mandat_es_par_proches non null
          echo "pompes_id : ".$value['Devenir_du_corps.Pompes_fun_bres_mandat_es_par_proches'].PHP_EOL;   // on en modifie la valeur par l'id des pompes crées au préalable
          $pompes = civicrm_api4('Contact', 'get', [
            'select' => [
              'id',
            ],
            'where' => [
              ['contact_sub_type', '=', 'Pompes'],
              ['external_identifier', '=', $value['Devenir_du_corps.Pompes_fun_bres_mandat_es_par_proches']],
            ],
            'checkPermissions' => FALSE,
          ]);
          if (isset($pompes)){
            //echo "pompes_id new : ".$pompes[0]['id'].PHP_EOL;
            $value['Devenir_du_corps.Pompes_fun_bres_mandat_es_par_proches']=$pompes[0]['id'];
            echo "pompes_id new: ".$value['Devenir_du_corps.Pompes_fun_bres_mandat_es_par_proches'].PHP_EOL;
          } else {
            echo "pompes_id externeal identifier n'existe pas : ".$value['Devenir_du_corps.Pompes_fun_bres_mandat_es_par_proches'].PHP_EOL;
          }
        }


        if (isset($value['employer_id'])){                       // si le contact a un champ employer_id non null
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
            //echo "employeur_id new : ".$employeur[0]['id'].PHP_EOL;
            $value['employer_id']=$employeur[0]['id'];
            echo "employer_id new: ".$value['employer_id'].PHP_EOL;
          } else {
            echo "employer_id  externeal identifier n'existe pas : ".$value['employer_id '].PHP_EOL;
          }
        }

        // Dans la nouvelle base un Champ Civilité user du groupe Complement Etat Civil
        // Les formules de politesse sont déduites de ce champ
        // Dans les anciennes versions, la civilité est données par prefix




        if (isset($value['contact_sub_type']) && $value['contact_type']=='Individual'){
            //echo "c'est un individu ";


            if (in_array('Animal',$value['contact_sub_type'])){
                //echo " et un animal ".PHP_EOL;
                unset ($value['prefix_id']);
                unset ($value['email_greeting_id']);
                unset ($value['postal_greeting_id']);
                unset ($value['Compl_m_nt_tat_civil.Civilit_user']);
                $value['gender_id']=NULL;
                //echo "unset gender et prefix".PHP_EOL;

            } else {

              //echo "prefix ".$value['prefix_id'].PHP_EOL;
              //echo "gender ".$value['gender_id'].PHP_EOL;

                //echo " mais pas un animal : modification civilité et formules politesse".PHP_EOL;
                //echo "civilité originale ".$value['prefix_id']." ".$value['prefix_id:name'].PHP_EOL;
                switch($value['prefix_id']){
                  case '1': // Mme
                    //echo "cas mme".PHP_EOL;
                    $value['gender_id']=1;
                    $value['Compl_m_nt_tat_civil.Civilit_user']=2;
                    $value['email_greeting_id:name']='Madame';
                    $value['postal_greeting_id:name']='Madame';
                    //$value['email_greeting_display'];
                    //$value['postal_greeting_display'];

                  break;

                  case '2': // Melle
                    //echo "cas MLLE".PHP_EOL;
                    $value['gender_id']=1;
                    $value['Compl_m_nt_tat_civil.Civilit_user']=3;
                    $value['email_greeting_id:name']='Mademoiselle';
                    $value['postal_greeting_id:name']='Mademoiselle';

                  break;

                  case '3': // Mr
                    //echo "cas MR".PHP_EOL;
                    $value['gender_id']=2;
                    $value['Compl_m_nt_tat_civil.Civilit_user']=1;
                    $value['email_greeting_id:name']='Monsieur';
                    $value['postal_greeting_id:name']='Monsieur';
                  break;

                  default :  // la civilité n'est pas définie ; on regarde la valeur du genre
                    //echo "PAS DE PREFIXE".PHP_EOL;

                    if ($value['gender_id']==1){ // féminin
                      $value['Compl_m_nt_tat_civil.Civilit_user']=2;
                      $value['email_greeting_id:name']='Madame';
                      $value['postal_greeting_id:name']='Madame';
                      $value['prefix_id']='1';
                    }

                    if ($value['gender_id']==2){ // Mascilin
                      $value['Compl_m_nt_tat_civil.Civilit_user']=1;
                      $value['email_greeting_id:name']='Monsieur';
                      $value['postal_greeting_id:name']='Monsieur';
                      $value['prefix_id']='3';
                    }

                    if ($value['gender_id']==3){ /// other
                      $value['Compl_m_nt_tat_civil.Civilit_user']=4;
                      $value['email_greeting_id:name']='{contact.first_name} {contact.last_name}';
                      $value['postal_greeting_id:name']='{contact.first_name} {contact.last_name}';
                    }

                    if ($value['gender_id']==NULL){ /// other
                      $value['email_greeting_id:name']='Madame, Monsieur';
                      $value['postal_greeting_id:name']='Madame, Monsieur';
                    }




                  break;
                }
            }

        } else { /// il s'agit d'une organisation ou d'un animal : pas de formule de politess ni de genre
            //echo "c'est une organisation ".PHP_EOL;
            unset ($value['prefix_id']);
            unset ($value['email_greeting_id']);
            unset ($value['postal_greeting_id']);
            unset ($value['Compl_m_nt_tat_civil.Civilit_user']);
            $value['gender_id']=NULL;
            //echo "unset gender et prefix".PHP_EOL;
        }
  //print_r($value);

        $contacts = civicrm_api4('Contact', 'get', [
  //            'select' => [
  //              'id',

  //           ],
            'where' => [
              ['external_identifier', '=', $value['external_identifier']],
            ],
            'limit' => 1,
            'checkPermissions' => FALSE,
          ]);

      //print_r($contacts);


 $value['addressee_id']=1;

        if (!isset($contacts[0]['id'])){             // si le contact n'existe pas on le crée
            $results = civicrm_api4('Contact', 'create', [
             'values' => $value,
              'checkPermissions' => FALSE,
            ]);
            echo $count." Created : ".$value['sort_name']." | external id : ".$value['external_identifier'].PHP_EOL;
            ++$count;

          } else {                                // si le contact exite on l'update

             $id_to_update=$contacts[0]['id'];

             $results = civicrm_api4('Contact', 'update', [
              'values' => $value,
              'where' => [
                ['id', '=', $id_to_update],
              ],
              'checkPermissions' => FALSE,
            ]);
            echo $count." Updated : ".$value['sort_name']." | external id : ".$value['external_identifier']." | id : ".$id_to_update.PHP_EOL;

            ++$count;
            //var_dump($creation);


           }

           //print_r($results[0]['id']);
           array_push($check, $results[0]['id']);



  }
    //print_r($check);
    echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);

    if (count($check)==count($values)) {// le bon nombre de lignes a été importées
        echo " ---> OK".PHP_EOL;
    }else {
      echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
    }
    return ($check);


}

function import_address(){
  $count=1;
  $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
  $values = func_get_arg(1);     // parametres de cette entité
  $check=array();
  //print_r($values);

  foreach ($values as $value) {

    //echo $value['contact_id'].PHP_EOL;

      // on verifie que le contact rattaché à l'adresse existe bien ; normalement vérifié à l'export
      $contacts = civicrm_api4('Contact', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $value['contact_id']],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

      //var_dump($contacts[0]);
      $contact_id=$contacts[0]['id'];

      if(isset($value['master_id.contact_id'])){                      // si un master_id existe (addresse partagée)
          echo "Recuperer l'adresse du contact partagé (original id : ".$value['master_id.contact_id'].PHP_EOL;
          $contacts = civicrm_api4('Contact', 'get', [     //  on récupère l'id du contact correspondant à partir de son id externe
            'select' => [
              'id',
            ],
            'where' => [
              ['external_identifier', '=', $value['master_id.contact_id']],
            ],
            'limit' => 1,
            'checkPermissions' => FALSE,
          ]);

          //print_r(contacts);

        $value['master_id.contact_id']=$contacts[0]['id'];               // on assigne l'id du contact maitre

      }

      if (isset($contact_id)){          // le contact lié à l'adresse existe
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
        $old_contact_id=$value['contact_id'];
        $value['contact_id']=$contact_id;


        //echo "Contact id : ".$contact_id." / adresse id : ".$address_to_create.PHP_EOL;

        if (!isset($addresses[0]['id'])){             //  cette adresse n'existe pas pour ce contact ; on la crée
          //$value['contact_id']=$contact_id;
          //print_r($value);

          $results = civicrm_api4('Address', 'create', [
           'values' => $value,
            'checkPermissions' => FALSE,
          ]);
          echo $count." Created adress: ".$value['street_address']." for contact id ".$value['contact_id'].PHP_EOL;
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
          echo $count." Updated address : ".$value['street_address']." for contact id ".$value['contact_id'].PHP_EOL;
          //echo $contact_id."   |   ".$value['street_address']."   |   ".$old_contact_id.PHP_EOL;

          //echo ".";
          ++$count;
          //var_dump($creation);

          //print_r($results);
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

function import_phone(){
  $count=1;
  $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
  $values = func_get_arg(1);     // parametres de cette entité
  $check=array();
  //print_r($values);

  foreach ($values as $value) {

    //echo $value['contact_id'].PHP_EOL;

      // on verifie que le contact rattaché à l'adresse existe bien ; normalement vérifié à l'export
      $contacts = civicrm_api4('Contact', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $value['contact_id']],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

      //var_dump($contacts[0]);

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


        //echo "Contact id : ".$contact_id." / phone id : ".$phone_to_create.PHP_EOL;

        if (!isset($phones[0]['id'])){             //  ce tel n'existe pas pour ce contact ; on la crée
          //$value['contact_id']=$contact_id;
          //print_r($value);

          $results = civicrm_api4('Phone', 'create', [
           'values' => $value,
            'checkPermissions' => FALSE,
          ]);
          echo $count." Created phone: ".$value['phone']." for contact id ".$value['contact_id'].PHP_EOL;
          ++$count;

        }  else {                                // ce tel existe pour ce contact ; on la cmodifie
            $phone_to_create = $phones[0]['id'];
            $creation = civicrm_api4('Phone', 'update', [
            'values' => $value,
            'where' => [
              ['id', '=', $phone_to_create],
            ],
            'checkPermissions' => FALSE,
          ]);
          echo $count." Updated phone : ".$value['phone']." for contact id ".$value['contact_id'].PHP_EOL;
          //echo $contact_id."   |   ".$value['phone']."   |   ".$old_contact_id.PHP_EOL;

          //echo ".";
          ++$count;
          //var_dump($creation);

          //print_r($results);
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

function import_email(){
  $count=1;
  $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
  $values = func_get_arg(1);     // parametres de cette entité
  $check=array();
  //print_r($values);

  foreach ($values as $value) {

    //echo "old : ".$value['contact_id'].PHP_EOL;


      // on verifie que le contact rattaché à l'adresse existe bien ; normalement vérifié à l'export
      $contacts = civicrm_api4('Contact', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $value['contact_id']],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

      $contact_id=$contacts[0]['id'];

      //print_r($contacts).PHP_EOL;
     // echo "new : ".$contact_id.PHP_EOL;

      if (isset($contacts[0]['id'])){            // le contact lié à l'adresse existe dans la nouvelle base
        $contact_id=$contacts[0]['id'];   // id du contact dans la nouvelle base
         $emails = civicrm_api4('Email', 'get', [   // on recherche si un téléphone identique existe pour ce contact
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


        //echo "Contact id : ".$contact_id." / phone id : ".$phone_to_create.PHP_EOL;

        if (!isset($emails[0]['id'])){             //  ce mail n'existe pas pour ce contact ; on la crée
          //$value['contact_id']=$contact_id;
          //print_r($value);

          $results = civicrm_api4('Email', 'create', [
           'values' => $value,
            'checkPermissions' => FALSE,
          ]);
          echo $count." Created email: ".$value['email']." for contact id new ".$value['contact_id'].PHP_EOL;
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
          echo $count." Updated email : ".$value['email']." for contact id new ".$value['contact_id'].PHP_EOL;
          //echo $contact_id."   |   ".$value['email']."   |   ".$old_contact_id.PHP_EOL;
          //echo ".";
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

function import_groups(){
  global $contact_default;                 // recupere la valeur de cette variable
  global $exp_dir;

  $count=1;
  $count_group_contact=1;
  $entity = func_get_arg(0);                  // nom de l'entité à créer (group...)
  $groups_imports = func_get_arg(1);                  // groupes à importer
  $groupscontacts_imports = func_get_arg(2);           // groupContact à importer
  $error_log=array();                          // liste des erreurs à retourner
  $check=array();                             // table de conversion entre id originale (clé) et nouvelle (veleur) des groupes
  $check_groupcontacs=array();                // table de conversion entre id originale (clé) et nouvelle (veleur) des groupeContacts

  // On importe les groupes en premier
  foreach ($groups_imports as $groups_import) {
    $group_id_orig=$groups_import['id'];
    unset($groups_import['id']);

    echo "Groupe ".$groups_import['name']." (id originale : ".$group_id_orig.")";

    // on récupère dans la nouvelle base l'id du contact ayant créé le groupe
    // si elle existe on remplace son id par celle de la nouvelle base
    // si ce contact n'est pas défini dans la base originale la valeur est laissée à null
    // si le contact n'existe pas dans la nouvelle base on met l'id du contact par defaut

    $contacts = civicrm_api4('Contact', 'get', [
      'select' => [
        'id',
      ],
      'where' => [
        ['external_identifier', '=', $groups_import['created_id']],
      ],
      'checkPermissions' => FALSE,
    ]);

    if (!isset($contacts[0]['id'])){                                // si aucun contact correspondant n'existe dans la nouvelle base
      $contacts[0]['id']=$contact_default;                          //  --> on met le contact par défaut en créateur
    }
    $groups_import['created_id']=$contacts[0]['id'];                // id du contact aynt cré le groupe ou du contact par défaut dans la nouvelle base ou

    // on récupère dans la nouvelle base l'id du contact ayant MAJ le groupe
    $contacts = civicrm_api4('Contact', 'get', [
      'select' => [
        'id',
      ],
      'where' => [
        ['external_identifier', '=', $groups_import['modified_id']],
      ],
      'checkPermissions' => FALSE,
    ]);

    if (!isset($contacts[0]['id'])){  // si aucun contact correspondant existe dans la nouvelle base et id non nulle dans l'ancienne
      $contacts[0]['id']=$contact_default;
    }
    $groups_import['modified_id']=$contacts[0]['id'];

    //print_r($groups_import);

    // on vérifie si ce groupe existe dans la nouvelle base
    $group = civicrm_api4($entity, 'get', [
      'select' => [
        'id',
      ],
      'where' => [
        ['title', '=', $groups_import['title']],
        //['name', '=', $groups_import['name']],
      ],
      'limit' => 1,
      'checkPermissions' => FALSE,
    ]);

    print_r($group);

    if (isset($group[0]['id'])){      // si le groupe existe : MAJ
      echo " existe dans la nouvelle base (id : ".$group[0]['id'].") - MISE A JOUR";
      $results = civicrm_api4($entity, 'update', [
        'values' => $groups_import,
        'where' => [
          ['id', '=', $group[0]['id']],
        ],
        'checkPermissions' => FALSE,
      ]);

    } else {                       // si le groupe n'existe pas : CREATION
      $results = civicrm_api4($entity, 'create', [
        'values' => $groups_import,
        'checkPermissions' => FALSE,
      ]);
      echo " n'existe pas dans la nouvelle base - CREATION avec l'id :".$results[0]['id'];
    }
    echo " --- Crée par contact : ".$results[0]['created_id']." et MAJ par contact ".$results[0]['modified_id'].PHP_EOL;
    ++$count;
    $check[$group_id_orig]=$results[0]['id'];
  }

  print_r($check);


  // ON importe ensuite les groupes contacts
  echo PHP_EOL.PHP_EOL;
  foreach($groupscontacts_imports as $groupscontacts_import){
    $groupcontact_id_orig=$groupscontacts_import['id'];
    unset($groupscontacts_import['id']);

    echo "GroupeContact id originale : ".$groupcontact_id_orig." - Groupe ".$groupscontacts_import['group_id']." - Contact  ".$groupscontacts_import['contact_id'];

    // on récupère l'id du groupe dans la nouvelle base
    $groupscontacts_import['group_id']=$check[$groupscontacts_import['group_id']];

    // on récupère l'id du contact dans la nouvelle base
    $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
          'id',
        ],
        'where' => [
          ['external_identifier', '=', $groupscontacts_import['contact_id']],
        ],
        'checkPermissions' => FALSE,
      ]);

    if (isset($contacts[0]['id'])){         // si un contact  existe dans la nouvelle base
      $groupscontacts_import['contact_id']=$contacts[0]['id'];

    } else {
      $error="GroupeContact id originale : ".$groupcontact_id_orig." - Le contact associé (id originale ".$groupscontacts_import['contact_id'].") n'existe pas dans la nouvelle base - PAS IMPORTE";
      echo PHP_EOL.$error.PHP_EOL;
      array_push($error_log,$error);
      continue;
    }

    // on regarde si un GroupContact identique existe

    $GroupContacts = civicrm_api4('GroupContact', 'get', [
      'select' => [
        'id',
      ],
      'where' => [
        ['contact_id', '=', $groupscontacts_import['contact_id']],
        ['group_id', '=', $groupscontacts_import['group_id']],
      ],
      'checkPermissions' => FALSE,
    ]);

    if (isset($GroupContacts[0]['id'])){      // si le groupeContact existe : MAJ
      echo " --->  MISE A JOUR - ";
      $results = civicrm_api4('GroupContact', 'update', [
        'values' => $groupscontacts_import,
        'where' => [
          ['id', '=', $GroupContacts[0]['id']],
        ],
        'checkPermissions' => FALSE,
      ]);

    } else {                       // si le groupeContact n'existe pas : CREATION
      $results = civicrm_api4('GroupContact', 'create', [
        'values' => $groupscontacts_import,
        'checkPermissions' => FALSE,
      ]);
      echo " ---> CREATION - ";
    }

    echo "id new : ".$results[0]['id']." - Groupe ".$results[0]['group_id']." - Contact  ".$results[0]['contact_id'].PHP_EOL;

    ++$count_group_contact;
    $check_groupcontacs[$groupcontact_id_orig]=$results[0]['id'];

  }

    //array_push($check, $old_contact_id); // crée un tableau avec les n° originaux des contacts (external id dans la nouvelle base)
                                          // à utiliser avec check_address.php


  echo PHP_EOL.$entity." : ".count($check)." groupes ont été importées sur ".count($groups_imports);
  echo PHP_EOL.$entity." : ".count($check_groupcontacs)." groupContacts ont été importées sur ".count($groupscontacts_imports);


    if ((count($check)==count($groups_imports)) AND (count($check_groupcontacs)==count($groupscontacts_imports))) {// le bon nombre de lignes a été importées
        echo " ---> OK".PHP_EOL;
    }else {
      echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
    }

    echo "Erreurs : ".PHP_EOL;
    print_r($error_log).PHP_EOL;

    $check_groupcontacs_file = $exp_dir."check_28_Groups_Groups_Contacts.txt";
    file_put_contents($check_groupcontacs_file, json_encode($check_groupcontacs, JSON_PRETTY_PRINT));
    echo $check_groupcontacs_file." écrit".PHP_EOL;

    return ($check);
}

function import_relationship(){
  $count=1;
  $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
  $values = func_get_arg(1);     // parametres de cette entité
  $check=array();

  // supoprime les relations "en attente"
  $results = civicrm_api4('Relationship', 'delete', [
    'where' => [
      ['relationship_type_id:name', '=', 'en attente'],
    ],
    'checkPermissions' => FALSE,
  ]);
  echo count($results)." relations en attente supprimées".PHP_EOL;

  foreach ($values as $value) {
    $relationship_id_old=$value['id'];
    unset($value['id']);
    //print_r($value);

    //echo "old : ".$value['contact_id'].PHP_EOL;
      // on verifie que la paire de contacts de cette relation existe bien ; normalement vérifié à l'export
      $contacts = civicrm_api4('Contact', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $value['contact_id_a']],
          ],
          'checkPermissions' => FALSE,
        ]);

      $contact_id_a=$contacts[0]['id'];

      $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
          'id',
        ],
        'where' => [
          ['external_identifier', '=', $value['contact_id_b']],
        ],
        'checkPermissions' => FALSE,
      ]);
      $contact_id_b=$contacts[0]['id'];

      //echo "contact id a | b new : ".$contact_id_a." | ".$contact_id_b.PHP_EOL;



      //print_r($contacts).PHP_EOL;
     // echo "new : ".$contact_id.PHP_EOL;

      if (($contact_id_a != 0) and ($contact_id_b != 0)) {          // la paire de contacts liée à l'adresse existe
         $relationships = civicrm_api4('Relationship', 'get', [   // on recherche si une relation existe pour cette paire de contacts
          'select' => [
            'id',
          ],
          'where' => [
            ['contact_id_a', '=', $contact_id_a],
            ['contact_id_b', '=', $contact_id_b],
            ['relationship_type_id:name', '=',$value['relationship_type_id:name']],
          ],
          'checkPermissions' => FALSE,

        ]);
        //print_r($relationships);

        $old_contact_id_a=$value['contact_id_a'];
        $old_contact_id_b=$value['contact_id_b'];

        $value['contact_id_a']=$contact_id_a;       // on remplace le contact_id de l'anceinne base par celui dans la nouvelle
        $value['contact_id_b']=$contact_id_b;       // on remplace le contact_id de l'anceinne base par celui dans la nouvelle


        //echo "Contact id : ".$contact_id." / phone id : ".$phone_to_create.PHP_EOL;

         if (!isset($relationships[0]['id'])){             //  cette relation n'existe pas pour cette paire de contacts ; on la crée
                                                      //  on la met en statut "en attente" car pas possible de creer en direct des reltion bidirec
          //$value['contact_id']=$contact_id;
          //print_r($value);

          $relation_orig=$value['relationship_type_id:name'];
          //echo $relation_orig.PHP_EOL;

          $value['relationship_type_id:name']='en attente';
          //print_r($value);
          //echo "toto".PHP_EOL;

          $results = civicrm_api4('Relationship', 'create', [
           'values' => $value,
            'checkPermissions' => FALSE,
          ]);



          $relationship_to_create = $results[0]['id'];
          echo $count." Relation provisoire ".$relationship_to_create." crée : ".$value['contact_id_a']." (new) ".$value['relationship_type_id:name']." ".$value['contact_id_b']." (new) ---> ";
          //++$count;
          $value['relationship_type_id:name']=$relation_orig;
        }  else {
          $relationship_to_create = $relationships[0]['id'];
        }


        //else {                                // ette relation existe pour cette paire de contacts ; on la MAJ
            //$relationship_to_create = $relationships[0]['id'];
            $creation = civicrm_api4('Relationship', 'update', [
            'values' => $value,
            'where' => [
              ['id', '=', $relationship_to_create],
            ],
            'checkPermissions' => FALSE,
          ]);
          echo $count." Relation ".$relationship_to_create." MAJ : ".$value['contact_id_a']." (new) ".$value['relationship_type_id:name']." ".$value['contact_id_b']." (new)";
          //echo ".";
          ++$count;

       //}
    }


    $check[$relationship_id_old]=$relationship_to_create;
    echo " [ OLD relationship id : ".$relationship_id_old." ]".PHP_EOL.PHP_EOL;

    //array_push($check, $old_contact_id_a); // crée un tableau avec les n° originaux des contacts (external id dans la nouvelle base)
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

function import_utilisation(){
  $count=1;
  $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
  $values = func_get_arg(1);     // parametres de cette entité
  $check=array();
  $error_log=array();                   // chaine contenant les messages d'erreur à loguer

  foreach ($values as $value) {
    print_r($value);
    //echo "old : ".$value['contact_id'].PHP_EOL;
    // on récupere les id des 3 contacts de cette utilisation dans la nouvelle base

    // Contact 1 : localiation de la pièce (peut etre null si piece élimiinée) dans nouvelle base
    $old_Lacalisation=$value['Lacalisation'];      // id de la localisation dans l'ancienne base

    $contacts = civicrm_api4('Contact', 'get', [
         'select' => [
          'id',
        ],
        'where' => [
            ['external_identifier', '=', $value['Lacalisation']],
          ],
          'checkPermissions' => FALSE,
        ]);


      if (isset($contacts[0]['id'])){
        $Lacalisation=$contacts[0]['id'];
      } else {
        $Lacalisation=NULL;                          // cas ou La pièce est éliminée : elle n'a pas de localisation
      }

      //echo "Localisation OLD: ".$old_Lacalisation."   |   NEW:".$Lacalisation;

      // Contact 2 : personne ayant préparé la pièce (peut etre null 5 ans apres depart du centre) dans nouvelle base
      unset($contacts);
      $old_Pr_par_par=$value['Pr_par_par'];                 // id du préparateur dans l'ancienne base

      $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
          'id',
        ],
        'where' => [
          ['external_identifier', '=', $value['Pr_par_par']],
        ],
        'checkPermissions' => FALSE,
      ]);

      if(isset($contacts[0]['id'])){
        $Pr_par_par=$contacts[0]['id'];                     // id su préparateur dans la nouvelle base
      } else {
        $Pr_par_par=NULL;                                   // id du préparateur a NULL si n'est plus la depuis 5ans
      }


      //echo "  |   Préparé par OLD:".$old_Pr_par_par."   |  NEW :".$Pr_par_par.PHP_EOL;

      // Contact 3 : donneur dot provient la pièce ; doit exister dans la nouvelle base
      unset($contacts);
      $old_entity_id=$value['entity_id'];                   // id du donneur dans l'ancienne base

      $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
          'id',
        ],
        'where' => [
          ['external_identifier', '=', $value['entity_id']],
        ],
        'checkPermissions' => FALSE,
      ]);

      if(isset($contacts[0]['id'])){                        // Si le donneur lié à la pièce existe dans la nouvelle base
        $entity_id=$contacts[0]['id'];                      // id du donneur dans la nouvelle base

         $utilisations = civicrm_api4('Custom_Utilisation_du_corps', 'get', [     // on recherche si une utilisation existe pour ce contacts et ce N° de piece
          'select' => [
            'id',
          ],
          'where' => [
            ['entity_id', '=', $entity_id],
            ['N_de_pi_ce_ou_de_corps', '=', $value['N_de_pi_ce_ou_de_corps']],
          ],
          'checkPermissions' => FALSE,

        ]);

        //var_dump($utilisations);

        $value['Pr_par_par']=$Pr_par_par;                         // on remplace les contact_id de l'anceinne base par ceux dans la nouvelle
        $value['entity_id']=$entity_id;
        $value['Lacalisation']=$Lacalisation;


        if (!isset($utilisations[0]['id'])){                     //  cette utilisaiton n'exista pas ; on la crée
          $results = civicrm_api4('Custom_Utilisation_du_corps', 'create', [
            'values' => $value,
            'checkPermissions' => FALSE,
          ]);

          echo $count." Custom_Utilisation_du_corps crée pour piece ou corps n° : ".$value['N_de_pi_ce_ou_de_corps'].PHP_EOL;
          ++$count;

          //$value['relationship_type_id:name']=$relation_orig;

          } else {                                                  // cette utilisation existe  ; on la MAJ
            $utilisation_to_create = $utilisations[0]['id'];

            $maj = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
            'values' => $value,
            'where' => [
              ['id', '=', $utilisation_to_create],
            ],
            'checkPermissions' => FALSE,
          ]);

            echo $count." Custom_Utilisation_du_corps ".$utilisation_to_create." MAJ pour piece ou corps n° : ".$value['N_de_pi_ce_ou_de_corps'].PHP_EOL;
            //echo ".";
            ++$count;
        }

        array_push($check, $value['N_de_pi_ce_ou_de_corps']); // crée un tableau avec les n° des pieces et corps


      } else{
        $entity_id=NULL;                                    // cas ou le donneur n'existe pas dans la nouvelle base : erreur
        $error = "Pas de contact lié à la pièce ".$value['N_de_pi_ce_ou_de_corps'];
        array_push($error_log,$error);
      }

  }

  echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);


    if (count($check)==count($values)) {// le bon nombre de lignes a été importées
        echo " ---> OK".PHP_EOL;
    }else {
      echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
    }

    if (isset($error_log)){
      echo PHP_EOL."Erreurs :".PHP_EOL;
      print_r($error_log).PHP_EOL;
    }

    return ($check);

}

function import_contribution(){
  $count=1;
  $entity = func_get_arg(0);                // nom de l'entité à créer (contact...)
  $values = func_get_arg(1);                // parametres de cette entité
  //$contribution_old = func_get_arg(2);      //tableau de coreespondance entre anciennes (clé) et nouvelle entité des contributions
  $check=array();
  $error_log=array();

  foreach ($values as $value) {
    $contribution_id_orig = $value['id'];
    unset($value['id']);

      $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
          'id',
        ],
        'where' => [
          ['external_identifier', '=', $value['contact_id']],
        ],
        'checkPermissions' => FALSE,
      ]);



      $old_contact_id=$value['contact_id'];                  // idem dans base orig

  //echo $old_contact_id.PHP_EOL;
  //echo $contacts[0]['id'].PHP_EOL;

      if (isset($contacts[0]['id'])) {                                       // le donneur asscocié à cette contribution existe
        $contact_id=$contacts[0]['id'];                        // donneur lié à cette contribution dans nouvelle base
         $contributions = civicrm_api4('Contribution', 'get', [     // on recherche si une contribution existe pour ce contacts ce montant et cette date
          'select' => [
            'id',
          ],
          'where' => [
            ['contact_id', '=', $contact_id],
            ['receive_date', '=', $value['receive_date']],
            ['total_amount', '=', $value['total_amount']],
          ],
          'checkPermissions' => FALSE,

        ]);

        $value['contact_id']=$contact_id;                         // on remplace le contact_id de l'anceinne base par celle dans la nouvelle
  //print_r($value);

        if (!isset($contributions[0]['id'])){                         //  cette contribution n'existe pas
          echo "creation".PHP_EOL;
          $results = civicrm_api4('Contribution', 'create', [   // on la crée
              'values' => $value,
              'checkPermissions' => FALSE,
            ]);
          echo $count." Contribution id ".$results[0]['id']." crée pour contact : ".$value['contact_id']." [old : ".$old_contact_id."]".PHP_EOL;

          $check[$contribution_id_orig]=$results[0]['id'];
          ++$count;

         } else {                                                  // cette contribution existe  ; on la MAJ
          $contribution_to_create = $contributions[0]['id'];
          $maj = civicrm_api4('Contribution', 'update', [
              'values' => $value,
              'where' => [
                ['id', '=', $contribution_to_create],
              ],
            'checkPermissions' => FALSE,
          ]);
          echo $count." Contribution ".$contribution_to_create." MAJ pour contact : ".$value['contact_id']." [old : ".$old_contact_id."]".PHP_EOL;
          //echo ".";
          $check[$contribution_id_orig]=$contribution_to_create;
          ++$count;
           }

       // array_push($check, $old_contact_id); // crée un tableau avec les id des contacts dans base originale
                                            // à utiliser avec check_address.php

      }else{        // le donneur associé à cette contribution n'existe pas
        $error="Contribution originale (id : ".$contribution_id_orig.") : le contact ayant l'id : ".$value['contact_id']." dans la base originale n'existe pas dans la nouvelle";
        //echo $error.PHP_EOL;
        array_push($error_log,$error);
      }
  }

  echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);

    if (count($check)==count($values)) {// le bon nombre de lignes a été importées
        echo " ---> OK".PHP_EOL;
    }else {
      echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
    }


    if (isset($error_log)){
      echo PHP_EOL."Erreurs :".PHP_EOL;
      print_r($error_log).PHP_EOL;
    }

    return ($check);

}

function import_event(){
  $count=1;
  $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
  $values = func_get_arg(1);     // parametres de cette entité
  $check=array();                // tableau pour vérification
  $error_log=array();                   // chaine contenant les messages d'erreur à loguer

  foreach ($values as $value) {   // pour chaque event a créer
    $old_id = $value['id'];
    unset ($value['id']);

    $events = civicrm_api4('Event', 'get', [
      'where' => [
        ['title', '=', $value['title']],
        ['start_date', '=', $value['start_date']],
      ],
      'checkPermissions' => FALSE,
    ]);

    //$event_to_create = $events[0]['id'];

    if (!isset($events[0]['id'])){     // cet evenement n'existe pas : on le crée

      // si la cérémonie comporte une adresse, on la crée
      if (($value['address.street_address']!=NULL) OR ($value['address.supplemental_address_1']) OR ($value['address.postal_code'] != NULL) OR ($value['address.city'] != NULL)){
        $address = civicrm_api4('Address', 'create', [
          'values' => [
            'street_address' => $value['address.street_address'],
            'supplemental_address_1' => $value['address.supplemental_address_1'],
            'postal_code' => $value['address.postal_code'],
            'city' => $value['address.city'],
          ],
          'checkPermissions' => FALSE,
        ]);
        $adresse_id = $address[0]['id'];
      } else {
        $adresse_id = NULL;         // id de l'adresse créee dans la nouvelle base
      }

      unset ($value['address.street_address']);           // on supprime les données en clair de l'adresse qui sont contenues dans $adresse_id
      unset ($value['address.supplemental_address_1']);
      unset ($value['address.postal_code']);
      unset ($value['address.city']);


      if ($value['phone.phone']!=NULL){                  // si la cérémonie comporte un telephone, on le crée
        $phone = civicrm_api4('Phone', 'create', [
          'values' => [
            'phone' => $value['phone.phone'],
          ],
          'checkPermissions' => FALSE,
        ]);
        $phone_id = $phone[0]['id'];
      } else {
        $phone_id = NULL;                               // id du tel crée dans la nouvelle base
      }
      unset ($value['phone.phone']);                    // on supprime les données en clair du tel qui sont contenues dans $phone_id

      if ($value['email.email']!=NULL){                  // si la cérémonie comporte un email, on le crée
        $email = civicrm_api4('Email', 'create', [
          'values' => [
            'email' => $value['email.email'],
          ],
          'checkPermissions' => FALSE,
        ]);
        $email_id = $email[0]['id'];                    // id du mail crée dans la nouvelle base
      } else {
        $email_id = NULL;
      }
      unset ($value['email.email']);                    // on supprime les données en clair du mail qui sont contenues dans $mail_id



      $locBlock = civicrm_api4('LocBlock', 'create', [      // on crée un LocBlock avec les id d'adresse, phone et email
        'values' => [
          'address_id' => $adresse_id,
          'phone_id' => $phone_id,
          'email_id' => $email_id,
        ],
        'checkPermissions' => FALSE,
      ]);

      $value['loc_block_id'] = $locBlock[0]['id'];    // id du LocBlock  créé dans la nouvelle base


      echo "LockBlock id ".$locBlock[0]['id']." créé avec adresse id ".$locBlock[0]['address_id']." | phone id ".$locBlock[0]['phone_id']." | email id ".$locBlock[0]['email_id'].PHP_EOL;
      //print_r($value);
      $results = civicrm_api4('Event', 'create', [    // on crée la cérémonie en lui attachant le loc_block_id crée dans la base d'import
        'values' => $value,
        'checkPermissions' => FALSE,
      ]);

      echo "     -->".$count." Cérémonie crée : ".$value['title']." id : ".$results[0]['id'].PHP_EOL;
      ++$count;

      $check[$old_id]=$results[0]['id'];
      //array_push($check, $value['title']);  // crée un tableau avec les titres des cérémonies
                                          // à utiliser avec check_address.php

    } else {                        // cet evenement existe  : on ne fait rien
      $event_to_create = $events[0]['id'];
      $error = "Cérémonie existe déja NON MODIFIEE: ".$value['title']." id : ".$event_to_create;
      echo $error.PHP_EOL;
      array_push($error_log,$error);
      $check[$old_id]=$event_to_create;
                                        }


  }
   //print_r($check);

  echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);

    if (count($check)==count($values)) {// le bon nombre de lignes a été importées
        echo " ---> OK".PHP_EOL;
    }else {
      echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
    }
    return ($check);

    if (isset($error_log)){
      echo PHP_EOL."Erreurs :".PHP_EOL;
      print_r($error_log).PHP_EOL;
    }

}

function import_participant(){
  $count=1;
  $entity = func_get_arg(0);          // nom de l'entité à créer (participant...)
  $values = func_get_arg(1);          // parametres de cette entité
  $toimport_event = func_get_arg(2);  // tableau de correspondance entre nouvelles et anciennes id d'evenements
  $check=array();

  foreach ($values as $value) {
      $participant_id_orig = $value['id'];
      unset($value['id']);

      $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
          'id',
        ],
        'where' => [
          ['external_identifier', '=', $value['contact_id']],
        ],
        'checkPermissions' => FALSE,
      ]);


      $old_contact_id=$value['contact_id'];                  // id du donneur dans base orig
      $event_id = $toimport_event[$value['event_id']];       // nouvelle valeur pour l'event id

      if (isset($contacts[0]['id'])) {                                       // le donneur existe
        $contact_id=$contacts[0]['id'];                        // id du donneur dans nouvelle base
         $events = civicrm_api4('Event', 'get', [     // on verifie que l'event existe dans le nouvelle base
          'select' => [
            'id',
          ],
          'where' => [
            ['id', '=', $event_id],
           ],
          'checkPermissions' => FALSE,

        ]);

        //var_dump($utilisations);

        if ($events[0]['id'] != 0){
          echo "Contact ".$contact_id." [".$old_contact_id."] et cérémonie ".$events[0]['id']." [".$value['event_id']."] existent ";
          $value['contact_id']=$contact_id;     // on remplace le contact_id dans l'ancienne base par celle dans la nouvelle
          $value['event_id']=$event_id;         // on remplace l'event id dans l'ancienne base par celle dans la nouvelle

          $participants = civicrm_api4('Participant', 'get', [      // verification si un participant existe
            'where' => [
              ['contact_id', '=', $contact_id],                             // avec ce contact id
              ['event_id', '=', $event_id],                         // pour cette ceremonie
            ],
            'checkPermissions' => FALSE,
          ]);


          if (!isset($participants[0]['id'])){        //  ce participant n'exista pas : creation
              $results = civicrm_api4('Participant', 'create', [
                'values' => $value,
                'checkPermissions' => FALSE,
              ]);
            ++$count;
            echo "-> Création du participant".PHP_EOL;
            $check[$participant_id_orig]=$results[0]['id'];

          } else {                               //  ce participant existe : MAJ
            $participant_to_create = $participants[0]['id'];
            $results = civicrm_api4('Participant', 'update', [
              'values' => $value,
              'where' => [
                ['id', '=', $participant_to_create],
              ],
              'checkPermissions' => FALSE,
            ]);
          ++$count;
          echo "-> MAJ du participant".PHP_EOL;
          $check[$participant_id_orig]=$participant_to_create;
          }

        } else {
          $error = "Contact ".$contact_id." [".$old_contact_id."] existe mais pas cérémonie ".$events[0]['id']." [".$value['event_id']."] -> Participant non créé";
          array_push($error_log,$error);
          echo $error.PHP_EOL;
        }

            //array_push($check, $old_contact_id); // crée un tableau avec les id des contacts dans base originale


      } else {
        $error ="Contact ".$contact_id." [".$old_contact_id."] n'existe pas --> Participant non créé";
        array_push($error_log,$error);
        echo $error.PHP_EOL;
      }
  }

  echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);

    if (count($check)==count($values)) {// le bon nombre de lignes a été importées
        echo " ---> OK".PHP_EOL;
    }else {
      echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
    }

    if (isset($error_log)){
      echo PHP_EOL."Erreurs :".PHP_EOL;
      print_r($error_log).PHP_EOL;
    }

    return ($check);

}

function import_activites(){
  $count=1;
  $entity = func_get_arg(0);     // nom de l'entité à créer (activité...)
  $values = func_get_arg(1);     // liste des entités à importer
  $check=array();


  foreach ($values as $value) {     /// pour chaque activtié à importer
    $target_ids=array();
    $assignee_ids=array();
    $activity_id_old=$value['id'];

    $corresp=array();

    if ($value['parent_id']!= NULL){
      echo $value['parent_id'].PHP_EOL;
      print_r($check).PHP_EOL;
      echo "new parent id : ".$check[$value['parent_id']].PHP_EOL;
      $value['parent_id']=$check[$value['parent_id']];
    }

    unset($value['id']);


    echo "   Source Contact Original id : ".$value['source_contact_id'];
    $contacts = civicrm_api4('Contact', 'get', [
      'select' => [
        'id',
      ],
      'where' => [
        ['external_identifier', '=', $value['source_contact_id']],
      ],
      'checkPermissions' => FALSE,
    ]);
    $value['source_contact_id']=$contacts[0]['id'];
    echo " Nouvelle id : ".$value['source_contact_id'].PHP_EOL;

    foreach($value['target_contact_id'] as $target_id){
      echo "   Target Contact Original id : ".$target_id;
      $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
          'id',
        ],
        'where' => [
          ['external_identifier', '=', $target_id],
        ],
        'checkPermissions' => FALSE,
      ]);
      array_push($target_ids, $contacts[0]['id']);
      echo " Nouvelle id : ".$contacts[0]['id'].PHP_EOL;

    }
    $value['target_contact_id']=$target_ids;

    if ($value['assignee_contact_id']!=NULL){
      foreach($value['assignee_contact_id'] as $assignee_id){
        echo "   Assignee Contact Original id : ".$assignee_id;
        $contacts = civicrm_api4('Contact', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $assignee_id],
          ],
          'checkPermissions' => FALSE,
        ]);
        array_push($assignee_ids, $contacts[0]['id']);
        echo " Nouvelle id : ".$contacts[0]['id'].PHP_EOL;

      }
      $value['assignee_contact_id']=$assignee_ids;
  }

    // on recherche si l'activité existe déja
    $source_id=$value['source_contact_id'];
    $target_id=$value['target_contact_id'];
    $assignee_id=$value['assignee_contact_id'];

    $activities = civicrm_api4('Activity', 'get', [
      'where' => [
        //['subject', '=', $value['subject']],
        ['activity_type_id:name', '=', $value['activity_type_id:name']],
        ['target_contact_id', 'IN', $value['target_contact_id']],
        ['source_contact_id', '=', $value['source_contact_id']],
        ['activity_date_time', "=", $value['activity_date_time']],
      ],
      'checkPermissions' => FALSE,
    ]);
    //print_r($activities);




    if (!isset($activities[0]['id'])){     // l'activité n'existe pas on la crée
      unset($value['phone_id']);
      unset($value['created_date']);
      unset($value['modified_date']);

      $results = civicrm_api4('Activity', 'create', [
        'values' => $value,
         'checkPermissions' => FALSE,
       ]);

      echo $count." Activité crée: ".$results[0]['id']." OLd : ".$activity_id_old.PHP_EOL.PHP_EOL;
      $check[$activity_id_old]=$results[0]['id'];
      ++$count;

    }else{                          // l'activté existe : on la MAJ
      $activity_to_create = $activities[0]['id'];
      $results = civicrm_api4('Activity', 'update', [
        'values' => $value,
        'where' => [
          ['id', '=', $activity_to_create],
        ],
        'checkPermissions' => FALSE,
      ]);
      echo $count." Activité MAJ : ".$results[0]['id']." OLd : ".$activity_id_old.PHP_EOL.PHP_EOL;
      $check[$activity_id_old]=$activity_to_create;
      ++$count;
    }
  }


  echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);

      if (count($check)==count($values)) {// le bon nombre de lignes a été importées
          echo " ---> OK".PHP_EOL;
      }else {
        echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
      }
      return ($check);
}

function import_files(){
  global $contact_default;                 // recupere la valeur de cette variable
  $entity = func_get_arg(0);              // nom de l'entité à créer (activité...)
  $filetoimport = func_get_arg(1);        // liste des fichiers à importer
  $filetoimport=array_column($filetoimport, null, 'id');  // reindexe file to import pour
  //print_r($filetoimport);
  $entityfiletoimport = func_get_arg(2);  // liste des entityfiles à importer
  $activitytable = func_get_arg(3);       // table de correspondance entre anciennes (key) et nouvelles id des activités
  $versiontable = func_get_arg(4);       // table de correspondance entre anciennes (key) et nouvelles id des versions
  $notetable = func_get_arg(5);          // table de correspondance entre anciennes (key) et nouvelles id des notes

  $check=array();
  $uri_to_keep=array(); // liste des uri (noms de fichiers) à conserver
  $error_log=array(); // liste des erreurs



  foreach ($entityfiletoimport as $entityfile){
    switch ($entityfile['entity_table']){         // target indique l'entité considérée ; note, activite ou case
      case 'civicrm_activity':
        $target = "Activity";
        $target_table = $activitytable;
        break;

      case 'civicrm_note':
        $target = "Note";
        $target_table = $notetable;
        break;

      case 'civicrm_case':
        $target = "Case";
        break;

      case 'civicrm_document_version':
        $target = "DocumentVersion";
        $target_table = $versiontable;
        break;

      default : // si une autre valeur, on passe au entityfile suivant
        continue 2; // 1 : suivant dans la boucle swith 2 dans la foreach
    }

    echo PHP_EOL."EntityFile Original id : ".$entityfile['id'].PHP_EOL;

    // on vérifie si un document version existe bien
    echo "   ".$target." id Original : ".$entityfile['entity_id'];

    if (isset($target_table[$entityfile['entity_id']])){
      echo "    New : ".$target_table[$entityfile['entity_id']].PHP_EOL;
      $entityfile['entity_id'] = $target_table[$entityfile['entity_id']];

    } else {
      $error = "EntityFile Original id : ".$entityfile['id']." : pas de ".$target." avec original id : ".$entityfile['entity_id']." dans la table de correspondance ".$target."--> PAS D'IMPORT";
      array_push ($error_log, $error);
      echo "  Pas d'entrée avec cette id originale".PHP_EOL;
      continue;
    }

    // on vérifie si un fichier existe bien
    echo "   File id Original : ".$entityfile['file_id'];
    if (isset($entityfile['file_id'])){
      echo " est bien présent dans la liste des fichiers exportés".PHP_EOL;
    }else{
      $error = "EntityFile Original id : ".$entityfile['id']." : pas de fichier avec original id : ".$entityfile['file_id']." dans la table de correspondance Fichiers --> PAS D'IMPORT";
      array_push ($error_log, $error);
      echo "  n'existe pas dans la table de correspondance".PHP_EOL;
      continue;
    }

    // on verifie que le contact ayant crée le fichier existe dans la nouvelle base
    $values=$filetoimport[$entityfile['file_id']];  // parametres du fichier à traiter
    $file_orig_id = $values['id'];
    echo "   Created id Original : ".$values['created_id'];
    $contacts = civicrm_api4('Contact', 'get', [
      'select' => [
        'id',
      ],
      'where' => [
        ['external_identifier', '=', $values['created_id']],
      ],
      'checkPermissions' => FALSE,
    ]);

      if (isset($contacts[0]['id'])){
        echo "    New : ".$contacts[0]['id'].PHP_EOL;
      }  else{
        $contacts[0]['id']=$contact_default;
        echo " Manquant !!!   New (assigné par defaut au contact id ".$contacts[0]['id'].")".PHP_EOL;
      }
      $values['created_id']=$contacts[0]['id'];
      unset ($values['id']);

    // on vérifie si un fichier avec le même nom sur le disque existe dans la nouvelle base
    echo "   Un fichier nommé : ".$values['uri'];
    $files = civicrm_api4('File', 'get', [
      'select' => [
        'id',
      ],
      'where' => [
        ['uri', '=', $values['uri']],
      ],
      'checkPermissions' => FALSE,
    ]);

    //print_r($files);

    if (isset($files[0]['id'])){        // si ce fichier existe dans la nouvelle base


      $results = civicrm_api4('File', 'update', [
        'where' => [
          ['id', '=', $files[0]['id']],
        ],
        'values' => $values,
        'checkPermissions' => FALSE,
      ]);
      echo " existe dans la nouvelle base avec l'id ".$results[0]['id']."--> MAJ".PHP_EOL;

    } else {                        // si ce fichier n'existe pas dans la nouvelle base
      //print_r($values).PHP_EOL;
      $results = civicrm_api4('File', 'create', [
        'values' => $values,
        'checkPermissions' => FALSE,
      ]);
      echo " n'existe pas dans la nouvelle base --> CREATION avec l'id :".$results[0]['id'].PHP_EOL;

    }
    $entityfile['file_id']=$results[0]['id'];

    //$uri_to_keep[$results[0]['id']]=$results[0]['uri'];

    $uri_to_keep[$file_orig_id]['new_id']=$results[0]['id'];
    $uri_to_keep[$file_orig_id]['url']=$results[0]['uri'];

    //print_r($values).PHP_EOL;

    // on modifie l'entity file
    unset($entityfile['id']);
    //print_r($entityfile).PHP_EOL;

    // on vérifie si l'entity file existe ou non
    $entityFiles = civicrm_api4('EntityFile', 'get', [
      'select' => [
        'id',
      ],
      'where' => [
        ['entity_table', '=', $entityfile['entity_table']],
        ['entity_id', '=', $entityfile['entity_id']],
        ['file_id', '=', $entityfile['file_id']],
      ],
      'checkPermissions' => FALSE,
    ]);

    if (!isset($entityFiles[0]['id'])){
      $results = civicrm_api4('EntityFile', 'create', [
        'values' => $entityfile,
        'checkPermissions' => FALSE,
      ]);
      echo "   Entity file crée; nouvelle id : ".$results[0]['id'].PHP_EOL;
    }else{
      echo "   Entity file identique existe déja avec l'id : ".$entityfile['entity_id']." Non modifiée".PHP_EOL;
    }
  }

  $check=$uri_to_keep;

  echo PHP_EOL.$entity." : ".count($check)." fichiers ont été importées sur ".count($filetoimport);

      if (count($check)==count($filetoimport)) {// le bon nombre de lignes a été importées
          echo " ---> OK".PHP_EOL;
      }else {
        echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;
      }
      print_r($error_log);

      return ($check);
}

function mv_files(){
  $filetoimport = func_get_arg(0);                       // liste des fichiers à déplacer (clé : id OLD du file)
                                                                    // 'new_id' et 'url'
  $custom = func_get_arg(1);                              // repertoire ou sont stockes les pdf
  $custom_orig=func_get_arg(2);   // repertoire termporaire pour les pdf copiés depuis la base originale
  $count=1;

  echo $custom.PHP_EOL;
  echo $custom_orig.PHP_EOL;

  $error_log=array(); // liste des erreurs

  foreach ($filetoimport as $move){
    $orig = $custom_orig."/".$move['url'];
    $target = $custom."/".$move['url'];
    if (file_exists($orig)){         // un fichier à déplacer existe
      if (file_exists($target)){        // et un fichier cible existe déja --> MAJ
        echo "    --> MAJ de : ".$target.PHP_EOL;
      }else{                            // et il n'y a pas de fichier cible  --> déplacement
        echo "    --> AJOUTE : ".$target.PHP_EOL;
      }
      rename ($orig, $target) ;
      ++$count;

    }else{                          // le fichier à déplacer n'existe pas
      if (file_exists($target)){        // et un fichier cible existe déja --> inchangé
        echo "    --> INCHANGE car présent dans seulement : ".$custom.PHP_EOL;
        ++$count;
      }else{                            // et il n'y a pas de fichier cible  --> rien à faire
        echo "    --> MANQUE; non importé :".$orig.PHP_EOL;
        $error = "File MANQUE, non importé :".$orig;
        array_push($error_log, $error);
      }
    }
  }
  echo $custom.PHP_EOL;
  echo $custom_orig.PHP_EOL;
  echo PHP_EOL.$count." fichiers ont été importés ou sont déja présents sur le disque sur ".count($filetoimport);

      if ($count==count($filetoimport)) {// le bon nombre de lignes a été importées
          echo " ---> OK".PHP_EOL;
      }else {
        echo "---> VERIFIER LIMPORT : fichiers MANQUANTS".PHP_EOL;
      }
      print_r($error_log);
      return ;
}

function import_notes(){
  $count=1;
  $entity = func_get_arg(0);                // nom de l'entité à créer (note...)
  $values = func_get_arg(1);                // liste des entités à importer
  $relationships_old = func_get_arg(2);     // table de correspondance entre ancienne et nouvelle id des relations
  $participant_old = func_get_arg(3);       // table de correspondance entre ancienne et nouvelle id des participant
  $contribution_old = func_get_arg(4);      // table de correspondance entre ancienne et nouvelle id des contributions

  $check=array();
  $error_log=array();

  foreach ($values as $value) {     /// pour chaque activtié à importer

    //print_r($value).PHP_EOL;
    $note_id_old=$value['id'];        // valeur originale de l'id pour la note traitée
    unset($value['id']);

    switch ($value['entity_table']){   // on détermine ($target) sur quoi porte la note (contact, relation...)
      case 'civicrm_contact':
        $target = 'Contact';
        $contacts = civicrm_api4('Contact', 'get', [          // On vérifie que le contact  existe
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $value['contact_id']],
            ['is_deleted', '=', FALSE],
          ],
          'checkPermissions' => FALSE,
        ]);

        //print_r($value).PHP_EOL;

        if(isset($contacts[0]['id'])){                        // si le contact existe on remplace son id initiale par son id ds la nouvelle base
          $value['contact_id'] = $contacts[0]['id'];
        }else {
          $value['contact_id'] = 2;
          $error = "Note ancienne id : ".$note_id_old." Contact ayant créé la note n'existe pas ; remplacé par le contact par défaut : ".$value['contact_id'];
          array_push($error_log,$error);
          echo $error.PHP_EOL;
        }

        $contacts = civicrm_api4('Contact', 'get', [          // On vérifie que le contact cible de la note (contact_id) existe
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $value['entity_id']],
            ['is_deleted', '=', FALSE],
          ],
          'checkPermissions' => FALSE,
        ]);

        if(isset($contacts[0]['id'])){                        // si le contact existe on remplace son id initiale par son id ds la nouvelle base
          $value['entity_id'] = $contacts[0]['id'];

        }else {
          $error = "Note : ".$note_id_old." : Le contact ayant l'id ".$value['entity_id']." dans la base originale n'existe pas";
          array_push($error_log,$error);
          $value['entity_id'] = NULL;
        }

        break;

      case 'civicrm_relationship':
        $target = 'Relationship';
        $contacts = civicrm_api4('Contact', 'get', [          // On vérifie que le contact impliqué dans la relation (contact_id) existe
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $value['contact_id']],
            ['is_deleted', '=', FALSE],
          ],
          'checkPermissions' => FALSE,
        ]);

        if(isset($contacts[0]['id'])){                        // si le contact existe on remplace son id initiale par son id ds la nouvelle base
          $value['contact_id'] = $contacts[0]['id'];
        }else {
          $error = "Note : ".$note_id_old." : Le contact ayant l'id ".$value['contact_id']." dans la base originale n'existe pas";
          array_push($error_log,$error);
          $value['entity_id'] = NULL;                     // si le contact n'existe pas on met entity_id à NULL ce qui induit le non traitement de la note
        }

        $relationships = civicrm_api4('Relationship', 'get', [         // on vérifie que cette relation existe bien dans la nouvelle base
          'select' => [                                                // $relationships_old[$value['entity_id']] : id de la relation dans la nouvellebase
            'id',
          ],
          'where' => [
            ['id', '=', $relationships_old[$value['entity_id']]],
          ],
          'checkPermissions' => FALSE,
        ]);

        if(isset($relationships[0]['id'])){                        // si la relation existe on remplace son id initiale par son id ds la nouvelle base
          $value['entity_id'] = $relationships_old[$value['entity_id']];

        }else {
          $error = "Note : ".$note_id_old." : La relation contact ayant l'id ".$value['entity_id']." dans la base originale n'existe pas";
          array_push($error_log,$error);
          $value['entity_id'] = NULL;                     // si le contact n'existe pas on met entity_id à NULL ce qui induit le non traitement de la note
        }
        break;

      case 'civicrm_participant':
        $target = 'Participant';
        $contacts = civicrm_api4('Contact', 'get', [      // On vérifie que le contact ayant créé la (contact_id) existe
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $value['contact_id']],
            ['is_deleted', '=', FALSE],
          ],
          'checkPermissions' => FALSE,
        ]);

        if(isset($contacts[0]['id'])){                        // si le contact existe on remplace son id initiale par son id ds la nouvelle base
          $value['contact_id'] = $contacts[0]['id'];
        }else {
          $value['contact_id'] = 2;
          $error = "Note : ".$note_id_old." Contact (".$value['contact_id']." ayant créé la note n'existe pas ; remplacé par le contact par défaut : ".$value['contact_id'];
          array_push($error_log,$error);
          echo $error.PHP_EOL;
        }

        $participant = civicrm_api4('Participant', 'get', [         // on vérifie que ce participant existe bien dans la nouvelle base
          'select' => [                                             // $$participant_old[$value['entity_id']] : id de la relation dans la nouvellebase
            'id',
          ],
          'where' => [
            ['id', '=', $participant_old[$value['entity_id']]],
          ],
          'checkPermissions' => FALSE,
        ]);

        if(isset($participant[0]['id'])){                        // si le participant existe on remplace son id initiale par son id ds la nouvelle base
          $value['entity_id'] = $participant_old[$value['entity_id']];

        }else {
          $error = "Note : ".$note_id_old." : Le participant  ayant l'id ".$value['entity_id']." dans la base originale n'existe pas";
          array_push($error_log,$error);
          $value['entity_id'] = NULL;                     // si le contact n'existe pas on met entity_id à NULL ce qui induit le non traitement de la note
        }
        break;

      case 'civicrm_contribution':
        $target = 'Contribution';

        $contacts = civicrm_api4('Contact', 'get', [      // On vérifie que le contact relatif à cette note (contact_id) existe et n'est pas supprimé
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $value['contact_id']],
            ['is_deleted', '=', FALSE],
          ],
          'checkPermissions' => FALSE,
        ]);

        if(isset($contacts[0]['id'])){                        // si le contact existe on remplace son id initiale par son id ds la nouvelle base
          $value['contact_id'] = $contacts[0]['id'];
        }else {
          $error = "Note : ".$note_id_old." : Le contact ayant l'id ".$value['contact_id']." dans la base originale n'existe pas";
          array_push($error_log,$error);
          $value['entity_id'] = NULL;                     // si le contact n'existe pas on met entity_id à NULL ce qui induit le non traitement de la note
        }

        $contribution = civicrm_api4($target, 'get', [     // on vérifie que cette contribution  existe bien dans la nouvelle base
          'select' => [                                   // $$participant_old[$value['entity_id']] : id de la relation dans la nouvellebase
            'id',
          ],
          'where' => [
            ['id', '=', $contribution_old[$value['entity_id']]],
          ],
          'checkPermissions' => FALSE,
        ]);

        if(isset($contribution[0]['id'])){                        // si le participant existe on remplace son id initiale par son id ds la nouvelle base
          $value['entity_id'] = $contribution_old[$value['entity_id']];

        }else {
          $error = "Note : ".$note_id_old." : la contribution ayant l'id ".$value['entity_id']." dans la base originale n'existe pas";
          array_push($error_log,$error);
          $value['entity_id'] = NULL;                     // si le contact n'existe pas on met entity_id à NULL ce qui induit le non traitement de la note
        }
        break;

      case 'civicrm_note':
        $target = 'Note';
        $error = "Note : ".$note_id_old." : note incluant une entity note : non traitée";
        array_push($error_log,$error);
        break;
    }

    //print_r($value).PHP_EOL;

    if(isset($value['entity_id'])){                    // si l'entité (contact, relation...) associée à cette note existe
      $notes = civicrm_api4('Note', 'get', [            // on vérifie si une note ayant les memes caractéristiques existe déja
        'where' => [
          ['entity_table', '=', $value['entity_table']],
          ['entity_id', '=', $value['entity_id']],
          ['contact_id', '=', $value['contact_id']],
          ['note_date', '=', $value['note_date']],
        ],
        'checkPermissions' => FALSE,
      ]);

      if (isset($notes[0]['id'])){
        $results = civicrm_api4('Note', 'update', [
          'values' => $value,
          'where' => [
            ['id', '=', $notes[0]['id']],
          ],
          'checkPermissions' => FALSE,
        ]);

        echo "Note ".$results[0]['id']." mise à jour".PHP_EOL;

      }else{
        $results = civicrm_api4('Note', 'create', [
          'values' => $value,
          'checkPermissions' => FALSE,
        ]);

        echo "Note ".$results[0]['id']." crée".PHP_EOL;
      }

      ++$count;
      $check[$note_id_old]=$results[0]['id'];

    }else{
      $error = "Note : ".$note_id_old." : non importée";
      array_push($error_log,$error);
      echo $error.PHP_EOL;
    }
  }
  echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);
      if (count($check)==count($values)) {// le bon nombre de lignes a été importées
          echo " ---> OK".PHP_EOL;
      }else {
        echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;

      }
      echo "Erreurs : ".PHP_EOL;
      print_r($error_log).PHP_EOL;
      return ($check);
}

function import_documents(){
  global $contact_default;                            // recupere la valeur de cette variable
  $count=1;
  $entity = func_get_arg(0);                          // nom de l'entité à créer (note...)
  $documentsContacts = func_get_arg(1);               // liste initiale documents Contacts à importer
  $documentsVersions = func_get_arg(2);               // liste initiale documents Version à importer

  $check=array();             // table des versions de document importées : clé ancienen id ; veleur nouvelell id
  $check_docs=array();        // table des documents importés : clé ancienne id ; veleur nouvelell id

  $error_log=array();

  foreach ($documentsContacts as $documentsContact) {     /// pour chaque Document contact à importer
    echo PHP_EOL."DocumentContact : ".$documentsContact['id'].PHP_EOL;

      // on vérifie si le contact ayant créé le document contact existe dans la nouvelle base
        echo " Document added by Contact OLD id : ".$documentsContact['document.added_by'];
        $contacts = civicrm_api4('Contact', 'get', [      // on vérifie si le contact ayant ajouté le document  existe dans la nouvelle base
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $documentsContact['document.added_by']],
          ],
          'checkPermissions' => FALSE,
        ]);

        if (isset($contacts[0]['id'])){                                // si le contact ayant ajoute le doc existe
          $documentsContact['document.added_by']=$contacts[0]['id'];      // on remplace contact_id de 'added-by' par l'id du contact dans la nouvelle base
        } else {                                                    // si le contact ayant ajoute le doc N'existe pas
          $error = "DocumentContact id ".$documentsContact['id']." associé au document ".$documentsContact['document_id']." : le contact 'added by' ".$documentsContact['document.added_by']." n'existe pas dans la nouvelle base. Remplacé par ".$contact_default;
          array_push($error_log,$error);
          $documentsContact['document.added_by']=$contact_default;      // on remplace contact_id de 'added-by' par l'id du contact dans la nouvelle base
        }
        echo "  NEW id : ".$documentsContact['document.added_by'].PHP_EOL;

      // on vérifie si le contact ayant MAJ le document contact existe dans la nouvelle base
          echo " Document updated by Contact OLD id : ".$documentsContact['document.updated_by'];
          $contacts = civicrm_api4('Contact', 'get', [      // on vérifie si le contact ayant modifié le document  existe dans la nouvelle base
            'select' => [
              'id',
            ],
            'where' => [
              ['external_identifier', '=', $documentsContact['document.updated_by']],
            ],
            'checkPermissions' => FALSE,
          ]);

          if (isset($contacts[0]['id'])){                                // si le contact associé au contact id existe
            $documentsContact['document.updated_by']=$contacts[0]['id'];      // on remplace contact_id de 'updated_by' par l'id du contact dans la nouvelle base
          } else {                                                    // si le contact associé au contact id N'existe pas
            $error = "DocumentContact id ".$documentsContact['id']." associé au document ".$documentsContact['document_id']." : le contact 'updated by' ".$documentsContact['document.updated_by']." n'existe pas dans la nouvelle base. Remplacé par ".$contact_default;
            array_push($error_log,$error);
            $documentsContact['document.updated_by']=$contact_default;      // on remplace contact_id de 'updated-by' par l'id du contact dans la nouvelle base
          }
          echo "  NEW id : ".$documentsContact['document.updated_by'].PHP_EOL;

      // on vérifie si le contact associé au document contact existe dans la nouvelle base
        echo " Contact associé au DocumentContact id OLD : ".$documentsContact['contact_id'];
        $contacts = civicrm_api4('Contact', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $documentsContact['contact_id']],
          ],
          'checkPermissions' => FALSE,
        ]);

        echo " NEW : ".$contacts[0]['id'].PHP_EOL;

        if (isset($contacts[0]['id'])){                                 // si le contact associé au contact id existe
          $documentsContact['contact_id']=$contacts[0]['id'];       // on remplace contact_id par l'id du contact dans la nouvelle base

        } else {                                                    // si le contact associé au contact id N'existe pas
          $error = "DocumentContact id ".$documentsContact['id']." non importée : Le contact associé (id ".$documentsContact['contact_id'].") n'existe pas dans la nouvelle base";
          array_push($error_log,$error);
          continue;             // on passe au Document_contact suivant
        }

      // on verifie si un couple Document/DocumentContact identique associé à ce contact existe dans la nouvelle base
        echo " Document OLD id : ".$documentsContact['document_id'];
        $document_id_orig = $documentsContact['document_id'];  // id originale du document (utilisé pour les versions)

        $documents = civicrm_api4('Document', 'get', [
          'select' => [
            'id',
          ],
          'join' => [
            ['DocumentContact AS document_contact', 'LEFT'],
          ],
          'where' => [
            ['document_contact.contact_id', '=', $documentsContact['contact_id']],
            ['subject', '=', $documentsContact['document.subject']],
            ['date_added', '=', $documentsContact['document.date_added']],
            ['date_updated', '=', $documentsContact['document.date_updated']],
            ['type_id:name', '=', $documentsContact['document.type_id:name']],
            ['status_id:name', '=', $documentsContact['document.status_id:name']],
            ['updated_by', '=', $documentsContact['document.updated_by']],
            ['added_by', '=', $documentsContact['document.added_by']],
          ],
          'checkPermissions' => FALSE,
          ]);

          if (!isset($documents[0]['id'])){     // si ce couple Document/DocumentContact n'existe pas on le crée
            $documents = civicrm_api4('Document', 'create', [
              'values' => [
                'subject' => $documentsContact['document.subject'],
                'date_added' => $documentsContact['document.date_added'],
                'date_updated' => $documentsContact['document.date_updated'],
                'type_id:name' => $documentsContact['document.type_id:name'],
                'status_id:name' => $documentsContact['document.status_id:name'],
                'updated_by' => $documentsContact['document.updated_by'],
                'added_by' => $documentsContact['document.added_by'],
              ],
              'checkPermissions' => FALSE,
            ]);

            echo " NEW (créé) ".$documents[0]['id'].PHP_EOL;
            $check_docs[$documentsContact['document_id']]=$documents[0]['id'];   // on ajoute ce nouveau docment à $check_docs

            $documentsContact['document_id']=$documents[0]['id'];   // on met l'id de ce document dans Document contact 'document_id'

            $results = civicrm_api4('DocumentContact', 'create', [
              'values' => [
                'document_id' => $documentsContact['document_id'],
                'contact_id' => $documentsContact['contact_id'],
              ],
              'checkPermissions' => FALSE,
            ]);

            echo " Document contact créé, id : ".$results[0]['id'].PHP_EOL;

          }else {
            $error = " DocumentContact id ".$documentsContact['id']." couple Document/DocumentContact non importé car existe déja";
            array_push($error_log,$error);
            echo " New (existe) : ".$documents[0]['id'].PHP_EOL;
            $check_docs[$documentsContact['document_id']]=$documents[0]['id'];   // on ajoute ce nouveau docment à $check_docs
            echo $error.PHP_EOL;
          }

      // On recheche les versions attachées à ce document
        // on cherche quelles vesions contiennent l'id originale du document dans dans la colonne document id
        $version_keys=array_keys(array_column($documentsVersions, 'document_id'), $document_id_orig );

        if (count($version_keys)!=0){     // si une version existe

          foreach($version_keys as $version_key){
            $version_to_create=$documentsVersions[$version_key];
            echo " Version (".$version_to_create['version'].") id OLD : ".$version_to_create['id']." MAJ par contact id OLD : ".$version_to_create['updated_by'].PHP_EOL;
            $version_to_create['document_id']=$documents[0]['id']; // on assigne la valeur de doc id dans la nouvelle base

            // on recherche si le contact ayant MAJ la version existe dans la base ; sinon on lui met la valeur par defaut
            $contacts = civicrm_api4('Contact', 'get', [
              'select' => [
                'id',
              ],
              'where' => [
                ['external_identifier', '=', $version_to_create['updated_by']],
              ],
              'checkPermissions' => FALSE,
            ]);

            if (isset($contacts[0]['id'])){                                // si le contact ayant MAJ la version existe dans la base
              $version_to_create['updated_by']=$contacts[0]['id'];     // on remplace son id par celle dans la nouvelle base

            } else {                                                    // si le contact ayant MAJ la version N'existe pas
              $error = "DocumentVersion id ORIG ".$version_to_create['id']." : le contact pour 'updated by' ".$version_to_create['updated_by']." n'existe pas dans la nouvelle base. Remplacé par ".$contact_default;
              array_push($error_log,$error);
              $version_to_create['updated_by']=$contact_default;      // on remplace contact_id de 'updated-by' par l'id du contact dans la nouvelle base
            }

            // on vérifie si cette version existe dans la nouvelle base
            $Versions = civicrm_api4('DocumentVersion', 'get', [
              'where' => [
                ['document_id', '=', $version_to_create['document_id']],
                ['version', '=', $version_to_create['version']],
              ],
              'checkPermissions' => FALSE,
            ]);

            $version_id_orig=$version_to_create['id'];
            unset($version_to_create['id']);

            if (!isset($Versions[0]['id'])){      // cette version n'existe pas dans la base ; on la crée
              $results = civicrm_api4('DocumentVersion', 'create', [
                'values' => $version_to_create,
                'checkPermissions' => FALSE,
              ]);
              echo " Version (".$version_to_create['version'].") id NEW  : ".$results[0]['id']." MAJ par contact id NEW : ".$version_to_create['updated_by']." CREATION".PHP_EOL;

            } else {                                // cette version existe dans la base : MAJ
              $results = civicrm_api4('DocumentVersion', 'update', [
                'values' => $version_to_create,
                'where' => [
                  ['id', '=', $Versions[0]['id'],],
                ],
                'checkPermissions' => FALSE,
              ]);
              echo " Version (".$version_to_create['version'].") id NEW  : ".$results[0]['id']." MAJ par contact id NEW : ".$version_to_create['updated_by']." UPDATED".PHP_EOL;

            }
            $check[$version_id_orig]=$results[0]['id'];   // on ajoute ce nouveau docment à $check
          }
        }
  }

  echo PHP_EOL.$entity." : ".count($check_docs)." documents/documentsContacts ont été importées ou MAJ sur ".count($documentsContacts);

      if (count($check_docs)==count($documentsContacts)) {// le bon nombre de lignes a été importées
          echo " ---> OK".PHP_EOL;
      }else {
        echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;

      }
      echo $entity." : ".count($check)." version ont été importées".PHP_EOL;
      echo "Erreurs : ".PHP_EOL;
      print_r($error_log).PHP_EOL;

      return ($check);
}

function import_protinvivo(){
  $count=1;
  $entity = func_get_arg(0);                          // nom de l'entité à créer (Protocole in vivo...)
  $protocoles = func_get_arg(1);               // liste initiale documents Contacts à importer
  $check=array();             // table des protocoles importées : clé ancienen id ; veleur nouvelell id
  $error_log=array();

  foreach ($protocoles as $protocole){
    $protocole_id_old = $protocole['id'];
    unset($protocole['id']);
    echo "Protocole in vivo OLD id : ".$protocole_id_old." (".$protocole['Intitul_du_protocole:name'].") ";
    $contacts = civicrm_api4('Contact', 'get', [    // liste les contacts correspondant (contact_id)
      'select' => [
          'id',
        ],
      'where' => [
        ['external_identifier', '=', $protocole['entity_id']],
        ['is_deleted', '=', FALSE],
      ],
      'checkPermissions' => FALSE,
    ]);

    if (isset($contacts[0]['id'])){                     // si le contact lié existe et n'est pas à la corbeille
      echo "pour Contact id :".$protocole['entity_id']." --> ".$contacts[0]['id'];
      $protocole['entity_id'] = $contacts[0]['id']; // on assigne la nouvelle id du contact

    } else {                                        // si ce contact n'existe pas ou à la corbeille
      $error="Protocole in vivo OLD id : ".$protocole_id_old." (".$protocole['Intitul_du_protocole:name']." Contact lié (".$protocole['entity_id'].") n'existe pas ou à la corbeille. PAS D'IMPORT";
      echo $error.PHP_EOL;
      array_push($error_log,$error);
      //print_r($error_log);
      continue;
    }

    $protocolesInVivos = civicrm_api4('Custom_Protocoles_in_vivo', 'get', [
      'limit' => 1,
      'where' => [
            ['Intitul_du_protocole:name', '=', $protocole['Intitul_du_protocole:name']],
            ['entity_id', '=', $protocole['entity_id']],
          ],
      'checkPermissions' => FALSE,
    ]);

    if (isset ($protocolesInVivos[0]['id'])){                     // si le protocole existe pour ce contact MAJ
      echo "  déja importé avec id :".$protocolesInVivos[0]['id']." --> MAJ".PHP_EOL;
      $results = civicrm_api4('Custom_Protocoles_in_vivo', 'update', [
        'values' => $protocole,
        'where' => [
          ['id', '=', $protocolesInVivos[0]['id']],
        ],
        'checkPermissions' => FALSE,
      ]);

    } else {                                         // si le protocole n'existe pas pour ce contact creation
      $results = civicrm_api4('Custom_Protocoles_in_vivo', 'create', [
        'values' => $protocole,
        'checkPermissions' => FALSE,
      ]);
      echo "  |    création avec id :".$results[0]['id'].PHP_EOL;

    }
    ++$count;
    $check[$protocole_id_old]=$results[0]['id'];
  }

  echo PHP_EOL.$entity." : ".count($check)." protocoles in vivo ont été importées ou MAJ sur ".count($protocoles);

  if (count($check)==count($protocoles)) {// le bon nombre de lignes a été importées
      echo " ---> OK".PHP_EOL;
  }else {
    echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;

  }

  echo "Erreurs : ".PHP_EOL;
  print_r($error_log).PHP_EOL;

  return ($check);

}



function import_tags(){
  global $exp_dir;
  $count=1;
  $entity = func_get_arg(0);                // nom de l'entité à créer (Tags...)
  $values = func_get_arg(1);                // liste des entités (Tags) à importer
  $entitytagstoimport = func_get_arg(2);    // liste des entitytags à importer
  $activitytable = func_get_arg(3);         // table de correspondance entre ancienne et nouvelle id des activiy
  $filestable = func_get_arg(4);            // table de correspondance entre ancienne et nouvelle id des files
                                                    // index : old id, ['new_id'], ['url']

  $check=array();
  $check_entity_tags=array();                // table de conversion entre id originale (clé) et nouvelle (veleur) des entitytags

  $error_log=array();

  // IMPORT DES TAGS

  foreach($values as $value){
    $tag_id_old = $value['id'];
    unset($value['id']);

    $tags = civicrm_api4('Tag', 'get', [
      'where' => [
        ['name', '=', $value['name']],
      ],
      'limit' => 1,
      'checkPermissions' => FALSE,
    ]);

    if (isset($tags[0]['id']))  {           // ce teg existe déja  ; on le met à jour
      echo "Tag ".$value['name']." existe - MAJ".PHP_EOL;
      $results = civicrm_api4('Tag', 'update', [
        'values' => $value,
        'where' => [
          ['name', '=', $value['name']],
        ],
        'checkPermissions' => FALSE,
      ]);

    } else {                                // ce tag n'existe pas
      echo "Tag ".$value['name']." n'existe pas - CREATION".PHP_EOL;
      $results = civicrm_api4('Tag', 'create', [
        'values' => $value,

        'checkPermissions' => FALSE,
      ]);
    }
    $check[$tag_id_old]=$results[0]['id'];
  }

  // IMPORT DES ENTITYTAGS


  foreach($entitytagstoimport as $entitytagtoimport){

    // on récupère la nouvelle id de l'entité
    switch ($entitytagtoimport['entity_table']){         // target indique l'entité considérée ; note, activite ou case
      /*case 'civicrm_activity':
        $target = "Activity";
        $target_table = $activitytable;
        break;*/

      case 'civicrm_contact':
        $target = "Contact";
        $contacts = civicrm_api4('Contact', 'get', [                          // on récupère l'id du contact dans la nouvelle base
          'select' => [
            'id',
          ],
          'where' => [
            ['external_identifier', '=', $entitytagtoimport['entity_id']],    // id du contact dans l'ancienne base
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

        if(isset($contacts[0])){                    // un contact dont l'id externe dans la nouvelle base correspond à l'id du contact dans l'ancienne base existe
          $entity_id_new=$contacts[0]['id'];

        } else {
          $error = $target." ayant une external id : ".$entitytagtoimport['entity_id']." n'existe pas dans la nouvelle base --> PAS D'IMPORT";
          array_push ($error_log, $error);
          echo $error.PHP_EOL;
          continue 2;
        }
        break;

      /*case 'civicrm_case':                  // non utilisés
        $target = "Case";
        break;*/

      /*case 'civicrm_file':
        print_r($entitytagtoimport);
        $target = "File";
        $target_table = $filestable;
        print_r($target_table[$entitytagtoimport['entity_id']]);

        if(isset($target_table[$entitytagtoimport['entity_id']])){
          $entity_id_new=$target_table[$entitytagtoimport['entity_id']]['new_id'];
        }
        echo $entity_id_new.PHP_EOL;
        echo $entitytagtoimport['entity_id'].PHP_EOL;
        //exit;
        break; */

      /*case 'civicrm_saved_search':          // non traitées car incluses dans installation
        $target = "SavedSearch";
        $target_table = $versiontable;
        break; */

      default : // si une autre valeur, on passe au entityfile suivant
        continue 2; // 1 : suivant dans la boucle swith 2 dans la foreach
    }

    // fin de la récupération de la nouvelle id de l'entité

    // on récupère la nouvelle id du tag

    if (isset($check[$entitytagtoimport['tag_id']])){
      $tag_id_new=$check[$entitytagtoimport['tag_id']];
    } else {
      $error = "Pas de tag ayant une id originale: ".$entitytagtoimport['tag_id']." dans la base originale --> PAS D'IMPORT";
      array_push ($error_log, $error);
      echo $error.PHP_EOL;
      continue;
    }

    // on recherche si un tag entity existe dans la nouvelle base avec cette entity table, la nouvelle id de l'entité et du tag

    $entityTags = civicrm_api4('EntityTag', 'get', [
      'where' => [
        ['entity_table', '=', $entitytagtoimport['entity_table']],
        ['entity_id', '=', $entity_id_new],
        ['tag_id', '=', $tag_id_new],
      ],
      'limit' => 1,
      'checkPermissions' => FALSE,
    ]);

    //echo "Tag pour entité : ".$target." old : ".$entitytagtoimport['entity_id']." | new : ".$entity_id_new;
    //echo " | Tag old : ".$entitytagtoimport['tag_id']." | new : ".$tag_id_new;

    if(isset($entityTags[0])){      // l'entity tag existe déja
      //echo "---> IGNOREE".PHP_EOL;
      echo".";
    } else {
      //echo "---> CREE".PHP_EOL;
      echo".";
      $entityTags = civicrm_api4('EntityTag', 'create', [
        'values' => [
          'entity_table' => $entitytagtoimport['entity_table'],
          'entity_id' => $entity_id_new,
          'tag_id' => $tag_id_new,
        ],
        'checkPermissions' => FALSE,
      ]);
    }


    $check_entity_tags[$entitytagtoimport['id']]=$entityTags[0]['id'];
    //echo $entitytagtoimport['id']."     ".$check_entity_tags[$entitytagtoimport['id']].PHP_EOL;

  }




  echo PHP_EOL.$entity." : ".count($check)." lignes ont été importées sur ".count($values);
      if (count($check)==count($values)) {// le bon nombre de lignes a été importées
          echo " ---> OK".PHP_EOL;
      }else {
        echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;

      }

  echo PHP_EOL."Entity Tags :".count($check_entity_tags)." lignes ont été importées sur ".count($entitytagstoimport);
      if (count($check_entity_tags)==count($entitytagstoimport)) {// le bon nombre de lignes entity tags a été importées
        echo " ---> OK".PHP_EOL;
    }else {
      echo "---> VERIFIER LIMPORT : LIGNES MANQUANTES".PHP_EOL;

    }

      echo "Erreurs : ".PHP_EOL;
      print_r($error_log).PHP_EOL;


      $check_tags_file = $exp_dir."check_130_EntityTags.txt";
      file_put_contents($check_tags_file, json_encode($check_entity_tags, JSON_PRETTY_PRINT));
      echo $check_tags_file." écrit".PHP_EOL;


      return ($check);
}


// check custom fields
  if ($check_custom_field == 1){
    $custom_file = $exp_dir."02_CustomField.txt";
    $json = file_get_contents($custom_file);
    $options = json_decode($json, true);
    check_custom($options);
    }

// check option values
  if ($check_option_values ==1){
    $option_file = $exp_dir."03_option_values.txt";
    $json = file_get_contents($option_file);
    $options = json_decode($json, true);
    check_option_values($options);
    }

// importe organisations
  if($import_organisations ==1) {
    $name =  "05_organisations";
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);
    echo count($toimport)." Organisations à importer".PHP_EOL;
    $check=import_stuff('Contact',$toimport);
    $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." exported".PHP_EOL;

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }

    }


// importe individus
  if($import_individus == 1){
      $contact_file = $exp_dir."10_individuals.txt";
      $json = file_get_contents($contact_file);
      $contacts = json_decode($json, true);
      import_stuff('Contact',$contacts);



      echo "Continuer à importer (O/N) ?";
      $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

      if ($kb=='O' OR $kb=='o'){
        echo "On y va !!!".PHP_EOL;
      } else {
        echo "Vérifiez vos imports et reprenez".PHP_EOL;
        exit;
      }
    }


// importe adresses
  if($import_adresses == 1){
    $name =  "15_adresses";
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);

      echo count($toimport)." Adresses à importer".PHP_EOL;
    $check=import_address('Address',$toimport); // appelle la fonction import  et assigne à check la liste des anciennes id de contact

        $chk_file = $exp_dir."check_".$name.".txt";
      file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
      echo $chk_file." écrit".PHP_EOL;

      echo "Continuer à importer (O/N) ?";
      $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

      if ($kb=='O' OR $kb=='o'){
        echo "On y va !!!".PHP_EOL;
      } else {
        echo "Vérifiez vos imports et reprenez".PHP_EOL;
        exit;
      }
    }

// importe telephones
  if($import_telephones == 1){
        $name = '20_telephone';                     // nom du fichier à importer sans le suffixe
        $toimport_file = $exp_dir.$name.".txt";
        $json = file_get_contents($toimport_file);
        $toimport = json_decode($json, true);

        echo count($toimport)." Téléphones à importer".PHP_EOL;
        $check=import_phone('Phone',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact

          $chk_file = $exp_dir."check_".$name.".txt";
          file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
        echo $chk_file." écrit".PHP_EOL;

        echo "Continuer à importer (O/N) ?";
        $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

      if ($kb=='O' OR $kb=='o'){
        echo "On y va !!!".PHP_EOL;
      } else {
        echo "Vérifiez vos imports et reprenez".PHP_EOL;
        exit;
      }
    }

// importe email
    if($import_email == 1){
      $name = '25_Email';                     // nom du fichier à importer sans le suffixe
      $toimport_file = $exp_dir.$name.".txt";
      $json = file_get_contents($toimport_file);
      $toimport = json_decode($json, true);

      echo count($toimport)." Emails à importer".PHP_EOL;
            $check=import_email('Email',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact
      $chk_file = $exp_dir."check_".$name.".txt";
      file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
      echo $chk_file." écrit".PHP_EOL;

      echo "Continuer à importer (O/N) ?";
      $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

      if ($kb=='O' OR $kb=='o'){
        echo "On y va !!!".PHP_EOL;
      } else {
        echo "Vérifiez vos imports et reprenez".PHP_EOL;
        exit;
      }

      }

// importe Groups
  if($import_groups ==1){
    $name = '28_Groups';                     // nom du fichier des groupes importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $groupstoimport = json_decode($json, true);

    $toimport_file = $exp_dir.$name."_Groups_Contacts.txt";
    $json = file_get_contents($toimport_file);
    $groupscontacttoimport = json_decode($json, true);

    //print_r($groupstoimport);

    echo count($groupstoimport)." Groupes à importer".PHP_EOL;
    echo count($groupscontacttoimport)." GroupContacts à importer".PHP_EOL;

    $check= import_groups('Group',$groupstoimport,$groupscontacttoimport); // appelle la fonction import  et assigne à check la liste des anciennes id de groupes

    $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }
  }

// importe relationships
  if($import_relationships ==1 ){
      $name = '30_Relationship';                     // nom du fichier à importer sans le suffixe
      $toimport_file = $exp_dir.$name.".txt";
      $json = file_get_contents($toimport_file);
      $toimport = json_decode($json, true);

      echo count($toimport)." Relationships à importer".PHP_EOL;
      $check=import_relationship('Relationship',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact


        $chk_file = $exp_dir."check_".$name.".txt";
      file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
      echo $chk_file." écrit".PHP_EOL;

      echo "Continuer à importer (O/N) ?";
      $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

      if ($kb=='O' OR $kb=='o'){
        echo "On y va !!!".PHP_EOL;
      } else {
        echo "Vérifiez vos imports et reprenez".PHP_EOL;
        exit;
      }
    }

// importe utilisations
  if($import_utilisations ==1){
    $name = '40_Custom_Utilisation_du_corps';                     // nom du fichier à importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);

    echo count($toimport)." Utilisations du corps à importer".PHP_EOL;
    $check=import_utilisation('Custom_Utilisation_du_corps',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact


      $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }
  }

// importe protocoles in vivo
  if($import_protinvivo ==1){
    $name = '45_Custom_ProtocolesInVivo';                     // nom du fichier à importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);

    echo count($toimport)." Protocoles in vivo à importer".PHP_EOL;
    $check=import_protinvivo('Custom_Protocoles_in_vivo',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact


      $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }
  }


// importe FinancialType
  if($import_FinancialType ==1){
    $name = '12_FinancialType';                     // nom du fichier à importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);
    $chk_file = $exp_dir."check_".$name.".txt";    // nom du fichier enreigstrant les entités crées

    echo count($toimport)." Financial types à importer".PHP_EOL;

      $check=import_FinancialType('FinancialType',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact
      //$check=import_contribution('Contribution',$toimport,$contribution_old);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact

      file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
      echo $chk_file." écrit".PHP_EOL;
    //}

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }

  }


// importe contributions
  if($import_contributions ==1){
    $name = '50_Contribution';                     // nom du fichier à importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);
    $chk_file = $exp_dir."check_".$name.".txt";    // nom du fichier enreigstrant les entités crées

    echo count($toimport)." Contributions à importer".PHP_EOL;

      $check=import_contribution('Contribution',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact
      //$check=import_contribution('Contribution',$toimport,$contribution_old);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact

      file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
      echo $chk_file." écrit".PHP_EOL;
    //}

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }

  }

// importe events
  if($import_events ==1){
    $name = '60_Event';                     // nom du fichier à importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);

    echo count($toimport)." Evenements à importer".PHP_EOL;
      $check=import_event('Event',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact


        $chk_file = $exp_dir."check_".$name.".txt";
      file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
      echo $chk_file." écrit".PHP_EOL;

      echo "Continuer à importer (O/N) ?";
      $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

      if ($kb=='O' OR $kb=='o'){
        echo "On y va !!!".PHP_EOL;
      } else {
        echo "Vérifiez vos imports et reprenez".PHP_EOL;
        exit;
      }
    }
// importe participants
  if($import_participants ==1){
    $name = '70_Participant';                     // nom du fichier des pariticpantsà importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);
    $chk_file = $exp_dir."check_".$name.".txt";

    echo count($toimport)." Participants à importer".PHP_EOL;

    $name = 'check_60_Event';                           // nom du fichier check event (correspondance ancienne et nouvelle id des event)
                                                        //des evenements à importer, sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport_event = json_decode($json, true);

    echo count($toimport_event)." id d'evenements chargées".PHP_EOL;


    $check=import_participant('Participant',$toimport,$toimport_event);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact


    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }
  }


// importe Activités
  if($import_activites ==1){
    $name = '80_Activites';                     // nom du fichier des pariticpantsà importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);

    echo count($toimport)." Activités à importer".PHP_EOL;
   $check= import_activites('Activity',$toimport);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact


    $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

    // $chk_file contient un element par activité crée ; cahque element comporte l'ancienne et la nouvelle id de l'activité créee
    // il sera utilisé pour les imports de documents.

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }
  }




// importe Notes
  if($import_notes ==1){
    $name = '100_Notes';                     // nom du fichier des notes importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $toimport = json_decode($json, true);

    $chk_file = $exp_dir."check_".$name.".txt";
    // $chk_file contient un element par note crée : clé ancienne id ; valeur nouvelle id.

    echo count($toimport)." Notes à importer".PHP_EOL;

    $name = 'check_30_Relationship';                     // nom du fichier des relations (clé = ancienne id ; valeur : nouvelle id)
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $relationships_old = json_decode($json, true);

    $name = 'check_70_Participant';                     // nom du fichier des participants (clé = ancienne id ; valeur : nouvelle id)
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $participant_old = json_decode($json, true);

    $name = 'check_50_Contribution';                     // nom du fichier des contributions (clé = ancienne id ; valeur : nouvelle id)
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $contribution_old = json_decode($json, true);

    $check= import_notes('Note',$toimport,$relationships_old,$participant_old,$contribution_old);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact



    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }
  }

// importe Documents
  if($import_documents ==1){
    $name = '115_DocumentsContact';                     // nom du fichier des documents contacts
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $documentsContacts = json_decode($json, true);


    echo count($documentsContacts)." Documents à importer".PHP_EOL;

    $name = '120_DocumentsVersion';                     // nom du fichier des documents version
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $documentsVersions = json_decode($json, true);
    $chk_file = $exp_dir."check_".$name.".txt";

    $check= import_documents('Documents',$documentsContacts, $documentsVersions);   // appelle la fonction import  et assigne à check la liste des anciennes id de contact

    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }
  }

// importe Files
  if($import_files ==1){
    $name = '90_Files';                     // nom du fichier à importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $filetoimport = json_decode($json, true);

    $toimport_file = $exp_dir.$name."_entityFiles.txt";
    $json = file_get_contents($toimport_file);
    $entityfiletoimport = json_decode($json, true);

    $toimport_file = $exp_dir."check_80_Activites.txt";
    $json = file_get_contents($toimport_file);
    $activitytable = json_decode($json, true);

    $toimport_file = $exp_dir."check_120_DocumentsVersion.txt";
    $json = file_get_contents($toimport_file);
    $versiontable = json_decode($json, true);

    $toimport_file = $exp_dir."check_100_Notes.txt";
    $json = file_get_contents($toimport_file);
    $notetable = json_decode($json, true);

    echo count($filetoimport)." Fichiers à importer".PHP_EOL;
    echo count($entityfiletoimport)." EntityFiles à importer".PHP_EOL;

    $check= import_files('Files',$filetoimport,$entityfiletoimport,$activitytable,$versiontable,$notetable);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact


    $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

    // $chk_file contient la liste des ficheirs à conserv er

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }
  }



// move Files
  if($mv_files ==1){
    $name = 'check_90_Files';                     // nom du fichier listant les fichiers à déplacer
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $filetoimport = json_decode($json, true);

    echo count($filetoimport)." Fichiers à déplacer".PHP_EOL;

    mv_files($filetoimport,$custom,$custom_orig);           // appelle la fonction mv files

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }
  }


// importe tags
  if($import_tags ==1){
    $name = '130_Tags';                     // nom du fichier à importer sans le suffixe
    $toimport_file = $exp_dir.$name.".txt";
    $json = file_get_contents($toimport_file);
    $tagstoimport = json_decode($json, true);

    $toimport_file = $exp_dir.$name."_entityFiles.txt";
    $json = file_get_contents($toimport_file);
    $entitytagstoimport = json_decode($json, true);

    $toimport_file = $exp_dir."check_80_Activites.txt";
    $json = file_get_contents($toimport_file);
    $activitytable = json_decode($json, true);

    $toimport_file = $exp_dir."check_90_Files.txt";
    $json = file_get_contents($toimport_file);
    $filestable = json_decode($json, true);

    echo count($tagstoimport)." Tags à importer".PHP_EOL;
    echo count($entitytagstoimport)." EntityTags à importer".PHP_EOL;

    $check= import_tags('Tags',$tagstoimport,$entitytagstoimport,$activitytable,$filestable);           // appelle la fonction import  et assigne à check la liste des anciennes id de contact


    $chk_file = $exp_dir."check_".$name.".txt";
    file_put_contents($chk_file, json_encode($check, JSON_PRETTY_PRINT));     // crée un fichier pour verifier les entites crrées
    echo $chk_file." écrit".PHP_EOL;

    // $chk_file contient la liste des ficheirs à conserv er

    echo "Continuer à importer (O/N) ?";
    $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

    if ($kb=='O' OR $kb=='o'){
      echo "On y va !!!".PHP_EOL;
    } else {
      echo "Vérifiez vos imports et reprenez".PHP_EOL;
      exit;
    }
  }


   echo "on continue".PHP_EOL;



