<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Dons_ann_e_en_cours',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Dons_ann_e_en_cours',
        'label' => E::ts('Bilan : dons année en cours'),
        'api_entity' => 'Contribution',
        'api_params' => [
          'version' => 4,
          'select' => [
            'receive_date',
            'contact_id.sort_name',
            'total_amount',
            'financial_type_id:label',
            'id',
          ],
          'orderBy' => [],
          'where' => [
            ['receive_date', '=', 'this.year'],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Dons_ann_e_en_cours_SearchDisplay_Dons_ann_e_en_cours_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Dons_ann_e_en_cours_Table_1',
        'label' => E::ts('Bilan : dons année Table 1'),
        'saved_search_id.name' => 'Dons_ann_e_en_cours',
        'type' => 'table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [],
          'limit' => 0,
          'pager' => FALSE,
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'receive_date',
              'dataType' => 'Timestamp',
              'label' => E::ts('Date du don'),
              'sortable' => TRUE,
              'tally' => [
                'fn' => NULL,
              ],
            ],
            [
              'type' => 'field',
              'key' => 'contact_id.sort_name',
              'dataType' => 'String',
              'label' => E::ts('Donneur'),
              'sortable' => TRUE,
              'tally' => [
                'fn' => 'COUNT',
              ],
            ],
            [
              'type' => 'field',
              'key' => 'total_amount',
              'dataType' => 'Money',
              'label' => E::ts('Montant du don'),
              'sortable' => TRUE,
              'tally' => [
                'fn' => 'SUM',
              ],
            ],
          ],
          'actions' => ['download'],
          'classes' => ['table', 'table-striped'],
          'headerCount' => TRUE,
          'tally' => [
            'label' => E::ts('Total des dons pour l\'année en cours'),
          ],
          'actions_display_mode' => 'menu',
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
