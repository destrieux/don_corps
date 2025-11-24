<?php
/**
 * Class for CiviRules soft deleting of contacts
 *
 * @author christophe Destrieux <christophe.destrieux@univ-tours.fr>
 * @license AGPL-3.0
 */
class CRM_DonCorps_CivirulesActions_Activite_Creeinventaire extends CRM_Civirules_Action {


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



  /**
   * Method processAction to execute the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access public
   *
   */

  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
  
   // ID de l'activité (OFFICIEL)
   $activityId = $triggerData->getEntityId();

   // ID du contact associé

       /// récupère la cible de l'activité (le local)
    $activityContacts = civicrm_api4('ActivityContact', 'get', [
      'select' => [
        'contact_id.display_name',
        'contact_id.id',
      ],
      'where' => [
        ['activity_id', '=', $activityId],
        ['record_type_id', '=', 3], // cible du contact
        ['contact_id.contact_sub_type', 'IN', ['CDC', 'Emprunteur']],
      ],
      'checkPermissions' => FALSE,
    ]);

    if (isset($activityContacts[0])){     // SI L'ACTIVITÉ A BIEN ETE CRÉE DEPUIS UN LOCAL OU UN CDC ON RÉCUPÈRE SON NOM ET SON ID
      $target = $activityContacts[0]['contact_id.display_name'];
      $target_id = $activityContacts[0]['contact_id.id'];

    } else {                              // SI L'ACTIVITÉ A ÉTÉ CREE DEPUIS UN AUTRE CONTACT, ON LA SUPPRIME 
      CRM_Core_Session::setStatus('Les inventaires doivent être créés depuis un lieu de stockage : inventaire non crée', 'alert');

      $results = civicrm_api4('Activity', 'delete', [
        'where' => [
          ['id', '=', $activityId],
        ],
        'checkPermissions' => FALSE,
      ]);

      $results = civicrm_api4('ActivityContact', 'delete', [
        'where' => [
          ['activity_id', '=', $activityId],
        ],
        'checkPermissions' => FALSE,
      ]);

      return ;
    }

    
   /// récupère la source de l'activité (le contact qui l'a créé)
   $activityContacts = civicrm_api4('ActivityContact', 'get', [
    'select' => [
      'contact_id.display_name',
      'contact_id.id',
    ],
    'where' => [
      ['activity_id', '=', $activityId],
      ['record_type_id', '=', 2], // source du contact
    ],
    'checkPermissions' => FALSE,
  ]);

  if (isset($activityContacts[0])){     // SI L'ACTIVITÉ A BIEN ETE CRÉE par un contact ON RÉCUPÈRE SON NOM ET SON ID
    $source = $activityContacts[0]['contact_id.display_name'];
  } 

    /// récupère les données de l'activité
    $activities = civicrm_api4('Activity', 'get', [
      'select' => [
        'activity_date_time',
        'details',
      ],
      'where' => [
        ['id', '=', $activityId],
      ],
      'checkPermissions' => FALSE,
    ]);

    if (isset($activities[0])){
      $date = $activities[0]['activity_date_time'];
      $details = $activities[0]['details'];
    
    }else{
      CRM_Core_Session::setStatus("Pas d'activité");
      return ; 
    }

    /// crée le nom de l'inventaire et ajoute le dans la liste des inventaires disponibles
    $inventaire_name=preg_replace('/\s/', '_', $date)."_".preg_replace('/\s/', '_', $target)."_".preg_replace('/\s/', '_', $source);
   
    $results = civicrm_api4('OptionValue', 'create', [						//  crée l'inventaire
      'values' => [
        'option_group_id.name' => 'Utilisation_du_corps_Inventaires',
                'label' => $inventaire_name,
        ],
      'checkPermissions' => FALSE,
        ]);


    $inventaire_value=$results[0]['value'];
    //CRM_Core_Error::debug_log_message(print_r($inventaire_value,TRUE));
    

    /// récupère les données de l'inventaire et les transforme en un tableau 
    //$details=trim($details);
    $details = trim(preg_replace('/<\/?p>/', ' ', $details));  // rempalcement des sauts de paragraphe par espaces
    $details = trim(preg_replace('/&nbsp;/', ' ', $details));  // remplacement des sauts de ligne par espaces
    $details = trim(preg_replace('/,+/', ' ', $details));      // remplacemen des virgules par des espaces
    $details = trim(preg_replace('/\s+/', '#', $details));     // remplacement des espaces par #

    $pieces = explode("#", $details); // tableau contenant les numeros de pieces à inventorier, sépare au niveau des #
    sort($pieces);                    // tableau classé par ordre croissant

