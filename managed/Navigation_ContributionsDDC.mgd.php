<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Navigation_ContributionsDDC',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Don financiers'),
        'name' => 'ContributionsDDC',
        'icon' => 'crm-i fa-money-bill-1',
        'permission' => [
          'access CiviContribute',
        ],
        'has_separator' => NULL,
        'weight' => 33,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ContributionsDDC_Navigation_tableau_bord_dons',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Tableau de bord dons financiers'),
        'name' => 'tableau bord dons',
        'url' => 'civicrm/contribute?reset=1',
        'permission' => [
          'accéder à CiviContribute',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'ContributionsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ContributionsDDC_Navigation_New_Contribution',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Nouveau Don financier'),
        'name' => 'New Contribution',
        'url' => 'civicrm/contribute/add?reset=1&action=add&context=standalone',
        'permission' => [
          'accéder à CiviContribute',
          'edit contributions',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'ContributionsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ContributionsDDC_Navigation_Find_Contributions',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Chercher Dons financiers'),
        'name' => 'Find Contributions',
        'url' => 'civicrm/contribute/search?reset=1',
        'permission' => [
          'accéder à CiviContribute',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'ContributionsDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_ContributionsDDC_Navigation_Import_Contributions',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Importer des Dons financiers'),
        'name' => 'Import Contributions',
        'url' => 'civicrm/import/contribution?reset=1',
        'permission' => [
          'accéder à CiviContribute',
          'edit contributions',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'ContributionsDDC',
        'has_separator' => 1,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
];
