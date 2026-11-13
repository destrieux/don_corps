<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Demandeurs_d_informations_plus_d_un_an',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Demandeurs_d_informations_plus_d_un_an',
        'label' => E::ts('Demandeurs d\'informations plus d\'un an'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'contact_sub_type:label',
            'more_greetings_group.greeting_field_1',
            'first_name',
            'last_name',
            'Demandeur_information.Date_d_envoi_d_informations',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              '=',
              'Demandeur_d_information',
            ],
            [
              'contact_sub_type:name',
              '!=',
              'Donateur',
            ],
            [
              'contact_sub_type:name',
              '!=',
              'Personnel',
            ],
            [
              'contact_sub_type:name',
              '!=',
              'Proches',
            ],
            [
              'NOT',
              [
                [
                  'Demandeur_information.Date_d_envoi_d_informations',
                  '=',
                  'ending.year',
                ],
              ],
            ],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'description' => E::ts('Personnes ayant demandé des informations il y a plus d\'un an et n\'ayant pas donné suite ; à détruire de la base.'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Demandeurs_d_informations_plus_d_un_an_Group_demandeurs_plus_un_an_40',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'demandeurs_plus_un_an_40',
        'title' => E::ts('demandeurs plus un an'),
        'description' => E::ts('Personnes ayant demandé des informations il y a plus d\'un an et n\'ayant pas donné suite ; à détruire de la base.'),
        'saved_search_id.name' => 'Demandeurs_d_informations_plus_d_un_an',
        'group_type' => [],
        'frontend_title' => E::ts('demandeurs plus un an'),
        'frontend_description' => E::ts('Personnes ayant demandé des informations il y a plus d\'un an et n\'ayant pas donné suite ; à détruire de la base.'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Demandeurs_d_informations_plus_d_un_an_SearchDisplay_Demandeurs_d_informations_sans_r_ponse_depuis_plus_d_un_an',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Demandeurs_d_informations_sans_r_ponse_depuis_plus_d_un_an',
        'label' => E::ts('Demandeurs d\'informations sans réponse depuis plus d\'un an'),
        'saved_search_id.name' => 'Demandeurs_d_informations_plus_d_un_an',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Personnes ayant demandé des informations et n\'ayant pas donné suite un an après l\'envoi des documents'),
          'sort' => [
            [
              'Demandeur_information.Date_d_envoi_d_informations',
              'ASC',
            ],
            ['last_name', 'ASC'],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'contact_sub_type:label',
              'dataType' => 'String',
              'label' => E::ts('Sous-type'),
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
              'key' => 'last_name',
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
              'key' => 'Demandeur_information.Date_d_envoi_d_informations',
              'dataType' => 'Date',
              'label' => E::ts('Date d\'envoi d\'informations'),
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
