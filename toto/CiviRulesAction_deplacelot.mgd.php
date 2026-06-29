<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesAction_deplacelot',
    'entity' => 'CiviRulesAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'deplacelot',
        'label' => E::ts('Déplace un lot de pièces anatomiques'),
        'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Deplacelot',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesAction_deplacelot_CiviRulesRuleAction_1',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'déplace_un_lot_de_pièces_anatomiques_',
        'action_id.name' => 'deplacelot',
      ],
    ],
  ],
];
