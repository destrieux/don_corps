<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CustomGroup_Participants',
    'entity' => 'CustomGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Participants',
        'title' => E::ts('Participants'),
        'extends' => 'Event',
        'extends_entity_column_value' => ['101'],
        'weight' => 19,
        'collapse_adv_display' => TRUE,
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'CustomGroup_Participants_CustomField_Nombre_d_apprenants',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Participants',
        'name' => 'Nombre_d_apprenants',
        'label' => E::ts('Nombre d\'apprenants'),
        'data_type:name' => 'Integer',
        'html_type:name' => 'Single-line input field (text or numeric)',
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
    'name' => 'CustomGroup_Participants_CustomField_Nombre_de_formateurs',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Participants',
        'name' => 'Nombre_de_formateurs',
        'label' => E::ts('Nombre de formateurs'),
        'data_type:name' => 'Integer',
        'html_type:name' => 'Single-line input field (text or numeric)',
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
    'name' => 'CustomGroup_Participants_CustomField_Nombre_de_Staff',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Participants',
        'name' => 'Nombre_de_Staff',
        'label' => E::ts('Nombre de Staff'),
        'data_type:name' => 'Integer',
        'html_type:name' => 'Single-line input field (text or numeric)',
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
    'name' => 'CustomGroup_Participants_CustomField_Tarif_par_apprenant',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Participants',
        'name' => 'Tarif_par_apprenant',
        'label' => E::ts('Tarif par apprenant'),
        'data_type' => 'Money',
        'html_type:name' => 'Single-line input field (text or numeric)',
        'default_value' => '450',
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
    'name' => 'CustomGroup_Participants_CustomField_Tarif_par_formateur',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Participants',
        'name' => 'Tarif_par_formateur',
        'label' => E::ts('Tarif par formateur'),
        'data_type' => 'Money',
        'html_type:name' => 'Single-line input field (text or numeric)',
        'default_value' => '450',
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
    'name' => 'CustomGroup_Participants_CustomField_Tarif_par_staff',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Participants',
        'name' => 'Tarif_par_staff',
        'label' => E::ts('Tarif par staff'),
        'data_type' => 'Money',
        'html_type:name' => 'Single-line input field (text or numeric)',
        'default_value' => '450',
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
