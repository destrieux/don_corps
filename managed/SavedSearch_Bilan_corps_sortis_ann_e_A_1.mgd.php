<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_corps_sortis_ann_e_A_1',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_corps_sortis_ann_e_A_1',
        'label' => E::ts('Bilan : corps sortis année A -1'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'Devenir_du_corps.devenir_effectif_du_corps:label',
            'COUNT(id) AS COUNT_id',
          ],
          'orderBy' => [],
          'where' => [
            [
              'Devenir_du_corps.Date_de_sortie_d_finitive',
              '=',
              'previous.year',
            ],
            [
              'OR',
              [
                [
                  'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires',
                  'IS EMPTY',
                ],
                [
                  'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires',
                  '=',
                  FALSE,
                ],
              ],
            ],
          ],
          'groupBy' => [
            'Devenir_du_corps.devenir_effectif_du_corps',
          ],
          'join' => [],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Bilan_corps_sortis_ann_e_A_1_SearchDisplay_corps_sortis_A_1_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'corps_sortis_A_1_Table_1',
        'label' => E::ts('corps sortis A -1 Table 1'),
        'saved_search_id.name' => 'Bilan_corps_sortis_ann_e_A_1',
        'type' => 'table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [
            ['sort_name', 'ASC'],
          ],
          'limit' => 0,
          'pager' => FALSE,
          'placeholder' => 0,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'Devenir_du_corps.devenir_effectif_du_corps:label',
              'label' => E::ts('Opérations funéraires réalisées'),
              'sortable' => FALSE,
              'tally' => [
                'fn' => NULL,
              ],
            ],
            [
              'type' => 'field',
              'key' => 'COUNT_id',
              'label' => E::ts('Nombre'),
              'sortable' => FALSE,
              'tally' => [
                'fn' => 'SUM',
              ],
            ],
          ],
          'actions' => FALSE,
          'classes' => [],
          'tally' => [
            'label' => E::ts('Nombre total de corps sortis l\'année précédente'),
          ],
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
