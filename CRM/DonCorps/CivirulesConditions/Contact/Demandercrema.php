<?php
/**
 * Class for CiviRules condition passage en demander cremation
 * 
 * Cette condition vérifie que le statut du mode d'élimination est "DEMANDER LA CRÉMATION DU CONTACT"
 * Sii vérifiée : deux actions sont déclenchées : envoi d'un mail aux PF et change statut vers "crémation demandée"
 * 
 * @author christophe Destrieux <christophe.destrieux@univ-tours.fr>
 * @license AGPL-3.0
 */
class CRM_DonCorps_CivirulesConditions_Contact_Demandercrema extends CRM_Civirules_Condition {
	/**
	 * Returns a redirect url to extra data input from the user after adding a condition
	 *
	 * Return false if you do not need extra data input
	 *
	 */
	public function getExtraDataInputUrl($ruleConditionId) {
	return FALSE;
	}

	/**
	 * Method is mandatory and checks if the condition is met
	 *
	 * @param CRM_Civirules_TriggerData_TriggerData $triggerData
	 * @return bool
	 * @access public
	 */
	public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData){
		$contactId = $triggerData->getContactId();

		$utilisationDuCorpses = civicrm_api4('Custom_Utilisation_du_corps', 'get', [
			'select' => [
				'Mode_limination_hors_corps_2:name',
			],
			'where' => [
				['entity_id.id', '=', $contactId],
				['Type_de_poi_ce_3:name', '=', 'Corps_entier_tronc'],
			],
			'checkPermissions' => FALSE,
			]);

		$status = $utilisationDuCorpses[0]['Mode_limination_hors_corps_2:name'];

		if ($status=='Demander_cr_mation'){
			#echo "envoyer le mail"."\n";
			return TRUE;
		}else{
			#echo "ne pas envoyer le mail"."\n";
			return FALSE;
		}
	}

	/**
	 * This function validates whether this condition works with the selected trigger.
	 *
	 * This function could be overriden in child classes to provide additional validation
	 * whether a condition is possible in the current setup. E.g. we could have a condition
	 * which works on contribution or on contributionRecur then this function could do
	 * this kind of validation and return false/true
	 *
	 * @param CRM_Civirules_Trigger $trigger
	 * @param CRM_Civirules_BAO_Rule $rule
	 * @return bool
	 */
	public function doesWorkWithTrigger(CRM_Civirules_Trigger $trigger, CRM_Civirules_BAO_Rule $rule) {
		//return $trigger->doesProvideEntity('Contact');
		return $trigger->doesProvideEntity('Individual');



		
	}
}
