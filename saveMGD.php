<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;

// Contact Layouts : civix exports crée tous les layouts avec le même nom qu'il faut modifier
// Il faut aussi modifier le nom dans le fichier mgd

function replaceInfile($file, $find, $replace) {
        if ($find != $replace) {
            //recupere la totalité du fichier
            $str = file_get_contents($file);
            if ($str === false) {
                return false;
            } else {
                //effectue le remplacement dans le texte
                $str = str_replace($find, $replace, $str);
                //remplace dans le fichier
                if (file_put_contents($file, $str) === false) {
                    return false;
                }
            }
        }
        return true;
    }


$uFFields = civicrm_api4('UFField', 'get', [
  'select' => [
    'field_name',
    'label',
    'field_name:name',
  ],
  'where' => [
    ['field_name', 'CONTAINS', 'custom_'],
  ],
  'orderBy' => [
    'field_name' => 'ASC',
  ],
  'checkPermissions' => FALSE,
]);



$uFFields= 
   [
  [
    'id' => 147,
    'field_name' => 'custom_37',
    'field_name:name' => 'Devenir_du_corps.CESP',
    'label' => E::ts('Avis du Comité éthique'),
  ],
  [
    'id' => 148,
    'field_name' => 'custom_38',
    'field_name:name' => 'Devenir_du_corps.ref_avis_CESP',
    'label' => E::ts('ref avis Comité éthique'),
  ],
  [
    'id' => 152,
    'field_name' => 'custom_34',
    'field_name:name' => 'Compl_m_nt_tat_civil.Heure_du_d_c_s',
    'label' => E::ts('Heure du décès'),
  ],
  [
    'id' => 154,
    'field_name' => 'custom_31',
    'field_name:name' => 'Compl_m_nt_tat_civil.Ville_de_naissance',
    'label' => E::ts('Ville de naissance'),
  ],
  [
    'id' => 155,
    'field_name' => 'custom_32',
    'field_name:name' => 'Compl_m_nt_tat_civil.Ann_e_naissance',
    'label' => E::ts('Année naissance (auto)'),
  ],
  [
    'id' => 158,
    'field_name' => 'custom_34',
    'field_name:name' => 'Compl_m_nt_tat_civil.Heure_du_d_c_s',
    'label' => E::ts('Heure du décès'),
  ],
  [
    'id' => 159,
    'field_name' => 'custom_35',
    'field_name:name' => 'Compl_m_nt_tat_civil.Ann_e_de_d_c_s_auto_',
    'label' => E::ts('Année de décès (auto)'),
  ],
  [
    'id' => 160,
    'field_name' => 'custom_29',
    'field_name:name' => 'Compl_m_nt_tat_civil.Civilit_user',
    'label' => E::ts('Civilité'),
  ],
  [
    'id' => 163,
    'field_name' => 'custom_36',
    'field_name:name' => 'Demandeur_information.Date_d_envoi_d_informations',
    'label' => E::ts('Date envoi informations'),
  ],
  [
    'id' => 167,
    'field_name' => 'custom_115',
    'field_name:name' => 'animal.Esp_ce',
    'label' => E::ts('Espèce'),
  ],
  [
    'id' => 168,
    'field_name' => 'custom_114',
    'field_name:name' => 'animal.Provenance',
    'label' => E::ts('Provenance'),
  ],
  [
    'id' => 171,
    'field_name' => 'custom_34',
    'field_name:name' => 'Compl_m_nt_tat_civil.Heure_du_d_c_s',
    'label' => E::ts('Heure du décès'),
  ],
  [
    'id' => 172,
    'field_name' => 'custom_29',
    'field_name:name' => 'Compl_m_nt_tat_civil.Civilit_user',
    'label' => E::ts('Civilité'),
  ],
  [
    'id' => 178,
    'field_name' => 'custom_41',
    'field_name:name' => 'Devenir_du_corps.Date_de_sortie_d_finitive',
    'label' => E::ts('Date de sortie définitive'),
  ],
  [
    'id' => 179,
    'field_name' => 'custom_40',
    'field_name:name' => 'Devenir_du_corps.devenir_effectif_du_corps',
    'label' => E::ts("Type d'opération funéraire réalisée"),
  ],
  [
    'id' => 180,
    'field_name' => 'custom_42',
    'field_name:name' => 'Devenir_du_corps.Date_op_rations_fun_raires',
    'label' => E::ts('Date opérations funéraires'),
  ],
  [
    'id' => 181,
    'field_name' => 'custom_43',
    'field_name:name' => 'Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires',
    'label' => E::ts('Date approximative de réalisation des opérations funéraires'),
  ],
  [
    'id' => 182,
    'field_name' => 'custom_29',
    'field_name:name' => 'Compl_m_nt_tat_civil.Civilit_user',
    'label' => E::ts('Civilité'),
  ],
  [
    'id' => 185,
    'field_name' => 'custom_33',
    'field_name:name' => 'Compl_m_nt_tat_civil.Adresse_incorrecte',
    'label' => E::ts('Adresse incorrecte'),
  ],
  [
    'id' => 190,
    'field_name' => 'custom_29',
    'field_name:name' => 'Compl_m_nt_tat_civil.Civilit_user',
    'label' => E::ts('Civilité'),
  ],
  [
    'id' => 195,
    'field_name' => 'custom_54',
    'field_name:name' => 'Promesse_de_don.Centre_de_don',
    'label' => E::ts('Centre de don'),
  ],
  [
    'id' => 196,
    'field_name' => 'custom_55',
    'field_name:name' => 'Promesse_de_don.N_de_don',
    'label' => E::ts('N° de don'),
  ],
  [
    'id' => 197,
    'field_name' => 'custom_56',
    'field_name:name' => 'Promesse_de_don.Date_du_don',
    'label' => E::ts('Date du don'),
  ],
  [
    'id' => 198,
    'field_name' => 'custom_29',
    'field_name:name' => 'Compl_m_nt_tat_civil.Civilit_user',
    'label' => E::ts('Civilité'),
  ],
  [
    'id' => 205,
    'field_name' => 'custom_46',
    'field_name:name' => 'Devenir_du_corps.Souhait_funeraire_personne_ref_rente',
    'label' => E::ts('Souhait funeraire personne reférente'),
  ],
  [
    'id' => 206,
    'field_name' => 'custom_44',
    'field_name:name' => 'Devenir_du_corps.Date_de_restitution',
    'label' => E::ts('Date de restitution'),
  ],
  [
    'id' => 207,
    'field_name' => 'custom_45',
    'field_name:name' => 'Devenir_du_corps.Pompes_fun_bres_mandat_es_par_proches',
    'label' => E::ts('Pompes funèbres mandatées par personne référente'),
  ],
];

