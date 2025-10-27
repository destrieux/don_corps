<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Tokens_PAQPF',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Tokens_PAQPF',
        'label' => E::ts('Tokens PAQPF'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'display_name',
            'Contact_RelationshipCache_Contact_01.near_contact_id.display_name',
            'id',
            'Contact_RelationshipCache_Contact_01.address_primary.supplemental_address_1',
            'Contact_RelationshipCache_Contact_01.address_primary.postal_code',
            'Contact_RelationshipCache_Contact_01.address_primary.city',
            'Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01.email',
            'Contact_RelationshipCache_Contact_01.phone_primary.phone',
            'Contact_RelationshipCache_Contact_01.address_primary.street_address',
            'Contact_RelationshipCache_Contact_01.email_greeting_display',
            'Contact_RelationshipCache_Contact_01.postal_greeting_display',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Donateur',
            ],
            [
              'Contact_RelationshipCache_Contact_01.far_contact_id.display_name',
              'IS NOT EMPTY',
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Contact AS Contact_RelationshipCache_Contact_01',
              'LEFT',
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
              [
                'Contact_RelationshipCache_Contact_01.is_active',
                '=',
                TRUE,
              ],
            ],
            [
              'Email AS Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01',
              'LEFT',
              [
                'Contact_RelationshipCache_Contact_01.id',
                '=',
                'Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01.contact_id',
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
    'name' => 'SavedSearch_Tokens_PAQPF_SearchDisplay_Tokens_PAQPF_Champs_de_fusion_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Tokens_PAQPF_Champs_de_fusion_1',
        'label' => E::ts('Tokens PAQPF Champs de fusion 1'),
        'saved_search_id.name' => 'Tokens_PAQPF',
        'type' => 'tokens',
        'settings' => [
          'columns' => [
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.near_contact_id.display_name',
              'dataType' => 'String',
              'label' => E::ts('PAQPF_nomAffiche'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.address_primary.supplemental_address_1',
              'dataType' => 'String',
              'label' => E::ts('PAQPF_cptAd1'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.address_primary.postal_code',
              'dataType' => 'String',
              'label' => E::ts('PAQPF_CP'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.address_primary.city',
              'dataType' => 'String',
              'label' => E::ts('PAQPF_Ville'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01.email',
              'dataType' => 'String',
              'label' => E::ts('PAQPF_Courriel'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.phone_primary.phone',
              'dataType' => 'String',
              'label' => E::ts('PAQPF_Téléphone'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.address_primary.street_address',
              'dataType' => 'String',
              'label' => E::ts('PAQPF_Rue'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.email_greeting_display',
              'dataType' => 'String',
              'label' => E::ts('PAQPF_GreetingCourriel'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.postal_greeting_display',
              'dataType' => 'String',
              'label' => E::ts('PAQPF_GreetingPostal'),
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
