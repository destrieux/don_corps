<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CustomGroup_CDC_admin',
    'entity' => 'CustomGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'CDC_admin',
        'title' => E::ts('CDC admin'),
        'extends' => 'Organization',
        'extends_entity_column_value' => ['CDC'],
        'weight' => 17,
        'collapse_adv_display' => TRUE,
        'created_date' => '2025-11-06 09:38:17',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'CustomGroup_CDC_admin_CustomField_Directeur_du_Centre_d_accueil',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'CDC_admin',
        'name' => 'Directeur_du_Centre_d_accueil',
        'label' => E::ts('Directeur du Centre d\'accueil'),
        'html_type' => 'Text',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'directeur_du_centre_d_accueil_122',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_CDC_admin_CustomField_Gestionnaire_du_centre_d_accueil',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'CDC_admin',
        'name' => 'Gestionnaire_du_centre_d_accueil',
        'label' => E::ts('Gestionnaire du centre d\'accueil'),
        'html_type' => 'Text',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'gestionnaire_du_centre_d_accueil_123',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_CDC_admin_CustomField_Pr_sident_du_CESP',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'CDC_admin',
        'name' => 'Pr_sident_du_CESP',
        'label' => E::ts('Président du CESP'),
        'html_type' => 'Text',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'pr_sident_du_cesp_120',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_CDC_admin_CustomField_Qualit_du_Pr_sident_du_CESP',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'CDC_admin',
        'name' => 'Qualit_du_Pr_sident_du_CESP',
        'label' => E::ts('Qualité du Président du CESP'),
        'html_type' => 'Text',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'qualit_du_pr_sident_du_cesp_121',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_CDC_admin_CustomField_DPO',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'CDC_admin',
        'name' => 'DPO',
        'label' => E::ts('DPO'),
        'html_type' => 'Text',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'dpo_109',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_CDC_admin_CustomField_Site_web',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'CDC_admin',
        'name' => 'Site_web',
        'label' => E::ts('Site web'),
        'data_type' => 'Link',
        'html_type' => 'Link',
        'text_length' => 2047,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'site_web_125',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
];
