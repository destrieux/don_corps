<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Tokens_de_contact_CB',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Tokens_de_contact_CB',
        'label' => E::ts('Tokens de contact CB'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'champs_caches.piece_prinicpale',
          ],
          'orderBy' => [],
          'where' => [],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'description' => E::ts('tokens à utiliser pour codes barres'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Tokens_de_contact_CB_SearchDisplay_Tokens_for_contact_Champs_de_fusion_1_copy_',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Tokens_for_contact_Champs_de_fusion_1_copy_',
        'label' => E::ts('Tokens for contact CB'),
        'saved_search_id.name' => 'Tokens_de_contact_CB',
        'type' => 'tokens',
        'settings' => [
          'columns' => [
            [
              'type' => 'field',
              'key' => 'champs_caches.piece_prinicpale',
              'dataType' => 'String',
              'label' => E::ts('CB_NumPiecePrincipale'),
            ],
            [
              'type' => 'field',
              'key' => 'id',
              'label' => E::ts('CB_id'),
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
