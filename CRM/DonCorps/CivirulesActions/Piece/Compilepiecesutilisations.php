<?php
/**
 * Class for CiviRules soft deleting of contacts
 *
 * @author christophe Destrieux <christophe.destrieux@univ-tours.fr>
 * @license AGPL-3.0
 */
class CRM_DonCorps_CivirulesActions_Piece_Compilepiecesutilisations extends CRM_Civirules_Action {

  /**
   * Method processAction to execute the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access public
   *
   */

public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
  
 $contactId = $triggerData->getContactId();

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



   if (isset($concat_pieces)){
     $concat_pieces_uniques= array_unique($concat_pieces);         // supprime les doublons des pieces
   } else {
     $concat_pieces_uniques =[''];
   }


 }else {
   //   echo "Pas de pieces".PHP_EOL;
   $concat_utilisations_uniques = ['Ind_termin_'];                           // en l'absence d'utilisation assigne la valeur En attente d'utilisation au champ toutes utilisations(groupe champs cachés)
   $concat_pieces_uniques =[''];

 }

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

 }


  /**
   * Method to return the url for additional form processing for action
   * and return false if none is needed
   *
   * @param int $ruleActionId
   * @return bool
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return FALSE;
  }

}
