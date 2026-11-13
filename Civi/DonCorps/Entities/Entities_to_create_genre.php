<?php
use CRM_DonCorps_ExtensionUtil as E;

// Option values du groupe gender, exportées le 2026-11-13 14:47
return [
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'gender',
      'name' => 'Female',
      'label' => E::ts('Féminin'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'gender',
      'name' => 'Male',
      'label' => E::ts('Masculin'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'gender',
      'name' => 'Other',
      'label' => E::ts('Autre'),
    ],
  ],
];
