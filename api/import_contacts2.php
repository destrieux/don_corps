<?php
eval(`cv php:boot`);

function import_stuff(){
    $entity = func_get_arg(0);     // nom de l'entité à créer (contact...)
    $values = func_get_arg(1);     // parametres de cette entité
   
    foreach ($values as $value) {
       // echo $value['id'].PHP_EOL;
        $value['external_identifier']=$value['id'];

        unset ($value['id']);
        unset ($value['hash']);
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

        $contacts = civicrm_api4('Contact', 'get', [
            'select' => [
              'id',
            ],
            'where' => [
              ['external_identifier', '=', $value['external_identifier']],
            ],
            'limit' => 1,
            'checkPermissions' => FALSE,
          ]);
  
        //echo "contact_id: ".$contacts[0]['id'].PHP_EOL;

        $id_to_update=$contacts[0]['id'];

        if ($id_to_update==0){             // si le contact n'existe pas on le crée
            echo "Creating : ".$value['sort_name']." | external id : ".$value['external_identifier'].PHP_EOL;
            $results = civicrm_api4('Contact', 'create', [
             'values' => $value,
              'checkPermissions' => FALSE,
            ]); 
  
          } else {                                // si le contact exite on l'update
            echo "Updating : ".$value['sort_name']." | external id : ".$value['external_identifier']." | id : ".$id_to_update.PHP_EOL;
            
            //print_r($value);
             $results = civicrm_api4('Contact', 'update', [
              'values' => $value,
              'where' => [
                ['id', '=', $id_to_update],
              ],
              'checkPermissions' => FALSE,
            ]); 
echo "toto".PHP_EOL;
            //print_r($results);
           }


}
}






// importe organisations

    $contact_file = "/Users/destri_c/Desktop/import/01_organisations.txt";
    $contenu = file_get_contents($contact_file);
    $contenu = trim($contenu);
    $contacts = eval($contenu);
    import_stuff('Contact',$contacts);

// importe individus
    $contact_file = "/Users/destri_c/Desktop/import/05_individuals.txt";
    $contenu = file_get_contents($contact_file);
    $contenu = trim($contenu);
    $contacts = eval($contenu);
    import_stuff('Contact',$contacts);