<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  'type' => 'block',
  'title' => E::ts('Arrivée du corps block'),
  'entity_type' => 'Individual',
  'icon' => 'fa-list-alt',
  'join_entity' => 'Custom_Arriv_e_du_corps_new',
];
