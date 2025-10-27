<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_nombre_de_corps_rec_us_ann_e_en_cours',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_nombre_de_corps_rec_us_ann_e_en_cours',
        'label' => E::ts('Bilan : nombre de corps reçus année en cours'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'COUNT(contact_sub_type:label) AS COUNT_contact_sub_type_label',
          ],
          'orderBy' => [],
          'where' => [
            [
              'Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC',
              '=',
              'this.year',
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
    'name' => 'SavedSearch_Bilan_nombre_de_corps_rec_us_ann_e_en_cours_Group_corps_recus_ann_e_en_cours_69',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'corps_recus_ann_e_en_cours_69',
        'title' => E::ts('corps recus année en cours'),
        'saved_search_id.name' => 'Bilan_nombre_de_corps_rec_us_ann_e_en_cours',
        'group_type' => [],
        'frontend_title' => E::ts('corps recus année en cours'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Bilan_nombre_de_corps_rec_us_ann_e_en_cours_SearchDisplay_Bilan_nombre_de_corps_rec_us_ann_e_en_cours_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_nombre_de_corps_rec_us_ann_e_en_cours_List_1',
        'label' => E::ts('corps reçus année en cours List 1'),
        'saved_search_id.name' => 'Bilan_nombre_de_corps_rec_us_ann_e_en_cours',
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
              'label' => E::ts('Nombre de corps accueillis par la structure pendant l\'année en cours :'),
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
