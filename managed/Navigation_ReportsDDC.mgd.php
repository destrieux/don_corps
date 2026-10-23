<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Navigation_ReportsDDC',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Rapports'),
        'name' => 'ReportsDDC',
        'icon' => 'crm-i fa-bar-chart',
        'permission' => [
          'access CiviReport',
        ],
        'has_separator' => NULL,
        'weight' => 105,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ReportsDDC_Navigation_Rapport_activit_',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Rapport activité'),
        'name' => 'Rapport activité',
        'url' => 'civicrm/activite',
        'icon' => 'crm-i fa-square-poll-horizontal',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'ReportsDDC',
        'has_separator' => 1,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ReportsDDC_Navigation_Contact_Reports',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Rapports sur les Contacts'),
        'name' => 'Contact Reports',
        'url' => 'civicrm/report/list?compid=99&reset=1',
        'permission' => [
          'Administrer CiviCRM',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'ReportsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ReportsDDC_Navigation_Contribution_Reports',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Rapport sur les Dons financiers'),
        'name' => 'Contribution Reports',
        'url' => 'civicrm/report/list?compid=2&reset=1',
        'permission' => [
          'accéder à CiviContribute',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'ReportsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ReportsDDC_Navigation_Event_Reports',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Rapports sur les Cérémonies'),
        'name' => 'Event Reports',
        'url' => 'civicrm/report/list?compid=1&reset=1',
        'permission' => [
          'access CiviCérémonie',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'ReportsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ReportsDDC_Navigation_Mailing_Reports',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Rapports sur les Mailings'),
        'name' => 'Mailing Reports',
        'url' => 'civicrm/report/list?compid=4&reset=1',
        'permission' => [
          'accéder à CiviMail',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'ReportsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ReportsDDC_Navigation_All_Reports',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Tous les rapports'),
        'name' => 'All Reports',
        'url' => 'civicrm/report/list?reset=1',
        'permission' => [
          'access CiviRapport',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'ReportsDDC',
        'has_separator' => 1,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ReportsDDC_Navigation_My_Reports',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Mes Rapports'),
        'name' => 'My Reports',
        'url' => 'civicrm/report/list?myreports=1&reset=1',
        'permission' => [
          'access CiviRapport',
          'access CiviCRM',
        ],
        'permission_operator' => 'OR',
        'parent_id.name' => 'ReportsDDC',
        'has_separator' => 1,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ReportsDDC_Navigation_afsearchAllImports',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('All imports'),
        'name' => 'afsearchAllImports',
        'url' => 'civicrm/imports/all-imports',
        'icon' => 'crm-i fa-list-alt',
        'permission' => [
          'administrer les files d\'attente',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'ReportsDDC',
        'is_active' => FALSE,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ReportsDDC_Navigation_afsearchMyImports',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('My Imports'),
        'name' => 'afsearchMyImports',
        'url' => 'civicrm/imports/my-listing',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'ReportsDDC',
        'is_active' => FALSE,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ReportsDDC_Navigation_afsearchTemplates',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Import Modèles'),
        'name' => 'afsearchTemplates',
        'url' => 'civicrm/imports/templates',
        'icon' => 'crm-i fa-list-alt',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'ReportsDDC',
        'is_active' => FALSE,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
];
