<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_corps_recus_et_devenir_ann_e_en_cours',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_corps_recus_et_devenir_ann_e_en_cours',
        'label' => E::ts('Bilan : corps sortis année en cours'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'COUNT(id) AS COUNT_id',
            'Devenir_du_corps.devenir_effectif_du_corps:label',
          ],
          'orderBy' => [],
          'where' => [
            [
              'Devenir_du_corps.Date_de_sortie_d_finitive',
              '=',
              'this.year',
            ],
            [
              'Devenir_du_corps.Date_de_sortie_d_finitive',
              '<=',
              'now',
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
    'name' => 'SavedSearch_Bilan_corps_recus_et_devenir_ann_e_en_cours_SearchDisplay_Bilan_corps_sortis_ann_e_en_cours_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_corps_sortis_ann_e_en_cours_Table_1',
        'label' => E::ts('Bilan : corps sortis année en cours Table 1'),
        'saved_search_id.name' => 'Bilan_corps_recus_et_devenir_ann_e_en_cours',
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
            'label' => E::ts('Nombre total de corps sortis dans l\'année en cours'),
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
