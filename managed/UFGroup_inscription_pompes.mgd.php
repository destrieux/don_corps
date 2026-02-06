<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_inscription_pompes',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'inscription_pompes',
        'group_type' => ['Pompes'],
        'title' => E::ts('Pompes'),
        'frontend_title' => E::ts('Création entreprise pompes funèbres'),
        'post_url' => 'http://localhost:8888/37_test/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid={contact.id}',
        'cancel_url' => 'http://localhost:8888/37_test/wp-admin/admin.php?page=CiviCRM',
        'created_date' => '2021-04-17 22:21:39',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_inscription_pompes_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'inscription_pompes',
        'field_name' => 'organization_name',
        'is_required' => TRUE,
        'visibility' => 'Public Pages and Listings',
        'in_selector' => TRUE,
        'label' => E::ts('Nom'),
        'field_type' => 'Pompes',
      ],
    ],
  ],
];
