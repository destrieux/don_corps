<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_saisines_A_1_segmentation',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_saisines_A_1_segmentation',
        'label' => E::ts('Bilan : saisines A-1 segmentation'),
        'api_entity' => 'Saisine',
        'api_params' => [
          'version' => 4,
          'select' => [
            'COUNT(id) AS COUNT_id',
            'suivi.CESP:label',
          ],
          'orderBy' => [],
          'where' => [
            [
              'suivi.Date_avis_CESP',
              '=',
              'previous.year',
            ],
            [
              'suivi.CESP:name',
              'CONTAINS ONE OF',
              ['Accept_', 'R_serv_', 'Refus_'],
            ],
            [
              'caracteristiques.Activit_s_exceptionnelles:name',
              'CONTAINS ONE OF',
              ['Segmentation'],
            ],
          ],
          'groupBy' => ['suivi.CESP'],
          'join' => [],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Bilan_saisines_A_1_segmentation_SearchDisplay_Bilan_saisines_A_1_segmentation',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_saisines_A_1_segmentation',
        'label' => E::ts('Bilan : saisines A-1 segmentation'),
        'saved_search_id.name' => 'Bilan_saisines_A_1_segmentation',
        'type:name' => 'crm-search-display-table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [],
          'limit' => 0,
          'pager' => FALSE,
          'placeholder' => 0,
          'actions' => FALSE,
          'classes' => [],
          'columnMode' => 'custom',
          'columns' => [
            [
              'type' => 'field',
              'key' => 'suivi.CESP:label',
              'label' => E::ts(''),
              'sortable' => TRUE,
              'tally' => [
                'fn' => NULL,
              ],
              'alignment' => 'text-right',
              'rewrite' => '[suivi.CESP:label] :',
            ],
            [
              'type' => 'field',
              'key' => 'COUNT_id',
              'label' => E::ts(''),
              'sortable' => TRUE,
              'tally' => [
                'fn' => 'SUM',
              ],
              'alignment' => '',
            ],
          ],
          'tally' => [
            'label' => E::ts('Saisines impliquant une segmentation'),
            'header' => FALSE,
            'footer' => TRUE,
          ],
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
