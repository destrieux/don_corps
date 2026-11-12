<?php
use CRM_DonCorps_ExtensionUtil as E;

// Création des préfixes de contact, à créer à l'installation
return [
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'individual_prefix',
      'label' => 'Mme',
      'value' => '1',
      'name' => 'Mrs.',
      'weight' => 1,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'individual_prefix',
      'label' => 'Melle',
      'value' => '2',
      'name' => 'Ms.',
      'weight' => 2,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'individual_prefix',
      'label' => 'M.',
      'value' => '3',
      'name' => 'Mr.',
      'weight' => 3,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'individual_prefix',
      'label' => 'Mx.',
      'value' => '4',
      'name' => 'Dr.',
      'weight' => 4,
      'is_active' => TRUE,
    ],
  ],
];