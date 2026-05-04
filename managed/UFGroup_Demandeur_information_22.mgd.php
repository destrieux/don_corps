<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Demandeur_information_22',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Demandeur_information_22',
        'group_type' => [
          'Contact',
          'Demandeur_d_information',
        ],
        'title' => E::ts('Demandeur information'),
        'frontend_title' => E::ts('Demandeur d\'information'),
        'post_url' => 'http://localhost:8888/preprod/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid={contact.id}',
        'cancel_url' => 'http://localhost:8888/preprod/wp-admin/admin.php?page=CiviCRM',
        'created_date' => '2023-04-29 05:51:58',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Demandeur_information_22_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Demandeur_information_22',
        'field_name:name' => 'Compl_m_nt_tat_civil.Civilit_user',
        'is_required' => TRUE,
        'in_selector' => TRUE,
        'label' => E::ts('Civilité'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Demandeur_information_22_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Demandeur_information_22',
        'field_name' => 'last_name',
        'is_required' => TRUE,
        'in_selector' => TRUE,
        'is_searchable' => TRUE,
        'label' => E::ts('Nom patronymique (de naissance)'),
        'field_type' => 'Demandeur_d_information',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Demandeur_information_22_UFField_3',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Demandeur_information_22',
        'field_name' => 'nick_name',
        'label' => E::ts('Nom d\'usage'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Demandeur_information_22_UFField_4',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Demandeur_information_22',
        'field_name' => 'nick_name',
        'is_required' => TRUE,
        'in_selector' => TRUE,
        'is_searchable' => TRUE,
        'label' => E::ts('Nom d\'usage'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Demandeur_information_22_UFField_5',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Demandeur_information_22',
        'field_name' => 'first_name',
        'is_required' => TRUE,
        'visibility' => 'Public Pages and Listings',
        'in_selector' => TRUE,
        'is_searchable' => TRUE,
        'label' => E::ts('Prénom'),
        'field_type' => 'Demandeur_d_information',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Demandeur_information_22_UFField_6',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Demandeur_information_22',
        'field_name:name' => 'Demandeur_information.Date_d_envoi_d_informations',
        'is_required' => TRUE,
        'visibility' => 'Public Pages and Listings',
        'in_selector' => TRUE,
        'label' => E::ts('Date envoi informations'),
        'field_type' => 'Contact',
      ],
    ],
  ],
];
