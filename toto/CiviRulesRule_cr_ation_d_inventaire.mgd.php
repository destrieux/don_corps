<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesRule_cr_ation_d_inventaire',
    'entity' => 'CiviRulesRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'création_d\'inventaire',
        'label' => E::ts('Création d\'inventaire'),
        'trigger_id.name' => 'new_activity',
        'trigger_params' => 'a:1:{s:11:"record_type";s:1:"3";}',
        'description' => E::ts('Crée un nouvel inventaire'),
        'help_text' => '<p>Lorsqu\'une activité de type inventaire est créée depuis un lieu de conservation, un rapport remplace la liste des pièces dans le champ détail ; les pièces sont éventuellement relocalisées et leur statut est corrigé. Le champ \'Inventaires\' des pièces et des corps concernés est mis à jour.</p>

<p>&nbsp;</p>
',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_cr_ation_d_inventaire_CiviRulesRuleAction_1',
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
  [
    'name' => 'CiviRulesRule_cr_ation_d_inventaire_CiviRulesRuleCondition_1',
    'entity' => 'CiviRulesRuleCondition',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'création_d\'inventaire',
        'condition_id.name' => 'activity_of_type',
        'condition_params' => [
          'operator' => '0',
          'activity_type_id' => [60],
        ],
      ],
    ],
  ],
];
