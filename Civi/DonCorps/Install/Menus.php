<?php
namespace Civi\DonCorps\Install;

/**
 * Gestion des menus de navigation CiviCRM à l'installation.
 */
class Menus {

  /**
   * Active les menus dont le nom figure dans la liste.
   *
   * @param string[] $names Noms techniques des menus (champ "name" de Navigation)
   * @return int Nombre de menus activés
   */
  public static function enable(array $names): int {
    return self::setActive($names, TRUE);
  }

  /**
   * Désactive les menus dont le nom figure dans la liste.
   *
   * @param string[] $names Noms techniques des menus (champ "name" de Navigation)
   * @return int Nombre de menus désactivés
   */
  public static function disable(array $names): int {
    return self::setActive($names, FALSE);
  }

  /**
   * Active ou désactive une liste de menus du domaine courant.
   */
  private static function setActive(array $names, bool $active): int {
    if (!$names) {
      return 0;
    }

    $where = [
      ['name', 'IN', $names],
      ['domain_id', '=', 'current_domain'],
    ];

    // Menus réellement trouvés, pour signaler les noms inconnus
    $found = civicrm_api4('Navigation', 'get', [
      'select' => ['name'],
      'where' => $where,
      'checkPermissions' => FALSE,
    ])->column('name');

    $missing = array_diff($names, $found);
    if ($missing) {
      \Civi::log()->warning('don_corps : menus introuvables : ' . implode(', ', $missing));
    }

    $count = civicrm_api4('Navigation', 'update', [
      'values' => ['is_active' => $active],
      'where' => $where,
      'checkPermissions' => FALSE,
    ])->count();

    // Reconstruit le menu affiché aux utilisateurs
    \CRM_Core_BAO_Navigation::resetNavigation();

    $action = $active ? 'activés' : 'désactivés';
    \Civi::log()->info("don_corps : $count menus $action");
    return $count;
  }

}