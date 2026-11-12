<?php
namespace Civi\DonCorps\Entities;

/**
 * Création / mise à jour des entités CiviCRM de l'extension don_corps
 * lors de l'installation ou d'un upgrade.
 */
class InstallEntities {

  /**
   * Champs qui identifient une entité existante (défaut : name).
   */
  private const MATCH_KEYS = [
    'CiviRulesRuleAction'    => ['rule_id.name', 'action_id.name'],
    'CiviRulesRuleCondition' => ['rule_id.name', 'condition_id.name'],
    'MessageTemplate'        => ['msg_title'],
    'Event'                  => ['is_template', 'template_title'],
    'EventMessageRule'       => ['event_id', 'template_id'],
    'OptionValue'            => ['option_group_id:name', 'name'],
    'UFJoin'                 => ['uf_group_id:name', 'module'],
    'UFField'                => ['uf_group_id.name', 'field_name'],
    'Navigation'             => ['parent_id:name', 'name'],
    'RelationshipType'       => ['name_a_b'],
  ];

  /**
   * Installe une ou plusieurs entités.
   *
   * @param array $defs Une définition ['entity' => ..., 'values' => [...]]
   *                    ou une liste de définitions.
   * @return array Identifiants (id, ou name pour Afform), dans l'ordre des
   *               définitions ; NULL pour une entité inactive ignorée.
   */
  
  public static function install(array $defs): array {
    // Accepte aussi une définition seule
    if (isset($defs['entity'])) {
      $defs = [$defs];
    }

    $ids = [];
    foreach ($defs as $def) {
      $ids[] = self::installOne($def['entity'], $def['values']);
    }
    return $ids;
  }
  
  /**
   * Installe les entités définies dans un fichier PHP qui retourne un tableau de définitions.
   *
   * @param string $file Chemin relatif à la racine de l'extension (ex. 'entities/contact_types.php')
   * @return array Identifiants, comme install()
   * @throws \CRM_Core_Exception
   */
  public static function installFile(string $fileDefs): array {

    $fileDefs = \CRM_DonCorps_ExtensionUtil::path('Civi/DonCorps/Entities/').$fileDefs;
      if (!file_exists($fileDefs)) {
      \Civi::log()->error("don_corps : fichier introuvable ($fileDefs), entité non crée");
      return FALSE;
      }

    $defs = require $fileDefs;
      if (!is_array($defs)) {
      \Civi::log()->error('don_corps : fichier illisible ($fileDefs),entité non crée');
      return FALSE;
      }

    // si définition seule -> transformation en tableau
    if (isset($defs['entity'])) {
      $defs = [$defs];
    }

    $ids = [];
    foreach ($defs as $def) {
        $ids[] = self::installOne($def['entity'], $def['values']);
    }
    return $ids;
  }

  /**
   * Crée ou met à jour une entité.
   *
   * @return mixed id (ou name pour Afform), NULL si ignorée
   * @throws \CRM_Core_Exception
   */
  private static function installOne(string $entity, array $values) {
    $keys    = self::MATCH_KEYS[$entity] ?? ['name'];
        // Prends la liste de champs prévue pour cette entité (par exemple ['option_group_id:name', 'name'] pour OptionValue). 
        // Si l’entité n’est pas dans la table, ?? utilise ['name'] par défaut.

    $idField = ($entity === 'Afform') ? 'name' : 'id';   // Afform n'a pas d'id
        // Si entity vaut exactement 'Afform' (mêmem cop,ntenu meme type)
        // alors $idField = name
        // sinon $idField = id

    $where = [];
    foreach ($keys as $key) {
      if (!array_key_exists($key, $values)) { // Si le tableau $value ne contient pas la clé $key
        throw new \CRM_Core_Exception("don_corps : $entity, champ d'identification '$key' manquant");
      }
      $where[] = [$key, '=', $values[$key]];
    }
    $descr = $entity . ' ' . implode(' / ', array_map(fn($k) => $values[$k], $keys));

    $existing = civicrm_api4($entity, 'get', [
      'select' => [$idField],
      'where' => $where,
      'checkPermissions' => FALSE,
    ])->first();

    if ($existing) {
      $id = $existing[$idField];
      civicrm_api4($entity, 'update', [
        'values' => $values,
        'where' => [[$idField, '=', $id]],
        'checkPermissions' => FALSE,
      ]);
      $msg = 'Entity '.$descr." existe - update ($id)";
    }
    elseif (isset($values['is_active']) && !$values['is_active']) {
      \Civi::log()->info("don_corps : $descr n'existe pas mais inactive - ignorée");
      return NULL;
    }
    else {
      $id = civicrm_api4($entity, 'create', [
        'values' => $values,
        'checkPermissions' => FALSE,
      ])->first()[$idField];
      $msg = "$descr n'existe pas - création ($id)";
    }

    \Civi::log()->info("don_corps : $msg");
    return $id;
  }

}