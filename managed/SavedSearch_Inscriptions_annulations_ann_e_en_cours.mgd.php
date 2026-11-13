<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Inscriptions_annulations_ann_e_en_cours',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Inscriptions_annulations_ann_e_en_cours',
        'label' => E::ts('Bilan : Inscriptions année en cours'),
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
                  'this.year',
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
    'name' => 'SavedSearch_Inscriptions_annulations_ann_e_en_cours_SearchDisplay_Inscriptions_ann_e_en_cours_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Inscriptions_ann_e_en_cours_List_1',
        'label' => E::ts('bilan : Inscriptions année en cours List 1'),
        'saved_search_id.name' => 'Inscriptions_annulations_ann_e_en_cours',
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
              'label' => E::ts('Nombre de cartes délivrées :'),
              'forceLabel' => TRUE,
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
