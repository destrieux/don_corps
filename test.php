<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;
use Civi\DonCorps\WPUsers\WPPriviledges;

//WPPriviledges::ExportCaps('00_Civicrm_WP_perm.txt');
WPPriviledges::ModifyCaps('00_Civicrm_WP_perm.txt');
   