foreach ($uFFields as $uFField){
  if ($uFField['field_name:name'] && $uFField['field_name'] && $uFField['label']){
    //echo "UFfield ".$uFField['id']." OK".PHP_EOL;
  } else {
    echo "---Erreur".PHP_EOL;
    echo "UFfield ".$uFField['id']." incomplet".PHP_EOL;
    echo "Les trois champs 'field_name:name' 'field_name' et 'label doivent avoir une valeur".PHP_EOL;
    print_r($uFField);
    exit;
  }
}
  
$convert=$uFFields;


   $exp_file = 'managed/ufnameconversion.txt'  ;        // nom du fichier à exporter
   file_put_contents($exp_file, json_encode($convert, JSON_PRETTY_PRINT));
   echo "Fichier de conversion sauvegardé : ".$exp_file.PHP_EOL;



// UF Groups
$uFGroups = civicrm_api4('UFGroup', 'get', [
    'select' => [
      '*',
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




$contactLayouts = civicrm_api4('ContactLayout', 'get', [
  'select' => [
    'id',
    'label',
  ],
  'where' => [
    ['base_module', '=', 'don_corps'],
  ],
  'checkPermissions' => FALSE,
]);

if(isset($contactLayouts[0])){
      foreach ($contactLayouts as $contactLayout){
        $name='ContactLayout_'.str_replace(' ', '_', $contactLayout['label']);
        echo "exporting Contact Layout ".$contactLayout['label']." (id : ".$contactLayout['id'].")".' as : '.$name.PHP_EOL;
        $cmd = "civix export ContactLayout ".$contactLayout['id'];
        echo $cmd.PHP_EOL;
        exec($cmd, $output, $retval);
        echo "Returned with status $retval and output:\n";
        print_r($output);
        unset ($output);

        $new="'name' => '".$name."'";
        echo $new.PHP_EOL;

        replaceInfile('managed/ContactLayout_1.mgd.php', "'name' => 'ContactLayout_1'", $new);
        rename('managed/ContactLayout_1.mgd.php', 'managed/'.$name.'.mgd.php');
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
      'title',
    ],
    'where' => [
      ['title', '=', 'Archives'],
    ],
    'checkPermissions' => FALSE,
  ]);



  if(isset ($groups[0])){
    foreach ($groups as $group){
      echo "exporting Search ".$group['title']." (".$group['id'].")".PHP_EOL;
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