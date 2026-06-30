<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;


    #$this->ctx->log->info('- Vérification de la version CiviRules');
    $ext = civicrm_api4('Extension', 'get', [
      'where' => [
        ['key', '=', 'org.civicoop.civirules'],
      ],
      'select' => ['version'],
      'checkPermissions' => FALSE,
    ]);

    //print_r($ext);


if (empty($ext[0]['version']) || version_compare($ext[0]['version'], '3.36.0', '<')) {
      echo "Echec : org.civicoop.civirules 3.36.0 ou supérieure est requise";
    ##     $this->ctx->log->info('Echec : org.civicoop.civirules 3.36.0 ou supérieure est requise');  
    ##      \Civi::log('don_corps')->info('Echec : org.civicoop.civirules 3.36.0 ou supérieure est requise');
    } else {

echo "OK".PHP_EOL;
//require_once __DIR__ . '/don_corps.php';
create_rules();

}