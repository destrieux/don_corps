<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Recherche activité au laboratoire'),
  'description' => E::ts('Formulaire de recherche d\'une activité au labo'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/rechActivite',
];
