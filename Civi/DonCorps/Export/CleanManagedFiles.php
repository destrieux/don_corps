<?php
namespace Civi\DonCorps\Export;

use CRM_DonCorps_ExtensionUtil as E;

/**
 * Outils de développement : préparation des fichiers exportés avec civix.
 */
class CleanManagedFiles {

  /**
   * Nettoie les fichiers managed/*.mgd.php après un export civix :
   *  1. supprime les 'value' des types d'activité (attribuées automatiquement à l'installation) ;
   *  2. remplace les valeurs numériques de extends_entity_column_value par leurs noms ;
   *  3. vérifie la syntaxe PHP de tous les fichiers.
   *
   * À lancer sur la base de développement :
   *   cv ev '\Civi\DonCorps\Export::cleanMgd();'
   *
   * @return int Nombre de fichiers en erreur de syntaxe
   */
  public static function cleanMgd(): int {
    $managed = E::path('managed');

    //self::out("1. Suppression des 'value' des types d'activité");
    //self::removeActivityTypeValues($managed);

    self::out('1. Conversion de extends_entity_column_value en noms');
    self::convertExtendsToNames($managed);

    self::out('2. Vérification de la syntaxe');
    $errors = self::checkSyntax($managed);

    self::out($errors ? "Terminé avec $errors erreur(s)." : 'Terminé.');
    return $errors;
  }

  /**
   * Supprime les lignes 'value' => … des fichiers de types d'activité.
   */
  private static function removeActivityTypeValues(string $managed): void {
    foreach (glob("$managed/05_OptionGroup_activity_type.mgd.php") as $file) {
      $content = file_get_contents($file);
      echo "file : ".$file.PHP_EOL;
      $cleaned = preg_replace("/^[ \t]*'value' =>.*\R/m", '', $content, -1, $count);
      if ($count) {
        file_put_contents($file, $cleaned);
        self::out('   Corrigé : ' . basename($file));
      }
    }
  }

  /**
   * Remplace extends_entity_column_value (valeurs numériques)
   * par extends_entity_column_value:name (noms lus dans la base).
   */
  private static function convertExtendsToNames(string $managed): void {
    $pattern = "/'extends_entity_column_value' => \[[^\]]*\]/";

    foreach (glob("$managed/CustomGroup_*.mgd.php") as $file) {
      $content = file_get_contents($file);
      if (!preg_match($pattern, $content)) {
        continue;
      }

      $names = self::extendsNames(self::customGroupName($file));
      if (!$names) {
        self::out('   Ignoré (noms introuvables) : ' . basename($file));
        continue;
      }

      $replacement = "'extends_entity_column_value:name' => [\n"
        . implode('', array_map(fn($n) => '        ' . var_export($n, TRUE) . ",\n", $names))
        . '      ]';
      file_put_contents($file, preg_replace($pattern, $replacement, $content, 1));

      self::out('   Corrigé : ' . basename($file) . ' → ' . implode(', ', $names));
    }
  }

  /**
   * Nom du groupe de champs déclaré dans un fichier .mgd.php.
   */
  private static function customGroupName(string $file): ?string {
    foreach (require $file as $entity) {
      if ($entity['entity'] === 'CustomGroup') {
        return $entity['params']['values']['name'] ?? NULL;
      }
    }
    return NULL;
  }

  /**
   * Noms des sous-types auxquels s'applique un groupe de champs.
   */
  private static function extendsNames(?string $groupName): ?array {
    if (!$groupName) {
      return NULL;
    }
    return civicrm_api4('CustomGroup', 'get', [
      'select' => ['extends_entity_column_value:name'],
      'where' => [['name', '=', $groupName]],
      'checkPermissions' => FALSE,
    ])->first()['extends_entity_column_value:name'] ?? NULL;
  }

  /**
   * Vérifie la syntaxe de tous les fichiers .mgd.php avec php -l.
   *
   * @return int Nombre de fichiers en erreur
   */
  private static function checkSyntax(string $managed): int {
    $errors = 0;
    foreach (glob("$managed/*.mgd.php") as $file) {
      $output = [];
      exec('php -l ' . escapeshellarg($file) . ' 2>&1', $output, $status);
      if ($status !== 0) {
        self::out('   Erreur de syntaxe : ' . basename($file));
        $errors++;
      }
    }
    return $errors;
  }

  /**
   * Affiche un message dans le terminal et l'écrit dans le journal CiviCRM.
   */
  private static function out(string $message): void {
    echo $message . PHP_EOL;
    \Civi::log()->info('don_corps export : ' . trim($message));
  }

}