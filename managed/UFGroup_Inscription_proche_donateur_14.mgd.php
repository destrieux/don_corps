<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Inscription_proche_donateur_14',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Inscription_proche_donateur_14',
        'group_type' => ['Contact', 'Proches'],
        'title' => E::ts('Inscription proche donateur'),
        'frontend_title' => E::ts('Inscription proche donateur'),
        'post_url' => 'http://localhost:8888/37_test/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid={contact.id}',
        'cancel_url' => 'http://localhost:8888/37_test/wp-admin/admin.php?page=CiviCRM',
        'created_date' => '2021-04-17 22:21:39',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_proche_donateur_14_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_proche_donateur_14',
        'field_name:name' => 'Compl_m_nt_tat_civil.Civilit_user',
        'is_required' => TRUE,
        'label' => E::ts('Civilité'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_proche_donateur_14_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_proche_donateur_14',
        'field_name' => 'last_name',
        'is_required' => TRUE,
        'label' => E::ts('Nom de famille'),
        'field_type' => 'Proches',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_proche_donateur_14_UFField_3',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_proche_donateur_14',
        'field_name' => 'first_name',
        'label' => E::ts('Prénom'),
        'field_type' => 'Proches',
      ],
    ],
  ],
];
