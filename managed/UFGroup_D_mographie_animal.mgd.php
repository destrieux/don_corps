<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_D_mographie_animal',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'D_mographie_animal',
        'group_type' => ['Individual', 'Contact'],
        'title' => E::ts('Démographie animal'),
        'frontend_title' => E::ts('Démographie animal'),
        'is_update_dupe' => TRUE,
        'created_date' => '2025-11-01 16:32:29',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_D_mographie_animal_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'D_mographie_animal',
        'field_name' => 'birth_date',
        'label' => E::ts('Date de naissance'),
        'field_type' => 'Individual',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_D_mographie_animal_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'D_mographie_animal',
        'field_name' => 'deceased_date',
        'label' => E::ts('Date de Décès'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_D_mographie_animal_UFField_3',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'D_mographie_animal',
        'field_name:name' => 'Compl_m_nt_tat_civil.Heure_du_d_c_s',
        'help_pre' => E::ts('hh:mm'),
        'label' => E::ts('Heure du décès'),
        'field_type' => 'Contact',
      ],
    ],
  ],
];
