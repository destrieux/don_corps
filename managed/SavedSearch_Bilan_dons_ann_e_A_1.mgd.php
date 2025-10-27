<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_dons_ann_e_A_1',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_dons_ann_e_A_1',
        'label' => E::ts('Bilan : dons année A -1'),
        'api_entity' => 'Contribution',
        'api_params' => [
          'version' => 4,
          'select' => [
            'receive_date',
            'Contribution_Contact_contact_id_01.sort_name',
            'net_amount',
          ],
          'orderBy' => [],
          'where' => [
            ['receive_date', '=', 'previous.year'],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Contact AS Contribution_Contact_contact_id_01',
              'LEFT',
              [
                'contact_id',
                '=',
                'Contribution_Contact_contact_id_01.id',
              ],
            ],
          ],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Bilan_dons_ann_e_A_1_SearchDisplay_dons_A_1_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'dons_A_1_Table_1',
        'label' => E::ts('dons A -1 Table 1'),
        'saved_search_id.name' => 'Bilan_dons_ann_e_A_1',
        'type' => 'table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [
            ['receive_date', 'ASC'],
          ],
          'limit' => 0,
          'pager' => FALSE,
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'receive_date',
              'label' => E::ts('Date'),
              'sortable' => TRUE,
              'tally' => [
                'fn' => NULL,
              ],
            ],
            [
              'type' => 'field',
              'key' => 'Contribution_Contact_contact_id_01.sort_name',
              'label' => E::ts('Donneur'),
              'sortable' => TRUE,
              'tally' => [
                'fn' => NULL,
              ],
            ],
            [
              'type' => 'field',
              'key' => 'net_amount',
              'label' => E::ts('Montant'),
              'sortable' => TRUE,
              'tally' => [
                'fn' => 'SUM',
              ],
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
          'actions_display_mode' => 'menu',
          'tally' => [
            'label' => E::ts('Total des dons pour l\'année précédente'),
          ],
          'headerCount' => TRUE,
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
