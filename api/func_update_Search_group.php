<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;


function update_search_group(){
    //////////// update_search_group() /////////
    // Cette fonction modifie le nom des groupes utilisés dans les requetes (par exemple Archives_64 en Archives_52)
    // Elle doit être invoquée en post installation
    //
    // syntaxe : update_search_archive($file, $group_title);
    //
    //      $file : nom du ficher mgd de la requete
    //      $group_title : titre du groupe (ex. Archives...) 
    //
    // elle est appelée par : function don_corps_civicrm_postinstall()
    //////////////

  $file = Civi::paths()->getPath("[civicrm.root]/ext/don_corps/managed/").func_get_arg(0);
  $group_title= func_get_arg(1);                        // $group_title
  $group_name_prefix = func_get_arg(2);                 // préfixe du nom de groupe
   
  
  echo "Title : ".$group_title.PHP_EOL;
  echo "Prefix :".$group_name_prefix.PHP_EOL;

  $groups = civicrm_api4('Group', 'get', [  // Récupère le nom du groupe associé au $group_title dans l'installation
    'select' => [
      'name',
    ],
    'where' => [
      ['title', '=', $group_title],
      ['name', 'CONTAINS', $group_name_prefix],
    ],
    'checkPermissions' => FALSE,
  ]);

//print_r($groups);

  //$group_title= substr($group_title, 0, 14);            // ne garde que les 14 premiers caracteres qui sont utilisés pour générer les noms de groupe
  //$group_title = str_replace(' ', '_', $group_title);   // remplace les espaces par des _
 

  if (!isset($groups)) {
      echo "Groupe introuvable avec title : ".$group_title." et nom : ".$group_name_prefix.PHP_EOL;
      return;
  } else{
      $group_name_new="['".$groups[0]['name']."']";   // nom du groupe associé au $group_title dans l'installation
      //$group_name_new=$groups[0]['name'];   // nom du groupe associé au $group_title dans l'installation
  }  


  if (!file_exists($file)) {    // Vérigie que le mgd file existe bien
      echo "Fichier introuvable : ".$file.PHP_EOL;
      return;
  }

  $content = file_get_contents($file); // Contenu du fichier mgd

  if ($content === false) {
      echo "Impossible de lire le fichier".PHP_EOL;
      return;
  }


  // Recherche $group_title'] dans le contenu du mgd file
  //preg_match_all("/".$group_title."_[^']*']/", $content, $matches);
  preg_match_all("/'".preg_quote($group_name_prefix, '/')."[0-9]+'/", $content, $matches);

  $group_name_orig="[".$matches[0][0]."]"; // nom du groupe dans le fichier mgd

  echo "Orig : ".$group_name_orig.PHP_EOL;
  echo "New : ".$group_name_new.PHP_EOL;

  if ($group_name_orig==$group_name_new){   // si aucun changement à faire
    echo "-Aucun remplacement du nom de groupe ayant pour title'".$group_title."' à effectuer dans ".$file.PHP_EOL;
    echo PHP_EOL;
    return;
  }

  $backup = $file . '.bak';     // Sauvegarde le fichier mgd original
  if (!copy($file, $backup)) {
      echo "Impossible de créer la sauvegarde : ".$backup.PHP_EOL;
      return;
  }

  // remplace ['$group_name_orig'] par ['$group_name_new'] dans le ficher mgd
  $content = str_replace($group_name_orig, $group_name_new, $content);

  // Écriture du fichier mgd
    if (file_put_contents($file, $content) === false) {
      die("Impossible d'écrire le fichier.\n");
  }

  echo "- Remplacement de ".$group_name_orig." par ".$group_name_new." effectué dans".$file.PHP_EOL;
  echo "  Sauvegarde : $backup\n";
  echo PHP_EOL;
}


$file = 'SavedSearch_Op_rations_fun_raires_de_plus_de_5_ans.mgd.php';
$group_title = "Archives";
$group_name_prefix = "Archives_";
update_search_group($file, $group_title, $group_name_prefix);

$file = 'SavedSearch_Donneurs_annul_s.mgd.php';
$group_title = "Archives";
$group_name_prefix = "Archives_";
update_search_group($file, $group_title, $group_name_prefix);

$file = 'SavedSearch_Archives_dans_protocole_in_ni_ex_vivo.mgd.php';
$group_title = "Archives sans protocole in ni ex vivo";
$group_name_prefix = "Archives_sans_protocole_in_ni_ex__";
update_search_group($file, $group_title, $group_name_prefix);

$file = 'SavedSearch_Archives_dans_protocole_in_ni_ex_vivo.mgd.php';
$group_title = "Archives";
$group_name_prefix = "Archives_";
update_search_group($file, $group_title, $group_name_prefix);

$file = 'SavedSearch_Donneurs_sans_PAQPF.mgd.php';
$group_title = "Archives";
$group_name_prefix = "Archives_";
update_search_group($file, $group_title, $group_name_prefix);

$file = 'SavedSearch_Donneurs_sans_PAQPF.mgd.php';
$group_title = "donneur avec PAQPF";
$group_name_prefix = "donneur_avec_P_";
update_search_group($file, $group_title, $group_name_prefix);

$file = 'SavedSearch_Donneurs_vivants_ano_ville_CP.mgd.php';
$group_title = "annulations (incluant anonymes)";
$group_name_prefix = "Annulation_";
update_search_group($file, $group_title, $group_name_prefix);
