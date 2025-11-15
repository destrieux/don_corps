<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('In Memoriam'),
  'description' => E::ts('Liste les donneurs ayant souhaité l\'affichage de leur nom sur la stèle ou le site web'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/memoriam',
];
