<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Personnels',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Personnels',
        'label' => E::ts('Personnels partis depuis plus de 5 ans'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'display_name',
            'employer_id.display_name',
            'job_title',
            'infos_personnel.Date_debut_fonctions',
            'infos_personnel.Date_fin_fonctions',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Personnel',
            ],
            [
              'NOT',
              [
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
        'description' => E::ts('Personnels ayant quitté la structure depuis plus de 5 ans (responsable de la structure d’accueil des corps, personnels habilités, personnels techniques de la structure d’accueil des corps, personnels concernés par les activités d’enseignement médical et de recherche, personnels titulaires d’une autorisation expresse délivrée par le responsable de la structure d’accueil des corps)'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Personnels_Group_Personnels_partis_plus_5_ans_52',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Personnels_partis_plus_5_ans_52',
        'title' => E::ts('Personnels partis plus 5 ans'),
        'description' => E::ts('Personnels partis depuis plus de 5 ans'),
        'saved_search_id.name' => 'Personnels',
        'group_type' => [],
        'frontend_title' => E::ts('Personnels partis plus 5 ans'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Personnels_SearchDisplay_Personnels_partis_depuis_plus_de_5_ans_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Personnels_partis_depuis_plus_de_5_ans_Table_1',
        'label' => E::ts('Personnels partis depuis plus de 5 ans Table 1'),
        'saved_search_id.name' => 'Personnels',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Personnels ayant quitté la structure depuis plus de 5 ans'),
          'sort' => [
            ['sort_name', 'ASC'],
          ],
          'limit' => 50,
          'pager' => [
            'show_count' => TRUE,
          ],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'html',
              'key' => 'display_name',
              'dataType' => 'String',
              'label' => E::ts('Nom'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'html',
              'key' => 'employer_id.display_name',
              'dataType' => 'String',
              'label' => E::ts('Employeur'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'job_title',
              'dataType' => 'String',
              'label' => E::ts('Fonction'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'infos_personnel.Date_debut_fonctions',
              'dataType' => 'Date',
              'label' => E::ts('Début des fonctions'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'infos_personnel.Date_fin_fonctions',
              'dataType' => 'Date',
              'label' => E::ts('Fin des fonctions'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
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
