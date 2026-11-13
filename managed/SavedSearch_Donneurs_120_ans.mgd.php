<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Donneurs_120_ans',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Donneurs_120_ans',
        'label' => E::ts('Donneurs > 120 ans'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'display_name',
            'birth_date',
            'age_years',
            'Promesse_de_don.N_de_don',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Donateur',
            ],
            ['age_years', '>=', 120],
            [
              'Prise_en_charge_au_d_c_s.N_de_d_c_s',
              'IS EMPTY',
            ],
            ['deceased_date', 'IS EMPTY'],
            ['is_deceased', '=', FALSE],
            ['last_name', '!=', 'ANONYMISE'],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'description' => E::ts('Donneurs âgés de plus de 120 ans et sans numéro de DC  : anonymisation'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Donneurs_120_ans_Group_don_plus_120_ans_38',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'don_plus_120_ans_38',
        'title' => E::ts('don plus 120 ans'),
        'description' => E::ts('Donneurs âgés de plus de 120 ans non décédés'),
        'saved_search_id.name' => 'Donneurs_120_ans',
        'group_type' => [],
        'frontend_title' => E::ts('don plus 120 ans'),
        'frontend_description' => E::ts('Donneurs âgés de plus de 120 ans et sans numéro de DC  : anonymisation'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Donneurs_120_ans_SearchDisplay_Donneurs_120_ans',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Donneurs_120_ans',
        'label' => E::ts('Donneurs > 120 ans'),
        'saved_search_id.name' => 'Donneurs_120_ans',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Donneurs âgés de plus de 120 ans et sans numéro de DC : anonymisation'),
          'sort' => [
            ['age_years', 'DESC'],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'display_name',
              'dataType' => 'String',
              'label' => E::ts('Nom'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => '',
                'target' => '_blank',
              ],
              'title' => E::ts('Voir Contact'),
            ],
            [
              'type' => 'field',
              'key' => 'birth_date',
              'dataType' => 'Date',
              'label' => E::ts('Date de naissance'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'age_years',
              'dataType' => 'Integer',
              'label' => E::ts('Âge'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Promesse_de_don.N_de_don',
              'dataType' => 'String',
              'label' => E::ts('N° de don'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
          'headerCount' => TRUE,
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
