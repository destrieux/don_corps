<?php
$fichier = \CRM_DonCorps_ExtensionUtil::path('managed/toto.json');
var_dump($fichier, file_exists($fichier));
var_dump(\Civi\DonCorps\Contacts\ImportCDC::ImportMain($fichier));
