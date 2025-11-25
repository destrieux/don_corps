<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Navigation_SupportDDC',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Support'),
        'name' => 'SupportDDC',
        'icon' => 'crm-i fa-life-ring',
        'has_separator' => NULL,
        'weight' => 112,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_Aide_civicrm_don_du_corps',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Aide civicrm don du corps'),
        'name' => 'Aide civicrm don du corps',
        'url' => 'https://destrieux.github.io/ddc-doc/',
        'icon' => 'crm-i fa-book-open-reader',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'SupportDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_User_Guide',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('User Guide'),
        'name' => 'User Guide',
        'url' => 'https://docs.civicrm.org/user/?src=iam',
        'permission_operator' => 'AND',
        'parent_id.name' => 'SupportDDC',
        'has_separator' => NULL,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_Proc_dure_administrative_don_du_corps',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Procédure administrative don du corps'),
        'name' => 'Procédure administrative don du corps',
        'url' => 'https://doncorps.fr/wiki/index.php/Proc%C3%A9dure_administrative_don_du_corps',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'SupportDDC',
        'has_separator' => 1,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_Get_Help',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Get Help'),
        'name' => 'Get Help',
        'url' => 'https://civicrm.org/help?src=iam',
        'permission_operator' => 'AND',
        'parent_id.name' => 'SupportDDC',
        'has_separator' => NULL,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_About_CiviCRM',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('About CiviCRM'),
        'name' => 'About CiviCRM',
        'url' => 'https://civicrm.org/about?src=iam',
        'permission_operator' => 'AND',
        'parent_id.name' => 'SupportDDC',
        'has_separator' => 1,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_Register_Your_Site',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Register Your Site'),
        'name' => 'Register Your Site',
        'url' => 'https://civicrm.org/register-your-site?src=iam&sid={sid}',
        'permission_operator' => 'AND',
        'parent_id.name' => 'SupportDDC',
        'has_separator' => NULL,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_Join_CiviCRM',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Join CiviCRM'),
        'name' => 'Join CiviCRM',
        'url' => 'https://civicrm.org/become-a-member?src=iam&sid={sid}',
        'permission_operator' => 'AND',
        'parent_id.name' => 'SupportDDC',
        'has_separator' => NULL,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_DeveloperDDC',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Développeur'),
        'name' => 'DeveloperDDC',
        'permission' => [
          'Administrer CiviCRM',
        ],
        'parent_id.name' => 'SupportDDC',
        'has_separator' => 1,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_DeveloperDDC_Navigation_API_Explorer',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Explorateur API v3'),
        'name' => 'API Explorer',
        'url' => 'civicrm/api3',
        'permission' => [
          'Administrer CiviCRM',
        ],
        'parent_id.name' => 'DeveloperDDC',
        'has_separator' => NULL,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_DeveloperDDC_Navigation_Api_Explorer_v4',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Explorateur API v4'),
        'name' => 'Api Explorer v4',
        'url' => 'civicrm/api4#/explorer',
        'permission' => [
          'Administrer CiviCRM',
        ],
        'parent_id.name' => 'DeveloperDDC',
        'has_separator' => NULL,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_SupportDDC_Navigation_DeveloperDDC_Navigation_Developer_Docs',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Developer Docs'),
        'name' => 'Developer Docs',
        'url' => 'https://civicrm.org/developer-documentation?src=iam',
        'permission' => [
          'Administrer CiviCRM',
        ],
        'parent_id.name' => 'DeveloperDDC',
        'has_separator' => NULL,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
];
