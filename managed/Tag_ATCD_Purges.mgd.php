<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Tag_ATCD_Purges',
    'entity' => 'Tag',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'ATCD Purges',
        'label' => E::ts('ATCD Purges'),
        'description' => E::ts('Donneurs non inclus dans protocole dont les ATCD ont été purges - évite des purges multiples'),
        'used_for' => [
          'civicrm_contact',
        ],
        'color' => '#2d8de6',
      ],
      'match' => ['name'],
    ],
  ],
];
