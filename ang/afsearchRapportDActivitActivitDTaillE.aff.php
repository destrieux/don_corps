<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Rapport d\'activité : activité détaillée'),
  'description' => E::ts('Liste les activités de l\'année'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/BilanActiviteDetaillee',
];
