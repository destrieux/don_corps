<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Anonymises_sans_protocole',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Anonymises_sans_protocole',
        'label' => E::ts('Anonymises sans protocole'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'sort_name',
            'Contact_Custom_Utilisation_du_corps_entity_id_01.Protocole_de_recherche_ex_vivo2:label',
          ],
          'orderBy' => [],
          'where' => [
            ['last_name', '=', 'ANONYMISE'],
            ['first_name', '=', 'Anonymisé'],
            [
              'OR',
              [
                [
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Protocole_de_recherche_ex_vivo2:name',
                  'CONTAINS',
                  'Pas_de_protocole',
                ],
                [
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Protocole_de_recherche_ex_vivo2:name',
                  'IS EMPTY',
                ],
              ],
            ],
            [
              'tags:name',
              'NOT IN',
              ['ATCD Purges'],
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Custom_Utilisation_du_corps AS Contact_Custom_Utilisation_du_corps_entity_id_01',
              'LEFT',
              [
                'id',
                '=',
                'Contact_Custom_Utilisation_du_corps_entity_id_01.entity_id',
              ],
            ],
          ],
          'having' => [],
        ],
        'description' => E::ts('Donneurs anonymisés dont les ATCD n\'ont pas été purgés et qui ne sont pas inclus dans un protocole. Utilisé pour préserver les ATCD des donneurs inclus dans un ptotocole'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Anonymises_sans_protocole_Group_Ano_NoProt_63',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Ano_NoProt_63',
        'title' => E::ts('ATCD Anonymises sans protocole'),
        'description' => E::ts('Donneurs anonymisés non inclus dans un protocole ex vivo'),
        'saved_search_id.name' => 'Anonymises_sans_protocole',
        'group_type' => [],
        'frontend_title' => E::ts('Ano_NoProt'),
      ],
      'match' => ['name'],
    ],
  ],
];
