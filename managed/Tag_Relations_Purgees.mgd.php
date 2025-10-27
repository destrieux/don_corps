<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Tag_Relations_Purgees',
    'entity' => 'Tag',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Relations Purgees',
        'label' => E::ts('Contacts dont les relations ont été purgées'),
        'description' => E::ts('Donneurs dont les relations ont été purgées - évite des purges multiples'),
        'used_for' => [
          'civicrm_contact',
        ],
        'color' => '#cd7979',
      ],
      'match' => ['name'],
    ],
  ],
];
