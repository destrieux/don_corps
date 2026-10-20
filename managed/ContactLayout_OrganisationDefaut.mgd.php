<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'ContactLayout_OrganisationDefaut',
    'entity' => 'ContactLayout',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('OrganisationDefaut'),
        'contact_type' => 'Organization',
        'weight' => 10,
        'blocks' => [
          [
            [
              [
                'name' => 'core.Address',
                'title' => E::ts('Adresse'),
              ],
            ],
            [],
          ],
        ],
        'tabs' => [
          [
            'id' => 'summary',
            'is_active' => TRUE,
          ],
          [
            'id' => 'contact_documents',
            'is_active' => TRUE,
          ],
          [
            'id' => 'contribute',
            'is_active' => FALSE,
          ],
          [
            'id' => 'participant',
            'is_active' => TRUE,
          ],
          [
            'id' => 'mailing',
            'is_active' => FALSE,
          ],
          [
            'id' => 'activity',
            'is_active' => TRUE,
          ],
          [
            'id' => 'rel',
            'is_active' => TRUE,
          ],
          [
            'id' => 'group',
            'is_active' => TRUE,
          ],
          [
            'id' => 'note',
            'is_active' => TRUE,
          ],
          [
            'id' => 'tag',
            'is_active' => FALSE,
          ],
          [
            'id' => 'custom_4',
            'is_active' => FALSE,
          ],
          [
            'id' => 'custom_11',
            'is_active' => FALSE,
          ],
          [
            'id' => 'custom_13',
            'is_active' => FALSE,
          ],
          [
            'id' => 'log',
            'is_active' => TRUE,
          ],
        ],
        'settings' => [
          'sub_type_operator' => 'OR',
        ],
      ],
    ],
  ],
];
