<?php
eval(`cv php:boot`);
use Civi\DonCorps\Install\CiviRules;

CiviRules::checkVersion();
CiviRules::installTriggers();

