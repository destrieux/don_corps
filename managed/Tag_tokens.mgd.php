<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Tag_tokens',
    'entity' => 'Tag',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'tokens',
        'label' => E::ts('tokens'),
        'used_for' => [
          'civicrm_saved_search',
        ],
        'color' => '#749a0e',
      ],
      'match' => ['name'],
    ],
  ],
];
