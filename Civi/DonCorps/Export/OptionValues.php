<?php
namespace Civi\DonCorps\Export;

use CRM_DonCorps_ExtensionUtil as E;

/**
 * Export des option values vers un fichier de définitions
 * utilisable par InstallEntities::installFile().
 */
class OptionValues {

  /**
   * Exporte les option values d'un groupe vers un fichier PHP.
   *
   * @param string $group        Nom du groupe d'options (ex. 'activity_type')
   * @param string $file         Chemin relatif à la racine de l'extension (ex. 'entities/activity_types.php')
   * @param bool   $onlyCustom   TRUE : exclut les valeurs réservées (fournies par CiviCRM)
   * @param bool   $onlyActive   TRUE : exclut les valeurs désactivées
   * @return int Nombre de valeurs exportées
   * @throws \CRM_Core_Exception si le fichier ne peut pas être écrit
   */
  public static function export(string $group, string $file, bool $onlyCustom = TRUE, bool $onlyActive = FALSE): int {
    $where = [['option_group_id:name', '=', $group]];
    if ($onlyCustom) {
      $where[] = ['is_reserved', '=', FALSE];
    }
    if ($onlyActive) {
      $where[] = ['is_active', '=', TRUE];
    } 


    $values = civicrm_api4('OptionValue', 'get', [
      'select' => ['label', 'name', 'description'],
      'where' => $where,
      'orderBy' => ['weight' => 'ASC'],
      'checkPermissions' => FALSE,
    ]);


    // Construction du fichier ligne par ligne
    // var_export() écrit chaque chaîne entre guillemets, en échappant les apostrophes
    $lines = [
      '<?php',
      'use CRM_DonCorps_ExtensionUtil as E;',
      '',
      "// Option values du groupe $group, exportées le " . date('Y-m-d H:i'),
      'return [',
    ];
    foreach ($values as $value) {
      $lines[] = '  [';
      $lines[] = "    'entity' => 'OptionValue',";
      $lines[] = "    'values' => [";
      $lines[] = "      'option_group_id:name' => " . var_export($group, TRUE) . ',';
      $lines[] = "      'name' => " . var_export($value['name'], TRUE) . ',';
      $lines[] = "      'label' => E::ts(" . var_export($value['label'], TRUE) . '),';
      if (!empty($value['description'])) {
        $lines[] = "      'description' => E::ts(" . var_export($value['description'], TRUE) . '),';
      }
      $lines[] = '    ],';
      $lines[] = '  ],';
    }
    $lines[] = '];';

    // Écriture du fichier (le dossier est créé si besoin)
    $path = \CRM_DonCorps_ExtensionUtil::path('Civi/DonCorps/Entities/').$file;

    if (!is_dir(dirname($path))) {
      mkdir(dirname($path), 0775, TRUE);
    }
    if (file_put_contents($path, implode(PHP_EOL, $lines) . PHP_EOL) === FALSE) {
      throw new \CRM_Core_Exception("don_corps : impossible d'écrire $path");
    }

    $count = $values->count();
    \Civi::log()->info("don_corps : $count option values du groupe $group exportées vers $file");
    return $count;
  }

}