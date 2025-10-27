<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'form',
  'title' => E::ts('Désactivation de l\'adresse postale'),
  'description' => E::ts('Active le champ Adresse Incorrecte'),
  'placement' => [
    'dashboard_dashlet',
  ],
  'icon' => 'fa-list-alt',
  'create_submission' => TRUE,
];
