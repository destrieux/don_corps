<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_bilan_nombre_de_corps_pr_sents_1_1_ann_e_A_1',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'bilan_nombre_de_corps_pr_sents_1_1_ann_e_A_1',
        'label' => E::ts('bilan : corps présents 1/1 A-1'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'COUNT(deceased_date) AS COUNT_deceased_date',
          ],
          'orderBy' => [],
          'where' => [
            ['deceased_date', 'IS NOT EMPTY'],
            [
              'OR',
              [
                [
                  'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'Pas_de_refus',
                ],
                [
                  'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  'IS EMPTY',
                ],
              ],
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
                  '=',
                  'this.year',
                ],
                [
                  'Devenir_du_corps.Date_de_sortie_d_finitive',
                  '=',
                  'previous.year',
                ],
              ],
            ],
            [
              'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC',
              '<',
              'previous.year',
            ],
            [
              'OR',
              [],
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
    'name' => 'SavedSearch_bilan_nombre_de_corps_pr_sents_1_1_ann_e_A_1_SearchDisplay_bilan_nombre_de_corps_pr_sents_1_1_ann_e_A_1_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'bilan_nombre_de_corps_pr_sents_1_1_ann_e_A_1_List_1',
        'label' => E::ts('corps 1/1 A-1 List 1'),
        'saved_search_id.name' => 'bilan_nombre_de_corps_pr_sents_1_1_ann_e_A_1',
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
              'key' => 'COUNT_deceased_date',
              'dataType' => 'Integer',
              'label' => E::ts('Nombre de corps présents au 1er janvier (A -1) :'),
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
