<?php
namespace Civi\DonCorps\Install;

/**
 * Met à jour le nom des groupes utilisés dans les SavedSearches
 * (par exemple Archives_64 → Archives_52), ces noms variant d'une installation à l'autre.
 * À appeler en post-installation.
 */
class UpdateSearchGroups {

  /**
   * @throws \CRM_Core_Exception à la première erreur
   */
  public static function updateAll(): void {
    \Civi::log()->info('don_corps : mise à jour des noms de groupes utilisés par les SavedSearches');

    self::updateSearchGroup('SavedSearch_Op_rations_fun_raires_de_plus_de_5_ans.mgd.php', 'Archives', 'Archives_');
    self::updateSearchGroup('SavedSearch_Donneurs_annul_s.mgd.php', 'Archives', 'Archives_');
    self::updateSearchGroup('SavedSearch_Archives_dans_protocole_in_ni_ex_vivo.mgd.php', 'Archives sans protocole in ni ex vivo', 'Archives_sans_protocole_in_ni_ex__');
    self::updateSearchGroup('SavedSearch_Archives_dans_protocole_in_ni_ex_vivo.mgd.php', 'Archives', 'Archives_');
    self::updateSearchGroup('SavedSearch_Donneurs_sans_PAQPF.mgd.php', 'Archives', 'Archives_');
    self::updateSearchGroup('SavedSearch_Donneurs_sans_PAQPF.mgd.php', 'donneur avec PAQPF', 'donneur_avec_P_');
    self::updateSearchGroup('SavedSearch_Donneurs_vivants_ano_ville_CP.mgd.php', 'annulations (incluant anonymes)', 'Annulation_');
  }

  /**
   * Remplace, dans un fichier .mgd.php, le nom de groupe d'origine (préfixe + nombre)
   * par le nom du groupe sur cette installation.
   *
   * @return bool TRUE si le fichier a été modifié, FALSE si aucun changement n'était nécessaire
   * @throws \CRM_Core_Exception en cas d'erreur
   */
  public static function updateSearchGroup(string $file, string $groupTitle, string $groupNamePrefix): bool {
    $file = \CRM_DonCorps_ExtensionUtil::path('managed/' . $file);

    // Nom du groupe sur cette installation
    $group = civicrm_api4('Group', 'get', [
      'select' => ['name'],
      'where' => [
        ['title', '=', $groupTitle],
        ['name', 'LIKE', $groupNamePrefix . '%'],
      ],
      'checkPermissions' => FALSE,
    ])->first();

    if (!$group) {
      self::fail("groupe introuvable : titre '$groupTitle', nom commençant par '$groupNamePrefix'");
    }
    $nameNew = "['" . $group['name'] . "']";

    // Lecture du fichier
    if (!is_file($file)) {
      self::fail("fichier introuvable : $file");
    }
    $content = file_get_contents($file);
    if ($content === FALSE) {
      self::fail("impossible de lire le fichier : $file");
    }

    // Nom d'origine dans le fichier
    if (!preg_match('/' . preg_quote($groupNamePrefix, '/') . '[0-9]+/', $content, $match)) {
      self::fail("nom de groupe '$groupNamePrefix…' introuvable dans $file");
    }
    $nameOrig = "['" . $match[0] . "']";

    if ($nameOrig === $nameNew) {
      \Civi::log()->info("don_corps : aucun remplacement nécessaire dans $file");
      return FALSE;
    }

    // Sauvegarde puis remplacement
    $backup = $file . '.bak';
    if (!copy($file, $backup)) {
      self::fail("impossible de créer la sauvegarde : $backup");
    }
    if (file_put_contents($file, str_replace($nameOrig, $nameNew, $content)) === FALSE) {
      self::fail("impossible d'écrire le fichier : $file");
    }

    \Civi::log()->info("don_corps : $nameOrig remplacé par $nameNew dans $file (sauvegarde : $backup)");
    return TRUE;
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