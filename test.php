<?php
eval(`cv php:boot`);


/* $savedSearches = civicrm_api4('SavedSearch', 'get', [
  'select' => [
    'id',
    'label',
  ],
  'where' => [
    ['base_module', 'CONTAINS', 'don_corps'],
  ],
  'orderBy' => [
    'label' => 'ASC',
  ],
  'checkPermissions' => FALSE,
]);

if(isset ($savedSearches[0])){
  foreach ($savedSearches as $savedSearch){
    echo "exporting Search ".$savedSearch['label']." (".$savedSearch['id'].")".PHP_EOL;
    $cmd = "civix export SavedSearch ".$savedSearch['id'];
    echo $cmd.PHP_EOL;
    exec($cmd, $output, $retval);
    echo "Returned with status $retval and output:\n";
    print_r($output);
    unset ($output);
  }
} */


$afforms = civicrm_api4('Afform', 'get', [
  'where' => [
    ['base_module', '=', 'don_corps'],
  ],
  'orderBy' => [
    'name' => 'ASC',
  ],
  'checkPermissions' => FALSE,
  'select' => [
    'name',
  ],
]);

if(isset ($afforms[0])){
  foreach ($afforms as $afform){
    echo "exporting afform ".$afform['name'].PHP_EOL;
    $cmd = "civix export Afform ".$afform['name'];
    echo $cmd.PHP_EOL;
    exec($cmd, $output, $retval);
    echo "Returned with status $retval and output:\n";
    print_r($output);
    unset ($output);
  }
}











//exec('civix export SavedSearch 55', $output, $retval);
//echo "Returned with status $retval and output:\n";
//print_r($output);