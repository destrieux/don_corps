<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_bilan_corps_presents_au_31_12_ann_e_A_1',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'bilan_corps_presents_au_31_12_ann_e_A_1',
        'label' => E::ts('bilan : corps presents au 31/12 année A -1'),
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
              'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC',
              '<',
              'this.year',
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
                  'previous.year',
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
    'name' => 'SavedSearch_bilan_corps_presents_au_31_12_ann_e_A_1_SearchDisplay_bilan_corps_presents_au_31_12_ann_e_A_1_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'bilan_corps_presents_au_31_12_ann_e_A_1_List_1',
        'label' => E::ts('corps au 31/12 A -1 List 1'),
        'saved_search_id.name' => 'bilan_corps_presents_au_31_12_ann_e_A_1',
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
              'label' => E::ts('Nombre de corps présents le 31/12 de l\'année précédente'),
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
