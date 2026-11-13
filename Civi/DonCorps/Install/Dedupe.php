<?php
namespace Civi\DonCorps\Install;

use CRM_DonCorps_ExtensionUtil as E;

/**
 * Règles de dédoublonnage de don_corps.
 *
 * Les fichiers .mgd référencent les champs par table et colonne, qui varient
 * d'une installation à l'autre pour les champs personnalisés. Les règles sont
 * donc créées ici, à partir du nom des champs.
 *
 * Usages :
 * - Supervised   : lancée lors d'une création depuis l'interface ;
 * - Unsupervised : lancée lors d'une création en ligne ou d'un import ;
 * - General      : disponible pour une recherche de doublons manuelle.
 */
class Dedupe {

  /**
   * Installe toutes les règles de dédoublonnage de don_corps.
   */
  public static function install(): void {
    // Les règles existantes passent en "General", les nôtres prennent ensuite
    // les usages Supervised / Unsupervised
    self::setAllGeneral(['Individual', 'Organization']); // self = alias pour la classe

    // Individus : n° de don, d'annulation ou de décès
    
    // $individualFields 
    // self::class, 'customField'], : appel de la méthode customfield 
    //                                avec un tableau contenant les champs utilisés par la règle
    //                                retourne les noms de table et de colonne de ces champs pour le système courant
    //
    //          par exemple : 
    //              ['civicrm_value_annulation_12', 'n_annulation_45'],
    //              ['civicrm_value_promesse_3',    'n_de_don_7'],
    //              ['civicrm_value_deces_5',       'n_de_d_c_s_19'],
    //
    $individualFields = array_map(
      [self::class, 'customField'],
      ['N_annulation', 'N_de_don', 'N_de_d_c_s']
    );

    // saveRuleGroup crée le groupe de règles, le vide et recrée les règles de ce groupe
    self::saveRuleGroup('num_don_annulation_deces', 'Individual', 'Supervised', 10,
      E::ts('num don ou annulation ou deces (supervisée)'), $individualFields);

    self::saveRuleGroup('numeros_don_annulation_dc_2', 'Individual', 'Unsupervised', 10,
      E::ts('num don ou annulation ou deces (automatique)'), $individualFields);

    // Organisations : nom et courriel
    $organizationFields = [
      ['civicrm_contact', 'organization_name'],
      ['civicrm_email', 'email'],
    ];
    self::saveRuleGroup('OrganizationSupervised', 'Organization', 'Supervised', 20,
      E::ts('Nom ou courriel (supervisée)'), $organizationFields);
    self::saveRuleGroup('OrganizationUnsupervised', 'Organization', 'Unsupervised', 20,
      E::ts('Nom ou courriel (automatique)'), $organizationFields);
  }

  /**
   * Passe en usage "General" tous les groupes de règles des types de contact donnés.
   *
   * @param string[] $contactTypes
   */
  public static function setAllGeneral(array $contactTypes): void {
    civicrm_api4('DedupeRuleGroup', 'update', [
      'values' => ['used' => 'General'],
      'where' => [['contact_type', 'IN', $contactTypes]],
      'checkPermissions' => FALSE,
    ]);
  }

  /**
   * Crée ou met à jour un groupe de règles, puis remplace ses règles.
   *
   * @param array $fields Liste de [table, colonne]
   * @return int id du groupe de règles
   */
  public static function saveRuleGroup(string $name, string $contactType, string $used,
                                       int $threshold, string $title, array $fields,
                                       int $weight = 10): int {
    $groupId = civicrm_api4('DedupeRuleGroup', 'save', [
      'records' => [[
        'name' => $name,
        'contact_type' => $contactType,
        'used' => $used,
        'threshold' => $threshold,
        'title' => $title,
      ]],
      'match' => ['name'],
      'checkPermissions' => FALSE,
    ])->first()['id'];

    // Remplace les règles existantes
    civicrm_api4('DedupeRule', 'delete', [
      'where' => [['dedupe_rule_group_id', '=', $groupId]],
      'checkPermissions' => FALSE,
    ]);

    $rules = [];
    foreach ($fields as [$table, $column]) {
      $rules[] = [
        'dedupe_rule_group_id' => $groupId,
        'rule_table' => $table,
        'rule_field' => $column,
        'rule_weight' => $weight,
      ];
    }
    civicrm_api4('DedupeRule', 'save', [
      'records' => $rules,
      'checkPermissions' => FALSE,
    ]);

    $list = implode(', ', array_map(fn($f) => "$f[0].$f[1]", $fields));
    \Civi::log()->info("don_corps : dédoublonnage $name (id $groupId, $used, seuil $threshold) : $list");

    return $groupId;
  }

  /**
   * Table et colonne d'un champ personnalisé, à partir de son nom.
   *
   * @return array [table, colonne]
   * @throws \CRM_Core_Exception si le champ n'existe pas
   */
  public static function customField(string $name): array {
    $field = civicrm_api4('CustomField', 'get', [
      'select' => ['column_name', 'custom_group_id.table_name'],
      'where' => [['name', '=', $name]],
      'checkPermissions' => FALSE,
    ])->first();

    if (!$field) {
      throw new \CRM_Core_Exception("don_corps : champ personnalisé '$name' introuvable pour le dédoublonnage");
    }
    return [$field['custom_group_id.table_name'], $field['column_name']];
  }

}