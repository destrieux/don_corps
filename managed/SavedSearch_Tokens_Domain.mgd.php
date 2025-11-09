<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Tokens_Domain',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Tokens_Domain',
        'label' => E::ts('Tokens : Domain'),
        'api_entity' => 'Organization',
        'api_params' => [
          'version' => 4,
          'select' => [
            'sort_name',
            'CDC_Administration.site_www',
            'CDC_Administration.Directeur',
            'CDC_Administration.Gestionnaire',
            'CDC_Administration.DPO',
            'CDC_Administration.Pr_parateur_s_',
            'id',
          ],
          'orderBy' => [],
          'where' => [
            ['id', '=', '1', NULL],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Tokens_Domain_SearchDisplay_Tokens_Domain',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Tokens_Domain',
        'label' => E::ts('Tokens : Domain'),
        'saved_search_id.name' => 'Tokens_Domain',
        'type' => 'tokens',
        'settings' => [
          'columns' => [
            [
              'type' => 'field',
              'key' => 'CDC_Administration.site_www',
              'label' => E::ts('Site www'),
            ],
            [
              'type' => 'field',
              'key' => 'CDC_Administration.Directeur',
              'label' => E::ts('Directeur'),
            ],
            [
              'type' => 'field',
              'key' => 'CDC_Administration.Gestionnaire',
              'label' => E::ts('Gestionnaire(s)'),
            ],
            [
              'type' => 'field',
              'key' => 'CDC_Administration.DPO',
              'label' => E::ts('DPO'),
            ],
            [
              'type' => 'field',
              'key' => 'CDC_Administration.Pr_parateur_s_',
              'label' => E::ts('Préparateur(s)'),
            ],
          ],
          'description' => E::ts('Tokens supplémentaires pour le Domain'),
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
