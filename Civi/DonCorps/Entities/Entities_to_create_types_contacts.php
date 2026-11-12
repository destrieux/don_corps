<?php
use CRM_DonCorps_ExtensionUtil as E;

// Création des types de contact, à créer à l'installation
return [
  [ // Donneur
    'entity' => 'ContactType',
    'values' => [
      'name' => 'Donateur',
      'label' => E::ts('Donneur'),
      'description' => E::ts('Donneur inscrit'),
      'parent_id.name' => 'Individual',
      'icon' => 'fa-user',
      'is_active' => TRUE,
    ],
  ],
  [ // Proches
    'entity' => 'ContactType',
    'values' => [
      'name' => 'Proches',
      'label' => E::ts('Proches'),
      'description' => E::ts('Proche lié à un donneur'),
      'parent_id.name' => 'Individual',
      'icon' => 'fa-user',
      'is_active' => TRUE,
    ],
  ],
  [ // Demandeur d'information
    'entity' => 'ContactType',
    'values' => [
      'name' => 'Demandeur_d_information',
      'label' => E::ts('Demandeur information'),
      'description' => E::ts('Personne demandant un dossier, non encore inscrite'),
      'parent_id.name' => 'Individual',
      'icon' => 'fa-user',
      'is_active' => TRUE,
    ],
  ],
  [ // Personnel
    'entity' => 'ContactType',
    'values' => [
      'name' => 'Personnel',
      'label' => E::ts('Personnel'),
      'description' => E::ts("Employés du centre d'accueil des corps"),
      'parent_id.name' => 'Individual',
      'is_active' => TRUE,
    ],
  ],
  [ // Animal
    'entity' => 'ContactType',
    'values' => [
      'name' => 'Animal',
      'label' => E::ts('Animal'),
      'description' => E::ts('Spécimens non humains'),
      'parent_id.name' => 'Individual',
      'icon' => 'fa-cow',
      'is_active' => TRUE,
    ],
  ],
  [ // Intervenant extérieur
    'entity' => 'ContactType',
    'values' => [
      'name' => 'Intervenant_ext_rieur',
      'label' => E::ts('Intervenant extérieur'),
      'description' => E::ts("Organisateur d'une activité utilisant des corps"),
      'parent_id.name' => 'Individual',
      'icon' => 'fa-person-walking-dashed-line-arrow-right',
      'is_active' => TRUE,
    ],
  ],
  [ // Pompes funèbres
    'entity' => 'ContactType',
    'values' => [
      'name' => 'Pompes',
      'label' => E::ts('Pompes'),
      'description' => E::ts('Entreprise de Pompes Funèbres'),
      'parent_id.name' => 'Organization',
      'is_active' => TRUE,
    ],
  ],
  [ // Centre de don du corps
    'entity' => 'ContactType',
    'values' => [
      'name' => 'CDC',
      'label' => E::ts('CDC'),
      'description' => E::ts('Centre de don du corps'),
      'parent_id.name' => 'Organization',
      'is_active' => TRUE,
    ],
  ],
  [ // Localisation des pièces
    'entity' => 'ContactType',
    'values' => [
      'name' => 'Emprunteur',
      'label' => E::ts('Localisation pièces'),
      'description' => E::ts("Localisation des pièces dans le CDC ou à l'extérieur"),
      'parent_id.name' => 'Organization',
      'is_active' => TRUE,
    ],
  ],
  [ // Mairies
    'entity' => 'ContactType',
    'values' => [
      'name' => 'Mairies',
      'label' => E::ts('Mairies'),
      'description' => E::ts('Mairies'),
      'parent_id.name' => 'Organization',
      'is_active' => TRUE,
    ],
  ],
];