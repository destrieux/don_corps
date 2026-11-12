<?php
namespace Civi\DonCorps\Install;

use CRM_DonCorps_ExtensionUtil as E;
use Civi\DonCorps\Entities\InstallEntities;

/**
 * Installe un profil (UFGroup + UFFields) décrit dans un fichier
 * managed/UFGroup_<profil>.mgd.php.disable, l'import natif fonctionnant mal.
 */
class InstallProfile {

  /**
   * @param string      $profile   Suffixe du fichier : managed/UFGroup_<profile>.mgd.php.disable
   * @param string|null $navParent Nom du menu parent, si le profil est appelé par un menu
   * @param string|null $nav       Nom de l'entrée de menu qui appelle le profil
   * @return int id de l'UFGroup
   * @throws \CRM_Core_Exception
   */
  public static function install(string $profile, ?string $navParent = NULL, ?string $nav = NULL): int {
    // Lecture du fichier : l'UFGroup d'un côté, ses UFFields de l'autre
    $entities = require E::path("managed/UFGroup_{$profile}.mgd.php.disable");

    $group = NULL;
    $fields = [];
    foreach ($entities as $entity) {
      if ($entity['entity'] === 'UFGroup') {
        $group = $entity['params']['values'];
      }
      elseif ($entity['entity'] === 'UFField') {
        $fields[] = $entity['params']['values'];
      }
    }
    if (!$group) {
      throw new \CRM_Core_Exception("don_corps : pas d'UFGroup dans UFGroup_{$profile}.mgd.php.disable");
    }

    $name = $group['name'];
    \Civi::log()->info("don_corps : installation du profil $name");

    // 1. UFGroup, avec les URL réécrites pour ce site
    if (isset($group['post_url'])) {
      //$group['post_url'] = admin_url('admin.php?page=CiviCRM&q=civicrm/contact/view&reset=1&cid=') . '{contact.id}';
      // Version indépendante du CMS (à tester en ligne de commande avant usage) :
      $group['post_url'] = \CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid=', TRUE, NULL, FALSE, FALSE, TRUE) . '{contact.id}';
    }
    if (isset($group['cancel_url'])) {
      //$group['cancel_url'] = admin_url('admin.php?page=CiviCRM');
      // Version indépendante du CMS (à tester en ligne de commande avant usage) :
      $group['cancel_url'] = \CRM_Utils_System::url('civicrm/dashboard', 'reset=1', TRUE, NULL, FALSE, FALSE, TRUE);
    }
    $group['is_active'] = TRUE;

    [$groupId] = InstallEntities::install(['entity' => 'UFGroup', 'values' => $group]);

    // 2. Entrée de menu qui appelle le profil
    if ($navParent && $nav) {
      InstallEntities::install([
        'entity' => 'Navigation',
        'values' => [
          'parent_id:name' => $navParent,
          'name' => $nav,
          'url' => "civicrm/profile/create?gid=$groupId&reset=1",
          'permission' => 'add contacts',
          'is_active' => TRUE,
        ],
      ]);
    }

    // 3. Utilisations du profil : toujours "Profile", plus "Contact Summary" s'il figure dans un Contact Layout
    $modules = ['Profile'];
    if (self::isInContactLayout($name)) {
      $modules[] = 'Contact Summary';
    }
    foreach ($modules as $module) {
      InstallEntities::install([
        'entity' => 'UFJoin',
        'values' => [
          'uf_group_id:name' => $name,
          'module' => $module,
          'is_active' => TRUE,
        ],
      ]);
    }

    // 4. Champs : suppression des anciens, puis recréation depuis le fichier
    civicrm_api4('UFField', 'delete', [
      'where' => [['uf_group_id', '=', $groupId]],
      'checkPermissions' => FALSE,
    ]);

    foreach ($fields as $field) {
      $field = self::resolveCustomFieldName($field);
      unset($field['id'], $field['uf_group_id.name'], $field['uf_group_id:name']);
      $field['uf_group_id'] = $groupId;
      $field['is_active'] = TRUE;

      civicrm_api4('UFField', 'create', [
        'values' => $field,
        'checkPermissions' => FALSE,
      ]);
    }

    return $groupId;
  }

  /**
   * Champ personnalisé écrit "groupe.champ" dans field_name:name
   * → field_name = custom_<id>, l'id dépendant de la base.
   * Les champs natifs (birth_date…) sont renvoyés tels quels.
   */
  private static function resolveCustomFieldName(array $field): array {
    if (!isset($field['field_name:name'])) {
      return $field;
    }

    [$groupName, $fieldName] = explode('.', $field['field_name:name'], 2);

    $customField = civicrm_api4('CustomField', 'get', [
      'select' => ['id'],
      'where' => [
        ['custom_group_id:name', '=', $groupName],
        ['name', '=', $fieldName],
      ],
      'checkPermissions' => FALSE,
    ])->first();

    if (!$customField) {
      throw new \CRM_Core_Exception("don_corps : champ personnalisé '{$field['field_name:name']}' introuvable");
    }

    unset($field['field_name:name']);
    $field['field_name'] = 'custom_' . $customField['id'];
    return $field;
  }

  /**
   * Le profil est-il affiché dans un Contact Layout ?
   * blocks = lignes → colonnes → blocs, chaque bloc ayant un 'name' ("profile.<nom>" pour un profil).
   */
  private static function isInContactLayout(string $name): bool {
    $layouts = civicrm_api4('ContactLayout', 'get', [
      'select' => ['blocks'],
      'checkPermissions' => FALSE,
    ]);

    foreach ($layouts as $layout) {
      foreach ($layout['blocks'] ?? [] as $row) {
        foreach ($row as $column) {
          foreach ($column as $block) {
            if (($block['name'] ?? '') === "profile.$name") {
              return TRUE;
            }
          }
        }
      }
    }
    return FALSE;
  }

}