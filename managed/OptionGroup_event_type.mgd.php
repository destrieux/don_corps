<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'OptionGroup_event_type',
    'entity' => 'OptionGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'event_type',
        'title' => E::ts('Type d\'événement'),
        'description' => E::ts('Utilisez les types d\'événements pour catégoriser vos événements. Les flux d\'événement peuvent être filtrés par types et les recherches sur les participants peuvent utiliser le type d\'événement comme critère.'),
        'data_type' => 'Integer',
        'option_value_fields' => ['name', 'label', 'description'],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'OptionGroup_event_type_OptionValue_C_r_monie_Hommage',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'event_type',
        'label' => E::ts('Cérémonie d\'hommage'),
        'value' => '100',
        'name' => 'Cérémonie Hommage',
        'is_default' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_event_type_OptionValue_Conference',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'event_type',
        'label' => E::ts('Conférence'),
        'value' => '1',
        'name' => 'Conference',
        'is_active' => FALSE,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_event_type_OptionValue_Exhibition',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'event_type',
        'label' => E::ts('Exposition'),
        'value' => '2',
        'name' => 'Exhibition',
        'is_active' => FALSE,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_event_type_OptionValue_Fundraiser',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'event_type',
        'label' => E::ts('Collecteur de fonds'),
        'value' => '3',
        'name' => 'Fundraiser',
        'is_active' => FALSE,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_event_type_OptionValue_Meeting',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'event_type',
        'label' => E::ts('Réunion'),
        'value' => '4',
        'name' => 'Meeting',
        'is_active' => FALSE,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_event_type_OptionValue_Performance',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'event_type',
        'label' => E::ts('Performance'),
        'value' => '5',
        'name' => 'Performance',
        'is_active' => FALSE,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_event_type_OptionValue_Activit_au_laboratoire',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'event_type',
        'label' => E::ts('Activité au laboratoire'),
        'value' => '101',
        'name' => 'Activité au laboratoire',
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
];
