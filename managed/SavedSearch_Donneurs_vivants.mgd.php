<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Donneurs_vivants',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Donneurs_vivants',
        'label' => E::ts('Donneurs vivants'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'Promesse_de_don.N_de_don',
            'display_name',
            'nick_name',
            'birth_date',
            'age_years',
            'Annulation.N_annulation',
            'id',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              '=',
              'Donateur',
            ],
            [
              'NOT',
              [
                [
                  'OR',
                  [
                    [
                      'Prise_en_charge_au_d_c_s.N_de_d_c_s',
                      'IS NOT EMPTY',
                    ],
                    ['is_deceased', '=', TRUE],
                    ['deceased_date', 'IS NOT EMPTY'],
                  ],
                ],
              ],
            ],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'description' => E::ts('Donneurs vivants'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Donneurs_vivants_SearchDisplay_Donneurs_vivants_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Donneurs_vivants_Table_1',
        'label' => E::ts('Donneurs vivants'),
        'saved_search_id.name' => 'Donneurs_vivants',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Donneurs vivants'),
          'sort' => [
            [
              'Promesse_de_don.N_de_don',
              'DESC',
            ],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'Promesse_de_don.N_de_don',
              'dataType' => 'String',
              'label' => E::ts('N° de don'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => '',
                'target' => '_blank',
              ],
              'title' => E::ts('Voir Contact'),
            ],
            [
              'type' => 'field',
              'key' => 'nick_name',
              'label' => E::ts('Nom d\'usage'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'display_name',
              'dataType' => 'String',
              'label' => E::ts('Nom'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => '',
                'target' => '_blank',
              ],
              'title' => E::ts('Voir Contact'),
            ],
            [
              'type' => 'field',
              'key' => 'birth_date',
              'dataType' => 'Date',
              'label' => E::ts('Date de naissance'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'age_years',
              'dataType' => 'Integer',
              'label' => E::ts('Âge'),
              'sortable' => TRUE,
              'cssRules' => [
                ['bg-danger', 'age_years', '>=', 120],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'Annulation.N_annulation',
              'dataType' => 'String',
              'label' => E::ts('N° annulation'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
          'headerCount' => TRUE,
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
