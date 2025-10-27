<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Donneurs annulés'),
  'description' => E::ts('Liste de donneurs annulés'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/donneurs_annules',
];
