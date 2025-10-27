<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_annulations_ann_e_A_1',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_annulations_ann_e_A_1',
        'label' => E::ts('Bilan : annulations année A-1'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'COUNT(Annulation.Date_d_annulation) AS COUNT_Annulation_Date_d_annulation',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Donateur',
            ],
            [
              'OR',
              [
                [
                  'Annulation.Date_d_annulation',
                  '=',
                  'previous.year',
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
    'name' => 'SavedSearch_Bilan_annulations_ann_e_A_1_SearchDisplay_Bilan_annulations_ann_e_A_1_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_annulations_ann_e_A_1_List_1',
        'label' => E::ts('Bilan : annulations année A-1 List 1'),
        'saved_search_id.name' => 'Bilan_annulations_ann_e_A_1',
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
              'key' => 'COUNT_Annulation_Date_d_annulation',
              'dataType' => 'Integer',
              'label' => E::ts('Nombre de renoncement au consentement par un donneur déja inscrit (A -1) :'),
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
