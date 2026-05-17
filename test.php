<?php
eval(`cv php:boot`);

$path=getenv('PATH');

var_dump ($path);

    $contactId = 33;

    $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
          'Compl_m_nt_tat_civil.Civilit_user:name',  // Mr_, Mme_, Mlle_, Mx civilité définie dans le profil, à la création du contact
          'gender_id:name',                          // Male, Female, Other
          'prefix_id:name',                          // Mr.,  Mrs., Ms., Dr. (pour autre)
          'postal_greeting_id:name',
          'postal_greeting_id',
          'email_greeting_id:name',
          'email_greeting_id',
          'postal_greeting_id:label',
          'postal_greeting_display',
          'email_greeting_id:label',
          'email_greeting_display',
        ],
        'where' => [
          ['id', '=', $contactId],
        ],
        'checkPermissions' => FALSE,
      ]);

print_r($contacts[0]);
exit;


    $civilite = $contacts[0]['Compl_m_nt_tat_civil.Civilit_user:name'];
    $gender = $contacts[0]['gender_id:name'];
    $prefix = $contacts[0]['prefix_id:name'];

    if ($civilite==NULL){           // si la civilité est nulle on regarde si le genre est défini pour reaffecter la civilité
      switch ($gender) {
            case 'Male':
                $civilite = 'Mr_';
            break ;

            case 'Female':
                if ($prefix=='Ms.'){
                    $civilite = 'Mlle_';
                } else {
                    $civilite = 'Mme_';
                }
            break ;

            default :
                $civilite = 'Mx';
            break ;
        }

    }

        switch ($civilite) {
            case 'Mr_':                                                    // Monsieur
                        $results = civicrm_api4('Contact', 'update', [
                            'values' => [
                            'Compl_m_nt_tat_civil.Civilit_user:name' => $civilite, // utilise si la civilité était nulle initialement
                            'postal_greeting_id:name' => 'Monsieur',
                            'email_greeting_id:name' => 'Monsieur',
                            //'postal_greeting_display' => 'Monsieur',
                            'prefix_id:name' => 'Mr.',
                            'gender_id:name' => 'Male',
                            ],
                            'where' => [
                            ['id', '=', $contactId],
                            ],
                            'checkPermissions' => FALSE,
                        ]);
            break;

            case 'Mme_':                                                  // Genre féminin déclaré
                $results = civicrm_api4('Contact', 'update', [
                    'values' => [
                    'Compl_m_nt_tat_civil.Civilit_user:name' => $civilite, // utilise si la civilité était nulle initialement
                    'postal_greeting_id:name' => 'Madame',
                    'email_greeting_id:name' => 'Madame',
                    //'postal_greeting_display' => 'Madame',
                    'prefix_id:name' => 'Mrs.',
                    'gender_id:name' => 'Female',
                    ],
                'where' => [
                    ['id', '=', $contactId],
                    ],
                'checkPermissions' => FALSE,
                ]);
            break;

            case 'Mlle_':                                                  // Mademoiselle
                $results = civicrm_api4('Contact', 'update', [
                    'values' => [
                    'Compl_m_nt_tat_civil.Civilit_user:name' => $civilite, // utilise si la civilité était nulle initialement
                    'postal_greeting_id:name' => 'Mademoiselle',
                    'email_greeting_id:name' => 'Mademoiselle',
                    //'postal_greeting_display' => 'Mademoiselle',
                    'prefix_id:name' => 'Ms.',
                    'gender_id:name' => 'Female',
                    ],
                    'where' => [
                    ['id', '=', $contactId],
                    ],
                    'checkPermissions' => FALSE,
                ]);
            break;

            case 'Mx':                                                  // indéterminé
                $results = civicrm_api4('Contact', 'update', [
                    'values' => [
                    'Compl_m_nt_tat_civil.Civilit_user:name' => $civilite, // utilise si la civilité était nulle initialement
                    'postal_greeting_id:name' => '{contact.first_name} {contact.last_name}',
                    'email_greeting_id:name' => '{contact.first_name} {contact.last_name}',
                    //'postal_greeting_display' => '{contact.first_name} {contact.last_name}',
                    'prefix_id:name' => 'Dr.',
                    'gender_id:name' => 'Other',
                    ],
                    'where' => [
                    ['id', '=', $contactId],
                    ],
                    'checkPermissions' => FALSE,
                ]);
            break;
        }

     $contacts = civicrm_api4('Contact', 'get', [
        'select' => [
          'Compl_m_nt_tat_civil.Civilit_user:name',  // Mr_, Mme_, Mlle_, Mx civilité définie dans le profil, à la création du contact
          'gender_id:name',                          // Male, Female, Other
          'prefix_id:name',                           // Mr.,  Mrs., Ms., Dr. (pour autre)
          'postal_greeting_id:name',
          'postal_greeting_display',
          'email_greeting_id:name',
          'Compl_m_nt_tat_civil.Civilit_user:name',

        ],
        'where' => [
          ['id', '=', $contactId],
        ],
        'checkPermissions' => FALSE,
      ]);

      print_r($contacts[0]);
      //print_r($results[0]);
