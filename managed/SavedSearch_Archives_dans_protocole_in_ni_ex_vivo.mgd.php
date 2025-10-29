<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Archives_dans_protocole_in_ni_ex_vivo',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Archives_dans_protocole_in_ni_ex_vivo',
        'label' => E::ts('Archives sans protocole in ni ex vivo'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'sort_name',
            'contact_type:label',
            'contact_sub_type:label',
            'Contact_Custom_Protocoles_in_vivo_entity_id_01.Intitul_du_protocole:label',
          ],
          'orderBy' => [],
          'where' => [
            [
              'groups:name',
              'IN',
              ['Archives_61'],
            ],
            [
              'tags:name',
              'NOT IN',
              ['ATCD Purges'],
            ],
            [
              'Contact_Custom_Protocoles_in_vivo_entity_id_01.Intitul_du_protocole:name',
              'IS EMPTY',
            ],
            [
              'groups:name',
              'NOT IN',
              [
                'contacts_protocole_ex__24',
              ],
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Custom_Protocoles_in_vivo AS Contact_Custom_Protocoles_in_vivo_entity_id_01',
              'LEFT',
              [
                'id',
                '=',
                'Contact_Custom_Protocoles_in_vivo_entity_id_01.entity_id',
              ],
            ],
          ],
          'having' => [],
        ],
        'description' => E::ts('Liste les contacts du groupe archive (i.e anonymisés) qui ne comportent pas de protocoles ex vivo (n\'appatiennent pas au groupe contacts protocole ex vivo)  ni de protocole ex vivo
Les antécédents et utilisations seront supprimés chez eux et conservés chez les autres. Pour supprimer les ATCD et utilisations chez tous, ajouter \'Usage\', et ATCD à l\'api purge pour le groupe souhaité.'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Archives_dans_protocole_in_ni_ex_vivo_Group_Archives_sans_protocole_in_ni_ex_23',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Archives_sans_protocole_in_ni_ex__23',
        'title' => E::ts('Archives sans protocole in ni ex vivo'),
        'description' => E::ts('Liste les contacts du groupe archive (i.e anonymisés) qui ne comportent pas de protocoles in ni ex vivo
Les antécédents et utilisations seront supprimés chez eux et conservés chez les autres. Pour supprimer les ATCD et utilisations chez tous, ajouter \'Usage\', et ATCD à l\'api purge pour le groupe souhaité.'),
        'saved_search_id.name' => 'Archives_dans_protocole_in_ni_ex_vivo',
        'group_type' => [],
        'frontend_title' => E::ts('Archives sans protocole in ni ex vivo'),
      ],
      'match' => ['name'],
    ],
  ],
];
