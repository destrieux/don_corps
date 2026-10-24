<?php
/**
 * Définition de l'entité API4 « Saisine ».
 *
 * Ce fichier constitue la définition canonique du schéma de l'entité
 * « Saisine » de l'extension don_corps.
 *
 * Il est utilisé par civimix-schema pour décrire et, lors de l'installation
 * de l'extension, créer la table :
 *
 *     civicrm_saisine
 *
 * L'entité est accessible dans CiviCRM via l'API4 :
 *
 *     Saisine.get
 *     Saisine.create
 *     Saisine.update
 *     Saisine.delete
 *
 * --------------------------------------------------------------------------
 * IMPORTANT : rôle de ce fichier
 * --------------------------------------------------------------------------
 *
 * Ce fichier décrit l'état courant attendu de la table civicrm_saisine.
 *
 * Lorsqu'une évolution de la structure de Saisine est nécessaire (ajout d'un
 * champ, modification d'un index, etc.), les modifications doivent être appliquées à deux endroits
 *  - dans ce fichier qui doit décrire la nouvelle structure finale attendue de l'entité.
 *  - dans CRM/DonsCorps/Upgrader.php, via un upgrade_XXXX(), via    
 *      public function upgrade_2034(): bool {
 *         ...
 *       }
 * --------------------------------------------------------------------------
 */

use CRM_DonCorps_ExtensionUtil as E;

return [
  'name' => 'Saisine',
  'table' => 'civicrm_saisine',
  'class' => 'CRM_DonCorps_DAO_Saisine',

  'getInfo' => fn() => [
    'title' => E::ts('Saisine'),
    'title_plural' => E::ts('Saisines'),
    'description' => E::ts('Saisine'),
    'label_field' => 'label',
    'log' => TRUE,
  ],

  'getFields' => fn() => [

    'id' => [
      'title' => E::ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'description' => E::ts('ID de saisine unique'),
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],

    'label' => [
  'title' => E::ts('N° de saisine'),
  'sql_type' => 'varchar(50)',
  'input_type' => 'Text',
  'required' => TRUE,
  'description' => E::ts('N° de saisine'),
  'usage' => [
    'create',
    'update',
  ],
],

'description' => [
  'title' => E::ts('Intitulé'),
  'sql_type' => 'varchar(50)',
  'input_type' => 'Text',
  'description' => E::ts('Intitulé de la saisine'),
  'usage' => [
    'create',
    'update',
  ],
],

'is_active' => [
  'title' => E::ts('Actif'),
  'sql_type' => 'boolean',
  'input_type' => 'CheckBox',
  'default' => TRUE,
  'description' => E::ts('La saisine est-elle active ?'),
  'usage' => [
    'create',
    'update',
  ],
],

    'created_date' => [
      'title' => E::ts('Date de création'),
      'sql_type' => 'datetime',
      'readonly' => TRUE,
    ],

    'modified_date' => [
      'title' => E::ts('Date de modification'),
      'sql_type' => 'datetime',
      'readonly' => TRUE,
    ],
  ],

  'getIndices' => fn() => [
    'label' => [
      'fields' => [
        'label' => TRUE,
      ],
      'unique' => FALSE,
    ],
  ],

  'getPaths' => fn() => [],
];