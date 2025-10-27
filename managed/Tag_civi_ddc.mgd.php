<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Tag_civi_ddc',
    'entity' => 'Tag',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'civi_ddc',
        'label' => E::ts('Requêtes utilisées pour créer pages'),
        'used_for' => [
          'civicrm_saved_search',
        ],
        'color' => '#ad95c9',
      ],
      'match' => ['name'],
    ],
  ],
];
