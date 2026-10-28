<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'form',
  'title' => E::ts('Activité au laboratoire'),
  'icon' => 'fa-chalkboard-user',
  'server_route' => 'civicrm/NouvelleActivite',
  'redirect' => 'civicrm/rechActivite',
  'create_submission' => TRUE,
];
