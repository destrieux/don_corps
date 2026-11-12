<?php
use CRM_DonCorps_ExtensionUtil as E;

// Rules : Déplace un lot de pièces

// Type d'activité utilisé par la condition
$activityType = civicrm_api4('OptionValue', 'get', [
  'select' => ['value'],
  'where' => [
    ['option_group_id:name', '=', 'activity_type'],
    ['name', '=', 'Déplacer un lot de pièces/corps'],
  ],
  'checkPermissions' => FALSE,
])->first();

if (!$activityType) {
  throw new \CRM_Core_Exception("don_corps : type d'activité 'Déplacer un lot de pièces/corps' introuvable");
}
 

return [
  [ // Action
    'entity' => 'CiviRulesAction',
    'values' => [
      'name' => 'deplacelot',
      'label' => E::ts('Déplace un lot de pièces anatomiques'),
      'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Deplacelot',
      'is_active' => TRUE,
    ],
  ],
  [ // Règle
    'entity' => 'CiviRulesRule',
    'values' => [
      'name' => 'déplace_un_lot_de_pièces_anatomiques_',
      'label' => E::ts('Déplace un lot de pièces anatomiques'),
      'trigger_id:name' => 'new_activity',
      'trigger_params' => 'a:1:{s:11:"record_type";s:1:"3";}',
      'description' => E::ts('Déplace un lot de pièces vers le local depuis lequel une activité déplacer pièces anatomiques est créée'),
      'help_text' => E::ts("<p>Déplace un lot de pièces identifiées par leurs codes-barres.</p>\r\n\r\n<p>Lorsqu'une action de type Déplacement de lot de pièce anatomique est créée, elle déplace les pièces figurant dans le champ détails de l'activité vers le contact depuis lequel l'activité est créée (local de conservation).</p>\r\n\r\n<p>Les pièces manquantes ou déjà détruites sont localisées dans cette pièce de stockage et leur statut est modifié en <em>Non Eliminé.</em></p>\r\n"),
      'is_active' => TRUE,
    ],
  ],
  [ // Action de la règle
    'entity' => 'CiviRulesRuleAction',
    'values' => [
      'rule_id.name' => 'déplace_un_lot_de_pièces_anatomiques_',
      'action_id.name' => 'deplacelot',
      'is_active' => TRUE,
    ],
  ],
  [ // Condition de la règle
    'entity' => 'CiviRulesRuleCondition',
    'values' => [
      'rule_id.name' => 'déplace_un_lot_de_pièces_anatomiques_',
      'condition_id.name' => 'activity_of_type',
      'condition_params' => [
        'operator' => '0',
        'activity_type_id' => [$activityType['value']],
      ],
      'is_active' => TRUE,
    ],
  ],
];