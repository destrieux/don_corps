<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Statut des inscription des PAQPF aux cérémonies'),
  'description' => E::ts('Personnes ayant qualité pour pourvoir aux funérailles non invitées à une cérémonie ou invitées à une cérémonie future'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/PAQPF',
];
