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
    $contactId = $triggerData->getContactId();  // id du contact passé par le trigger


   // Liste les contacts ayant une utilisation avec une piece de type corps
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
        ['custom_utilisation_du_corps.Type_de_poi_ce_3:name', '=', 'Corps_entier_tronc'],
        ['id', '=', $contactId],
      ],
      'checkPermissions' => FALSE,
    ]);

    //print_r($contacts);


    if (isset($contacts[0])){     // si ce contact a une pièce de type corps, $piece_ple prend la valeur du code barre de cette piece
      //echo "il y a une piece de type corps".PHP_EOL;
      $piece_ple = $contacts[0]['custom_utilisation_du_corps.N_de_pi_ce_ou_de_corps'];

    } else {                      // Si ce contact n'a pas de piece de type corps on met $piece_ple à NULL
      //echo "Pas de piece de type corps".PHP_EOL;
      $piece_ple = NULL;
    }

    //echo "piece ppale : ".$piece_ple."\n" ;

    //on met à jour le champ cache piece_prinicpale à jour pour ce contact
        $results = civicrm_api4('Contact', 'update', [
          'values' => [
            'champs_caches.piece_prinicpale' => $piece_ple,
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

