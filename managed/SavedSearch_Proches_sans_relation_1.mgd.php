<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Proches_sans_relation_1',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Proches_sans_relation_1',
        'label' => E::ts('Proches sans relation'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'contact_sub_type:label',
            'display_name',
            'GROUP_CONCAT(DISTINCT Contact_RelationshipCache_Contact_01.near_contact_id.display_name) AS GROUP_CONCAT_Contact_RelationshipCache_Contact_01_near_contact_id_display_name',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Proches',
            ],
            [
              'contact_sub_type:name',
              'NOT CONTAINS',
              'Personnel',
            ],
            [
              'contact_sub_type:name',
              'NOT CONTAINS',
              'Donateur',
            ],
          ],
          'groupBy' => ['id'],
          'join' => [
            [
              'Contact AS Contact_RelationshipCache_Contact_01',
              'EXCLUDE',
              'RelationshipCache',
              [
                'id',
                '=',
                'Contact_RelationshipCache_Contact_01.far_contact_id',
              ],
            ],
          ],
          'having' => [],
        ],
        'description' => E::ts('Proches sans relation : à supprimer'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Proches_sans_relation_1_Group_Proches_sans_relation_42',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Proches_sans_relation_42',
        'title' => E::ts('Proches sans relation'),
        'description' => E::ts('Proches sans relation : à supprimer'),
        'saved_search_id.name' => 'Proches_sans_relation_1',
        'group_type' => [],
        'frontend_title' => E::ts('Proches sans relation'),
        'frontend_description' => E::ts('Proches sans relation : à supprimer'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Proches_sans_relation_1_SearchDisplay_Proches_sans_relation',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Proches_sans_relation',
        'label' => E::ts('Proches sans relation'),
        'saved_search_id.name' => 'Proches_sans_relation_1',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Proches sans relation active'),
          'sort' => [
            ['display_name', 'ASC'],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'contact_sub_type:label',
              'dataType' => 'String',
              'label' => E::ts('Type de contact'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'display_name',
              'dataType' => 'String',
              'label' => E::ts('Contact'),
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
              'key' => 'Contact_RelationshipCache_Contact_01.far_relation:label',
              'dataType' => 'String',
              'label' => E::ts('Relation depuis le contact'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.near_relation:label',
              'dataType' => 'String',
              'label' => E::ts('Relation vers le contact'),
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
