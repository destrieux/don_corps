<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesAction_supprimelot',
    'entity' => 'CiviRulesAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'supprimelot',
        'label' => E::ts('Supprime un lot de pièces anatomiques'),
        'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Supprimelot',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesAction_supprimelot_CiviRulesRuleAction_1',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'supprime_lot_de_pièces',
        'action_id.name' => 'supprimelot',
      ],
    ],
  ],
];
