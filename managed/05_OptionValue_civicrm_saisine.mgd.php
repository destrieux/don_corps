<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'OptionValue_civicrm_saisine',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'cg_extend_objects',
        'label' => E::ts('Saisines'),
        'value' => 'Saisine',
        'name' => 'civicrm_saisine',
        'weight' => 3,
        'is_reserved' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
];
