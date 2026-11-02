<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'block',
  'title' => E::ts('Utilisation du corps block'),
  'entity_type' => 'Individual',
  'icon' => 'fa-list-alt',
  'join_entity' => 'Custom_Utilisation_du_corps',
];
