<?php
namespace Civi\Api4;

/**
 * Entité API4 « Saisine » de l'extension don_corps.
 *
 * Cette classe expose l'entité Saisine dans l'API4 de CiviCRM.
 *
 * La définition de l'entité et de sa structure de données est fournie
 * séparément par :
 *
 *     schema/Saisine.entityType.php
 *
 * Ce fichier ne définit donc ni la table SQL, ni les champs, ni les index.
 * Il déclare simplement la classe API4 correspondant à l'entité Saisine.
 *
 * --------------------------------------------------------------------------
 * Héritage
 * --------------------------------------------------------------------------
 *
 * Saisine hérite de :
 *
 *     Civi\Api4\Generic\DAOEntity
 *
 * DAOEntity fournit le comportement générique permettant à API4 de manipuler
 * une entité reposant sur un DAO CiviCRM.
 *
 * Le DAO associé est :
 *
 *     CRM_DonCorps_DAO_Saisine
 *
 * qui représente la table :
 *
 *     civicrm_saisine
 *
 * --------------------------------------------------------------------------
 * API4
 * --------------------------------------------------------------------------
 *
 * La classe permet notamment d'utiliser les actions API4 standard :
 *
 *     Saisine.get
 *     Saisine.create
 *     Saisine.update
 *     Saisine.delete
 *
 * Par exemple :
 *
 *     cv api4 Saisine.get
 *
 * ou :
 *
 *     cv api4 Saisine.create +v label="Ma saisine"
 *
 * Les champs et leurs propriétés sont déterminés à partir de la définition
 * de l'entité dans :
 *
 *     schema/Saisine.entityType.php
 *
 * --------------------------------------------------------------------------
 * Relation avec les autres composants
 * --------------------------------------------------------------------------
 *
 * L'architecture de l'entité Saisine est organisée ainsi :
 *
 *     schema/Saisine.entityType.php
 *              │
 *              │ définition du schéma
 *              ▼
 *     civicrm_saisine
 *              │
 *              ▼
 *     CRM_DonCorps_DAO_Saisine
 *              │
 *              ▼
 *     Civi\Api4\Saisine
 *              │
 *              ▼
 *     API4 : Saisine.get/create/update/delete
 *
 * La couche BAO éventuelle est fournie par :
 *
 *     CRM_DonCorps_BAO_Saisine
 *
 * --------------------------------------------------------------------------
 * Évolution
 * --------------------------------------------------------------------------
 *
 * En règle générale, aucune logique métier ou logique de schéma ne doit être
 * ajoutée directement dans cette classe.
 *
 * La structure de l'entité doit être modifiée dans :
 *
 *     schema/Saisine.entityType.php
 *
 * Les modifications nécessaires pour les bases existantes doivent être
 * réalisées dans :
 *
 *     CRM/DonCorps/Upgrader.php
 *
 * Si un comportement API4 spécifique à Saisine devient nécessaire, il peut
 * alors être ajouté dans cette classe.
 *
 * --------------------------------------------------------------------------
 * Extension
 * --------------------------------------------------------------------------
 *
 * Cette classe est volontairement vide : le comportement standard fourni
 * par Generic\DAOEntity est suffisant pour l'entité Saisine.
 *
 * Elle constitue néanmoins le point d'extension API4 de l'entité et peut
 * accueillir ultérieurement un comportement spécifique si nécessaire.
 *
 * @package Civi\Api4
 */
class Saisine extends Generic\DAOEntity {

}
