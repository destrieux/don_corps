<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Centre_d_accueil_des_corps',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Centre_d_accueil_des_corps',
        'group_type' => ['CDC'],
        'title' => E::ts('Centre d\'accueil des corps'),
        'frontend_title' => E::ts('Centre d\'accueil des corps'),
        'post_url' => 'http://localhost:8888/preprod/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid={contact.id}',
        'cancel_url' => 'http://localhost:8888/preprod/wp-admin/admin.php?page=CiviCRM',
        'created_date' => '2025-02-15 19:25:41',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Centre_d_accueil_des_corps_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Centre_d_accueil_des_corps',
        'field_name' => 'organization_name',
        'is_required' => TRUE,
        'label' => E::ts('Nom'),
        'field_type' => 'CDC',
      ],
    ],
  ],
];
