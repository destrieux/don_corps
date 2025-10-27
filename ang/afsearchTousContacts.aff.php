<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Tous contacts'),
  'description' => E::ts('Affiche tous les individus (donneurs, demandeurs, proches, personnel) avec filtres'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/tous_contacts',
];
