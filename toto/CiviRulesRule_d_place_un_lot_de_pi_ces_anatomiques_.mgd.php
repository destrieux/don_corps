<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesRule_d_place_un_lot_de_pi_ces_anatomiques_',
    'entity' => 'CiviRulesRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'déplace_un_lot_de_pièces_anatomiques_',
        'label' => E::ts('Déplace un lot de pièces anatomiques '),
        'trigger_id.name' => 'new_activity',
        'trigger_params' => 'a:1:{s:11:"record_type";s:1:"3";}',
        'description' => E::ts('Déplace un lot de pièces vers le local depuis lequel une activité déplacer pièces anatomiques est créée'),
        'help_text' => '<p>Déplace un lot de pièces identifiées par leurs codes-Barres.</p>

<p>Lorsqu\'une action de type Déplacement de lot de pièce anatomique est créée, elle déplace les pièces figurant dans le champ détails de l\'activité vers le contact depuis lequel l\'activité est crée (local de conservation).</p>

<p>Les pièces manquantes ou déjà détruites sont localisées dans cette pièce de stockage et leur statut est modifié en <em>Non Eliminé.</em></p>
',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_d_place_un_lot_de_pi_ces_anatomiques_CiviRulesRuleAction_1',
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
  [
    'name' => 'CiviRulesRule_d_place_un_lot_de_pi_ces_anatomiques_CiviRulesRuleCondition_1',
    'entity' => 'CiviRulesRuleCondition',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'déplace_un_lot_de_pièces_anatomiques_',
        'condition_id.name' => 'activity_of_type',
        'condition_params' => [
          'operator' => '0',
          'activity_type_id' => [62],
        ],
      ],
    ],
  ],
];
