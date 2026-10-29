<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'OptionGroup_gender',
    'entity' => 'OptionGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'gender',
        'title' => E::ts('Genre'),
        'description' => E::ts('CiviCRM est pré-configuré avec des choix standards de genre (ex. masculin, féminin, autre...). Modifiez ces options selon les besoins de votre installation.'),
        'data_type' => 'Integer',
        'option_value_fields' => ['name', 'label', 'description'],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'OptionGroup_gender_OptionValue_Female',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'gender',
        'label' => E::ts('Féminin'),
        'value' => '1',
        'name' => 'Female',
        'is_default' => NULL,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_gender_OptionValue_Male',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'gender',
        'label' => E::ts('Masculin'),
        'value' => '2',
        'name' => 'Male',
        'is_default' => NULL,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_gender_OptionValue_Other',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'gender',
        'label' => E::ts('Autre'),
        'value' => '3',
        'name' => 'Other',
        'is_default' => NULL,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
];
