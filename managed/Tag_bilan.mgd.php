<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Tag_bilan',
    'entity' => 'Tag',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'bilan',
        'label' => E::ts('bilan'),
        'used_for' => [
          'civicrm_saved_search',
        ],
        'color' => '#3df542',
      ],
      'match' => ['name'],
    ],
  ],
];
