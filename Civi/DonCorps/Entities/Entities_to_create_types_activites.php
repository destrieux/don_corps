<?php
use CRM_DonCorps_ExtensionUtil as E;

// Option values du groupe activity_type, exportées le 2026-11-22 16:54
return [
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'Pledge write-off',
      'label' => E::ts('Annulation d\'une promesse de don'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'Devis',
      'label' => E::ts('Devis'),
      'description' => E::ts('Devis pour une activité se déroulant au laboratoire/centre de don'),
    ],
  ],
];
