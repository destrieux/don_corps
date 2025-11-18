<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_CESP_29',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'CESP_29',
        'group_type' => ['Donateur'],
        'title' => E::ts('Comité éthique'),
        'frontend_title' => E::ts('Comité éthique'),
        'is_update_dupe' => TRUE,
        'created_date' => '2023-05-01 19:02:52',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_CESP_29_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'CESP_29',
        'field_name' => 'custom_37',
        'label' => E::ts('Avis du Comité éthique'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_CESP_29_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'CESP_29',
        'field_name' => 'custom_38',
        'label' => E::ts('ref avis Comité éthique'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
];
