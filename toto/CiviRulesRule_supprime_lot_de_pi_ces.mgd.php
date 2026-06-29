<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesRule_supprime_lot_de_pi_ces',
    'entity' => 'CiviRulesRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'supprime_lot_de_pièces',
        'label' => E::ts('Supprime lot de pièces'),
        'trigger_id.name' => 'new_activity',
        'trigger_params' => 'a:1:{s:11:"record_type";s:1:"3";}',
        'description' => E::ts('Supprime un lot de pièces identifiées par leur code Barres.'),
        'help_text' => '<p>Lorsqu\'une action de type Suppression de lot de pièce anatomique est créée, elle supprime les pièces figurant dans le champ détails de l\'activité.</p>

<p>Si un code-barres de corps est saisi, l\'utilisateur est invité à utiliser le tableau de bord des corps.&nbsp;Les pièces manquantes ou déja détruites sont ignorées.</p>

<p>Sinon, la pièce est passée en "Crémation" et sa localisation est supprimée.</p>

<p>Un rapport remplace les données du champ Détails</p>

<p>&nbsp;</p>
',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_supprime_lot_de_pi_ces_CiviRulesRuleAction_1',
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
  [
    'name' => 'CiviRulesRule_supprime_lot_de_pi_ces_CiviRulesRuleCondition_1',
    'entity' => 'CiviRulesRuleCondition',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'supprime_lot_de_pièces',
        'condition_id.name' => 'activity_of_type',
        'condition_params' => [
          'operator' => '0',
          'activity_type_id' => [61],
        ],
      ],
    ],
  ],
];
