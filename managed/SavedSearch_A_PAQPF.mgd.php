<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_A_PAQPF',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'A_PAQPF',
        'label' => E::ts('A PAQPF'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'sort_name',
            'contact_type:label',
            'contact_sub_type:label',
          ],
          'orderBy' => [],
          'where' => [
            [
              'Contact_RelationshipCache_Contact_01.far_relation:name',
              '=',
              'a pour PAQPF',
            ],
            [
              'contact_sub_type:name',
              'CONTAINS',
              ['Donateur'],
            ],
            [
              'OR',
              [
                ['deceased_date', 'IS NOT EMPTY'],
                ['is_deceased', '=', TRUE],
                [
                  'Prise_en_charge_au_d_c_s.N_de_d_c_s',
                  'IS NOT EMPTY',
                ],
              ],
            ],
          ],
          'groupBy' => ['id'],
          'join' => [
            [
              'Contact AS Contact_RelationshipCache_Contact_01',
              'LEFT',
              'RelationshipCache',
              [
                'id',
                '=',
                'Contact_RelationshipCache_Contact_01.far_contact_id',
              ],
            ],
          ],
          'having' => [],
        ],
        'description' => E::ts('Donneurs DCD ou avec une date de décès ou un numéro de DC ayant une PAQPF'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_A_PAQPF_Group_A_P_25',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'A_P_25',
        'title' => E::ts('A PAQPF'),
        'description' => E::ts('Donneurs DCD ou avec une date de décès ou un numéro de DC ayant une PAQPF'),
        'saved_search_id.name' => 'A_PAQPF',
        'group_type' => [],
        'frontend_title' => E::ts('A PAQPF'),
      ],
      'match' => ['name'],
    ],
  ],
];
