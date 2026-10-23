<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Navigation_Calendrier',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Calendrier'),
        'name' => 'Calendrier',
        'url' => 'civicrm/doncorps/ical-token',
        'icon' => 'crm-i fa-calendar-days',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'weight' => 107,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
];
