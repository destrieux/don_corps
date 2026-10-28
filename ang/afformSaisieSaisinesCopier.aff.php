<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'form',
  'title' => E::ts('Saisine'),
  'description' => E::ts('Formulaire de saisie de saisines'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/Saisine',
  'redirect' => 'civicrm/rechSaisine',
  'create_submission' => TRUE,
];
