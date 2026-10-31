<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_saisines_A_1_recherche',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_saisines_A_1_recherche',
        'label' => E::ts('Bilan : saisines A-1 recherche'),
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
              'this.year',
            ],
            [
              'caracteristiques.Type_d_activit_:name',
              'CONTAINS ONE OF',
              [
                'Recherche_master',
                'Recherche_th_se',
                'Recherche_autre',
              ],
            ],
            [
              'suivi.CESP:name',
              'CONTAINS ONE OF',
              ['Accept_', 'R_serv_', 'Refus_'],
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
    'name' => 'SavedSearch_Bilan_saisines_A_1_recherche_SearchDisplay_Bilan_saisines_A_1_recherche',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_saisines_A_1_recherche',
        'label' => E::ts('Bilan : saisines A-1 recherche'),
        'saved_search_id.name' => 'Bilan_saisines_A_1_recherche',
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
            'label' => E::ts('Saisines portant sur des programmes de recherche :'),
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
