<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'DedupeRuleGroup_numeros_don_annulation_dc_2',
    'entity' => 'DedupeRuleGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'contact_type' => 'Individual',
        'threshold' => 10,
        'used' => 'Unsupervised',
        'name' => 'numeros_don_annulation_dc_2',
        'title' => E::ts('numeros don annulation dc 2'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'DedupeRuleGroup_numeros_don_annulation_dc_2_DedupeRule_1',
    'entity' => 'DedupeRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'dedupe_rule_group_id.name' => 'numeros_don_annulation_dc_2',
        'rule_table' => 'civicrm_value_annulation_1',
        'rule_field' => 'n_annulation_18',
        'rule_weight' => 10,
      ],
    ],
  ],
  [
    'name' => 'DedupeRuleGroup_numeros_don_annulation_dc_2_DedupeRule_2',
    'entity' => 'DedupeRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'dedupe_rule_group_id.name' => 'numeros_don_annulation_dc_2',
        'rule_table' => 'civicrm_value_promesse_de_d_9',
        'rule_field' => 'n_de_don_14',
        'rule_weight' => 10,
      ],
    ],
  ],
  [
    'name' => 'DedupeRuleGroup_numeros_don_annulation_dc_2_DedupeRule_3',
    'entity' => 'DedupeRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'dedupe_rule_group_id.name' => 'numeros_don_annulation_dc_2',
        'rule_table' => 'civicrm_value_prise_en_char_8',
        'rule_field' => 'n_de_d_c_s_20',
        'rule_weight' => 10,
      ],
    ],
  ],
];
