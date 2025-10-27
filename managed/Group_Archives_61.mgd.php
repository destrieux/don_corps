<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Group_Archives_61',
    'entity' => 'Group',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Archives_61',
        'title' => E::ts('Archives'),
        'description' => E::ts('Donneurs ayant été purgés'),
        'group_type' => [],
        'frontend_title' => E::ts('Archives'),
      ],
      'match' => ['name'],
    ],
  ],
];
