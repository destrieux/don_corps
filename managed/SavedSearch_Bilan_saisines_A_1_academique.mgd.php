<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_saisines_A_1_academique',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_saisines_A_1_academique',
        'label' => E::ts('Bilan : saisines A-1 academique'),
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
              ['Accept_', 'Refus_', 'R_serv_'],
            ],
            [
              'caracteristiques.Participation_d_une_entit_tierce:name',
              '=',
              'Acad_mique',
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
    'name' => 'SavedSearch_Bilan_saisines_A_1_academique_SearchDisplay_Bilan_saisines_annee_en_cours_acad_mique_copy_',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_saisines_annee_en_cours_acad_mique_copy_',
        'label' => E::ts('Bilan : saisines A -1  académique'),
        'saved_search_id.name' => 'Bilan_saisines_A_1_academique',
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
            'label' => E::ts('Saisines avec intervention d\'une entité tierce académique'),
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
