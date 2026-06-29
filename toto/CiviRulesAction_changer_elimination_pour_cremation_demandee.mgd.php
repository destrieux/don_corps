<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesAction_changer_elimination_pour_cremation_demandee',
    'entity' => 'CiviRulesAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'changer_elimination_pour_cremation_demandee',
        'label' => E::ts('Change le mode d\'élimination pour crémation demandée'),
        'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_Changeelimination',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesAction_changer_elimination_pour_cremation_demandee_CiviRulesRuleAction_1',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'envoyer_mail_si_demande_cremation',
        'action_id.name' => 'changer_elimination_pour_cremation_demandee',
      ],
    ],
  ],
];
