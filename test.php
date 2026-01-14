<?php

eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;


    //$contactId = $triggerData->getContactId();
    $contactId = 24;

   // Liste les contacts ayant une utilisation avec une piece de type corps
    $contacts = civicrm_api4('Contact', 'get', [
      'select' => [
        'contact_type:name',
        'champs_caches.piece_prinicpale',
        'custom_utilisation_du_corps.N_de_pi_ce_ou_de_corps',
      ],
      'join' => [
        ['Custom_Utilisation_du_corps AS custom_utilisation_du_corps', 'LEFT', ['custom_utilisation_du_corps.entity_id', '=', 'id']],
      ],
      'where' => [
        ['custom_utilisation_du_corps.Type_de_poi_ce_3:name', '=', 'Corps_entier_tronc'],
        ['id', '=', $contactId],
      ],
      'checkPermissions' => FALSE,
    ]);

    //print_r($contacts);


    if (isset($contacts[0])){     // si ce contact a une pièce de type corps, $piece_ple prend la valeur du code barre de cette piece
      echo "il y a une piece de type corps".PHP_EOL;
      $piece_ple = $contacts[0]['custom_utilisation_du_corps.N_de_pi_ce_ou_de_corps'];

    } else {                      // Si ce contact n'a pas de piece de type corps on met $piece_ple à NULL
      echo "Pas de piece de type corps".PHP_EOL;             
      $piece_ple = NULL;
    }

    //echo "piece ppale : ".$piece_ple."\n" ;

    //on met à jour le champ cache piece_prinicpale à jour pour ce contact
        $results = civicrm_api4('Contact', 'update', [
          'values' => [
            'champs_caches.piece_prinicpale' => $piece_ple,
          ],
          'where' => [
            ['id', '=', $contactId],
          ],
          'checkPermissions' => FALSE,
        ]);
  



