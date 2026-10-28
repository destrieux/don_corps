<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Activit_s_labo',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Activit_s_labo',
        'label' => E::ts('Activités labo'),
        'api_entity' => 'Event',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'title',
            'start_date',
            'end_date',
            'Event_Saisine_id_de_saisine_01.label',
            'Caract_ristiques_de_l_activit_.En_attente_de_confirmation',
            'Event_Saisine_id_de_saisine_01.caracteristiques.Type_d_activit_:label',
            'ISNOTNULL(Documents_de_l_activit_.Feuille_d_margement) AS ISNOTNULL_Documents_de_l_activit__Feuille_d_margement',
          ],
          'orderBy' => [],
          'where' => [
            [
              'event_type_id:name',
              '=',
              'Activité au laboratoire',
            ],
          ],
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
    'name' => 'SavedSearch_Activit_s_labo_SearchDisplay_Activit_s_labo_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Activit_s_labo_Table_1',
        'label' => E::ts('Activités labo Table 1'),
        'saved_search_id.name' => 'Activit_s_labo',
        'type:name' => 'crm-search-display-table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
          'columnMode' => 'custom',
          'actions_display_mode' => 'menu',
          'columns' => [
            [
              'type' => 'field',
              'key' => 'title',
              'label' => E::ts('Titre'),
              'sortable' => TRUE,
              'link' => [
                'path' => 'civicrm/NouvelleActivite#?Event1=[id]',
                'entity' => '',
                'action' => '',
                'join' => '',
                'target' => '_blank',
                'task' => '',
              ],
            ],
            [
              'type' => 'field',
              'key' => 'Event_Saisine_id_de_saisine_01.label',
              'label' => E::ts('N° de saisine'),
              'sortable' => TRUE,
              'link' => [
                'path' => 'civicrm/Saisine#?Saisine1=[Event_Saisine_id_de_saisine_01.id]',
                'entity' => '',
                'action' => '',
                'join' => '',
                'target' => '_blank',
                'task' => '',
              ],
              'alignment' => 'text-center',
              'cssRules' => [
                [
                  'disabled',
                  'Caract_ristiques_de_l_activit_.En_attente_de_confirmation',
                  '=',
                  FALSE,
                ],
                [
                  'bg-danger',
                  'Event_Saisine_id_de_saisine_01.label',
                  'IS EMPTY',
                ],
                [
                  'bg-success',
                  'Event_Saisine_id_de_saisine_01.label',
                  'IS NOT EMPTY',
                ],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'Caract_ristiques_de_l_activit_.En_attente_de_confirmation',
              'label' => E::ts('Confirmé'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
              'editable' => TRUE,
              'cssRules' => [
                [
                  'disabled',
                  'Caract_ristiques_de_l_activit_.En_attente_de_confirmation',
                  '=',
                  FALSE,
                ],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'start_date',
              'label' => E::ts('Date de début'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
              'cssRules' => [
                [
                  'disabled',
                  'Caract_ristiques_de_l_activit_.En_attente_de_confirmation',
                  '=',
                  FALSE,
                ],
              ],
              'editable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'end_date',
              'label' => E::ts('Date de fin'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
              'editable' => TRUE,
              'cssRules' => [
                [
                  'disabled',
                  'Caract_ristiques_de_l_activit_.En_attente_de_confirmation',
                  '=',
                  FALSE,
                ],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'Event_Saisine_id_de_saisine_01.caracteristiques.Type_d_activit_:label',
              'label' => E::ts('Type d\'activité'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'ISNOTNULL_Documents_de_l_activit__Feuille_d_margement',
              'label' => E::ts('Feuille d\'émargement téléversée'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
              'cssRules' => [
                [
                  'bg-success',
                  'ISNOTNULL_Documents_de_l_activit__Feuille_d_margement',
                  '=',
                  'FALSE',
                ],
                [
                  'bg-danger',
                  'ISNOTNULL_Documents_de_l_activit__Feuille_d_margement',
                  '!=',
                  'FALSE',
                ],
              ],
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
