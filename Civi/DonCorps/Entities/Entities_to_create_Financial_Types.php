<?php
use CRM_DonCorps_ExtensionUtil as E;

// Création des types de contributions financières, à créer à l'installation
return [
  [
    'entity' => 'FinancialType',
    'values' => [
      'name' => 'Donation',
      'label' => E::ts('Don'),
      'is_deductible' => TRUE,
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'FinancialType',
    'values' => [
      'name' => 'Don',
      'label' => E::ts('Don'),
      'is_deductible' => TRUE,
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'FinancialType',
    'values' => [
      'name' => 'Member Dues',
      'label' => E::ts('Cotisations'),
      'is_deductible' => TRUE,
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'FinancialType',
    'values' => [
      'name' => 'Cotisation des membres',
      'label' => E::ts('Cotisation des membres'),
      'is_deductible' => TRUE,
      'is_active' => FALSE,   // à mettre à FALSE hors Tours
    ],
  ],
  [
    'entity' => 'FinancialType',
    'values' => [
      'name' => 'Contribution Prise en charge Corps',
      'label' => E::ts('Contribution Prise en charge Corps'),
      'is_deductible' => TRUE,
      'is_active' => FALSE,   // à mettre à FALSE hors Tours
    ],
  ],
  [
    'entity' => 'FinancialType',
    'values' => [
      'name' => 'Campaign Contribution',
      'label' => E::ts('Contribution à la campagne'),
      'is_active' => FALSE,
    ],
  ],
  [
    'entity' => 'FinancialType',
    'values' => [
      'name' => 'Event Fee',
      'label' => E::ts('Inscription événement'),
      'is_active' => FALSE,
    ],
  ],
];