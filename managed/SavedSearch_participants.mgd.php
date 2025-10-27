<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_participants',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'participants',
        'label' => E::ts('Participants'),
        'api_entity' => 'Participant',
        'api_params' => [
          'version' => 4,
          'select' => [
            'event_id.title',
            'Participant_Event_event_id_01.start_date',
            'Participant_Contact_contact_id_01.contact_sub_type:label',
            'role_id:label',
            'status_id:label',
            'contact_id.sort_name',
            'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.far_relation:label',
            'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.near_contact_id.sort_name',
            'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.deceased_date',
          ],
          'orderBy' => [],
          'where' => [
            [
              'OR',
              [
                [
                  'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.relationship_type_id',
                  'CONTAINS ONE OF',
                  [12, 14, 13],
                ],
                [
                  'Participant_Contact_contact_id_01.contact_sub_type:name',
                  'CONTAINS ONE OF',
                  ['Personnel'],
                ],
              ],
            ],
            [
              'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.far_relation:name',
              'CONTAINS ONE OF',
              [
                'est la PAQPF',
                'est la personne de confiance 2',
                'est la personne de confiance de',
              ],
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Contact AS Participant_Contact_contact_id_01',
              'LEFT',
              [
                'contact_id',
                '=',
                'Participant_Contact_contact_id_01.id',
              ],
              [
                'Participant_Contact_contact_id_01.contact_type:name',
                'CONTAINS ONE OF',
                '"Individual"',
              ],
            ],
            [
              'Contact AS Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01',
              'LEFT',
              'RelationshipCache',
              [
                'Participant_Contact_contact_id_01.id',
                '=',
                'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.far_contact_id',
              ],
            ],
            [
              'Event AS Participant_Event_event_id_01',
              'LEFT',
              [
                'event_id',
                '=',
                'Participant_Event_event_id_01.id',
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
    'name' => 'SavedSearch_participants_SearchDisplay_Participants',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Participants',
        'label' => E::ts('Participants'),
        'saved_search_id.name' => 'participants',
        'type' => 'table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [
            [
              'Participant_Event_event_id_01.start_date',
              'DESC',
            ],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'event_id.title',
              'label' => E::ts('Titre'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Participant_Event_event_id_01.start_date',
              'label' => E::ts('Date'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Participant_Contact_contact_id_01.contact_sub_type:label',
              'label' => E::ts('Type'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'role_id:label',
              'label' => E::ts('Rôle'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'status_id:label',
              'label' => E::ts('Statut'),
              'sortable' => TRUE,
              'cssRules' => [
                ['bg-success', 'status_id:name', '=', 'Registered'],
                ['bg-danger', 'status_id:name', '=', 'Cancelled'],
                ['bg-info', 'status_id:name', '=', 'Expired'],
              ],
              'editable' => TRUE,
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'contact_id.sort_name',
              'label' => E::ts('Participant'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => 'contact_id',
                'target' => '_blank',
                'task' => '',
              ],
              'title' => E::ts('Voir Contact'),
            ],
            [
              'type' => 'field',
              'key' => 'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.far_relation:label',
              'label' => E::ts('Relation au défunt'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.near_contact_id.sort_name',
              'label' => E::ts('Défunt'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => 'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.near_contact_id',
                'target' => '_blank',
                'task' => '',
              ],
              'title' => E::ts('Voir Contact (Near side)'),
            ],
            [
              'type' => 'field',
              'key' => 'Participant_Contact_contact_id_01_Contact_RelationshipCache_Contact_01.deceased_date',
              'label' => E::ts('Date du décès'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
          'actions_display_mode' => 'menu',
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
