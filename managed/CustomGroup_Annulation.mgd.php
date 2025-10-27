<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CustomGroup_Annulation',
    'entity' => 'CustomGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Annulation',
        'title' => E::ts('Annulation'),
        'extends' => 'Individual',
        'extends_entity_column_value' => ['Donateur'],
        'weight' => 2,
        'created_date' => '2021-07-21 13:48:40',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'CustomGroup_Annulation_CustomField_N_annulation',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Annulation',
        'name' => 'N_annulation',
        'label' => E::ts('N° annulation'),
        'html_type' => 'Text',
        'is_searchable' => TRUE,
        'help_pre' => E::ts('DD#AAAA-NNNNN
DD : Département du centre d\'accueil des corps
AAAA: année
NNNNN: numéro (5 chiffres)'),
        'text_length' => 20,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'n_annulation_18',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_Annulation_CustomField_Date_d_annulation',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Annulation',
        'name' => 'Date_d_annulation',
        'label' => E::ts('Date annulation'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'text_length' => 255,
        'date_format' => 'dd/mm/yy',
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'date_d_annulation_19',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_Annulation_CustomField_ancien_N_annulation',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Annulation',
        'name' => 'ancien_N_annulation',
        'label' => E::ts('Ancien N° annulation'),
        'html_type' => 'Text',
        'is_active' => NULL,
        'is_view' => TRUE,
        'text_length' => 15,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'ancien_n_annulation_95',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
];
