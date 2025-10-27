<?php
/**
 * Class for CiviRules soft deleting of contacts
 *
 * @author christophe Destrieux <christophe.destrieux@univ-tours.fr>
 * @license AGPL-3.0
 */
class CRM_DonCorps_CivirulesActions_Contact_FixCivilite extends CRM_Civirules_Action {

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
          'Compl_m_nt_tat_civil.Civilit_user:name',
        ],
        'where' => [
          ['id', '=', $contactId],
        ],
        'checkPermissions' => FALSE,
      ]);
    
      $civilite = $contacts[0]['Compl_m_nt_tat_civil.Civilit_user:name'];
        switch ($civilite) {
            case 'Mr_':                                                    // Monsieur
                        $results = civicrm_api4('Contact', 'update', [
                            'values' => [
                            'postal_greeting_id:name' => 'Monsieur',
                            'email_greeting_id:name' => 'Monsieur',
                            'postal_greeting_display' => 'Monsieur',
                            'prefix_id:name' => 'Mr.',
                            'gender_id:name' => 'Male',
                            ],
                            'where' => [
                            ['id', '=', $contactId],
                            ],
                            'checkPermissions' => FALSE,
                        ]);
            break;
    
            case 'Mme_':                                                  // Genre féminin déclaré
                $results = civicrm_api4('Contact', 'update', [
                    'values' => [
                    'postal_greeting_id:name' => 'Madame',
                    'email_greeting_id:name' => 'Madame',
                    'postal_greeting_display' => 'Madame',
                    'prefix_id:name' => 'Mrs.',
                    'gender_id:name' => 'Female',
                    ],
                'where' => [
                    ['id', '=', $contactId],
                    ],
                'checkPermissions' => FALSE,
                ]);
            break;
    
            case 'Mlle_':                                                  // Mademoiselle
                $results = civicrm_api4('Contact', 'update', [
                    'values' => [
                    'postal_greeting_id:name' => 'Mademoiselle',
                    'email_greeting_id:name' => 'Mademoiselle',
                    'postal_greeting_display' => 'Mademoiselle',
                    'prefix_id:name' => 'Ms.', 
                    'gender_id:name' => 'Female',
                    ],
                    'where' => [
                    ['id', '=', $contactId],
                    ],
                    'checkPermissions' => FALSE,
                ]);
            break;
    
            case 'Mx':                                                  // indéterminé
                $results = civicrm_api4('Contact', 'update', [
                    'values' => [
                    'postal_greeting_id:name' => '{contact.first_name} {contact.last_name}',
                    'email_greeting_id:name' => '{contact.first_name} {contact.last_name}',
                    'postal_greeting_display' => '{contact.first_name} {contact.last_name}',
                    'prefix_id:name' => 'Mx.', 
                    'gender_id:name' => 'Other',
                    ],
                    'where' => [
                    ['id', '=', $contactId],
                    ],
                    'checkPermissions' => FALSE,
                ]);
            break;
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


