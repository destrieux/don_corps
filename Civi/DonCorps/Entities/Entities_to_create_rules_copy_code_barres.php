<?php
use CRM_DonCorps_ExtensionUtil as E;

// Copie code-barres corps : action, règle et condition CiviRules

// Champ personnalisé surveillé par la condition
$customField = civicrm_api4('CustomField', 'get', [
  'select' => ['id'],
  'where' => [
    ['name', '=', 'N_de_pi_ce_ou_de_corps'],
  ],
  'checkPermissions' => FALSE,
])->first();

if (!$customField) {
  throw new \CRM_Core_Exception("don_corps : champ personnalisé 'N_de_pi_ce_ou_de_corps' introuvable");
}

return [
  [ // Action
    'entity' => 'CiviRulesAction',
    'values' => [
      'name' => 'contact_CopyBarCode',
      'label' => E::ts("Copie du code-barres du corps d'un donneur dans le champ caché pièce principale"),
      'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_CopyBarCode',
      'is_active' => TRUE,
    ],
  ],
  [ // Règle
    'entity' => 'CiviRulesRule',
    'values' => [
      'name' => 'copie_code_barre_corps',
      'label' => E::ts('Copie code-barres corps'),
      'trigger_id:name' => 'changed_individual_custom_data',
      'description' => E::ts('Copie le code-barres du corps du donneur dans le champ caché "pièce principale" lors des modifications de n° de pièce'),
      'is_active' => TRUE,
    ],
  ],
  [ // Action de la règle
    'entity' => 'CiviRulesRuleAction',
    'values' => [
      'rule_id.name' => 'copie_code_barre_corps',
      'action_id.name' => 'contact_CopyBarCode',
      'is_active' => TRUE,
    ],
  ],
  [ // Condition de la règle
    'entity' => 'CiviRulesRuleCondition',
    'values' => [
      'rule_id.name' => 'copie_code_barre_corps',
      'condition_id.name' => 'contact_custom_field_changed',
      'condition_params' => [
        'custom_field_id' => [$customField['id']],
      ],
      'is_active' => TRUE,
    ],
  ],
];