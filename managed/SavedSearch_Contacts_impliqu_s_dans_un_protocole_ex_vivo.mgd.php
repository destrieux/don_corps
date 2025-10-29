<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Contacts_impliqu_s_dans_un_protocole_ex_vivo',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Contacts_impliqu_s_dans_un_protocole_ex_vivo',
        'label' => E::ts('Contacts impliqués dans un protocole ex vivo'),
        'api_entity' => 'Custom_Utilisation_du_corps',
        'api_params' => [
          'version' => 4,
          'select' => [
            'entity_id',
            'GROUP_CONCAT(DISTINCT entity_id.display_name) AS GROUP_CONCAT_entity_id_display_name',
            'COUNT(Custom_Utilisation_du_corps_Contact_entity_id_01.id) AS COUNT_Custom_Utilisation_du_corps_Contact_entity_id_01_id',
            'GROUP_CONCAT(DISTINCT N_de_pi_ce_ou_de_corps) AS GROUP_CONCAT_N_de_pi_ce_ou_de_corps',
            'GROUP_CONCAT(DISTINCT Protocole_de_recherche_ex_vivo2:label) AS GROUP_CONCAT_Protocole_de_recherche_ex_vivo2_label',
          ],
          'orderBy' => [],
          'where' => [
            [
              'Protocole_de_recherche_ex_vivo2:name',
              'IS NOT EMPTY',
            ],
            [
              'Protocole_de_recherche_ex_vivo2:name',
              'NOT CONTAINS ONE OF',
              [
                'Pas_de_protocole',
              ],
            ],
          ],
          'groupBy' => ['entity_id'],
          'join' => [
            [
              'Contact AS Custom_Utilisation_du_corps_Contact_entity_id_01',
              'LEFT',
              [
                'entity_id',
                '=',
                'Custom_Utilisation_du_corps_Contact_entity_id_01.id',
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
    'name' => 'SavedSearch_Contacts_impliqu_s_dans_un_protocole_ex_vivo_Group_contacts_protocole_ex_24',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'contacts_protocole_ex__24',
        'title' => E::ts('contacts protocole ex vivo'),
        'description' => E::ts('liste les contacts impliqués dans un protocole ex vivo'),
        'saved_search_id.name' => 'Contacts_impliqu_s_dans_un_protocole_ex_vivo',
        'group_type' => [],
        'frontend_title' => E::ts('contacts protocole ex vivo'),
      ],
      'match' => ['name'],
    ],
  ],
];
