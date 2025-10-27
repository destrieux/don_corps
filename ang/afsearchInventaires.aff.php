<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('inventaires'),
  'description' => E::ts('Liste les pièces présentes dans un inventaire ou attachées à une localisation'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/inventaires',
];
