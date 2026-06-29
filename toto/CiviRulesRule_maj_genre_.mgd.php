<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesRule_maj_genre_',
    'entity' => 'CiviRulesRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'maj_genre_',
        'label' => E::ts('MAJ Genre '),
        'trigger_id.name' => 'changed_contact_custom_data',
        'description' => E::ts('Met à jour le genre et les formules de politesse'),
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_maj_genre_CiviRulesRuleAction_1',
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
  [
    'name' => 'CiviRulesRule_maj_genre_CiviRulesRuleCondition_1',
    'entity' => 'CiviRulesRuleCondition',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'maj_genre_',
        'condition_id.name' => 'contact_custom_field_changed',
        'condition_params' => [
          'custom_field_id' => [31],
        ],
      ],
    ],
  ],
];
