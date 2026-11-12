<?php
use CRM_DonCorps_ExtensionUtil as E;

// Compile pièces et utilisations : action, règle et condition CiviRules

// Champs personnalisés surveillés par la condition
$fieldNames = ['Type_de_poi_ce_3', 'Utilisation2'];

$customFieldIds = civicrm_api4('CustomField', 'get', [
  'select' => ['id'],
  'where' => [
    ['name', 'IN', $fieldNames],
  ],
  'checkPermissions' => FALSE,
])->column('id');

if (count($customFieldIds) !== count($fieldNames)) {
  throw new \CRM_Core_Exception('don_corps : champs personnalisés introuvables parmi ' . implode(', ', $fieldNames));
}

return [
  [ // Action
    'entity' => 'CiviRulesAction',
    'values' => [
      'name' => 'compile_pieces_et_utlisations',
      'label' => E::ts("Compile les pièces et utilisations d'un corps"),
      'class_name' => 'CRM_DonCorps_CivirulesActions_Piece_Compilepiecesutilisations',
      'is_active' => TRUE,
    ],
  ],
  [ // Règle
    'entity' => 'CiviRulesRule',
    'values' => [
      'name' => 'update_pieces_et_utilisations',
      'label' => E::ts('Update pièces et utilisations'),
      'trigger_id:name' => 'changed_individual_custom_data',
      'description' => E::ts("Update la liste des pièces utilisées et des utilisations d'un corps"),
      'is_active' => TRUE,
    ],
  ],
  [ // Action de la règle
    'entity' => 'CiviRulesRuleAction',
    'values' => [
      'rule_id.name' => 'update_pieces_et_utilisations',
      'action_id.name' => 'compile_pieces_et_utlisations',
      'is_active' => TRUE,
    ],
  ],
  [ // Condition de la règle
    'entity' => 'CiviRulesRuleCondition',
    'values' => [
      'rule_id.name' => 'update_pieces_et_utilisations',
      'condition_id.name' => 'contact_custom_field_changed',
      'condition_params' => [
        'custom_field_id' => $customFieldIds,
      ],
      'is_active' => TRUE,
    ],
  ],
];