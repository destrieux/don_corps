<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Mairies',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Mairies',
        'label' => E::ts('Mairies'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'sort_name',
            'email_primary',
            'address_primary.street_address',
            'address_primary.supplemental_address_1',
            'address_primary.postal_code',
            'address_primary.city',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Mairies',
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
            ],
          ],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Mairies_Group_mairies_66',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'mairies_66',
        'title' => E::ts('mairies'),
        'saved_search_id.name' => 'Mairies',
        'group_type' => [],
        'frontend_title' => E::ts('mairies'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Mairies_SearchDisplay_Mairies',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Mairies',
        'label' => E::ts('Mairies'),
        'saved_search_id.name' => 'Mairies',
        'type' => 'table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [
            [
              'address_primary.city',
              'ASC',
            ],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'sort_name',
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
              'key' => 'email_primary',
              'dataType' => 'Integer',
              'label' => E::ts('Courriel'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'address_primary.street_address',
              'dataType' => 'String',
              'label' => E::ts('Addresse'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'address_primary.supplemental_address_1',
              'dataType' => 'String',
              'label' => E::ts('Complément d\'adresse'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'address_primary.postal_code',
              'dataType' => 'String',
              'label' => E::ts('Code postal'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'address_primary.city',
              'dataType' => 'String',
              'label' => E::ts('Ville'),
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
