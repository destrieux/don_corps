<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'ContactLayout_Personnel',
    'entity' => 'ContactLayout',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Personnel'),
        'contact_type' => 'Individual',
        'contact_sub_type' => ['Personnel'],
        'weight' => 4,
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
                'name' => 'profile.Employeur',
                'title' => E::ts('Employeur'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => FALSE,
              ],
              [
                'name' => 'custom.infos_personnel',
                'title' => E::ts('Informations Personnel'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => FALSE,
              ],
            ],
            [
              [
                'name' => 'core.Address',
                'title' => E::ts('Adresse'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
              ],
              [
                'name' => 'core.Email',
                'title' => E::ts('Courriel'),
              ],
              [
                'name' => 'core.Phone',
                'title' => E::ts('Téléphone'),
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
            'id' => 'group',
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
            'id' => 'log',
            'is_active' => 1,
          ],
          [
            'id' => 'contact_documents',
            'is_active' => 1,
          ],
          [
            'id' => 'custom_4',
            'is_active' => 0,
            'icon' => 'crm-i fa-ambulance',
          ],
          [
            'id' => 'custom_13',
            'is_active' => 0,
            'icon' => 'crm-i fa-sign-language',
          ],
          [
            'id' => 'custom_11',
            'is_active' => 0,
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
