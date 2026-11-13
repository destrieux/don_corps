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
        'label' => E::ts('PAQFP non inscrits ou cérémonie à venir'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'Compl_m_nt_tat_civil.Civilit_user:label',
            'sort_name',
            'Contact_RelationshipCache_Contact_01.far_relation:label',
            'Contact_RelationshipCache_Contact_01.display_name',
            'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.N_de_d_c_s',
            'Contact_RelationshipCache_Contact_01.deceased_date',
            'Contact_RelationshipCache_Contact_01.Devenir_du_corps.Date_op_rations_fun_raires',
            'Contact_RelationshipCache_Contact_01.Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:label',
            'Contact_Participant_contact_id_01.status_id:label',
            'Contact_Participant_contact_id_01_Participant_Event_event_id_01.title',
            'Contact_Participant_contact_id_01_Participant_Event_event_id_01.start_date',
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
                'OR',
                [
                  [
                    'Contact_RelationshipCache_Contact_01.far_relation:name',
                    '=',
                    '"est la PAQPF"',
                  ],
                ],
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
    'name' => 'SavedSearch_Personnes_r_f_rentes_dont_le_donneur_est_d_c_d_sans_les_deux_mois_pr_c_dents_SearchDisplay_PAQFP_des_d_funts_d_c_d_s_non_inscrits_une_c_r_monie_ou_inscrits_une_c_r_monie_venir_Table_2',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'PAQFP_des_d_funts_d_c_d_s_non_inscrits_une_c_r_monie_ou_inscrits_une_c_r_monie_venir_Table_2',
        'label' => E::ts('PAQPFTable 2'),
        'saved_search_id.name' => 'Personnes_r_f_rentes_dont_le_donneur_est_d_c_d_sans_les_deux_mois_pr_c_dents',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('PAQFP non inscrits à une cérémonie ou cérémonie à venir'),
          'sort' => [
            ['sort_name', 'ASC'],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'Compl_m_nt_tat_civil.Civilit_user:label',
              'label' => E::ts('Civilité'),
              'sortable' => TRUE,
              'alignment' => 'text-right',
            ],
            [
              'type' => 'field',
              'key' => 'sort_name',
              'label' => E::ts('Nom'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => '',
                'target' => '_blank',
                'task' => '',
              ],
              'title' => E::ts('Voir Contact'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.far_relation:label',
              'label' => E::ts('Relation'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
              'cssRules' => [
                [
                  'bg-danger',
                  'Contact_RelationshipCache_Contact_01.far_relation:label',
                  '!=',
                  'est la PAQPF',
                ],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.display_name',
              'label' => E::ts('Défunt'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => 'Contact_RelationshipCache_Contact_01',
                'target' => '_blank',
                'task' => '',
              ],
              'title' => E::ts('Voir Contact Contacts liés'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.N_de_d_c_s',
              'label' => E::ts('N° de décès'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => 'Contact_RelationshipCache_Contact_01',
                'target' => '_blank',
                'task' => '',
              ],
              'title' => E::ts('Voir Contact Contacts liés'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.deceased_date',
              'label' => E::ts('Date de décès'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.Devenir_du_corps.Date_op_rations_fun_raires',
              'label' => E::ts('Date opérations funéraires'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:label',
              'label' => E::ts('Prévenir référent de cérémonie'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Participant_contact_id_01.status_id:label',
              'label' => E::ts('Statut d\'inscription à la cérémonie'),
              'sortable' => TRUE,
              'cssRules' => [
                [
                  'bg-success',
                  'Contact_Participant_contact_id_01.status_id:name',
                  '=',
                  'Registered',
                ],
                [
                  'bg-danger',
                  'Contact_Participant_contact_id_01.status_id:name',
                  'IS EMPTY',
                ],
                [
                  'bg-warning',
                  'Contact_Participant_contact_id_01.status_id:name',
                  '=',
                  'On waitlist',
                ],
                [
                  'bg-info',
                  'Contact_Participant_contact_id_01.status_id:name',
                  '=',
                  'Cancelled',
                ],
              ],
              'alignment' => 'text-center',
              'editable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Participant_contact_id_01_Participant_Event_event_id_01.title',
              'label' => E::ts('Cérémonie'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Participant_contact_id_01_Participant_Event_event_id_01.start_date',
              'label' => E::ts('Date de la cérémonie'),
              'sortable' => TRUE,
              'alignment' => 'text-center',
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
