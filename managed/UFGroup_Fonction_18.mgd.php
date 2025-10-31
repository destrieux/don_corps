<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Fonction_18',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Fonction_18',
        'group_type' => ['Individual', 'Personnel'],
        'title' => E::ts('Fonction'),
        'frontend_title' => E::ts('Fonction'),
        'is_update_dupe' => TRUE,
        'created_date' => '2022-04-17 07:45:48',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Fonction_18_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Fonction_18',
        'field_name' => 'job_title',
        'label' => E::ts('Fonction'),
        'field_type' => 'Individual',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Fonction_18_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Fonction_18',
        'field_name' => 'current_employer',
        'label' => E::ts('Centre d\'accueil des corps'),
        'field_type' => 'Individual',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Fonction_18_UFField_3',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Fonction_18',
        'field_name:name' => 'infos_personnel.M_tier',
        'label' => E::ts('Métier'),
        'field_type' => 'Personnel',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Fonction_18_UFField_4',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Fonction_18',
        'field_name:name' => 'infos_personnel.Cat_gorie',
        'label' => E::ts('Catégorie'),
        'field_type' => 'Personnel',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Fonction_18_UFField_5',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Fonction_18',
        'field_name:name' => 'infos_personnel.BAP',
        'label' => E::ts('BAP'),
        'field_type' => 'Personnel',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Fonction_18_UFField_6',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Fonction_18',
        'field_name:name' => 'infos_personnel.Contrat',
        'label' => E::ts('Contrat'),
        'field_type' => 'Personnel',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Fonction_18_UFField_7',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Fonction_18',
        'field_name:name' => 'infos_personnel.Date_debut_fonctions',
        'label' => E::ts('Date du début des fonctions'),
        'field_type' => 'Personnel',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Fonction_18_UFField_8',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Fonction_18',
        'field_name:name' => 'infos_personnel.Date_fin_fonctions',
        'label' => E::ts('Date de la fin des fonctions'),
        'field_type' => 'Personnel',
      ],
    ],
  ],
];
