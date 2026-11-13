<?php
/**
 * Remplace, dans managed/CustomGroup_*.mgd.php, les valeurs numériques de
 * extends_entity_column_value par leurs noms (extends_entity_column_value:name).
 *
 * À lancer sur la base de développement, après un export civix :
 *   cv scr bin/fix-mgd-extends.php
 */
use CRM_DonCorps_ExtensionUtil as E;

foreach (glob(E::path('managed/CustomGroup_*.mgd.php')) as $file) {
  $content = file_get_contents($file);

  // Fichier sans valeurs numériques à convertir : rien à faire
  $pattern = "/'extends_entity_column_value' => \[[^\]]*\]/";
  if (!preg_match($pattern, $content)) {
    continue;
  }

  // Nom du groupe de champs, lu dans le fichier lui-même
  $groupName = NULL;
  foreach (require $file as $entity) {
    if ($entity['entity'] === 'CustomGroup') {
      $groupName = $entity['params']['values']['name'];
    }
  }

  // Noms correspondant aux valeurs, lus dans la base
  $names = civicrm_api4('CustomGroup', 'get', [
    'select' => ['extends_entity_column_value:name'],
    'where' => [['name', '=', $groupName]],
    'checkPermissions' => FALSE,
  ])->first()['extends_entity_column_value:name'] ?? NULL;

  if (!$names) {
    echo "Ignoré (noms introuvables) : " . basename($file) . PHP_EOL;
    continue;
  }

  // Remplacement de la ligne dans le fichier
  $replacement = "'extends_entity_column_value:name' => [\n"
    . implode('', array_map(fn($n) => '        ' . var_export($n, TRUE) . ",\n", $names))
    . '      ]';
  file_put_contents($file, preg_replace($pattern, $replacement, $content, 1));

  echo "Corrigé : " . basename($file) . ' → ' . implode(', ', $names) . PHP_EOL;
}