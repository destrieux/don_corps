<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'ParticipantStatusType_Registered',
    'entity' => 'ParticipantStatusType',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Registered',
        'label' => E::ts('Confirmé'),
        'class' => 'Positive',
        'is_reserved' => TRUE,
        'is_counted' => TRUE,
        'weight' => 2,
        'visibility_id:name' => 'public',
      ],
    ],
  ],
];
