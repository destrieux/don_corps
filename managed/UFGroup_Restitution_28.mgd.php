<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Restitution_28',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Restitution_28',
        'group_type' => ['Donateur'],
        'title' => E::ts('Restitution'),
        'frontend_title' => E::ts('Restitution'),
        'is_update_dupe' => TRUE,
        'created_date' => '2023-05-01 19:00:52',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Restitution_28_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Restitution_28',
        'field_name' => 'custom_48',
        'label' => E::ts('Souhait funeraire personne reférente'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Restitution_28_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Restitution_28',
        'field_name' => 'custom_46',
        'label' => E::ts('Date de restitution'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Restitution_28_UFField_3',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Restitution_28',
        'field_name' => 'custom_47',
        'label' => E::ts('Pompes funèbres mandatées par personne référente'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
];
