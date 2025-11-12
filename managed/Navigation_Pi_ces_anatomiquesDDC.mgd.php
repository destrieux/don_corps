<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'Navigation_Pi_ces_anatomiquesDDC',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Corps et pièces anatomiques'),
        'name' => 'Pièces anatomiquesDDC',
        'icon' => 'crm-i fa-heart-o',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'weight' => 56,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_Pi_ces_anatomiquesDDC_Navigation_afsearchInventaires',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('inventaires'),
        'name' => 'afsearchInventaires',
        'url' => 'civicrm/inventaires',
        'icon' => 'crm-i fa-list-alt',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'Pièces anatomiquesDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_Pi_ces_anatomiquesDDC_Navigation_New_Emprunteur',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Créer Localisation pièces'),
        'name' => 'New Emprunteur',
        'url' => 'civicrm/profile/create/?gid=23&reset=1',
        'icon' => 'crm-i fa-rectangle-list',
        'permission' => [
          'Ajouter des contacts',
        ],
        'permission_operator' => 'AND',
        'parent_id.name' => 'Pièces anatomiquesDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_Pi_ces_anatomiquesDDC_Navigation_LIste_emprunteurs',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Lieux de conservation'),
        'name' => 'LIste emprunteurs',
        'url' => 'civicrm/stockage',
        'icon' => 'crm-i fa-rectangle-list',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'Pièces anatomiquesDDC',
        'has_separator' => 1,
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_Pi_ces_anatomiquesDDC_Navigation_afsearchRechercheCodeBarres',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Recherche Code barres'),
        'name' => 'afsearchRechercheCodeBarres',
        'url' => 'civicrm/codesbarres',
        'icon' => 'crm-i fa-list-alt',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'Pièces anatomiquesDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
  [
    'name' => 'Navigation_Pi_ces_anatomiquesDDC_Navigation_afsearchTableauDeBordDesCorpsPrSents1',
    'entity' => 'Navigation',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'label' => E::ts('Tableau de bord des corps présents'),
        'name' => 'afsearchTableauDeBordDesCorpsPrSents1',
        'url' => 'civicrm/tbleauBordCorps2',
        'icon' => 'crm-i fa-list-alt',
        'permission' => ['access CiviCRM'],
        'permission_operator' => 'AND',
        'parent_id.name' => 'Pièces anatomiquesDDC',
      ],
      'match' => ['name', 'domain_id'],
    ],
  ],
];
