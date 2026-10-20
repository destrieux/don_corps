<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'ContactLayout_Organisateur',
    'entity' => 'ContactLayout',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Organisateur'),
        'contact_type' => 'Individual',
        'contact_sub_type' => [
          'Intervenant_ext_rieur',
        ],
        'weight' => 6,
        'blocks' => [
          [
            [
              [
                'name' => 'profile.Organisateur',
                'title' => E::ts('Organisateur'),
                'collapsible' => TRUE,
              ],
            ],
            [
              [
                'name' => 'core.Address',
                'title' => E::ts('Adresse'),
              ],
              [
                'name' => 'core.Phone',
                'title' => E::ts('Téléphone'),
              ],
              [
                'name' => 'core.Email',
                'title' => E::ts('Courriel'),
              ],
            ],
          ],
        ],
        'settings' => [
          'sub_type_operator' => 'OR',
        ],
      ],
    ],
  ],
];
