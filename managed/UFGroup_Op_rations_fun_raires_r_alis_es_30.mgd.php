<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Op_rations_fun_raires_r_alis_es_30',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Op_rations_fun_raires_r_alis_es_30',
        'group_type' => ['Donateur'],
        'title' => E::ts('Opérations funéraires réalisées'),
        'frontend_title' => E::ts('Opérations funéraires réalisées'),
        'is_update_dupe' => TRUE,
        'created_date' => '2023-05-01 19:05:10',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Op_rations_fun_raires_r_alis_es_30_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Op_rations_fun_raires_r_alis_es_30',
        'field_name' => 'custom_41',
        'label' => E::ts('Date de sortie définitive'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Op_rations_fun_raires_r_alis_es_30_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Op_rations_fun_raires_r_alis_es_30',
        'field_name' => 'custom_40',
        'label' => E::ts('Type d\'opération funéraire réalisée'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Op_rations_fun_raires_r_alis_es_30_UFField_3',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Op_rations_fun_raires_r_alis_es_30',
        'field_name' => 'custom_42',
        'label' => E::ts('Date opérations funéraires'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Op_rations_fun_raires_r_alis_es_30_UFField_4',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Op_rations_fun_raires_r_alis_es_30',
        'field_name' => 'custom_43',
        'label' => E::ts('Date approximative de réalisation des opérations funéraires'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
];
