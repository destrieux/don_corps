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
        'weight' => 43,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_afsearchTableauParticipants',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Tableau Participants'),
        'name' => 'afsearchTableauParticipants',
        'url' => 'civicrm/participants',
        'icon' => 'crm-i fa-list-alt',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'EventsDDC',
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
        ],
        'permission_operator' => 'AND',
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
          'edit all events',
        ],
        'permission_operator' => 'AND',
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
          'edit all events',
        ],
        'permission_operator' => 'AND',
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
          'edit all events',
        ],
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
          'edit event participants',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'EventsDDC',
        'has_separator' => 2,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_EventsDDC_Navigation_Find_Participants',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Rechercher Participants aux Cérémonies'),
        'name' => 'Find Participants',
        'url' => 'civicrm/event/search?reset=1',
        'permission' => [
          'access CiviCérémonie',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'EventsDDC',
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
          'edit event participants',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'EventsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
];
