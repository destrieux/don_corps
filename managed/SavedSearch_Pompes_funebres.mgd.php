<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Pompes_funebres',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Pompes_funebres',
        'label' => E::ts('Pompes funebres'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'sort_name',
            'Contact_Address_contact_id_01.street_address',
            'Contact_Address_contact_id_01.supplemental_address_1',
            'Contact_Address_contact_id_01.supplemental_address_2',
            'Contact_Address_contact_id_01.city',
            'Contact_Address_contact_id_01.postal_code',
            'email_primary.email',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Pompes',
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
        'description' => E::ts('Pompes funèbres'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Pompes_funebres_Group_Pompes_64',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Pompes_64',
        'title' => E::ts('Pompes'),
        'description' => E::ts('Pompes funèbres'),
        'saved_search_id.name' => 'Pompes_funebres',
        'group_type' => [],
        'frontend_title' => E::ts('Pompes'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Pompes_funebres_SearchDisplay_Pompes_funebres_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Pompes_funebres_Table_1',
        'label' => E::ts('Pompes funebres Table 1'),
        'saved_search_id.name' => 'Pompes_funebres',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Pompes funebres'),
          'sort' => [
            ['sort_name', 'ASC'],
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
                'task' => '',
              ],
              'title' => E::ts('Voir Contact'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.street_address',
              'dataType' => 'String',
              'label' => E::ts('Adresse'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.supplemental_address_1',
              'dataType' => 'String',
              'label' => E::ts('Complément d\'adresse 1'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.supplemental_address_2',
              'dataType' => 'String',
              'label' => E::ts('Complément d\'adresse 2'),
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
              'key' => 'email_primary.email',
              'dataType' => 'String',
              'label' => E::ts('Courriel'),
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
