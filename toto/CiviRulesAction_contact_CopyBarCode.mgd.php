<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesAction_contact_CopyBarCode',
    'entity' => 'CiviRulesAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'contact_CopyBarCode',
        'label' => E::ts('Copie du code barre du corps d\'un donneur dans le champ cache piece principale'),
        'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_CopyBarCode',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesAction_contact_CopyBarCode_CiviRulesRuleAction_1',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'copie_code_barre_corps',
        'action_id.name' => 'contact_CopyBarCode',
      ],
    ],
  ],
];
