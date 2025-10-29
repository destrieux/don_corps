<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CustomGroup_CDC_Administration',
    'entity' => 'CustomGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'CDC_Administration',
        'title' => E::ts('CDC Administration'),
        'extends' => 'Organization',
        'extends_entity_column_value' => ['CDC'],
        'weight' => 5,
        'collapse_adv_display' => TRUE,
        'created_date' => '2025-10-19 06:33:04',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'CustomGroup_CDC_Administration_CustomField_Directeur',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'CDC_Administration',
        'name' => 'Directeur',
        'label' => E::ts('Directeur'),
        'html_type' => 'Text',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'directeur_98',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_CDC_Administration_CustomField_Gestionnaire',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'CDC_Administration',
        'name' => 'Gestionnaire',
        'label' => E::ts('Gestionnaire(s)'),
        'html_type' => 'Text',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'gestionnaire_99',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_CDC_Administration_CustomField_Pr_parateur_s_',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'CDC_Administration',
        'name' => 'Pr_parateur_s_',
        'label' => E::ts('Préparateur(s)'),
        'html_type' => 'Text',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'pr_parateur_s__101',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_CDC_Administration_CustomField_site_www',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'CDC_Administration',
        'name' => 'site_www',
        'label' => E::ts('site www'),
        'data_type' => 'Link',
        'html_type' => 'Link',
        'text_length' => 2047,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'site_www_100',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
];
