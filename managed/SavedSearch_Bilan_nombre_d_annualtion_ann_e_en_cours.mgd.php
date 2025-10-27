<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_nombre_d_annualtion_ann_e_en_cours',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_nombre_d_annualtion_ann_e_en_cours',
        'label' => E::ts('Bilan : nombre d\'annulations année en cours'),
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
    'name' => 'SavedSearch_Bilan_nombre_d_annualtion_ann_e_en_cours_SearchDisplay_Bilan_nombre_d_annualtion_ann_e_en_cours_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_nombre_d_annualtion_ann_e_en_cours_List_1',
        'label' => E::ts('Bilan : nombre d\'annulations année en cours List 1'),
        'saved_search_id.name' => 'Bilan_nombre_d_annualtion_ann_e_en_cours',
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
              'label' => E::ts('Nombre de renoncement au consentement par un donneur déja inscrit année en cours :'),
              'cssRules' => [
                [''],
              ],
            ],
          ],
          'placeholder' => 0,
          'description' => E::ts(''),
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
