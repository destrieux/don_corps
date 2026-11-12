<?php
use CRM_DonCorps_ExtensionUtil as E;

// Rôles des participants : renommage des rôles utilisés, désactivation des autres
return [
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'participant_role',
      'name' => 'Attendee',
      'label' => E::ts('Invité'),
      'value' => '1',
      'filter' => 1,
      'weight' => 1,
      'is_default' => TRUE,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'participant_role',
      'name' => 'Volunteer',
      'label' => E::ts('Organisateur'),
      'value' => '2',
      'weight' => 2,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'participant_role',
      'name' => 'Host',
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'participant_role',
      'name' => 'Speaker',
      'is_active' => FALSE,
    ],
  ],
];