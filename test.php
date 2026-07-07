<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;


  $toimport_file = Civi::paths()->getPath("[civicrm.root]/ext/don_corps/managed/ufnameconversion.txt");
  echo $toimport_file.PHP_EOL;

$toimport_file = __DIR__.'/managed/ufnameconversion.txt';

  echo $path.PHP_EOL;
