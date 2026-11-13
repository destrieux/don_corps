<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Donneurs_sans_PAQPF',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Donneurs_sans_PAQPF',
        'label' => E::ts('Donneurs sans PAQPF avec personne de confiance 1 ou 2 : à corriger'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'sort_name',
            'contact_sub_type:label',
            'GROUP_CONCAT(DISTINCT Contact_RelationshipCache_Contact_01.far_relation:label) AS GROUP_CONCAT_Contact_RelationshipCache_Contact_01_far_relation_label',
            'GROUP_CONCAT(DISTINCT Contact_RelationshipCache_Contact_01.near_contact_id.display_name) AS GROUP_CONCAT_Contact_RelationshipCache_Contact_01_near_contact_id_display_name',
            'deceased_date',
          ],
          'orderBy' => [],
          'where' => [
            [
              'groups:name',
              'NOT IN',
              [
                'donneur_avec_P_27',
              ],
            ],
            [
              'contact_sub_type:name',
              'CONTAINS ONE OF',
              ['Donateur'],
            ],
            [
              'OR',
              [
                ['is_deceased', '=', TRUE],
                ['deceased_date', 'IS NOT EMPTY'],
                [
                  'Prise_en_charge_au_d_c_s.N_de_d_c_s',
                  'IS NOT EMPTY',
                ],
              ],
            ],
            [
              'OR',
              [
                [
                  'Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:name',
                  '!=',
                  'Non',
                ],
                [
                  'Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:name',
                  'IS EMPTY',
                ],
              ],
            ],
            [
              'Promesse_de_don.Refus_personne_referente',
              '=',
              FALSE,
            ],
            [
              'groups:name',
              'NOT IN',
              ['Archives_61'],
            ],
            [
              'Contact_RelationshipCache_Contact_01.far_relation:name',
              'CONTAINS ONE OF',
              [
                'a pour personne de confiance 2',
                'a pour personne de confiance',
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
              [
                'Contact_RelationshipCache_Contact_01.is_active',
                '=',
                TRUE,
              ],
            ],
          ],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Donneurs_sans_PAQPF_SearchDisplay_Donneurs_sans_PAQPF_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Donneurs_sans_PAQPF_Table_1',
        'label' => E::ts('Donneurs sans PAQPF Table 1'),
        'saved_search_id.name' => 'Donneurs_sans_PAQPF',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Liste des défunts avec référents pour lesquels une PAQPF devrait être désignée'),
          'sort' => [
            ['sort_name', 'ASC'],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'sort_name',
              'label' => E::ts('Nom du défunt'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => '',
                'target' => '_blank',
                'task' => '',
              ],
              'title' => E::ts('Voir Contact'),
            ],
            [
              'type' => 'field',
              'key' => 'deceased_date',
              'label' => E::ts('Décédé le'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'GROUP_CONCAT_Contact_RelationshipCache_Contact_01_far_relation_label',
              'label' => E::ts('Relation(s)'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
              'cssRules' => [
                [
                  'bg-warning',
                  'GROUP_CONCAT_Contact_RelationshipCache_Contact_01_far_relation_label',
                  'IS NOT EMPTY',
                ],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'GROUP_CONCAT_Contact_RelationshipCache_Contact_01_near_contact_id_display_name',
              'label' => E::ts('Personne(s) de confiance'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
          'actions_display_mode' => 'menu',
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
