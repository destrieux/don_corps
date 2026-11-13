<?php
use CRM_DonCorps_ExtensionUtil as E;

// Option values du groupe activity_type, exportées le 2026-11-13 14:47
return [
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'Case Client Removed',
      'label' => E::ts('Le client du dossier a été retiré du dossier'),
      'description' => E::ts('Le client du dossier a été retiré d\'un dossier'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'Document ajouté au dossier',
      'label' => E::ts('Document ajouté au dossier'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'Modification des coordonnées',
      'label' => E::ts('Modification des coordonnées'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'Retrait effets personnels',
      'label' => E::ts('Retrait effets personnels'),
      'description' => E::ts('<p>Retrait d\'effet personnel de valeur</p>'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'Retrait de pace maker',
      'label' => E::ts('Retrait de pace maker'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'Inventaire',
      'label' => E::ts('Inventaire'),
      'description' => E::ts('<p>Inventaire des pièces et corps d\'un lieu de conservation,<strong> à créer depuis la fiche du lieu de conservation</strong>.</p>

<p><strong>Les numéros de pièces (codes-barres) doivent être saisis dans le champ Détail</strong>.</p>

<p>A la validation un rapport remplace la liste des pièces dans le champ détail :&nbsp;</p>

<p>- Pièces bien localisées</p>

<p>- Pièces inscrites dans la base comme détruites, manquantes ou crématisées et présentes dans l\'inventaire : leur statut est mis à Non éliminé et elles sont relocalisées</p>

<p>- Pièces absentes de la base : à créer manuellement</p>

<p>- Pièces inscrites dans la base dans une autre pièce : relocalisées</p>'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'Suppression d\'une pièces',
      'label' => E::ts('Suppression d\'un lot de pièces'),
      'description' => E::ts('<p>Permet la suppression des pièces par lot.&nbsp;</p>

<p>Pour les corps : passer par le tableau de bord des corps présents et demander la crémation.</p>'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'Déplacer un lot de pièces/corps',
      'label' => E::ts('Déplacer un lot de pièces/corps'),
      'description' => E::ts('<p>Déplace vers ce local un lot de pièces ou de corps dont les codes-barres sont saisis dans le champ <em>Détails</em> de l\'activité.</p>'),
    ],
  ],
  [
    'entity' => 'OptionValue',
    'values' => [
      'option_group_id:name' => 'activity_type',
      'name' => 'event_message_sent',
      'label' => E::ts('Event Message sent'),
    ],
  ],
];
