<?php
use CRM_DonCorps_ExtensionUtil as E;

// Modèle d'événement : cérémonie d'hommage
// (les statuts et rôles des participants et le type d'événement doivent être créés avant)
return [
  [
    'entity' => 'Event',
    'values' => [
      'is_template' => TRUE,
      'template_title' => 'Modèle de cérémonie test',
      'title' => 'Cérémonie test',
      'summary' => E::ts("Cérémonie d'hommage aux donneurs et à leurs familles"),
      'event_type_id:name' => 'Cérémonie Hommage',
      'default_role_id:name' => 'Attendee',
      'event_full_text' => E::ts('Cet événement est actuellement complet.'),
      'is_public' => FALSE,
      'is_show_location' => FALSE,
      'is_share' => FALSE,
      'is_active' => TRUE,
    ],
  ],
];