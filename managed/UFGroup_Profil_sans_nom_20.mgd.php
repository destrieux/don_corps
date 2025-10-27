<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Profil_sans_nom_20',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Profil_sans_nom_20',
        'group_type' => ['Contact'],
        'title' => E::ts('Profil sans nom'),
        'frontend_title' => E::ts('Profil sans nom'),
        'is_update_dupe' => TRUE,
        'created_date' => '2023-03-30 13:52:58',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Profil_sans_nom_20_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Profil_sans_nom_20',
        'field_name:name' => 'Compl_m_nt_tat_civil.Adresse_incorrecte',
        'label' => E::ts('Adresse incorrecte'),
        'field_type' => 'Contact',
      ],
    ],
  ],
];
