<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Lieu_de_stockage',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Lieu_de_stockage',
        'group_type' => ['Emprunteur'],
        'title' => E::ts('Lieu de stockage'),
        'frontend_title' => E::ts('Locaux de conservation'),
        'post_url' => 'http://localhost:8888/37_test/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid={contact.id}',
        'cancel_url' => 'http://localhost:8888/37_test/wp-admin/admin.php?page=CiviCRM&q=civicrm%2Fstockage',
        'created_date' => '2025-02-15 19:35:55',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Lieu_de_stockage_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Lieu_de_stockage',
        'field_name' => 'organization_name',
        'is_required' => TRUE,
        'label' => E::ts('Nom'),
        'field_type' => 'Emprunteur',
      ],
    ],
  ],
];
