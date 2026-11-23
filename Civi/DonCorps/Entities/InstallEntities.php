<?php
namespace Civi\DonCorps\Entities;

/**
 * Création / mise à jour des entités CiviCRM de l'extension don_corps
 * lors de l'installation ou d'un upgrade.
 *
 * Chaque définition a la forme :
 *   ['entity' => 'OptionValue', 'values' => [...]]
 *
 * Une entité existante (retrouvée grâce à MATCH_KEYS) est mise à jour,
 * sinon elle est créée.
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
   * Installe les entités définies dans un fichier de Civi/DonCorps/Entities/.
   *
   * @param string $file Nom du fichier (ex. 'contact_types.php')
   * @return array Identifiants, comme install()
   * @throws \CRM_Core_Exception si le fichier est introuvable ou ne retourne pas un tableau
   */
  public static function installFile(string $file): array {
    $path = \CRM_DonCorps_ExtensionUtil::path('Civi/DonCorps/Entities/' . $file);

    if (!is_file($path)) {
      self::fail("fichier introuvable ($path), entités non créées");
    }

    $defs = require $path;
    if (!is_array($defs)) {
      self::fail("le fichier $path ne retourne pas un tableau, entités non créées");
    }

    \Civi::log()->info("don_corps : installation de $file");
    return self::install($defs);
  }

  /**
   * Installe une ou plusieurs entités.
   *
   * @param array $defs Une définition ['entity' => ..., 'values' => [...]]
   *                    ou une liste de définitions.
   * @return array Identifiants (id, ou name pour Afform), dans l'ordre des
   *               définitions ; NULL pour une entité inactive ignorée.
   * @throws \CRM_Core_Exception
   */
  public static function install(array $defs): array {
    // Accepte aussi une définition seule
    if (isset($defs['entity'])) {
      $defs = [$defs];
    }

    $ids = [];
    foreach ($defs as $i => $def) {
      if (!isset($def['entity'], $def['values']) || !is_array($def['values'])) {
        self::fail("définition n° $i invalide : 'entity' et 'values' sont attendus");
      }
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
    $idField = ($entity === 'Afform') ? 'name' : 'id';   // Afform n'a pas d'id

    // Critères de recherche de l'entité existante
    $where = [];
    foreach ($keys as $key) {
      if (!array_key_exists($key, $values)) {
        self::fail("$entity : champ d'identification '$key' manquant");
      }
      $where[] = [$key, '=', $values[$key]];
    }
    $descr = $entity . ' ' . implode(' / ', array_map(fn($k) => json_encode($values[$k], JSON_UNESCAPED_UNICODE), $keys));

    $existing = civicrm_api4($entity, 'get', [
      'select' => [$idField],
      'where' => $where,
      'checkPermissions' => FALSE,
    ])->first();

    // Existe : mise à jour
    if ($existing) {
      $id = $existing[$idField];
      civicrm_api4($entity, 'update', [
        'values' => $values,
        'where' => [[$idField, '=', $id]],
        'checkPermissions' => FALSE,
      ]);
      \Civi::log()->info("don_corps : $descr existe - mise à jour ($id)");
      return $id;
    }

    // N'existe pas et inactive : ignorée
    if (isset($values['is_active']) && !$values['is_active']) {
      \Civi::log()->info("don_corps : $descr n'existe pas mais inactive - ignorée");
      return NULL;
    }

    // N'existe pas : création
    $id = civicrm_api4($entity, 'create', [
      'values' => $values,
      'checkPermissions' => FALSE,
    ])->first()[$idField];
    \Civi::log()->info("don_corps : $descr n'existe pas - création ($id)");
    return $id;
  }

  /**
   * Écrit l'erreur dans le journal et arrête l'exécution.
   *
   * @throws \CRM_Core_Exception
   */
  private static function fail(string $message): void {
    \Civi::log()->error("don_corps : $message");
    throw new \CRM_Core_Exception("don_corps : $message");
  }

}