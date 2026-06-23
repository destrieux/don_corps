<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Personnel_de_centre_de_don_de_corps',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Personnel_de_centre_de_don_de_corps',
        'group_type' => ['Contact', 'Personnel'],
        'title' => E::ts('Personnel de centre de don de corps'),
        'frontend_title' => E::ts('Personnel de centre de don de corps'),
        'post_url' => 'http://localhost:8888/preprod/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid={contact.id}',
        'cancel_url' => 'http://localhost:8888/preprod/wp-admin/admin.php?page=CiviCRM',
        'created_date' => '2025-02-15 19:13:21',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Personnel_de_centre_de_don_de_corps_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Personnel_de_centre_de_don_de_corps',
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
    'name' => 'UFGroup_Personnel_de_centre_de_don_de_corps_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Personnel_de_centre_de_don_de_corps',
        'field_name' => 'last_name',
        'is_required' => TRUE,
        'in_selector' => TRUE,
        'label' => E::ts('Nom paronymique (de naissance)'),
        'field_type' => 'Personnel',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Personnel_de_centre_de_don_de_corps_UFField_3',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Personnel_de_centre_de_don_de_corps',
        'field_name' => 'nick_name',
        'label' => E::ts('Nom d\'usage'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Personnel_de_centre_de_don_de_corps_UFField_4',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Personnel_de_centre_de_don_de_corps',
        'field_name' => 'nick_name',
        'is_required' => TRUE,
        'visibility' => 'Public Pages and Listings',
        'in_selector' => TRUE,
        'label' => E::ts('Nom d\'usage'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Personnel_de_centre_de_don_de_corps_UFField_5',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Personnel_de_centre_de_don_de_corps',
        'field_name' => 'nick_name',
        'is_required' => TRUE,
        'visibility' => 'Public Pages and Listings',
        'in_selector' => TRUE,
        'label' => E::ts('Nom d\'usage'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Personnel_de_centre_de_don_de_corps_UFField_6',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Personnel_de_centre_de_don_de_corps',
        'field_name' => 'nick_name',
        'is_required' => TRUE,
        'visibility' => 'Public Pages and Listings',
        'in_selector' => TRUE,
        'label' => E::ts('Nom d\'usage'),
        'field_type' => 'Contact',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Personnel_de_centre_de_don_de_corps_UFField_7',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Personnel_de_centre_de_don_de_corps',
        'field_name' => 'first_name',
        'is_required' => TRUE,
        'visibility' => 'Public Pages and Listings',
        'in_selector' => TRUE,
        'label' => E::ts('Prénom'),
        'field_type' => 'Personnel',
      ],
    ],
  ],
];
