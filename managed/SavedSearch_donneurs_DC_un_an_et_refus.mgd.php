<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_donneurs_DC_un_an_et_refus',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'donneurs_DC_un_an_et_refus',
        'label' => E::ts('donneurs DC > un an et refus'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'display_name',
            'deceased_date',
            'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:label',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              '=',
              'Donateur',
            ],
            [
              'OR',
              [
                [
                  'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'D_lai_d_pass_',
                ],
                [
                  'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'Maladie_infectieuse',
                ],
                [
                  'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'Obstacle_m_dico_l_gal',
                ],
                [
                  'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'D_c_s_l_tranger',
                ],
                [
                  'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'Transfert_vers_autre_centre',
                ],
                [
                  'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'Etat_de_conservation_du_corps',
                ],
              ],
            ],
            [
              'NOT',
              [
                ['deceased_date', '=', 'ending.year'],
              ],
            ],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'description' => E::ts('Donneurs décédés depuis plus d\'un an et refusés en raison du motif de décès ou de l\'état de conservation : suppression'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_donneurs_DC_un_an_et_refus_Group_dons_refuses_plus_un_an_39',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'dons_refuses_plus_un_an_39',
        'title' => E::ts('dons refuses plus un an'),
        'description' => E::ts('Donneurs décédés depuis plus d\'un an et refusés en raison du motif de décès ou de l\'état de conservation : suppression'),
        'saved_search_id.name' => 'donneurs_DC_un_an_et_refus',
        'group_type' => [],
        'frontend_title' => E::ts('dons refuses plus un an'),
        'frontend_description' => E::ts('Donneurs décédés depuis plus d\'un an et refusés en raison du motif de décès ou de l\'état de conservation : suppression'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_donneurs_DC_un_an_et_refus_SearchDisplay_donneurs_DC_un_an_et_refus',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'donneurs_DC_un_an_et_refus',
        'label' => E::ts('donneurs DC > un an et refus'),
        'saved_search_id.name' => 'donneurs_DC_un_an_et_refus',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Donneurs décédés depuis plus d\'un an et refusés en raison du motif de décès ou de l\'état de conservation'),
          'sort' => [
            ['deceased_date', 'DESC'],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'display_name',
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
              'key' => 'deceased_date',
              'dataType' => 'Date',
              'label' => E::ts('Date du décès'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:label',
              'dataType' => 'Integer',
              'label' => E::ts('Motif de refus du corps'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
          'button' => NULL,
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
