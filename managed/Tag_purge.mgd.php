<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Tag_purge',
    'entity' => 'Tag',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'purge',
        'label' => E::ts('Requêtes utilisées pour les purges'),
        'used_for' => [
          'civicrm_saved_search',
        ],
        'color' => '#d3ac0a',
      ],
      'match' => ['name'],
    ],
  ],
];
