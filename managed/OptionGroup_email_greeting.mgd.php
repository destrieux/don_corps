<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'OptionGroup_email_greeting',
    'entity' => 'OptionGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'email_greeting',
        'title' => E::ts('Type de formule de politesse par courriel'),
        'option_value_fields' => ['name', 'label', 'description'],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'OptionGroup_email_greeting_OptionValue_Dear_contact_first_name_',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'email_greeting',
        'label' => E::ts('Cher(e) {contact.first_name}'),
        'value' => '1',
        'name' => 'Dear {contact.first_name}',
        'filter' => 1,
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
    'name' => 'OptionGroup_email_greeting_OptionValue_Dear_contact_prefix_id_label_contact_first_name_contact_last_name_',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'email_greeting',
        'label' => E::ts('Cher(e) {contact.prefix_id:label} {contact.first_name} {contact.last_name}'),
        'value' => '2',
        'name' => 'Dear {contact.prefix_id:label} {contact.first_name} {contact.last_name}',
        'filter' => 1,
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
    'name' => 'OptionGroup_email_greeting_OptionValue_Dear_contact_prefix_id_label_contact_last_name_',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'email_greeting',
        'label' => E::ts('Cher(e) {contact.prefix_id:label} {contact.last_name}'),
        'value' => '3',
        'name' => 'Dear {contact.prefix_id:label} {contact.last_name}',
        'filter' => 1,
        'is_default' => TRUE,
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
    'name' => 'OptionGroup_email_greeting_OptionValue_Dear_contact_household_name_',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'email_greeting',
        'label' => E::ts('Cher(e) {contact.household_name}'),
        'value' => '5',
        'name' => 'Dear {contact.household_name}',
        'filter' => 2,
        'is_default' => TRUE,
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
    'name' => 'OptionGroup_email_greeting_OptionValue_Madame',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'email_greeting',
        'label' => E::ts('Madame'),
        'value' => '6',
        'name' => 'Madame',
        'filter' => 1,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_email_greeting_OptionValue_Monsieur',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'email_greeting',
        'label' => E::ts('Monsieur'),
        'value' => '7',
        'name' => 'Monsieur',
        'filter' => 1,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_email_greeting_OptionValue_contact_first_name_contact_last_name_',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'email_greeting',
        'label' => E::ts('{contact.first_name} {contact.last_name}'),
        'value' => '8',
        'name' => '{contact.first_name} {contact.last_name}',
        'filter' => 1,
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
    'name' => 'OptionGroup_email_greeting_OptionValue_Mademoiselle',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'email_greeting',
        'label' => E::ts('Mademoiselle'),
        'value' => '9',
        'name' => 'Mademoiselle',
        'filter' => 1,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_email_greeting_OptionValue_Customized',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'email_greeting',
        'label' => E::ts('Madame, Monsieur,'),
        'value' => '4',
        'name' => 'Customized',
        'is_default' => TRUE,
        'is_reserved' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
];
