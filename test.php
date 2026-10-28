<?php
eval(`cv php:boot`);

      \Civi\DonCorps\Install\CustomFieldFilter::applyGroupFilter('D_posant', 'groupe_d_posants_porteurs_potent_24');
      \Civi\DonCorps\Install\CustomFieldFilter::applyGroupFilter('Demandeur', 'groupe_d_posants_porteurs_potent_24');
