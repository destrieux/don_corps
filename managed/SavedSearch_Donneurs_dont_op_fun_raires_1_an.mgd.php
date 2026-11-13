<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Donneurs_dont_op_fun_raires_1_an',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Donneurs_dont_op_fun_raires_1_an',
        'label' => E::ts('Donneurs dont op funéraires > 1 an'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'display_name',
            'Devenir_du_corps.Date_op_rations_fun_raires',
            'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires:label',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Donateur',
            ],
            [
              'Devenir_du_corps.Date_op_rations_fun_raires',
              '<',
              'now - 1 year',
            ],
            [
              'tags:name',
              'NOT IN',
              [
                'Relations Purgees',
              ],
            ],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'description' => E::ts('Donneurs dont les opérations funéraires ont été achevées il y a plus d\'un an : suppression des relations (avant purge des proches sans relations)
Les donneurs dont les relations sont déja purgés (tag "relations purgées" sont exclus'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Donneurs_dont_op_fun_raires_1_an_Group_op_funeraires_plus_un_an_43',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'op_funeraires_plus_un_an_43',
        'title' => E::ts('op funeraires plus un an'),
        'description' => E::ts('Donneurs dont les opérations funéraires ont été achevées il y a plus d\'un an : suppression des relations (avant purge des proches sans relations)
Les donneurs dont les relations ont déja été purgées (tag "relations purgees") sont exclus'),
        'saved_search_id.name' => 'Donneurs_dont_op_fun_raires_1_an',
        'group_type' => [],
        'frontend_title' => E::ts('op funeraires plus un an'),
        'frontend_description' => E::ts('Donneurs dont les opérations funéraires ont été achevées il y a plus d\'un an : suppression des relations (avant purge des proches sans relations)'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Donneurs_dont_op_fun_raires_1_an_SearchDisplay_Donneurs_dont_op_fun_raires_1_an_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Donneurs_dont_op_fun_raires_1_an_Table_1',
        'label' => E::ts('Donneurs dont op funéraires > 1 an'),
        'saved_search_id.name' => 'Donneurs_dont_op_fun_raires_1_an',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Donneurs dont les opérations funéraires ont été achevées il y a plus d\'un an : suppression des relations (avant purge des proches sans relations)'),
          'sort' => [
            [
              'Devenir_du_corps.Date_op_rations_fun_raires',
              'DESC',
            ],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
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
              'key' => 'Devenir_du_corps.Date_op_rations_fun_raires',
              'dataType' => 'Date',
              'label' => E::ts('Date opérations funéraires'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires:label',
              'dataType' => 'Boolean',
              'label' => E::ts('Date approximative'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => TRUE,
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
