<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_in_memoriam',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'in_memoriam',
        'label' => E::ts('in memoriam'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'prefix_id:label',
            'CONCAT_WS(" ", first_name, last_name, nick_name) AS CONCAT_WS_first_name_last_name_nick_name',
            'birth_date',
            'deceased_date',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              ['Donateur'],
            ],
            ['is_deceased', '=', TRUE],
            [
              'Promesse_de_don.Souhiat_affichage_st_le:name',
              '=',
              'Oui',
            ],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_in_memoriam_SearchDisplay_in_memoriam_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'in_memoriam_Table_1',
        'label' => E::ts('memoriam Table 1'),
        'saved_search_id.name' => 'in_memoriam',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Liste les donneurs décédés ayant souhaité que leur nom soit affiche sur le site ou la stele'),
          'sort' => [
            ['last_name', 'ASC'],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'prefix_id:label',
              'label' => E::ts('Civilité'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'CONCAT_WS_first_name_last_name_nick_name',
              'label' => E::ts('(Combine text)   Prénom Nom de famille Surnom/sigle'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'birth_date',
              'label' => E::ts('Date de naissance'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'deceased_date',
              'label' => E::ts('Décès'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
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
