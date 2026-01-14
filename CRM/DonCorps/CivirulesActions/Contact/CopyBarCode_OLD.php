<?php
/**
 * Class for CiviRules soft deleting of contacts
 *
 * @author christophe Destrieux <christophe.destrieux@univ-tours.fr>
 * @license AGPL-3.0
 */
class CRM_DonCorps_CivirulesActions_Contact_CopyBarCode extends CRM_Civirules_Action {

  /**
   * Method processAction to execute the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access public
   *
   */
  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $contactId = $triggerData->getContactId();

   // liste les numeros de pieces qd type de piece = corps entier/tronc


    $contacts = civicrm_api4('Contact', 'get', [
      'select' => [
        'contact_type:name',
        'champs_caches.piece_prinicpale',
        'custom_utilisation_du_corps.N_de_pi_ce_ou_de_corps',
      ],
      'join' => [
        ['Custom_Utilisation_du_corps AS custom_utilisation_du_corps', 'LEFT', ['custom_utilisation_du_corps.entity_id', '=', 'id']],
      ],
      'where' => [
        //['custom_utilisation_du_corps.Type_de_poi_ce_3', '=', 1],
        ['custom_utilisation_du_corps.Type_de_poi_ce_3:name', '=', 'Corps_entier_tronc'],
        ['id', '=', $contactId],
      ],
      'checkPermissions' => FALSE,
    ]);

    if (isset($contacts[0]['champs_caches.piece_prinicpale'])){
      $piece_ple = $contacts[0]['champs_caches.piece_prinicpale'];
      $num_corps = $contacts[0]['custom_utilisation_du_corps.N_de_pi_ce_ou_de_corps'];
    }else{
      $num_corps = NULL;
      $piece_ple = 1;
    }

    //echo "piece ppale : ".$piece_ple."\n" ;
    //echo "code barres : ".$num_corps."\n";

    //CRM_Core_Session::setStatus('ppale : '.$piece_ple.' CB : '.$num_corps, 'Succès', 'success');

    if ($piece_ple != $num_corps)
      {

        $results = civicrm_api4('Contact', 'update', [
          'values' => [
            'champs_caches.piece_prinicpale' => $num_corps,
          ],
          'where' => [
            ['id', '=', $contactId],
          ],
          'checkPermissions' => FALSE,
        ]);
      }


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
    return NULL;
  }


}

