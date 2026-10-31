<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Tokens_activit_s_labo',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Tokens_activit_s_labo',
        'label' => E::ts('Tokens activités labo'),
        'api_entity' => 'Event',
        'api_params' => [
          'version' => 4,
          'select' => [
            'title',
            'start_date',
            'end_date',
            'Event_Saisine_id_de_saisine_01.caracteristiques.D_posant.display_name',
            'id',
          ],
          'orderBy' => [],
          'where' => [],
          'groupBy' => [],
          'join' => [
            [
              'Saisine AS Event_Saisine_id_de_saisine_01',
              'LEFT',
              [
                'Caract_ristiques_de_l_activit_.id_de_saisine',
                '=',
                'Event_Saisine_id_de_saisine_01.id',
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
    'name' => 'SavedSearch_Tokens_activit_s_labo_SearchDisplay_Tokens_activit_s_labo_Champs_de_fusion_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Tokens_activit_s_labo_Champs_de_fusion_1',
        'label' => E::ts('Tokens activités labo Champs de fusion 1'),
        'saved_search_id.name' => 'Tokens_activit_s_labo',
        'type:name' => 'crm-search-display-tokens',
        'settings' => [
          'columns' => [
            [
              'type' => 'field',
              'key' => 'title',
              'label' => E::ts('Titre'),
            ],
            [
              'type' => 'field',
              'key' => 'start_date',
              'label' => E::ts('Début'),
              'format' => 'dateformatFull',
            ],
            [
              'type' => 'field',
              'key' => 'end_date',
              'label' => E::ts('Fin'),
              'format' => 'dateformatFull',
            ],
            [
              'type' => 'field',
              'key' => 'Event_Saisine_id_de_saisine_01.caracteristiques.D_posant.display_name',
              'label' => E::ts('Déposant'),
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
