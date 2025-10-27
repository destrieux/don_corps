<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Recherche PAQF pour cérémonies futures'),
  'description' => E::ts('Liste les PAQF des donneurs décédés dans les deux ans précédents, n\'ayant pas refusé que leurs proches soient prévenus, non inscrits à une cérémonie ou inscrites à une cérémonie à venir'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/PAQF',
];
