<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'DedupeRuleGroup_num_don_annulation_deces',
    'entity' => 'DedupeRuleGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'contact_type' => 'Individual',
        'threshold' => 10,
        'used' => 'Supervised',
        'name' => 'num_don_annulation_deces',
        'title' => E::ts('num don annulation deces'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'DedupeRuleGroup_num_don_annulation_deces_DedupeRule_1',
    'entity' => 'DedupeRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'dedupe_rule_group_id.name' => 'num_don_annulation_deces',
        'rule_table' => 'civicrm_value_annulation_19',
        'rule_field' => 'n_annulation_18',
        'rule_weight' => 10,
      ],
    ],
  ],
  [
    'name' => 'DedupeRuleGroup_num_don_annulation_deces_DedupeRule_2',
    'entity' => 'DedupeRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'dedupe_rule_group_id.name' => 'num_don_annulation_deces',
        'rule_table' => 'civicrm_value_prise_en_char_18',
        'rule_field' => 'n_de_d_c_s_20',
        'rule_weight' => 10,
      ],
    ],
  ],
  [
    'name' => 'DedupeRuleGroup_num_don_annulation_deces_DedupeRule_3',
    'entity' => 'DedupeRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'dedupe_rule_group_id.name' => 'num_don_annulation_deces',
        'rule_table' => 'civicrm_value_promesse_de_d_17',
        'rule_field' => 'n_de_don_14',
        'rule_weight' => 10,
      ],
    ],
  ],
];
