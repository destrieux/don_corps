<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesAction_creeinventaire',
    'entity' => 'CiviRulesAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'creeinventaire',
        'label' => E::ts('Crée un inventaire de pièces anatomiques'),
        'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Creeinventaire',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesAction_creeinventaire_CiviRulesRuleAction_1',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'création_d\'inventaire',
        'action_id.name' => 'creeinventaire',
      ],
    ],
  ],
];
