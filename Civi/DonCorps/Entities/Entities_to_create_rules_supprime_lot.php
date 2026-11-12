<?php
use CRM_DonCorps_ExtensionUtil as E;

// Supprime lot : action, règle et condition CiviRules

// Type d'activité utilisé par la condition
$activityType = civicrm_api4('OptionValue', 'get', [
  'select' => ['value'],
  'where' => [
    ['option_group_id:name', '=', 'activity_type'],
    ['name', '=', "Suppression d'une pièces"],
  ],
  'checkPermissions' => FALSE,
])->first();

if (!$activityType) {
  throw new \CRM_Core_Exception("don_corps : type d'activité \"Suppression d'une pièces\" introuvable");
}

return [
  [ // Action
    'entity' => 'CiviRulesAction',
    'values' => [
      'name' => 'supprimelot',
      'label' => E::ts('Supprime un lot de pièces anatomiques'),
      'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Supprimelot',
      'is_active' => TRUE,
    ],
  ],
  [ // Règle
    'entity' => 'CiviRulesRule',
    'values' => [
      'name' => 'supprime_lot_de_pièces',
      'label' => E::ts('Supprime lot de pièces'),
      'trigger_id:name' => 'new_activity',
      'trigger_params' => 'a:1:{s:11:"record_type";s:1:"3";}',
      'description' => E::ts('Supprime un lot de pièces identifiées par leur code-barres.'),
      'help_text' => E::ts("<p>Lorsqu'une action de type Suppression de lot de pièce anatomique est créée, elle supprime les pièces figurant dans le champ détails de l'activité.</p>\r\n\r\n<p>Si un code-barres de corps est saisi, l'utilisateur est invité à utiliser le tableau de bord des corps. Les pièces manquantes ou déjà détruites sont ignorées.</p>\r\n\r\n<p>Sinon, la pièce est passée en \"Crémation\" et sa localisation est supprimée.</p>\r\n\r\n<p>Un rapport remplace les données du champ Détails.</p>\r\n"),
      'is_active' => TRUE,
    ],
  ],
  [ // Action de la règle
    'entity' => 'CiviRulesRuleAction',
    'values' => [
      'rule_id.name' => 'supprime_lot_de_pièces',
      'action_id.name' => 'supprimelot',
      'is_active' => TRUE,
    ],
  ],
  [ // Condition de la règle
    'entity' => 'CiviRulesRuleCondition',
    'values' => [
      'rule_id.name' => 'supprime_lot_de_pièces',
      'condition_id.name' => 'activity_of_type',
      'condition_params' => [
        'operator' => '0',
        'activity_type_id' => [$activityType['value']],
      ],
      'is_active' => TRUE,
    ],
  ],
];