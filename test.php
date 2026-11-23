<?php

eval(`cv php:boot`);

use Civi\DonCorps\Entities\InstallEntities;     ## création des entités


      InstallEntities::installFile('Entities_to_create_rules_demande_cremation.php');
