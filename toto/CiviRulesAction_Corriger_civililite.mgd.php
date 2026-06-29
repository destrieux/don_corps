<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesAction_Corriger_civililite',
    'entity' => 'CiviRulesAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Corriger_civililite',
        'label' => E::ts('Corriger la civilite'),
        'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_FixCivilite',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesAction_Corriger_civililite_CiviRulesRuleAction_1',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'maj_genre_',
        'action_id.name' => 'Corriger_civililite',
      ],
    ],
  ],
];
