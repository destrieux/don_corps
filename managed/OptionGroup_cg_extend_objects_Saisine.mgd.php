<?php

use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'cg_extend_objects:Saisine',
    'entity' => 'OptionValue',
    'cleanup' => 'always',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'cg_extend_objects',
        'label' => E::ts('Saisines'),
        'value' => 'Saisine',
        'name' => 'civicrm_saisine',
        'is_reserved' => TRUE,
        'is_active' => TRUE,
      ],
    ],
  ],
];