<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Donneurs vivants'),
  'description' => E::ts('Liste les donneurs vivants'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/donneurs_vivants',
];
