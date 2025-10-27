<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'search',
  'title' => E::ts('Anomalies adresse donneurs vivants'),
  'description' => E::ts('Liste de ssdodnneurs vivants dont l\'adresse est incomplete (ville ou CP) ou le CP erroné'),
  'icon' => 'fa-list-alt',
  'server_route' => 'civicrm/errCP',
];
