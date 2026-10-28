<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'block',
  'title' => E::ts('Données de la saisine'),
  'entity_type' => 'Event',
  'requires' => ['saisineDisplay'],
];
