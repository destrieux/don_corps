<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CustomGroup_Caract_ristiques_de_l_activit_',
    'entity' => 'CustomGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Caract_ristiques_de_l_activit_',
        'title' => E::ts('Caractéristiques de l\'activité'),
        'extends' => 'Event',
        'extends_entity_column_value' => ['101'],
        'weight' => 17,
        'collapse_adv_display' => TRUE,
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'CustomGroup_Caract_ristiques_de_l_activit_CustomField_En_attente_de_confirmation',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Caract_ristiques_de_l_activit_',
        'name' => 'En_attente_de_confirmation',
        'label' => E::ts('Confirmé'),
        'data_type:name' => 'Yes or No',
        'html_type' => 'Toggle',
        'default_value' => '0',
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
    'name' => 'CustomGroup_Caract_ristiques_de_l_activit_CustomField_id_de_saisine',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Caract_ristiques_de_l_activit_',
        'name' => 'id_de_saisine',
        'label' => E::ts('id de saisine'),
        'data_type:name' => 'Entity Reference',
        'html_type' => 'Autocomplete-Select',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'fk_entity' => 'Saisine',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
];
