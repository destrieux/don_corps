<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_saisines_annee_en_cours_sortie_corps',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_saisines_annee_en_cours_sortie_corps',
        'label' => E::ts('Bilan : saisines annee en cours sortie corps'),
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
              'caracteristiques.Activit_s_exceptionnelles:name',
              'CONTAINS ONE OF',
              [
                'Sortie_temporaire_de_corps',
              ],
            ],
            [
              'suivi.CESP:name',
              'CONTAINS ONE OF',
              ['Accept_', 'Refus_', 'R_serv_'],
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
    'name' => 'SavedSearch_Bilan_saisines_annee_en_cours_sortie_corps_SearchDisplay_Bilan_saisines_annee_en_cours_sortie_corps',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_saisines_annee_en_cours_sortie_corps',
        'label' => E::ts('Bilan : saisines annee en cours sortie corps'),
        'saved_search_id.name' => 'Bilan_saisines_annee_en_cours_sortie_corps',
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
            'label' => E::ts('Saisines impliquant une sortie temporaire du corps'),
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
