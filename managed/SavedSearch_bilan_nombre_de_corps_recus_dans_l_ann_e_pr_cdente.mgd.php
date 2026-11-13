<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_bilan_nombre_de_corps_recus_dans_l_ann_e_pr_cdente',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'bilan_nombre_de_corps_recus_dans_l_ann_e_pr_cdente',
        'label' => E::ts('bilan : corps recus année A-1'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'COUNT(Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC) AS COUNT_Prise_en_charge_au_d_c_s_Date_d_arriv_e_au_CDC',
          ],
          'orderBy' => [],
          'where' => [
            [
              'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC',
              '=',
              'previous.year',
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
    'name' => 'SavedSearch_bilan_nombre_de_corps_recus_dans_l_ann_e_pr_cdente_SearchDisplay_bilan_nombre_de_corps_recus_dans_l_ann_e_pr_cdente_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'bilan_nombre_de_corps_recus_dans_l_ann_e_pr_cdente_List_1',
        'label' => E::ts('corps A-1 List 1'),
        'saved_search_id.name' => 'bilan_nombre_de_corps_recus_dans_l_ann_e_pr_cdente',
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
              'key' => 'COUNT_Prise_en_charge_au_d_c_s_Date_d_arriv_e_au_CDC',
              'dataType' => 'Integer',
              'label' => E::ts('Nombre de corps accueillis par la structure (A -1) :'),
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
