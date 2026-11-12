<?php
use CRM_DonCorps_ExtensionUtil as E;

## Liste les entités non gérées par mgd files, à créer à l'installation

return [
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'relative_date_filters',
      'label' => E::ts("Derniers 5 ans incluant aujourd'hui"),
      'value' => 'ending_5.year',
      'name' => "Derniers 5 ans incluant aujourd'hui",
      'weight' => 65,
      'is_active' => TRUE,
    ],
  ],
  // autres définitions…
];