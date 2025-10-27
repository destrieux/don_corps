<?php
eval(`cv php:boot`);


// UF Groups
$uFGroups = civicrm_api4('UFGroup', 'get', [
    'select' => [
      'id',
      'name',
    ],
    'where' => [
      ['base_module', '=', 'don_corps'],
    ],
    'checkPermissions' => FALSE,
  ]);

  if(isset ($uFGroups[0])){
    foreach ($uFGroups as $uFGroup){
      echo "exporting UFGroup ".$uFGroup['name']." (".$uFGroup['id'].")".PHP_EOL;
      $cmd = "civix export UFGroup ".$uFGroup['id'];
      echo $cmd.PHP_EOL;
      exec($cmd, $output, $retval);
      echo "Returned with status $retval and output:\n";
      print_r($output);
      unset ($output);
    }
  }


// Tags

$tags = civicrm_api4('Tag', 'get', [
    'select' => [
      'id',
      'name',
    ],
    'where' => [
      ['base_module', '=', 'don_corps'],
    ],
    'checkPermissions' => FALSE,
  ]);

  if(isset ($tags[0])){
    foreach ($tags as $tag){
      echo "exporting tag ".$tag['name']." (".$tag['id'].")".PHP_EOL;
      $cmd = "civix export Tag ".$tag['id'];
      echo $cmd.PHP_EOL;
      exec($cmd, $output, $retval);
      echo "Returned with status $retval and output:\n";
      print_r($output);
      unset ($output);
    }
  }


// Saved searches not created by afform (tokens et purges)
$savedSearches = civicrm_api4('SavedSearch', 'get', [
    'select' => [
      'id',
      'label',
    ],
    'where' => [
      ['base_module', '=', 'don_corps'],
      ['tags:label', 'IN', ['Requêtes utilisées pour les purges', 'tokens']],
    ],
    'orderBy' => [
      'name' => 'ASC',
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
} 

// Groupe archives
$groups = civicrm_api4('Group', 'get', [
    'select' => [
      'id',
      'titre',
    ],
    'where' => [
      ['title', '=', 'Archives'],
    ],
    'checkPermissions' => FALSE,
  ]);

  if(isset ($groups[0])){
    foreach ($groups as $group){
      echo "exporting Search ".$group['titre']." (".$group['id'].")".PHP_EOL;
      $cmd = "civix export Group ".$group['id'];
      echo $cmd.PHP_EOL;
      exec($cmd, $output, $retval);
      echo "Returned with status $retval and output:\n";
      print_r($output);
      unset ($output);
    }
  } 






/// custom groups
$customGroups = civicrm_api4('CustomGroup', 'get', [
    'select' => [
      'id',
      'name',
    ],
    'where' => [
      ['base_module', '=', 'don_corps'],
    ],
    'orderBy' => [
      'id' => 'ASC',
    ],
    'checkPermissions' => FALSE,
  ]);

  if(isset ($customGroups[0])){
    foreach ($customGroups as $customGroup){
      echo "exporting CustomGroup ".$customGroup['name']." (".$customGroup['id'].")".PHP_EOL;
      $cmd = "civix export CustomGroup ".$customGroup['id'];
      echo $cmd.PHP_EOL;
      exec($cmd, $output, $retval);
      echo "Returned with status $retval and output:\n";
      print_r($output);
      unset ($output);
    }
  }
  
/// Option groups


$optionGroups = civicrm_api4('OptionGroup', 'get', [
    'select' => [
      'id',
      'name',
    ],
    'where' => [
      ['OR', [['name', '=', 'document_type'], ['name', '=', 'activity_type'], ['name', '=', 'email_greeting'], ['name', '=', 'gender'], ['name', '=', 'postal_greeting']]],
    ],
    'checkPermissions' => FALSE,
  ]);

  if(isset ($optionGroups[0])){
    foreach ($optionGroups as $optionGroup){
      echo "exporting optionGroups ".$optionGroup['name']." (".$optionGroup['id'].")".PHP_EOL;
      $cmd = "civix export OptionGroup ".$optionGroup['id'];
      echo $cmd.PHP_EOL;
      exec($cmd, $output, $retval);
      echo "Returned with status $retval and output:\n";
      print_r($output);
      unset ($output);
    }
  }






  // dedupe rules

  $dedupeRuleGroups = civicrm_api4('DedupeRuleGroup', 'get', [
    'select' => [
      'id',
      'name',
    ],
    'where' => [
      ['base_module', '=', 'don_corps'],
    ],
    'checkPermissions' => FALSE,
  ]);

  if(isset ($dedupeRuleGroups[0])){
    foreach ($dedupeRuleGroups as $dedupeRuleGroup){
      echo "exporting DedupeRuleGroup ".$dedupeRuleGroup['name']." (".$dedupeRuleGroup['id'].")".PHP_EOL;
      $cmd = "civix export DedupeRuleGroup ".$dedupeRuleGroup['id'];
      echo $cmd.PHP_EOL;
      exec($cmd, $output, $retval);
      echo "Returned with status $retval and output:\n";
      print_r($output);
      unset ($output);
    }
  }

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

// remove navigation menus mgd files created by afform (sinon doublons)

$navigations = scandir('./managed/');
//print_r($navigations);

foreach($navigations as $navigation){
    if(preg_match("/Navigation/i",$navigation)){
        $navigation='./managed/'.$navigation;
        echo "Suppression de : ".$navigation.PHP_EOL;
        unlink($navigation);

    }
}


$navigations = civicrm_api4('Navigation', 'get', [
    'select' => [
      'id',
      'label',
    ],
    'where' => [
      ['base_module', '=', 'don_corps'],
      ['parent_id', 'IS EMPTY'],
    ],
    'checkPermissions' => FALSE,
  ]);


if(isset ($navigations[0])){
  foreach ($navigations as $navigation){
    echo "exporting navigation menu ".$navigation['label'].' ('.$navigation['id'].')'.PHP_EOL;
    $cmd = "civix export Navigation ".$navigation['id'];
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