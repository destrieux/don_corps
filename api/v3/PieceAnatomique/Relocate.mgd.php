<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
return [
  [
    'name' => 'Cron:PieceAnatomique.Relocate',
    'entity' => 'Job',
    'params' => [
      'version' => 3,
      'name' => 'Call PieceAnatomique.Relocate API',
      'description' => 'Call PieceAnatomique.Relocate API',
      'run_frequency' => 'Hourly',
      'api_entity' => 'PieceAnatomique',
      'api_action' => 'Relocate',
      'parameters' => '',
    ],
  ],
];
