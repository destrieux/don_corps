<?php

// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
return [
  [
    'name' => 'Cron:Log.Purgelogscivirule',
    'entity' => 'Job',
    'params' => [
      'version' => 3,
      'name' => 'Call Log.Purgelogscivirule API',
      'description' => 'Purge Hebdomadaire des logs de CiviRules',
      'run_frequency' => 'Weekly',
      'api_entity' => 'Log',
      'api_action' => 'Purgelogscivirule',
      'parameters' => '',
    ],
  ],
];
