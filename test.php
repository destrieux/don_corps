<?php

eval(`cv php:boot`);


$target_id=8; // n° du local
$activityId = 61; // N° de l'activité

$pieces = array("1234", "3333", "666666", "567", "7777777","1234567");
sort($pieces);

 print_r($pieces); 


###############

 $pieces_noUtilisation=array();   // pièces qui ne sont pas rattachées à une utilisation
 $pieces_detruites=array();       // pieces détruites, manquante, crematisées
 $pieces_locOK=array();           // pièces rattachées à une localisation et dans le bon local
 $pieces_locbad=array();          // pièces rattachées à une localisation mais localisées ailleurs -> à rappatrier

foreach($pieces as $piece){

 $utilisationDuCorpses = civicrm_api4('Custom_Utilisation_du_corps', 'get', [
   'select' => [
     'id',
     'N_de_pi_ce_ou_de_corps',
     'Type_de_poi_ce_3:name',
     'Lacalisation',
     'Mode_limination_hors_corps_2:name',
     'Protocole_de_recherche_ex_vivo2:label',
   ],
   'where' => [
     ['N_de_pi_ce_ou_de_corps', '=', $piece],
   ],
   'checkPermissions' => FALSE,
 ]);
  echo PHP_EOL."Pièce : ".$piece;


 if(isset($utilisationDuCorpses[0])){      //// la piece existe dans la base

     // si la piece apparait dans l'inventaire alors qu'elle a été détruite ou manquante ou crematisée
     $elim = $utilisationDuCorpses[0]['Mode_limination_hors_corps_2:name'];
     echo " --> ".$elim;

     if(($elim=='Cr_mation_comme_pi_ce_anatomiqu')||($elim=='Manquante')||($elim=='Destruction_par_la_m_thode_util')) {
        array_push($pieces_detruites,$piece);
        // On repasse en "non eliminé" et on relocalise dans la bonne pièce
        $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
          'values' => [
            'Lacalisation' => $target_id,
            'Mode_limination_hors_corps_2:name' => 'Non_limin_e',
          ],
          'where' => [
            ['id', '=', $utilisationDuCorpses[0]['id']],
          ],
          'checkPermissions' => FALSE,
        ]);
        echo " --> relocalisée vers ".$target_id." et passée en Non Eliminée".PHP_EOL;
        continue;
      } 

     // si la piece apparait dans linventaire mais n'est pas localisée dans la bonne pièce
     $loc =  $utilisationDuCorpses[0]['Lacalisation'];
     echo " et localisée dans : ".$loc;

     if($loc!=$target_id){
      array_push($pieces_locbad,$piece);
      // On relocalise la pièce : 
      $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
        'values' => [
          'Lacalisation' => $target_id,
        ],
        'where' => [
          ['id', '=', $utilisationDuCorpses[0]['id']],
        ],
        'checkPermissions' => FALSE,
      ]);
      echo " --> relocalisée de ".$loc." vers ".$target_id.PHP_EOL;
      

     }else{ // la piece apparait dans linventaire  et est  localisée dans la bonne pièce
      array_push($pieces_locOK,$piece);
      echo " --> bien localisée dans ".$loc.PHP_EOL;
     }

  }else{                                  //// la piece n'existe pas dans la base : ajoutée à $pieces_noUtilisation
    echo " --> Pas dans la base".PHP_EOL; 
    array_push($pieces_noUtilisation,$piece);
    continue;
  } 
}

### Prépare le rapport 
if(count($pieces_locOK)!=0){
$rapport1="<p>####### Pièces localisées correctement dans ce local : ".implode(", ",$pieces_locOK)."</p>";
  } else{
  $rapport1="";
  }

if(count($pieces_detruites)!=0){
  $rapport2="<p>####### Pièces notées détruites avant inventaire --> Passées en non éliminées et relocalisées ici : ".implode(", ",$pieces_detruites)."</p>";
  } else{
  $rapport2="";
  }


if(count($pieces_locbad)!=0){
  $rapport3="<p>####### Pièces localisées ailleurs avant inventaire --> Relocalisées dans ce local : ".implode(", ",$pieces_locbad)."</p>";
  } else{
  $rapport3="";
  }

if(count($pieces_noUtilisation)!=0){
  $rapport4="<p>####### Pièces absentes de la base --> A CREER MANUELLEMENT : ".implode(", ",$pieces_noUtilisation)."</p>";
  } else{
  $rapport4="";
  }

$rapport=$rapport4.$rapport2.$rapport3.$rapport1;
echo PHP_EOL.$rapport.PHP_EOL;

/// INSCRIT LE RAPPORT ET LE SUJET DANS L'ACTIVITÉ

$results = civicrm_api4('Activity', 'update', [
  'values' => [
    'details' => $rapport,
    'status_id:name' => 'Completed',
    'subject' => 'SUJET A AJOUTER ICI',
  ],
  'where' => [
    ['id', '=', $activityId],
  ],
  'checkPermissions' => FALSE,
]);

