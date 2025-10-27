<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'UFGroup_Inscription_anat_compar_e',
    'entity' => 'UFGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Inscription_anat_compar_e',
        'group_type' => ['Animal'],
        'title' => E::ts('Inscription anat comparée'),
        'frontend_title' => E::ts('Inscription anat comparée'),
        'description' => E::ts('Inscription pièce anatomie comparée'),
        'post_url' => 'http://localhost:8888/37_test/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid={contact.id}',
        'created_date' => '2025-10-06 16:02:42',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_anat_compar_e_UFField_1',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_anat_compar_e',
        'field_name' => 'first_name',
        'label' => E::ts('Identifiant alpha (rappel espèce..)'),
        'field_type' => 'Animal',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_anat_compar_e_UFField_2',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_anat_compar_e',
        'field_name' => 'last_name',
        'label' => E::ts('Identifiant numérique'),
        'field_type' => 'Animal',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_anat_compar_e_UFField_3',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_anat_compar_e',
        'field_name' => 'gender_id',
        'label' => E::ts('Genre'),
        'field_type' => 'Animal',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_anat_compar_e_UFField_4',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_anat_compar_e',
        'field_name:name' => 'animal.Esp_ce',
        'label' => E::ts('Espèce'),
        'field_type' => 'Animal',
      ],
    ],
  ],
  [
    'name' => 'UFGroup_Inscription_anat_compar_e_UFField_5',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'uf_group_id.name' => 'Inscription_anat_compar_e',
        'field_name:name' => 'animal.Provenance',
        'label' => E::ts('Provenance'),
        'field_type' => 'Animal',
      ],
    ],
  ],
];
