<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CustomGroup_Documents_de_l_activit_',
    'entity' => 'CustomGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Documents_de_l_activit_',
        'title' => E::ts('Documents de l\'activité'),
        'extends' => 'Event',
        'extends_entity_column_value' => ['101'],
        'weight' => 20,
        'collapse_adv_display' => TRUE,
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'CustomGroup_Documents_de_l_activit_CustomField_Feuille_d_margement',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Documents_de_l_activit_',
        'name' => 'Feuille_d_margement',
        'label' => E::ts('Feuille d\'émargement'),
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
