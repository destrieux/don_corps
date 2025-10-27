<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_inscription_ann_e_A_1',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_inscription_ann_e_A_1',
        'label' => E::ts('Bilan : inscription année A-1'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'COUNT(Promesse_de_don.Date_du_don) AS COUNT_Promesse_de_don_Date_du_don',
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
                  'Promesse_de_don.Date_du_don',
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
    'name' => 'SavedSearch_Bilan_inscription_ann_e_A_1_SearchDisplay_Bilan_inscription_ann_e_A_1_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_inscription_ann_e_A_1_List_1',
        'label' => E::ts('Bilan : inscription année A-1 List 1'),
        'saved_search_id.name' => 'Bilan_inscription_ann_e_A_1',
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
              'key' => 'COUNT_Promesse_de_don_Date_du_don',
              'dataType' => 'Integer',
              'label' => E::ts('Nombre de cartes délivrées (A -1):'),
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
