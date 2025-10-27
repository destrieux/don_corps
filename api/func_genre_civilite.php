<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;


//////////// fonction create_entity2 () /////////
//  Cette fonction créer ou upgrade les entités
//  Suyntaxe : create_entity2($type d'entité à créer $veleurs pour cette entité)
//
//////////////

      
function change_civilite(){
  
    $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
            'last_name',
            'email_greeting_id:name',
            'postal_greeting_id:name',
            'prefix_id:name',
            'gender_id:name',
          ],
        'where' => [
          ['contact_type', '=', 'Individual'],
        ],
        'checkPermissions' => FALSE,
      ]);

//print_r($contacts);
 //return $results[0]['id']; // retourne l'id de l'entité créée

    foreach($contacts as $contact){
        echo $contact['last_name']." : ";
        //   echo $contact['gender_id:name']." ".$contact['prefix_id:name']." ".$contact['email_greeting_id:name']." ".$contact['postal_greeting_id:label'].PHP_EOL;
        
        $genre=$contact['gender_id:name'];
        $civilite=$contact['prefix_id:name'];
        $id= $contact['id'];

        //  echo "civilité : ".$civilite.PHP_EOL;
        //  echo "postal greeting : ".$contact['postal_greeting_id:name'].PHP_EOL;
        //  echo "email greeting : ".$contact['email_greeting_id:name'].PHP_EOL;
        //  echo "postal_greeting_display : ".$contact['postal_greeting_display'].PHP_EOL.PHP_EOL;

        switch ($genre) {
            case 'Male':                                                    // Genre masculin déclaré
                if ($civilite == "Mrs." or $civilite == 'Ms.' or $civilite == "Mx."){             // Male et civilté Mrs ou Ls. (Mlle)ou Mx.--> erreur et civilité Mx. et nom prenom en formule de politesse
                    echo "ERREUR : Masculin et Mme. Melle. ou Mx. -> Mx nom, prénom".PHP_EOL;
                    $results = civicrm_api4('Contact', 'update', [
                        'values' => [
                        'postal_greeting_id:name' => '{contact.first_name} {contact.last_name}',
                        'email_greeting_id:name' => '{contact.first_name} {contact.last_name}',
                        'postal_greeting_display' => '{contact.first_name} {contact.last_name}',
                        'prefix_id:name' => 'Mx.', 
                        ],
                        'where' => [
                        ['id', '=', $id],
                        ],
                        'checkPermissions' => FALSE,
                    ]);


                } else {                                                    // Male et pas de civilité ou Monsieur --> Monsieur pour les formules de politesse et Mr. pour civilité
                    echo "Masculin et Mr. ou pas de civilité -> M., Monsieur".PHP_EOL;
                        $results = civicrm_api4('Contact', 'update', [
                            'values' => [
                            'postal_greeting_id:name' => 'Monsieur',
                            'email_greeting_id:name' => 'Monsieur',
                            'postal_greeting_display' => 'Monsieur',
                            'prefix_id:name' => 'Mr.',
                            ],
                            'where' => [
                            ['id', '=', $id],
                            ],
                            'checkPermissions' => FALSE,
                        ]);
                }
            break;
    
            case 'Female':                                                  // Genre féminin déclaré
                if ($civilite == "Mr." or $civilite == "Mx."){             // Female et civilté Mr ou Mx.--> erreur et civilité Mx. et nom prenom en formule de politesse
                    echo "ERREUR : Feminin et Mr. ou Mx. -> Mx. et nom prenom".PHP_EOL;
                    $results = civicrm_api4('Contact', 'update', [
                        'values' => [
                        'postal_greeting_id:name' => '{contact.first_name} {contact.last_name}',
                        'email_greeting_id:name' => '{contact.first_name} {contact.last_name}',
                        'postal_greeting_display' => '{contact.first_name} {contact.last_name}',
                        'prefix_id:name' => 'Mx.', 
                        ],
                        'where' => [
                        ['id', '=', $id],
                        ],
                        'checkPermissions' => FALSE,
                    ]);


                } elseif ($civilite == "Ms."){                          // Female et Miss --> Mademoiselle
                    echo "Féminin et Melle --> Melle et Mademoiselle".PHP_EOL;
                    $results = civicrm_api4('Contact', 'update', [
                        'values' => [
                        'postal_greeting_id:name' => 'Mademoiselle',
                        'email_greeting_id:name' => 'Mademoiselle',
                        'postal_greeting_display' => 'Mademoiselle',
                        'prefix_id:name' => 'Ms.', 
                        ],
                        'where' => [
                        ['id', '=', $id],
                        ],
                        'checkPermissions' => FALSE,
                    ]);

                }else {                                                    // Femaale et pas de civilité ou Madame --> Madame pour les formules de politesse et Mrs. pour civilité
                    echo "Féminin et Mme ou pas de civilité -> Mme, Madame".PHP_EOL;
                        $results = civicrm_api4('Contact', 'update', [
                            'values' => [
                            'postal_greeting_id:name' => 'Madame',
                            'email_greeting_id:name' => 'Madame',
                            'postal_greeting_display' => 'Madame',
                            'prefix_id:name' => 'Mrs.',
                            ],
                            'where' => [
                            ['id', '=', $id],
                            ],
                            'checkPermissions' => FALSE,
                        ]);
                }
            break;

            default:            // autre genre déclaré ou pas de genre
                echo "Autre Genre ou pas de genre -> Mx., nom prénom".PHP_EOL;
                $results = civicrm_api4('Contact', 'update', [
                    'values' => [
                    'postal_greeting_id:name' => '{contact.first_name} {contact.last_name}',
                    'email_greeting_id:name' => '{contact.first_name} {contact.last_name}',
                    'postal_greeting_display' => '{contact.first_name} {contact.last_name}',
                    'prefix_id:name' => 'Mx.', 
                    ],
                    'where' => [
                    ['id', '=', $id],
                    ],
                    'checkPermissions' => FALSE,
                ]);
            break;

        }
                /*  $output = civicrm_api4('Contact', 'get', [
                        'select' => [
                            'last_name',
                            'email_greeting_id:name',
                            'postal_greeting_id:name',
                            'postal_greeting_display',
                            'prefix_id:label',
                            'gender_id:name',
                        ],
                        'where' => [
                            ['id', '=', $id],
                        ],
                        'checkPermissions' => FALSE,
                    ]);
                    echo "civilité : ".$output[0]['prefix_id:label'].PHP_EOL;
                    echo "postal greeting : ".$output[0]['postal_greeting_id:name'].PHP_EOL;
                    echo "email greeting : ".$output[0]['email_greeting_id:name'].PHP_EOL;
                    echo "postal_greeting_display : ".$output[0]['postal_greeting_display'].PHP_EOL.PHP_EOL;
    */
    } 
}           // Fin déclaration fonction change_civilite();
change_civilite();
