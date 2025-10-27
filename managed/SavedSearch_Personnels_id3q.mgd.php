<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Personnels_id3q',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Personnels_id3q',
        'label' => E::ts('Bilan : personnels'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'display_name',
            'employer_id.display_name',
            'infos_personnel.Date_debut_fonctions',
            'infos_personnel.Date_fin_fonctions',
            'infos_personnel.M_tier:label',
            'infos_personnel.Cat_gorie:label',
            'infos_personnel.BAP:label',
            'infos_personnel.Contrat:label',
            'job_title',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Personnel',
            ],
            [
              'OR',
              [
                [
                  'infos_personnel.Date_fin_fonctions',
                  'IS EMPTY',
                ],
                [
                  'infos_personnel.Date_fin_fonctions',
                  '=',
                  'ending_5.year',
                ],
              ],
            ],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'description' => E::ts('liste des personels non purgés (5ans apres la fin des fonctions)'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Personnels_id3q_SearchDisplay_Personnels_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Personnels_Table_1',
        'label' => E::ts('Personnels Table 1'),
        'saved_search_id.name' => 'Personnels_id3q',
        'type' => 'table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [
            ['sort_name', 'ASC'],
          ],
          'limit' => 0,
          'pager' => FALSE,
          'placeholder' => 0,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'display_name',
              'label' => E::ts('Nom'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'infos_personnel.Date_debut_fonctions',
              'dataType' => 'Date',
              'label' => E::ts('Date d\'arrivée'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'infos_personnel.Date_fin_fonctions',
              'dataType' => 'Date',
              'label' => E::ts('Date de départ'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'infos_personnel.M_tier:label',
              'label' => E::ts('Métier'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'infos_personnel.Cat_gorie:label',
              'label' => E::ts('Catégorie'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'infos_personnel.BAP:label',
              'label' => E::ts('BAP'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'infos_personnel.Contrat:label',
              'label' => E::ts('Contrat'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'job_title',
              'dataType' => 'String',
              'label' => E::ts('Fonction'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => FALSE,
          'classes' => ['table', 'table-striped'],
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
