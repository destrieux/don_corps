<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'ContactLayout_Animal',
    'entity' => 'ContactLayout',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Animal'),
        'contact_type' => 'Individual',
        'contact_sub_type' => ['Animal'],
        'weight' => 8,
        'blocks' => [
          [
            [
              [
                'name' => 'custom.animal',
                'title' => E::ts('animal'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => FALSE,
              ],
            ],
            [
              [
                'name' => 'profile.D_mographie_animal',
                'title' => E::ts('Démographie animal'),
                'collapsible' => FALSE,
                'collapsed' => FALSE,
                'showTitle' => FALSE,
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
            'is_active' => 0,
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
            'is_active' => 0,
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
