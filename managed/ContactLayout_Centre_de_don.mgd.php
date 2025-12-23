<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'ContactLayout_1',
    'entity' => 'ContactLayout',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Centre de don'),
        'contact_type' => 'Organization',
        'contact_sub_type' => ['CDC', 'Emprunteur'],
        'weight' => 7,
        'blocks' => [
          [
            [
              [
                'name' => 'custom.CDC_admin',
                'title' => E::ts('CDC admin'),
                'collapsible' => TRUE,
                'collapsed' => FALSE,
              ],
            ],
            [
              [
                'name' => 'core.Phone',
                'title' => E::ts('Téléphone'),
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
                'name' => 'core.Email',
                'title' => E::ts('Courriel'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => TRUE,
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
            'is_active' => 0,
            'icon' => 'crm-i fa-money',
          ],
          [
            'id' => 'participant',
            'is_active' => 0,
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
            'is_active' => 0,
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
