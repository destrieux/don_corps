<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'ParticipantStatusType_Cancelled',
    'entity' => 'ParticipantStatusType',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Cancelled',
        'label' => E::ts('Annulé - Refus'),
        'class' => 'Negative',
        'is_reserved' => TRUE,
        'weight' => 3,
        'visibility_id:name' => 'admin',
      ],
    ],
  ],
];
