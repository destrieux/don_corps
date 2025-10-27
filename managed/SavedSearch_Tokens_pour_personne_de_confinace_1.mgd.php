<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Tokens_pour_personne_de_confinace_1',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Tokens_pour_personne_de_confinace_1',
        'label' => E::ts('Tokens pour personne de confiance 1'),
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
                '"a pour personne de confiance"',
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
    'name' => 'SavedSearch_Tokens_pour_personne_de_confinace_1_SearchDisplay_PernConf_1_Champs_de_fusion_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'PernConf_1_Champs_de_fusion_1',
        'label' => E::ts('PersConf1'),
        'saved_search_id.name' => 'Tokens_pour_personne_de_confinace_1',
        'type' => 'tokens',
        'settings' => [
          'columns' => [
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.near_contact_id.display_name',
              'dataType' => 'String',
              'label' => E::ts('NomAffiche'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.address_primary.street_address',
              'dataType' => 'String',
              'label' => E::ts('Rue'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.address_primary.supplemental_address_1',
              'dataType' => 'String',
              'label' => E::ts('Adresse_cpt1'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.address_primary.postal_code',
              'dataType' => 'String',
              'label' => E::ts('CP'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.address_primary.city',
              'dataType' => 'String',
              'label' => E::ts('Ville'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01_Contact_Email_contact_id_01.email',
              'dataType' => 'String',
              'label' => E::ts('Courriel'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.phone_primary.phone',
              'dataType' => 'String',
              'label' => E::ts('Tel'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.email_greeting_display',
              'dataType' => 'String',
              'rewrite' => '',
              'label' => E::ts('Greeeting_postal'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.postal_greeting_display',
              'dataType' => 'String',
              'label' => E::ts('Greeeting_mail'),
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
