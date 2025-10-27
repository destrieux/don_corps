<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Op_rations_fun_raires_de_plus_de_5_ans',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Op_rations_fun_raires_de_plus_de_5_ans',
        'label' => E::ts('Opérations funéraires de plus de 5 ans'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'Promesse_de_don.N_de_don',
            'more_greetings_group.greeting_field_1',
            'first_name',
            'UPPER(last_name) AS UPPER_last_name',
            'Prise_en_charge_au_d_c_s.N_de_d_c_s',
            'deceased_date',
            'Devenir_du_corps.Date_op_rations_fun_raires',
            'Devenir_du_corps.devenir_effectif_du_corps:label',
            'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires:label',
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
                  'Devenir_du_corps.Date_op_rations_fun_raires',
                  '=',
                  'ending_5.year',
                ],
              ],
            ],
            [
              'Devenir_du_corps.Date_op_rations_fun_raires',
              '<',
              'now',
            ],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'description' => E::ts('Opérations funéraires de plus de 5 ans:
- vider toutes les données (anonymisation)
- sauf le numéro de carte qui ne peut être réutilisé.'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Op_rations_fun_raires_de_plus_de_5_ans_Group_Op_funeraires_de_plus_de_5_ans_33',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Op_funeraires_de_plus_de_5_ans_33',
        'title' => E::ts('Op funeraires de plus de 5 ans'),
        'saved_search_id.name' => 'Op_rations_fun_raires_de_plus_de_5_ans',
        'group_type' => [],
        'frontend_title' => E::ts('Op funeraires de plus de 5 ans'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Op_rations_fun_raires_de_plus_de_5_ans_SearchDisplay_Op_rations_fun_raires_de_plus_de_5_ans_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Op_rations_fun_raires_de_plus_de_5_ans_Table_1',
        'label' => E::ts('Opérations funéraires de plus de 5 ans'),
        'saved_search_id.name' => 'Op_rations_fun_raires_de_plus_de_5_ans',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Opérations funéraires de plus de 5 ans à purger'),
          'sort' => [
            [
              'Devenir_du_corps.Date_op_rations_fun_raires',
              'DESC',
            ],
            [
              'UPPER_last_name',
              'ASC',
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
            ],
            [
              'type' => 'field',
              'key' => 'more_greetings_group.greeting_field_1',
              'dataType' => 'String',
              'label' => E::ts('Civilité'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'first_name',
              'dataType' => 'String',
              'label' => E::ts('Prénom'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'UPPER_last_name',
              'dataType' => 'String',
              'label' => E::ts('Nom de famille'),
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
              'key' => 'Prise_en_charge_au_d_c_s.N_de_d_c_s',
              'dataType' => 'String',
              'label' => E::ts('N° de décès'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'deceased_date',
              'dataType' => 'Date',
              'label' => E::ts('Date du décès'),
              'sortable' => TRUE,
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
              'key' => 'Devenir_du_corps.devenir_effectif_du_corps:label',
              'dataType' => 'String',
              'label' => E::ts('Opération funéraire réalisée'),
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
          'classes' => ['table', 'table-striped', 'table-bordered'],
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
