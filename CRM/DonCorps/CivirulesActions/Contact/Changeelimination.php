<?php
/**
 * Class for CiviRules soft deleting of contacts
 * Cette action 
 *  - récupère les corps pour lesquels le mode d'élimination est "demander crémation"
 *  - passe leur statut en "crémation demandée
 * Elle est lancée par une civirule qui détecte une modification du mode d'élimination vers demander crémation du contact
 * 
 *    /!\ NE PAS LAISSER DE ECHO OU PRINT_R NON COMMENTES SINON ERREUR
 * 
 * @author christophe Destrieux <christophe.destrieux@univ-tours.fr>
 * @license AGPL-3.0
 */
class CRM_DonCorps_CivirulesActions_Contact_Changeelimination extends CRM_Civirules_Action {

  /**
   * Method processAction to execute the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access public
   *
   */
  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $contactId = $triggerData->getContactId();

    $contacts = civicrm_api4('Contact', 'get', [
      'select' => [
        'last_name',
        'custom_utilisation_du_corps.Type_de_poi_ce_3:name',
        'custom_utilisation_du_corps.id',
        'custom_utilisation_du_corps.Mode_limination_hors_corps_2:name',
      ],
      'join' => [
        ['Custom_Utilisation_du_corps AS custom_utilisation_du_corps', 'LEFT', ['id', '=', 'custom_utilisation_du_corps.entity_id']],
      ],
      'where' => [
        ['id', '=', $contactId],
        ['custom_utilisation_du_corps.Type_de_poi_ce_3:name', '=', 'Corps_entier_tronc'],
        ['custom_utilisation_du_corps.Mode_limination_hors_corps_2:name', '=', 'Demander_cr_mation'],
      ],
      'checkPermissions' => FALSE,
      ]);

    if (isset($contacts[0])){
      $utilisation_id=$contacts[0]['custom_utilisation_du_corps.id'];

      //echo "Corps entier en demander cremation - Modification en cremation demndee".PHP_EOL;
      $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
        'values' => [
          'Mode_limination_hors_corps_2:name' => 'Cr_mation_demand_e',
        ],
        'where' => [
          ['id', '=', $utilisation_id],
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
    return FALSE;
  }

}
