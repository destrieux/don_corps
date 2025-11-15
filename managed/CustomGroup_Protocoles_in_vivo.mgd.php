<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CustomGroup_Protocoles_in_vivo',
    'entity' => 'CustomGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Protocoles_in_vivo',
        'title' => E::ts('Protocoles in vivo'),
        'extends' => 'Individual',
        'extends_entity_column_value' => ['Donateur'],
        'style' => 'Tab with table',
        'weight' => 11,
        'is_multiple' => TRUE,
        'collapse_adv_display' => TRUE,
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'OptionGroup_Protocoles_in_vivo_intitul_du_protocole',
    'entity' => 'OptionGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Protocoles_in_vivo_intitul_du_protocole',
        'title' => E::ts('Protocoles in vivo :: intitulé du protocole'),
        'data_type' => 'Int',
        'is_reserved' => FALSE,
        'option_value_fields' => ['name', 'label'],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'OptionGroup_Protocoles_in_vivo_intitul_du_protocole_OptionValue_Protocole_Test',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'Protocoles_in_vivo_intitul_du_protocole',
        'label' => E::ts('Protocole Test'),
        'value' => '1',
        'name' => 'Protocole_Test',
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_Protocoles_in_vivo_CustomField_Intitul_du_protocole',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Protocoles_in_vivo',
        'name' => 'Intitul_du_protocole',
        'label' => E::ts('intitulé du protocole'),
        'data_type' => 'Int',
        'html_type' => 'Select',
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'intitul_du_protocole_194',
        'option_group_id.name' => 'Protocoles_in_vivo_intitul_du_protocole',
        'in_selector' => TRUE,
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_Protocoles_in_vivo_CustomField_identifiant_dans_le_s_protocole_s_',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Protocoles_in_vivo',
        'name' => 'identifiant_dans_le_s_protocole_s_',
        'label' => E::ts('identifiant dans le protocole'),
        'html_type' => 'Text',
        'is_searchable' => TRUE,
        'text_length' => 255,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'identifiant_dans_le_protocole_121',
        'in_selector' => TRUE,
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup_Protocoles_in_vivo_CustomField_date_d_inclusion_in_vivo',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Protocoles_in_vivo',
        'name' => 'date_d_inclusion_in_vivo',
        'label' => E::ts('date inclusion in vivo'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'text_length' => 255,
        'date_format' => 'dd/mm/yy',
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'date_inclusion_in_vivo_122',
        'in_selector' => TRUE,
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
];
