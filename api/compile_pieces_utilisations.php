<?php
eval(`cv php:boot`);

//$contactId=9875;

 // $contactId = $triggerData->getContactId();
 $contactId = '3';

 unset($concat_utilisations);
 unset($concat_pieces);

 $utilisationDuCorpses = civicrm_api4('Custom_Utilisation_du_corps', 'get', [// récupère les utilisations pour ce contact
   'select' => [
     'id',
     'Utilisation2:name',
     'Type_de_poi_ce_3:name',
   ],
   'where' => [
     ['entity_id.id', '=', $contactId],
   ],
   'checkPermissions' => FALSE,
 ]);

 //print_r($utilisationDuCorpses);

 if(isset($utilisationDuCorpses[0])){
   //echo "il y a des pieces".PHP_EOL;

   foreach($utilisationDuCorpses as $utilisationDuCorpse){
       foreach($utilisationDuCorpse['Utilisation2:name'] as $utilindiv){
         if ($utilindiv <> 'Ind_termin_'){                   // si il existe une utilisation précise
           if(isset($concat_utilisations)){                  // on ajoute cette utilisation à la liste des utilisations (concat utilisations)
             array_push($concat_utilisations, $utilindiv);
           } else {
             $concat_utilisations[0]=$utilindiv;
           }
         } 
       }
         
       foreach($utilisationDuCorpse['Type_de_poi_ce_3:name'] as $pieceindiv){
         if($pieceindiv!='Corps_entier_tronc'){              // si c'est une piece et non un corps
           if(isset($concat_pieces)){ 
             array_push($concat_pieces, $pieceindiv);
           } else {
             $concat_pieces[0]=$pieceindiv;
           }
         }
       
   }

 }


   if (isset($concat_utilisations)){
     $concat_utilisations_uniques= array_unique($concat_utilisations);         // supprime les doublons des utilisations
   } else {
     $concat_utilisations_uniques = ['Ind_termin_'];
   }

   //echo PHP_EOL."concat_pieces : ".PHP_EOL;
   //print_r($concat_pieces);

   if (isset($concat_pieces)){
     $concat_pieces_uniques= array_unique($concat_pieces);         // supprime les doublons des pieces
   } else {
     $concat_pieces_uniques =[''];
   }


 }else {
   //   echo "Pas de pieces".PHP_EOL;
   $concat_utilisations_uniques = ['Ind_termin_'];                           // en l'absence d'utilisation assigne la valeur En attente d'utilisation au champ toutes utilisations(groupe champs cachés)
   $concat_pieces_uniques =[''];
   // $concat_pieces_uniques =[''];
 }


echo PHP_EOL."utilisations uniques".PHP_EOL;
print_r($concat_utilisations_uniques).PHP_EOL;
echo PHP_EOL."pieces uniques".PHP_EOL;
print_r($concat_pieces_uniques).PHP_EOL;


 $results = civicrm_api4('Contact', 'update', [                            // assigne le résultat au champ toutes utilisations(groupe champs cachés)
   'values' => [
     'champs_caches.toutes_utilisations:name' => $concat_utilisations_uniques,
     'champs_caches.toutes_pieces:name' => $concat_pieces_uniques,
   ],
   'where' => [
     ['id', '=', $contactId],
   ],
   'checkPermissions' => FALSE,
 ]);