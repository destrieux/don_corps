<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_tokens_lastDons',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'tokens_lastDons',
        'label' => E::ts('tokens_lastDons'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'sort_name',
            'GROUP_FIRST(Contact_Contribution_contact_id_01.total_amount ORDER BY Contact_Contribution_contact_id_01.receive_date DESC) AS GROUP_FIRST_Contact_Contribution_contact_id_01_total_amount_Contact_Contribution_contact_id_01_receive_date',
            'GROUP_FIRST(Contact_Contribution_contact_id_01.receive_date ORDER BY Contact_Contribution_contact_id_01.receive_date DESC) AS GROUP_FIRST_Contact_Contribution_contact_id_01_receive_date_Contact_Contribution_contact_id_01_receive_date',
            'id',
          ],
          'orderBy' => [],
          'where' => [
            [
              'Contact_Contribution_contact_id_01.total_amount',
              '!=',
              '0',
            ],
          ],
          'groupBy' => ['id'],
          'join' => [
            [
              'Contribution AS Contact_Contribution_contact_id_01',
              'LEFT',
              [
                'id',
                '=',
                'Contact_Contribution_contact_id_01.contact_id',
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
    'name' => 'SavedSearch_tokens_lastDons_SearchDisplay_tokens_lastDons',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'tokens_lastDons',
        'label' => E::ts('tokens_lastDons'),
        'saved_search_id.name' => 'tokens_lastDons',
        'type' => 'tokens',
        'settings' => [
          'columns' => [
            [
              'type' => 'field',
              'key' => 'GROUP_FIRST_Contact_Contribution_contact_id_01_total_amount_Contact_Contribution_contact_id_01_receive_date',
              'dataType' => 'Money',
              'label' => E::ts('LastDon_montant'),
              'rewrite' => '',
            ],
            [
              'type' => 'field',
              'key' => 'GROUP_FIRST_Contact_Contribution_contact_id_01_receive_date_Contact_Contribution_contact_id_01_receive_date',
              'dataType' => 'Timestamp',
              'label' => E::ts('LastDon_date'),
              'rewrite' => '[GROUP_FIRST_Contact_Contribution_contact_id_01_receive_date_Contact_Contribution_contact_id_01_receive_date][CURDATE()]',
            ],
          ],
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
