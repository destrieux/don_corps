<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'ContactLayout_Donneur',
    'entity' => 'ContactLayout',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Donneur'),
        'contact_type' => 'Individual',
        'contact_sub_type' => ['Donateur'],
        'weight' => 2,
        'blocks' => [
          [
            [
              [
                'name' => 'profile.Type_de_contact_23',
                'title' => E::ts('Type de contact'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => FALSE,
              ],
              [
                'name' => 'profile.Dates_naissance_et_d_c_s_17',
                'title' => E::ts('Dates naissance et décès'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'core.Address',
                'title' => E::ts('Adresse'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'profile.Profil_sans_nom_20',
                'title' => E::ts('Vérification adresse'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => FALSE,
              ],
              [
                'name' => 'core.Email',
                'title' => E::ts('Courriel'),
              ],
              [
                'name' => 'core.Phone',
                'title' => E::ts('Téléphone'),
              ],
              [
                'name' => 'custom.Ant_c_dents_m_dicaux',
                'title' => E::ts('Antécédents médicaux'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'custom.Demandeur_information',
                'title' => E::ts('Demande d\'information'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'custom.Promesse_de_don',
                'title' => E::ts('Promesse de don'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'custom.Annulation',
                'title' => E::ts('Annulation'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
            ],
            [
              [
                'name' => 'custom.Prise_en_charge_au_d_c_s',
                'title' => E::ts('Prise en charge au décès'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'custom.Transfert_vers_autre_centre',
                'title' => E::ts('En cas de transfert vers un autre centre'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'profile.CESP_29',
                'title' => E::ts('CESP'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'profile.Op_rations_fun_raires_r_alis_es_30',
                'title' => E::ts('Opérations funéraires réalisées'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'profile.Restitution_28',
                'title' => E::ts('Restitution'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'custom.champs_caches',
                'title' => E::ts('champs caches'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => FALSE,
              ],
            ],
          ],
        ],
        'tabs' => [
          [
            'id' => 'summary',
            'is_active' => 1,
          ],
          [
            'id' => 'contact_documents',
            'is_active' => 1,
          ],
          [
            'id' => 'contribute',
            'is_active' => 1,
            'icon' => 'crm-i fa-money',
          ],
          [
            'id' => 'participant',
            'is_active' => 1,
            'icon' => 'crm-i fa-users',
          ],
          [
            'id' => 'mailing',
            'is_active' => 1,
          ],
          [
            'id' => 'activity',
            'is_active' => 1,
          ],
          [
            'id' => 'rel',
            'is_active' => 1,
          ],
          [
            'id' => 'note',
            'is_active' => 1,
          ],
          [
            'id' => 'tag',
            'is_active' => 1,
          ],
          [
            'id' => 'group',
            'is_active' => 1,
          ],
          [
            'id' => 'log',
            'is_active' => 1,
          ],
          [
            'id' => 'custom_4',
            'is_active' => 1,
            'icon' => 'crm-i fa-ambulance',
          ],
          [
            'id' => 'custom_13',
            'is_active' => 1,
            'icon' => 'crm-i fa-sign-language',
          ],
          [
            'id' => 'custom_11',
            'is_active' => 1,
            'icon' => 'crm-i fa-flask',
          ],
        ],
        'settings' => [
          'sub_type_operator' => 'OR',
        ],
      ],
    ],
  ],
];
