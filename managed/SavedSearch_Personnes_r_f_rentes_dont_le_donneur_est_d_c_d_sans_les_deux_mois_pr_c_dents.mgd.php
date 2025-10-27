<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Personnes_r_f_rentes_dont_le_donneur_est_d_c_d_sans_les_deux_mois_pr_c_dents',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Personnes_r_f_rentes_dont_le_donneur_est_d_c_d_sans_les_deux_mois_pr_c_dents',
        'label' => E::ts('PAQFP des défunts décédés non inscrits à une cérémonie ou inscrits à une cérémonie à venir'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'sort_name',
            'Contact_RelationshipCache_Contact_01.far_relation:label',
            'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.N_de_d_c_s',
            'Contact_RelationshipCache_Contact_01.display_name',
            'Contact_RelationshipCache_Contact_01.deceased_date',
            'Contact_RelationshipCache_Contact_01.Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:label',
            'Contact_Participant_contact_id_01.event_id.title',
            'Contact_Participant_contact_id_01.status_id:label',
            'Contact_Participant_contact_id_01_Participant_Event_event_id_01.title',
            'Contact_Participant_contact_id_01_Participant_Event_event_id_01.start_date',
            'id',
          ],
          'orderBy' => [],
          'where' => [
            [
              'OR',
              [
                [
                  'contact_type:name',
                  '=',
                  'Individual',
                ],
              ],
            ],
            [
              'Contact_RelationshipCache_Contact_01.deceased_date',
              '=',
              'ending_2.year',
            ],
            [
              'OR',
              [
                [
                  'Contact_RelationshipCache_Contact_01.Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:name',
                  'IS EMPTY',
                ],
                [
                  'Contact_RelationshipCache_Contact_01.Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:name',
                  '!=',
                  'Non',
                ],
              ],
            ],
            [
              'OR',
              [
                [
                  'Contact_Participant_contact_id_01_Participant_Event_event_id_01.start_date',
                  'IS EMPTY',
                ],
                [
                  'Contact_Participant_contact_id_01_Participant_Event_event_id_01.start_date',
                  '>',
                  'now',
                ],
              ],
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Contact AS Contact_RelationshipCache_Contact_01',
              'INNER',
              'RelationshipCache',
              [
                'id',
                '=',
                'Contact_RelationshipCache_Contact_01.far_contact_id',
              ],
              [
                'Contact_RelationshipCache_Contact_01.far_relation:name',
                '=',
                '"est la PAQPF"',
              ],
            ],
            [
              'Participant AS Contact_Participant_contact_id_01',
              'LEFT',
              [
                'id',
                '=',
                'Contact_Participant_contact_id_01.contact_id',
              ],
            ],
            [
              'Event AS Contact_Participant_contact_id_01_Participant_Event_event_id_01',
              'LEFT',
              [
                'Contact_Participant_contact_id_01.event_id',
                '=',
                'Contact_Participant_contact_id_01_Participant_Event_event_id_01.id',
              ],
            ],
          ],
          'having' => [],
        ],
        'description' => E::ts('PAQFP des défunts décédés non inscrits à une cérémonie ou inscrits à une cérémonie à venir'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Personnes_r_f_rentes_dont_le_donneur_est_d_c_d_sans_les_deux_mois_pr_c_dents_SearchDisplay_Personnes_r_f_rentes_dont_le_donneur_est_d_c_d_dans_les_deux_mois_pr_c_dents',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Personnes_r_f_rentes_dont_le_donneur_est_d_c_d_dans_les_deux_mois_pr_c_dents',
        'label' => E::ts('PAQPF non incrits ou incrits à cérémonie à venir'),
        'saved_search_id.name' => 'Personnes_r_f_rentes_dont_le_donneur_est_d_c_d_sans_les_deux_mois_pr_c_dents',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('PAQFP des défunts décédés non inscrits à une cérémonie ou inscrits à une cérémonie à venir'),
          'sort' => [
            [
              'Contact_Participant_contact_id_01_Participant_Cérémonie_event_id_01.start_date',
              'ASC',
            ],
            ['last_name', 'ASC'],
            ['first_name', 'ASC'],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'more_greetings_group.greeting_field_1',
              'dataType' => 'String',
              'label' => E::ts('Civilité'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'first_name',
              'dataType' => 'String',
              'label' => E::ts('Prénom'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'last_name',
              'dataType' => 'String',
              'label' => E::ts('Nom'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => '',
                'target' => '_blank',
              ],
              'title' => E::ts('Voir Contact'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.far_relation:label',
              'dataType' => 'String',
              'label' => E::ts('relation au défunt'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.display_name',
              'dataType' => 'String',
              'label' => E::ts('Défunt'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => 'Contact_RelationshipCache_Contact_01',
                'target' => '_blank',
              ],
              'title' => E::ts('Voir Contact Contacts liés'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.N_de_d_c_s',
              'dataType' => 'String',
              'label' => E::ts('N° de décès'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.deceased_date',
              'dataType' => 'Date',
              'label' => E::ts('Date du décès'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:label',
              'dataType' => 'Integer',
              'label' => E::ts('Prévenir personne référence'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Participant_contact_id_01_Participant_Event_event_id_01.title',
              'dataType' => 'String',
              'label' => E::ts('Cérémonie'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Participant_contact_id_01_Participant_Event_event_id_01.start_date',
              'dataType' => 'Timestamp',
              'label' => E::ts('Date de cérémonie'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Participant_contact_id_01.status_id:label',
              'dataType' => 'Integer',
              'label' => E::ts('Statut'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => [
            'contact.103',
            'contact.9',
            'contact.mailing',
            'contact.107',
            'download',
            'contact.3',
            'contact.16',
          ],
          'classes' => ['table', 'table-striped'],
          'headerCount' => TRUE,
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
