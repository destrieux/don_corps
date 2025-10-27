<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Donneurs décédés'),
  'description' => E::ts('Liste des donneurs décédés'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/donneurs_DCD',
];
