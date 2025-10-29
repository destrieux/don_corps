<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Personnels_tous',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Personnels_tous',
        'label' => E::ts('Personnels_tous'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'sort_name',
            'contact_type:label',
            'contact_sub_type:label',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              ['Personnel'],
            ],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'description' => E::ts('Liste les personnels des centres de don ; utilisé comme filtre dans les utilisations de corps pour assigne une préparation à une personne'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Personnels_tous_Group_Personnel_centre_de_22',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Personnel_centre_de_22',
        'title' => E::ts('Personnel centre de don'),
        'saved_search_id.name' => 'Personnels_tous',
        'group_type' => [],
        'frontend_title' => E::ts('Personnel centre de don'),
      ],
      'match' => ['name'],
    ],
  ],
];
