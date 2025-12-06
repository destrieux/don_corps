<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Employeur',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Employeur',
        'group_type' => ['Individual'],
        'title' => E::ts('Employeur'),
        'frontend_title' => E::ts('Employeur'),
        'is_update_dupe' => TRUE,
        'created_date' => '2025-11-30 11:10:25',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Employeur_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Employeur',
        'field_name' => 'current_employer',
        'label' => E::ts('Centre de don'),
        'field_type' => 'Individual',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Employeur_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Employeur',
        'field_name' => 'job_title',
        'label' => E::ts('Fonction'),
        'field_type' => 'Individual',
      ],
    ],
  ],
];
