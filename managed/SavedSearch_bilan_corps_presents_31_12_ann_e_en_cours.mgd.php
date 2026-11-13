<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_bilan_corps_presents_31_12_ann_e_en_cours',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'bilan_corps_presents_31_12_ann_e_en_cours',
        'label' => E::ts('bilan : au 31/12 année en cours'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'COUNT(id) AS COUNT_id',
          ],
          'orderBy' => [],
          'where' => [
            ['deceased_date', 'IS NOT EMPTY'],
            [
              'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
              '=',
              'Pas_de_refus',
            ],
            [
              'OR',
              [
                [
                  'Devenir_du_corps.Date_de_sortie_d_finitive',
                  'IS EMPTY',
                ],
                [
                  'Devenir_du_corps.Date_de_sortie_d_finitive',
                  '>',
                  'now',
                ],
              ],
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
          'groupBy' => ['contact_type'],
          'join' => [],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_bilan_corps_presents_31_12_ann_e_en_cours_Group_corps_au_31_12_anne_en_cours_74',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'corps_au_31_12_anne_en_cours_74',
        'title' => E::ts('corps au 31/12 anne en cours'),
        'saved_search_id.name' => 'bilan_corps_presents_31_12_ann_e_en_cours',
        'group_type' => [],
        'frontend_title' => E::ts('corps au 31/12 anne en cours'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_bilan_corps_presents_31_12_ann_e_en_cours_SearchDisplay_bilan_corps_presents_31_12_ann_e_en_cours_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'bilan_corps_presents_31_12_ann_e_en_cours_List_1',
        'label' => E::ts('corps au 31/12 année en cours List 1'),
        'saved_search_id.name' => 'bilan_corps_presents_31_12_ann_e_en_cours',
        'type' => 'list',
        'settings' => [
          'style' => 'ul',
          'limit' => 0,
          'sort' => [
            ['sort_name', 'ASC'],
          ],
          'pager' => FALSE,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'COUNT_id',
              'dataType' => 'Integer',
              'label' => E::ts('Nombre de corps présents au jour du bilan :'),
              'rewrite' => '[COUNT_id]',
            ],
          ],
          'placeholder' => 0,
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
