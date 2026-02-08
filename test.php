<?php
eval(`cv php:boot`);

$navigations = civicrm_api4('Navigation', 'get', [
  'select' => [
    '*',
  ],
  'checkPermissions' => FALSE,
]);

print_r($navigations);


