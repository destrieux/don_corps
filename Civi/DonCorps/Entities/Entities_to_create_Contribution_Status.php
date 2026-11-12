<?php
use CRM_DonCorps_ExtensionUtil as E;

// Statuts de contribution : renommage des statuts de contribution utilisés, désactivation des autres
return [
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'contribution_status',
      'name' => 'Completed',
      'label' => E::ts('Encaissé'),
      'weight' => 1,
      'is_reserved' => TRUE,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'contribution_status',
      'name' => 'Pending',
      'label' => E::ts('Promesse de don'),
      'weight' => 2,
      'is_default' => TRUE,
      'is_reserved' => TRUE,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'contribution_status',
      'name' => 'Cancelled',
      'label' => E::ts('Annulé'),
      'weight' => 3,
      'is_reserved' => TRUE,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'contribution_status',
      'name' => 'Failed',
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'contribution_status',
      'name' => 'Refunded',
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'contribution_status',
      'name' => 'Partially paid',
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'contribution_status',
      'name' => 'Pending refund',
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'contribution_status',
      'name' => 'Chargeback',
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'contribution_status',
      'name' => 'Template',
      'is_active' => FALSE,
    ],
  ],
];