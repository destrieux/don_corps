<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Emprunteurs',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Emprunteurs',
        'label' => E::ts('Emprunteurs'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'contact_sub_type:label',
            'display_name',
            'Contact_Address_contact_id_01.street_address',
            'Contact_Address_contact_id_01.supplemental_address_1',
            'Contact_Address_contact_id_01.postal_code',
            'Contact_Address_contact_id_01.city',
            'phone_primary.phone',
          ],
          'orderBy' => [],
          'where' => [
            [
              'OR',
              [
                [
                  'contact_sub_type:name',
                  'CONTAINS',
                  'Emprunteur',
                ],
                [
                  'contact_sub_type:name',
                  'CONTAINS',
                  'CDC',
                ],
              ],
            ],
            [
              'contact_type:name',
              '=',
              'Organization',
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Address AS Contact_Address_contact_id_01',
              'LEFT',
              [
                'id',
                '=',
                'Contact_Address_contact_id_01.contact_id',
              ],
              [
                'Contact_Address_contact_id_01.is_primary',
                '=',
                TRUE,
              ],
            ],
          ],
          'having' => [],
        ],
        'description' => E::ts('Lieux de conservation des corps et des pièces : CDC, locaux des CDC et emprunteurs'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Emprunteurs_Group_Emprunteurs_44',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Emprunteurs_44',
        'title' => E::ts('Emprunteurs'),
        'description' => E::ts('Organismes emprunteurs : emprunteurs et CDC'),
        'saved_search_id.name' => 'Emprunteurs',
        'group_type' => [],
        'frontend_title' => E::ts('Emprunteurs'),
        'frontend_description' => E::ts('Organismes emprunteurs : emprunteurs et CDC'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Emprunteurs_SearchDisplay_Emprunteurs_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Emprunteurs_Table_1',
        'label' => E::ts('Emprunteurs table'),
        'saved_search_id.name' => 'Emprunteurs',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Lieux de conservation des corps et des pièces : CDC, locaux des CDC et emprunteurs'),
          'sort' => [
            ['sort_name', 'ASC'],
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
              'key' => 'Contact_Address_contact_id_01.street_address',
              'dataType' => 'String',
              'label' => E::ts('Rue'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.supplemental_address_1',
              'dataType' => 'String',
              'label' => E::ts('Complément d\'adresse'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.postal_code',
              'dataType' => 'String',
              'label' => E::ts('Code postal'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.city',
              'dataType' => 'String',
              'label' => E::ts('Ville'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'phone_primary.phone',
              'dataType' => 'String',
              'label' => E::ts('Téléphone'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped', 'table-bordered'],
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
