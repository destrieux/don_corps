<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesRule_neutralise_adresse_postale',
    'entity' => 'CiviRulesRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'neutralise_adresse_postale',
        'label' => E::ts('Neutralise adresse postale'),
        'trigger_id.name' => 'new_activity',
        'trigger_params' => 'a:1:{s:11:"record_type";s:1:"0";}',
        'description' => E::ts('Neutralise adresse postale en cas de retour de courrier'),
        'help_text' => '<p>Si une activité de type "Modification des coordonnées" avec le sujet "Retour mail postal pour adresse erronnée" est créee&nbsp;</p>

<p>passage à OUI de adresse erronée</p>
',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_neutralise_adresse_postale_CiviRulesRuleAction_1',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'neutralise_adresse_postale',
        'action_id.name' => 'set_custom_field',
        'action_params' => [
          'field_id' => '35',
          'value' => '1',
        ],
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_neutralise_adresse_postale_CiviRulesRuleCondition_1',
    'entity' => 'CiviRulesRuleCondition',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'neutralise_adresse_postale',
        'condition_id.name' => 'activity_of_type',
        'condition_params' => [
          'operator' => '0',
          'activity_type_id' => ['57'],
        ],
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_neutralise_adresse_postale_CiviRulesRuleCondition_2',
    'entity' => 'CiviRulesRuleCondition',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'neutralise_adresse_postale',
        'condition_link' => 'AND',
        'condition_id.name' => 'contact_has_activity_with_details',
        'condition_params' => [
          'operator' => 'contains',
          'text' => E::ts('Retour mail postal pour adresse erronée'),
        ],
      ],
    ],
  ],
];
