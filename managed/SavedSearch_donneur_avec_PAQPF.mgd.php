<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_donneur_avec_PAQPF',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'donneur_avec_PAQPF',
        'label' => E::ts('donneur avec PAQPF'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'sort_name',
            'contact_type:label',
            'contact_sub_type:label',
            'Contact_RelationshipCache_Contact_01.far_relation:label',
            'Contact_RelationshipCache_Contact_01.near_contact_id.display_name',
          ],
          'orderBy' => [],
          'where' => [
            [
              'Contact_RelationshipCache_Contact_01.far_relation:name',
              '=',
              'a pour PAQPF',
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Contact AS Contact_RelationshipCache_Contact_01',
              'INNER',
              'RelationshipCache',
              [
                'id',
                '=',
                'Contact_RelationshipCache_Contact_01.far_contact_id',
              ],
              [
                'Contact_RelationshipCache_Contact_01.far_relation:name',
                '=',
                '"a pour PAQPF"',
              ],
            ],
          ],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_donneur_avec_PAQPF_Group_donneur_avec_P_27',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'donneur_avec_P_27',
        'title' => E::ts('donneur avec PAQPF'),
        'description' => E::ts('Liste les personnes ayant une personne ayant qualité pour pourvoir aux funérailles'),
        'saved_search_id.name' => 'donneur_avec_PAQPF',
        'group_type:name' => ['Access Control', 'Mailing List'],
        'frontend_title' => E::ts('donneur avec PAQPF'),
      ],
      'match' => ['name'],
    ],
  ],
];
