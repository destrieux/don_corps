<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_tableau_bord_2',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'tableau_bord_2',
        'label' => E::ts('Tableau bord corps'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'sort_name',
            'contact_sub_type:label',
            'Contact_Custom_Utilisation_du_corps_entity_id_01.Type_de_poi_ce_3:label',
            'deceased_date',
            'Prise_en_charge_au_d_c_s.N_de_d_c_s',
            'Contact_Custom_Arriv_e_du_corps_new_entity_id_01.Retrait_Stimulateur_piles:label',
            'Promesse_de_don.Devenir_souhait_:label',
            'Contact_Custom_Utilisation_du_corps_entity_id_01.Lacalisation.display_name',
            'Contact_Custom_Utilisation_du_corps_entity_id_01.N_de_pi_ce_ou_de_corps',
            'Contact_Custom_Utilisation_du_corps_entity_id_01.Utilisation2:label',
            'champs_caches.toutes_utilisations:label',
            'champs_caches.toutes_pieces:label',
            'Contact_Custom_Utilisation_du_corps_entity_id_01.Compl_ment',
            'Devenir_du_corps.Date_de_sortie_d_finitive',
            'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:label',
            'Devenir_du_corps.Date_op_rations_fun_raires',
            'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires:label',
            'id',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Donateur',
            ],
            [
              'OR',
              [
                [
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Type_de_poi_ce_3:name',
                  'CONTAINS',
                  'Corps_entier_tronc',
                ],
                [
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.N_de_pi_ce_ou_de_corps',
                  'IS EMPTY',
                ],
              ],
            ],
            [
              'OR',
              [
                ['is_deceased', '=', TRUE],
                [
                  'Prise_en_charge_au_d_c_s.N_de_d_c_s',
                  'IS NOT EMPTY',
                ],
                ['deceased_date', 'IS NOT EMPTY'],
                [
                  'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '!=',
                  'Pas_de_refus',
                ],
              ],
            ],
            [
              'OR',
              [
                [
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:name',
                  '=',
                  'Non_limin_e',
                ],
                [
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:name',
                  '=',
                  'Conservation_illimit_e',
                ],
                [
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:name',
                  '=',
                  'Demander_cr_mation',
                ],
                [
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:name',
                  '=',
                  'Cr_mation_demand_e',
                ],
              ],
            ],
            [
              'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
              '=',
              'Pas_de_refus',
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
            [
              'Custom_Arriv_e_du_corps_new AS Contact_Custom_Arriv_e_du_corps_new_entity_id_01',
              'LEFT',
              [
                'id',
                '=',
                'Contact_Custom_Arriv_e_du_corps_new_entity_id_01.entity_id',
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
    'name' => 'SavedSearch_tableau_bord_2_SearchDisplay_Tableau_bord_corps',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Tableau_bord_corps',
        'label' => E::ts('Tableau bord corps'),
        'saved_search_id.name' => 'tableau_bord_2',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Tableau de bord des corps présents (Donneurs décédés sans date d\'opérations funéraires)
Alertes si : restitution, pas de numéro de décès ou de code barres, stimulateur à piles non vérifié, pièce non localisée'),
          'sort' => [
            ['sort_name', 'ASC'],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'sort_name',
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
              'cssRules' => [
                [
                  'bg-danger',
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.N_de_pi_ce_ou_de_corps',
                  'IS EMPTY',
                ],
                [
                  'bg-danger',
                  'Prise_en_charge_au_d_c_s.N_de_d_c_s',
                  'IS EMPTY',
                ],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'Prise_en_charge_au_d_c_s.N_de_d_c_s',
              'dataType' => 'String',
              'label' => E::ts('N° de décès'),
              'sortable' => TRUE,
              'cssRules' => [],
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'deceased_date',
              'dataType' => 'Date',
              'label' => E::ts('Date du décès'),
              'sortable' => TRUE,
              'cssRules' => [],
              'alignment' => 'text-center',
              'editable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Custom_Arriv_e_du_corps_new_entity_id_01.Retrait_Stimulateur_piles:label',
              'label' => E::ts('Retrait Stimulateur à piles'),
              'sortable' => TRUE,
              'editable' => TRUE,
              'cssRules' => [
                [
                  'bg-danger',
                  'Contact_Custom_Arriv_e_du_corps_new_entity_id_01.Retrait_Stimulateur_piles:name',
                  '=',
                  'A vérifier',
                ],
                [
                  'bg-success',
                  'Contact_Custom_Arriv_e_du_corps_new_entity_id_01.Retrait_Stimulateur_piles:name',
                  '!=',
                  '',
                ],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'Promesse_de_don.Devenir_souhait_:label',
              'dataType' => 'Integer',
              'label' => E::ts('Devenir souhaité'),
              'sortable' => TRUE,
              'cssRules' => [
                [
                  'bg-danger',
                  'Promesse_de_don.Devenir_souhait_:name',
                  '!=',
                  'Cr_mation_et_dispersion_par_le_',
                ],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Custom_Utilisation_du_corps_entity_id_01.N_de_pi_ce_ou_de_corps',
              'dataType' => 'String',
              'label' => E::ts('Code Barres'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Custom_Utilisation_du_corps',
                'action' => 'view',
                'join' => 'Contact_Custom_Utilisation_du_corps_entity_id_01',
                'target' => 'crm-popup',
              ],
              'title' => E::ts('Voir Utilisation du corps'),
              'cssRules' => [
                [
                  'bg-danger',
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.N_de_pi_ce_ou_de_corps',
                  'IS EMPTY',
                ],
              ],
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Custom_Utilisation_du_corps_entity_id_01.Lacalisation.display_name',
              'dataType' => 'String',
              'label' => E::ts('Localisation'),
              'sortable' => TRUE,
              'cssRules' => [
                [
                  'bg-warning',
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Lacalisation.display_name',
                  '=',
                  'CDC Tours - A localiser',
                ],
                [
                  'bg-warning',
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Lacalisation.display_name',
                  'IS EMPTY',
                ],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:label',
              'dataType' => 'String',
              'label' => E::ts('Mode élimination'),
              'sortable' => TRUE,
              'cssRules' => [
                [
                  'bg-info',
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:name',
                  '=',
                  'Demander_cr_mation',
                ],
                [
                  'bg-success',
                  'Contact_Custom_Utilisation_du_corps_entity_id_01.Mode_limination_hors_corps_2:name',
                  '=',
                  'Cr_mation_demand_e',
                ],
              ],
              'editable' => TRUE,
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'Devenir_du_corps.Date_de_sortie_d_finitive',
              'dataType' => 'Date',
              'label' => E::ts('Date de sortie définitive'),
              'sortable' => TRUE,
              'editable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Devenir_du_corps.Date_op_rations_fun_raires',
              'dataType' => 'Date',
              'label' => E::ts('Date opérations funéraires'),
              'sortable' => TRUE,
              'editable' => TRUE,
              'alignment' => 'text-center',
            ],
            [
              'type' => 'field',
              'key' => 'champs_caches.toutes_utilisations:label',
              'dataType' => 'String',
              'label' => E::ts('Toutes utilisations'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'champs_caches.toutes_pieces:label',
              'dataType' => 'String',
              'label' => E::ts('Pieces prélevées'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Custom_Utilisation_du_corps_entity_id_01.Compl_ment',
              'dataType' => 'String',
              'label' => E::ts('Identifiant ou complément'),
              'sortable' => TRUE,
              'cssRules' => [],
              'editable' => TRUE,
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
          'headerCount' => TRUE,
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
