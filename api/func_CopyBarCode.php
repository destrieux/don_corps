<?php
eval(`cv php:boot`);

$contactId=4;


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
    ['custom_utilisation_du_corps.Type_de_poi_ce_3', '=', 1],
    ['id', '=', $contactId],
  ],
  'checkPermissions' => FALSE,
]);

if (isset($contacts[0]['champs_caches.piece_prinicpale'])){
  $piece_ple = $contacts[0]['champs_caches.piece_prinicpale'];
  $num_corps = $contacts[0]['custom_utilisation_du_corps.N_de_pi_ce_ou_de_corps'];
}else{
  $num_corps = NULL;
  $piece_ple = 1;
}

echo "piece ppale : ".$piece_ple."\n" ;
echo "code barres : ".$num_corps."\n";

if ($piece_ple != $num_corps)
  {

    $results = civicrm_api4('Contact', 'update', [
      'values' => [
        'champs_caches.piece_prinicpale' => $num_corps,
      ],
      'where' => [
        ['id', '=', $contactId],
      ],
      'checkPermissions' => FALSE,
    ]);
  }








