<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Donneurs_vivants_ano_ville_CP',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Donneurs_vivants_ano_ville_CP',
        'label' => E::ts('Donneurs vivants ano ville CP'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'Promesse_de_don.N_de_don',
            'display_name',
            'Contact_Address_contact_id_01.street_address',
            'Contact_Address_contact_id_01.postal_code',
            'Contact_Address_contact_id_01.city',
            'Compl_m_nt_tat_civil.Adresse_incorrecte:label',
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
                  'Contact_Address_contact_id_01.postal_code',
                  'IS EMPTY',
                ],
                [
                  'Contact_Address_contact_id_01.city',
                  'IS EMPTY',
                ],
                [
                  'NOT',
                  [
                    [
                      'Contact_Address_contact_id_01.postal_code',
                      'LIKE',
                      '_____',
                    ],
                  ],
                ],
                [
                  'address_primary.street_address',
                  'IS EMPTY',
                ],
              ],
            ],
            [
              'Compl_m_nt_tat_civil.Adresse_incorrecte',
              '=',
              FALSE,
            ],
            ['is_deceased', '=', FALSE],
            ['deceased_date', 'IS EMPTY'],
            [
              'Prise_en_charge_au_d_c_s.N_de_d_c_s',
              'IS EMPTY',
            ],
            [
              'groups:name',
              'NOT IN',
              ['Annulation_32'],
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Address AS Contact_Address_contact_id_01',
              'LEFT',
              [
                'id',
                '=',
                'Contact_Address_contact_id_01.contact_id',
              ],
            ],
          ],
          'having' => [],
        ],
        'description' => E::ts('Donneurs vivants, non annulés avec une adresse notée valide dont : le code postal ne fait pas 5 caractères,  ou dont la ville est vide, ou dont l\'adresse manque'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Donneurs_vivants_ano_ville_CP_SearchDisplay_Donneurs_vivants_ano_ville_CP',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Donneurs_vivants_ano_ville_CP',
        'label' => E::ts('Donneurs vivants ano ville CP'),
        'saved_search_id.name' => 'Donneurs_vivants_ano_ville_CP',
        'type' => 'table',
        'settings' => [
          'description' => E::ts('Donneurs vivants, non annulés avec une adresse notée valide dont : le code postal ne fait pas 5 caractères,  ou dont la ville est vide, ou dont l\'adresse manque'),
          'sort' => [
            [
              'Promesse_de_don.N_de_don',
              'ASC',
            ],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'Promesse_de_don.N_de_don',
              'dataType' => 'String',
              'label' => E::ts('N° de don'),
              'sortable' => TRUE,
            ],
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
              'key' => 'Contact_Address_contact_id_01.street_address',
              'dataType' => 'String',
              'label' => E::ts('Rue'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.postal_code',
              'dataType' => 'String',
              'label' => E::ts('Code postal'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.city',
              'dataType' => 'String',
              'label' => E::ts('Ville'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Compl_m_nt_tat_civil.Adresse_incorrecte:label',
              'dataType' => 'Boolean',
              'label' => E::ts('Adresse incorrecte'),
              'sortable' => TRUE,
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
