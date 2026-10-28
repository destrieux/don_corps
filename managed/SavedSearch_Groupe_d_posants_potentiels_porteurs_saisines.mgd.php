<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Groupe_d_posants_potentiels_porteurs_saisines',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Groupe_d_posants_potentiels_porteurs_saisines',
        'label' => E::ts('Groupe déposants potentiels porteurs saisines'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'id',
            'sort_name',
            'contact_type:label',
            'contact_sub_type:label',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS ONE OF',
              [
                'Personnel',
                'Intervenant_ext_rieur',
              ],
            ],
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
    'name' => 'SavedSearch_Groupe_d_posants_potentiels_porteurs_saisines_Group_groupe_d_posants_porteurs_potent_24',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'groupe_d_posants_porteurs_potent_24',
        'title' => E::ts('groupe déposants porteurs potentiels'),
        'description' => E::ts('Liste les déposants poteintiels de saisine : personnels et intervenants externes'),
        'saved_search_id.name' => 'Groupe_d_posants_potentiels_porteurs_saisines',
        'group_type' => [],
        'frontend_title' => E::ts('groupe déposants porteurs potentiels'),
      ],
      'match' => ['name'],
    ],
  ],
];
