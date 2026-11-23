<?php
use CRM_DonCorps_ExtensionUtil as E;

// Corriger la civilité : action, règle et condition CiviRules

// Champ personnalisé surveillé par la condition
$customField = civicrm_api4('CustomField', 'get', [
  'select' => ['id'],
  'where' => [
    ['name', '=', 'Civilit_user'],
    ['custom_group_id:name', '=','Compl_m_nt_tat_civil'],
  ],
  'checkPermissions' => FALSE,
])->first();

if (!$customField) {
  throw new \CRM_Core_Exception("don_corps : champ personnalisé 'Civilit_user' introuvable");
}

return [
  [ // Action
    'entity' => 'CiviRulesAction',
    'values' => [
      'name' => 'Corriger_civililite',
      'label' => E::ts('Corriger la civilité'),
      'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_FixCivilite',
      'is_active' => TRUE,
    ],
  ],
  [ // Règle
    'entity' => 'CiviRulesRule',
    'values' => [
      'name' => 'maj_genre_',
      'label' => E::ts('MAJ Genre'),
      'trigger_id:name' => 'changed_contact_custom_data',
      'description' => E::ts('Met à jour le genre et les formules de politesse'),
      'is_active' => TRUE,
    ],
  ],
  [ // Action de la règle
    'entity' => 'CiviRulesRuleAction',
    'values' => [
      'rule_id.name' => 'maj_genre_',
      'action_id.name' => 'Corriger_civililite',
      'is_active' => TRUE,
    ],
  ],
  [ // Condition de la règle
    'entity' => 'CiviRulesRuleCondition',
    'values' => [
      'rule_id.name' => 'maj_genre_',
      'condition_id.name' => 'contact_custom_field_changed',
      'condition_params' => [
        'custom_field_id' => [$customField['id']],
      ],
      'is_active' => TRUE,
    ],
  ],
];