<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesRule_copie_code_barre_corps',
    'entity' => 'CiviRulesRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'copie_code_barre_corps',
        'label' => E::ts('Copie code barre corps'),
        'trigger_id.name' => 'changed_individual_custom_data',
        'description' => E::ts('Copie le code barre du corps du donneur dans le champ caché "piece principale" lors des modif de n° de piece'),
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_copie_code_barre_corps_CiviRulesRuleAction_1',
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
  [
    'name' => 'CiviRulesRule_copie_code_barre_corps_CiviRulesRuleCondition_1',
    'entity' => 'CiviRulesRuleCondition',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'copie_code_barre_corps',
        'condition_id.name' => 'contact_custom_field_changed',
        'condition_params' => [
          'custom_field_id' => [74],
        ],
      ],
    ],
  ],
];
