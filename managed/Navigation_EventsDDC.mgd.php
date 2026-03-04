<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Navigation_EventsDDC',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Cérémonies'),
        'name' => 'EventsDDC',
        'icon' => 'crm-i fa-users',
        'permission' => [
          'access CiviEvent',
        ],
        'has_separator' => NULL,
        'weight' => 46,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_Tableau_de_bord_Ceremonies',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Tableau de bord Cérémonies'),
        'name' => 'Tableau de bord Ceremonies',
        'url' => 'civicrm/event?reset=1',
        'permission' => [
          'access CiviCérémonie',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'EventsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_Nouvelle_c_r_monie',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Nouvelle cérémonie'),
        'name' => 'Nouvelle cérémonie',
        'url' => 'civicrm/event/add?reset=1&action=add',
        'permission' => [
          'access CiviCérémonie',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'EventsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_G_rer_les_c_r_monies',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Gérer les cérémonies'),
        'name' => 'Gérer les cérémonies',
        'url' => 'civicrm/event/manage?reset=1',
        'permission' => [
          'access CiviCérémonie',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'EventsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_Mod_les_de_C_r_monies',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Modèles de Cérémonies'),
        'name' => 'Modèles de Cérémonies',
        'url' => 'civicrm/admin/eventTemplate?reset=1',
        'permission' => [
          'access CiviCérémonie',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'EventsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_D_funts_sans_PAQPF',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Défunts sans PAQPF'),
        'name' => 'Défunts sans PAQPF',
        'url' => 'civicrm/pasPAQPF',
        'icon' => 'crm-i fa-square-poll-horizontal',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'EventsDDC',
        'has_separator' => 2,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_PAQPF',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Statut des PAQPF pour cérémonies'),
        'name' => 'PAQPF',
        'url' => 'civicrm/PAQPF',
        'icon' => 'crm-i fa-square-poll-horizontal',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'EventsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_Register_Event_Participant',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Inscrire un Participant à une Cérémonie'),
        'name' => 'Register Event Participant',
        'url' => 'civicrm/participant/add?reset=1&action=add&context=standalone',
        'permission' => [
          'access CiviCérémonie',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'EventsDDC',
        'has_separator' => 2,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_Import_Participants',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Import Participants'),
        'name' => 'Import Participants',
        'url' => 'civicrm/import/participant?reset=1',
        'permission' => [
          'access CiviCérémonie',
          'access CiviCRM',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'EventsDDC',
        'is_active' => FALSE,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_In_Memoriam',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('In Memoriam'),
        'name' => 'In Memoriam',
        'url' => 'civicrm/memoriam',
        'icon' => 'crm-i fa-people-group',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'EventsDDC',
        'has_separator' => 2,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
];
