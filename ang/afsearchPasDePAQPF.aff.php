<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Défunts sans PAQPF'),
  'description' => E::ts('Défunts sans PAQPF mais avec referents'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/pasPAQPF',
];
