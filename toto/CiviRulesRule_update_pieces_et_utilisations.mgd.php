<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesRule_update_pieces_et_utilisations',
    'entity' => 'CiviRulesRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'update_pieces_et_utilisations',
        'label' => E::ts('update pieces et utilisations'),
        'trigger_id.name' => 'changed_individual_custom_data',
        'description' => E::ts('Update la liste des pieces utilisées et des utilisations d\'un corps'),
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_update_pieces_et_utilisations_CiviRulesRuleAction_1',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'update_pieces_et_utilisations',
        'action_id.name' => 'compile_pieces_et_utlisations',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_update_pieces_et_utilisations_CiviRulesRuleCondition_1',
    'entity' => 'CiviRulesRuleCondition',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'update_pieces_et_utilisations',
        'condition_id.name' => 'contact_custom_field_changed',
        'condition_params' => [
          'custom_field_id' => [75, 78],
        ],
      ],
    ],
  ],
];
