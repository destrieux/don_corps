<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Proches_dont_donneur_DC_1_an_et_refuses',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Proches_dont_donneur_DC_1_an_et_refuses',
        'label' => E::ts('Proches dont donneur DC >1 an et refuses'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'display_name',
            'contact_sub_type:label',
            'Contact_RelationshipCache_Contact_01.near_relation:label',
            'Contact_RelationshipCache_Contact_01.far_relation:label',
            'Contact_RelationshipCache_Contact_01.contact_sub_type:label',
            'Contact_RelationshipCache_Contact_01.display_name',
            'Contact_RelationshipCache_Contact_01.deceased_date',
          ],
          'orderBy' => [],
          'where' => [
            [
              'OR',
              [
                [
                  'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'D_lai_d_pass_',
                ],
                [
                  'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'Maladie_infectieuse',
                ],
                [
                  'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'Obstacle_m_dico_l_gal',
                ],
                [
                  'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'D_c_s_l_tranger',
                ],
                [
                  'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'Transfert_vers_autre_centre',
                ],
                [
                  'Contact_RelationshipCache_Contact_01.Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
                  '=',
                  'Etat_de_conservation_du_corps',
                ],
              ],
            ],
            [
              'contact_sub_type:name',
              '=',
              'Proches',
            ],
            [
              'NOT',
              [
                [
                  'Contact_RelationshipCache_Contact_01.deceased_date',
                  '=',
                  'ending.year',
                ],
              ],
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Contact AS Contact_RelationshipCache_Contact_01',
              'LEFT',
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
                    '"est la personne de confiance de"',
                  ],
                  [
                    'Contact_RelationshipCache_Contact_01.far_relation:name',
                    '=',
                    '"Child of"',
                  ],
                  [
                    'Contact_RelationshipCache_Contact_01.far_relation:name',
                    '=',
                    '"Parent of"',
                  ],
                  [
                    'Contact_RelationshipCache_Contact_01.far_relation:name',
                    '=',
                    '"Spouse of"',
                  ],
                  [
                    'Contact_RelationshipCache_Contact_01.far_relation:name',
                    '=',
                    '"Sibling of"',
                  ],
                  [
                    'Contact_RelationshipCache_Contact_01.far_relation:name',
                    '=',
                    '"est la personne de confiance 2"',
                  ],
                  [
                    'Contact_RelationshipCache_Contact_01.far_relation:name',
                    '=',
                    '"est la PAQPF"',
                  ],
                ],
              ],
            ],
          ],
          'having' => [],
        ],
        'description' => E::ts('Proches dont les donneurs sont décédés il y a plus d\'un an et refusés : suppression'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Proches_dont_donneur_DC_1_an_et_refuses_Group_proches_refuses_pllus_un_an_41',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'proches_refuses_pllus_un_an_41',
        'title' => E::ts('proches refuses pllus un an'),
        'description' => E::ts('Proches dont les donneurs sont décédés il y a plus d\'un an et refusés : suppression'),
        'saved_search_id.name' => 'Proches_dont_donneur_DC_1_an_et_refuses',
        'group_type' => [],
        'frontend_title' => E::ts('proches refuses pllus un an'),
        'frontend_description' => E::ts('Proches dont les donneurs sont décédés il y a plus d\'un an et refusés : suppression'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Proches_dont_donneur_DC_1_an_et_refuses_SearchDisplay_Proches_dont_donneur_DC_1_an_et_refuses',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Proches_dont_donneur_DC_1_an_et_refuses',
        'label' => E::ts('Proches dont donneur DC >1 an et refuses'),
        'saved_search_id.name' => 'Proches_dont_donneur_DC_1_an_et_refuses',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Proches dont les donneurs sont décédés il y a plus d\'un an et refusés : suppression'),
          'sort' => [
            ['sort_name', 'ASC'],
          ],
          'limit' => 50,
          'pager' => [
            'show_count' => FALSE,
          ],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'display_name',
              'dataType' => 'String',
              'label' => E::ts('Proche'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.far_relation:label',
              'dataType' => 'String',
              'label' => E::ts('Relation depuis le contact'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.display_name',
              'dataType' => 'String',
              'label' => E::ts('Donneur'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_RelationshipCache_Contact_01.deceased_date',
              'dataType' => 'Date',
              'label' => E::ts('Date du décès du donneur'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => TRUE,
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
