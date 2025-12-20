<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_corps_pr_sents_au_1_1_ann_e_en_cours',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'corps_pr_sents_au_1_1_ann_e_en_cours',
        'label' => E::ts('Bilan : corps présents au 1/1 année en cours'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'contact_type:label',
            'COUNT(contact_sub_type:label) AS COUNT_contact_sub_type_label',
            'GROUP_CONCAT(DISTINCT contact_sub_type:label) AS GROUP_CONCAT_contact_sub_type_label',
          ],
          'orderBy' => [],
          'where' => [
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
              'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires',
              '!=',
              TRUE,
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
    'name' => 'SavedSearch_corps_pr_sents_au_1_1_ann_e_en_cours_Group_corps_au_1_1_ann_e_en_cours_68',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'corps_au_1_1_ann_e_en_cours_68',
        'title' => E::ts('corps au 1/1 année en cours'),
        'saved_search_id.name' => 'corps_pr_sents_au_1_1_ann_e_en_cours',
        'group_type' => [],
        'frontend_title' => E::ts('corps au 1/1 année en cours'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_corps_pr_sents_au_1_1_ann_e_en_cours_SearchDisplay_corps_pr_sents_au_1_1_ann_e_en_cours_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'corps_pr_sents_au_1_1_ann_e_en_cours_List_1',
        'label' => E::ts('bilan : corps présents au 1/1 année en cours List 1'),
        'saved_search_id.name' => 'corps_pr_sents_au_1_1_ann_e_en_cours',
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
              'key' => 'COUNT_contact_sub_type_label',
              'dataType' => 'Integer',
              'label' => E::ts('Nombre de corps présents au 1er janvier de l\'année en cours :'),
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
