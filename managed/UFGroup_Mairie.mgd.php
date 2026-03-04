<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Mairie',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Mairie',
        'group_type' => ['Mairies'],
        'title' => E::ts('Mairie'),
        'frontend_title' => E::ts('Mairie'),
        'post_url' => 'http://localhost:8888/preprod/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid={contact.id}',
        'cancel_url' => 'http://localhost:8888/preprod/wp-admin/admin.php?page=CiviCRM',
        'created_date' => '2025-02-15 19:37:36',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Mairie_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Mairie',
        'field_name' => 'organization_name',
        'is_required' => TRUE,
        'label' => E::ts('Nom'),
        'field_type' => 'Mairies',
      ],
    ],
  ],
];
