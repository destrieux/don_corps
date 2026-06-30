<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Token_Contacts_arriv_e_corps',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Token_Contacts_arriv_e_corps',
        'label' => E::ts('Token Contacts arrivée corps'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'Contact_Custom_Arriv_e_du_corps_new_entity_id_01.Effets_personnels:label',
            'id',
            'id',
            'id',
          ],
          'orderBy' => [],
          'where' => [],
          'groupBy' => [],
          'join' => [
            [
              'Custom_Arriv_e_du_corps_new AS Contact_Custom_Arriv_e_du_corps_new_entity_id_01',
              'LEFT',
              [
                'id',
                '=',
                'Contact_Custom_Arriv_e_du_corps_new_entity_id_01.entity_id',
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
    'name' => 'SavedSearch_Token_Contacts_arriv_e_corps_SearchDisplay_Token_Contacts_arriv_e_corps_Champs_de_fusion_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Token_Contacts_arriv_e_corps_Champs_de_fusion_1',
        'label' => E::ts('Token Contacts arrivée corps'),
        'saved_search_id.name' => 'Token_Contacts_arriv_e_corps',
        'type' => 'tokens',
        'settings' => [
          'columns' => [
            [
              'type' => 'field',
              'key' => 'Contact_Custom_Arriv_e_du_corps_new_entity_id_01.Effets_personnels:label',
              'label' => E::ts('Effets personnels'),
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
