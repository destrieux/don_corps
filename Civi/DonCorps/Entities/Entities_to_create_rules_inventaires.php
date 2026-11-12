<?php
use CRM_DonCorps_ExtensionUtil as E;

// Inventaire : action, règle et condition CiviRules

// Type d'activité utilisé par la condition
$activityType = civicrm_api4('OptionValue', 'get', [
  'select' => ['value'],
  'where' => [
    ['option_group_id:name', '=', 'activity_type'],
    ['name', '=', 'Inventaire'],
  ],
  'checkPermissions' => FALSE,
])->first();

if (!$activityType) {
  throw new \CRM_Core_Exception("don_corps : type d'activité 'Inventaire' introuvable");
}

return [
  [ // Action
    'entity' => 'CiviRulesAction',
    'values' => [
      'name' => 'creeinventaire',
      'label' => E::ts('Crée un inventaire de pièces anatomiques'),
      'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Creeinventaire',
      'is_active' => TRUE,
    ],
  ],
  [ // Règle
    'entity' => 'CiviRulesRule',
    'values' => [
      'name' => "création_d'inventaire",
      'label' => E::ts("Création d'inventaire"),
      'trigger_id:name' => 'new_activity',
      'trigger_params' => 'a:1:{s:11:"record_type";s:1:"3";}',
      'description' => E::ts('Crée un nouvel inventaire'),
      'help_text' => E::ts("<p>Lorsqu'une activité de type inventaire est créée depuis un lieu de conservation, un rapport remplace la liste des pièces dans le champ détail ; les pièces sont éventuellement relocalisées et leur statut est corrigé. Le champ 'Inventaires' des pièces et des corps concernés est mis à jour.</p>\r\n"),
      'is_active' => TRUE,
    ],
  ],
  [ // Action de la règle
    'entity' => 'CiviRulesRuleAction',
    'values' => [
      'rule_id.name' => "création_d'inventaire",
      'action_id.name' => 'creeinventaire',
      'is_active' => TRUE,
    ],
  ],
  [ // Condition de la règle
    'entity' => 'CiviRulesRuleCondition',
    'values' => [
      'rule_id.name' => "création_d'inventaire",
      'condition_id.name' => 'activity_of_type',
      'condition_params' => [
        'operator' => '0',
        'activity_type_id' => [$activityType['value']],
      ],
      'is_active' => TRUE,
    ],
  ],
];