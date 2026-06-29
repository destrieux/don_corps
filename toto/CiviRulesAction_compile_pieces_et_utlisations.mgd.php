<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesAction_compile_pieces_et_utlisations',
    'entity' => 'CiviRulesAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'compile_pieces_et_utlisations',
        'label' => E::ts('Compile les pièces et utilisations d\'un corps'),
        'class_name' => 'CRM_DonCorps_CivirulesActions_Piece_Compilepiecesutilisations',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesAction_compile_pieces_et_utlisations_CiviRulesRuleAction_1',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'update_pieces_et_utilisations',
        'action_id.name' => 'compile_pieces_et_utlisations',
      ],
    ],
  ],
];