        ############
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
        'Inventaires',
      ],
      'where' => [
        ['N_de_pi_ce_ou_de_corps', '=', $piece],
      ],
      'checkPermissions' => FALSE,
    ]);
    
    if(isset($utilisationDuCorpses[0])){      //// la piece existe dans la base


      //CRM_Core_Error::debug_log_message(print_r($utilisationDuCorpses, TRUE));

        // on récupère la liste des inventaires existants
        $inventaires_list=$utilisationDuCorpses[0]['Inventaires'];	// liste initiale des inventaires

        //CRM_Core_Error::debug_log_message(print_r($inventaires_list,TRUE));
        array_push($inventaires_list, $inventaire_value);            // liste actualisée des inventaires

        //CRM_Core_Error::debug_log_message(print_r($inventaires_list,TRUE));



        // si la piece apparait dans l'inventaire alors qu'elle a été détruite ou manquante ou crematisée
        $elim = $utilisationDuCorpses[0]['Mode_limination_hors_corps_2:name'];
   
        if(($elim=='Cr_mation_comme_pi_ce_anatomiqu')||($elim=='Manquante')||($elim=='Destruction_par_la_m_thode_util')) {
           array_push($pieces_detruites,$piece);
           // On repasse en "non eliminé" et on relocalise dans la bonne pièce
           $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
             'values' => [
               'Lacalisation' => $target_id,
               'Mode_limination_hors_corps_2:name' => 'Non_limin_e',
               'Inventaires' => $inventaires_list,
             ],
             'where' => [
               ['id', '=', $utilisationDuCorpses[0]['id']],
             ],
             'checkPermissions' => FALSE,
           ]);
           //CRM_Core_Session::setStatus('Pièce '.$piece.' notée éliminée dans la base - restaurées dans ce local ', 'Succès', 'success');
           continue;
         } 
   
        // si la piece apparait dans linventaire mais n'est pas localisée dans la bonne pièce
        $loc =  $utilisationDuCorpses[0]['Lacalisation'];
   
        if($loc!=$target_id){
         array_push($pieces_locbad,$piece);
         // On relocalise la pièce : 
         $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
           'values' => [
             'Lacalisation' => $target_id,
             'Inventaires' => $inventaires_list,
           ],
           'where' => [
             ['id', '=', $utilisationDuCorpses[0]['id']],
           ],
           'checkPermissions' => FALSE,
         ]);
        
   
        }else{ // la piece apparait dans linventaire  et est  localisée dans la bonne pièce -> on ajoute l'inventaire à la pièce
         array_push($pieces_locOK,$piece);

           $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
            'values' => [
              'Inventaires' => $inventaires_list,
            ],
            'where' => [
              ['id', '=', $utilisationDuCorpses[0]['id']],
            ],
            'checkPermissions' => FALSE,
             ]);
          
           //CRM_Core_Error::debug_log_message(print_r($results, TRUE));

        }
   
     }else{                                  //// la piece n'existe pas dans la base : ajoutée à $pieces_noUtilisation
       array_push($pieces_noUtilisation,$piece);
       continue;
     } 
   }
   
   ### Prépare le rapport 
   if(count($pieces_locOK)!=0){
   $rapport1="<p>####### Pièces localisées correctement dans ce local : ".implode(", ",$pieces_locOK)."</p>";
   CRM_Core_Session::setStatus(implode(", ",$pieces_locOK), 'Pièces bien localisées', 'success');
     } else{
     $rapport1="";
     }
   
   if(count($pieces_detruites)!=0){
     $rapport2="<p>####### Pièces notées détruites avant inventaire --> Passées en non éliminées et relocalisées ici : ".implode(", ",$pieces_detruites)."</p>";
     CRM_Core_Session::setStatus(implode(", ",$pieces_detruites), 'Pièces notées détruites avant inventaire : recréees - relocalisées', 'success');
     } else{
     $rapport2="";
     }
   
   
   if(count($pieces_locbad)!=0){
     $rapport3="<p>####### Pièces localisées ailleurs avant inventaire --> Relocalisées dans ce local : ".implode(", ",$pieces_locbad)."</p>";
     CRM_Core_Session::setStatus(implode(", ",$pieces_locbad), 'Pièces localisées ailleurs avant inventaire : relocalisées', 'success');
     } else{
     $rapport3="";
     }
   
   if(count($pieces_noUtilisation)!=0){
     $rapport4="<p>####### Pièces absentes de la base --> A CREER MANUELLEMENT : ".implode(", ",$pieces_noUtilisation)."</p>";  
     CRM_Core_Session::setStatus(implode(", ",$pieces_noUtilisation), 'Pièces absentes de la base : à créer', 'alert'); 
    } else{
     $rapport4="";
     }
   
   $rapport=$rapport4.$rapport2.$rapport3.$rapport1;
   
   /// INSCRIT LE RAPPORT ET LE SUJET DANS L'ACTIVITÉ
   
   $results = civicrm_api4('Activity', 'update', [
     'values' => [
       'details' => $rapport,
       'status_id:name' => 'Completed',
       'subject' => $inventaire_name,
     ],
     'where' => [
       ['id', '=', $activityId],
     ],
     'checkPermissions' => FALSE,
   ]);


    ###########

    //CRM_Core_Error::debug_log_message(print_r($triggerData, TRUE));
    //CRM_Core_Error::debug_log_message(print_r($pieces,TRUE));
    //CRM_Core_Error::debug_log_message(print_r($pieces_noUtilisation, TRUE));
    //CRM_Core_Error::debug_log_message(print_r($pieces_detruites, TRUE)); 
    //CRM_Core_Error::debug_log_message(print_r($pieces_locOK, TRUE));
    //CRM_Core_Error::debug_log_message(print_r($pieces_locbad, TRUE));

  } // fin de process action



} //fin de définition de classe





