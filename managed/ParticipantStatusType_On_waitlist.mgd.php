<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'ParticipantStatusType_On_waitlist',
    'entity' => 'ParticipantStatusType',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'On waitlist',
        'label' => E::ts('Invité'),
        'class' => 'Waiting',
        'is_reserved' => TRUE,
        'weight' => 1,
        'visibility_id:name' => 'admin',
      ],
    ],
  ],
];
