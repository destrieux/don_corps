<?php
eval(`cv php:boot`);
use Civi\DonCorps\Install\UpdateSearchGroups;   ## MAJ du nom des grouopes utilisés dans les requêtes


    UpdateSearchGroups::updateAll();