<?php
use CRM_DonCorps_ExtensionUtil as E;

// Moyens de paiement : désactivation, renommage et ajout
return [
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'payment_instrument',
      'name' => 'Credit Card',
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'payment_instrument',
      'name' => 'Debit Card',
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'payment_instrument',
      'name' => 'Cash',
      'label' => E::ts('Espèces'),
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'payment_instrument',
      'name' => 'Check',
      'label' => E::ts('Chèque Bancaire'),
      'weight' => 1,
      'is_default' => TRUE,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'payment_instrument',
      'name' => 'EFT',
      'label' => E::ts('Virement'),
      'weight' => 2,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'payment_instrument',
      'name' => 'Leg',
      'label' => E::ts('Leg'),
      'value' => '6',
      'weight' => 3,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'payment_instrument',
      'name' => 'Assurance',
      'label' => E::ts('Assurance'),
      'value' => '7',
      'weight' => 4,
      'is_active' => TRUE,
    ],
  ],
];
