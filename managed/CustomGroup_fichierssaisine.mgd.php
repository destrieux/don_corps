<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CustomGroup_fichierssaisine',
    'entity' => 'CustomGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'fichierssaisine',
        'title' => E::ts('fichierssaisine'),
        'extends' => 'Saisine',
        'weight' => 23,
        'collapse_adv_display' => TRUE,
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'CustomGroup_fichierssaisine_CustomField_Avis_CESP',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'fichierssaisine',
        'name' => 'Avis_CESP',
        'label' => E::ts('Avis CESP'),
        'data_type' => 'File',
        'html_type' => 'File',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_fichierssaisine_CustomField_Convention',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'fichierssaisine',
        'name' => 'Convention',
        'label' => E::ts('Convention'),
        'data_type' => 'File',
        'html_type' => 'File',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_fichierssaisine_CustomField_Devis',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'fichierssaisine',
        'name' => 'Devis',
        'label' => E::ts('Devis'),
        'data_type' => 'File',
        'html_type' => 'File',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_fichierssaisine_CustomField_Liste_des_participants',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'fichierssaisine',
        'name' => 'Liste_des_participants',
        'label' => E::ts('Liste des participants'),
        'data_type' => 'File',
        'html_type' => 'File',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_fichierssaisine_CustomField_Programme',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'fichierssaisine',
        'name' => 'Programme',
        'label' => E::ts('Programme'),
        'data_type' => 'File',
        'html_type' => 'File',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_fichierssaisine_CustomField_Saisine',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'fichierssaisine',
        'name' => 'Saisine',
        'label' => E::ts('Saisine'),
        'data_type' => 'File',
        'html_type' => 'File',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
];
