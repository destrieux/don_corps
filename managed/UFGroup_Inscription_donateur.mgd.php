<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Inscription_donateur',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Inscription_donateur',
        'group_type' => ['Contact', 'Donateur'],
        'title' => E::ts('Inscription donateur'),
        'frontend_title' => E::ts('Inscription donateur'),
        'post_url' => 'http://localhost:8888/37_test/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid={contact.id}',
        'cancel_url' => 'http://localhost:8888/37_test/wp-admin/admin.php?page=CiviCRM',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_donateur_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_donateur',
        'field_name:name' => 'Compl_m_nt_tat_civil.Civilit_user',
        'is_required' => TRUE,
        'visibility' => 'Public Pages and Listings',
        'in_selector' => TRUE,
        'label' => E::ts('Civilité'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_donateur_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_donateur',
        'field_name' => 'last_name',
        'is_required' => TRUE,
        'visibility' => 'Public Pages',
        'in_selector' => TRUE,
        'label' => E::ts('Nom de famille'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_donateur_UFField_3',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_donateur',
        'field_name' => 'nick_name',
        'in_selector' => TRUE,
        'label' => E::ts('Nom de naissance'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_donateur_UFField_4',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_donateur',
        'field_name' => 'first_name',
        'is_required' => TRUE,
        'visibility' => 'Public Pages',
        'in_selector' => TRUE,
        'label' => E::ts('Prénom'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_donateur_UFField_5',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_donateur',
        'field_name' => 'birth_date',
        'is_required' => TRUE,
        'in_selector' => TRUE,
        'label' => E::ts('Date de naissance'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_donateur_UFField_6',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_donateur',
        'field_name' => 'custom_54',
        'is_required' => TRUE,
        'in_selector' => TRUE,
        'label' => E::ts('Centre de don'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_donateur_UFField_7',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_donateur',
        'field_name' => 'custom_56',
        'is_required' => TRUE,
        'visibility' => 'Public Pages and Listings',
        'in_selector' => TRUE,
        'label' => E::ts('Date du don'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_donateur_UFField_8',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_donateur',
        'field_name' => 'custom_55',
        'is_required' => TRUE,
        'help_post' => E::ts('37#AAAA-NNNNN
AAAA: année
NNNNN: numéro (5 chiffres)'),
        'help_pre' => E::ts('37#AAAA-NNNNN
AAAA: année
NNNNN: numéro (5 chiffres)'),
        'visibility' => 'Public Pages and Listings',
        'in_selector' => TRUE,
        'label' => E::ts('N° de don'),
        'field_type' => 'Donateur',
      ],
    ],
  ],
];
