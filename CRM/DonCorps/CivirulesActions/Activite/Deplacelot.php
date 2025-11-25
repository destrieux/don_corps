<?php
/**
 * Class for CiviRules soft deleting of contacts
 *
 * @author christophe Destrieux <christophe.destrieux@univ-tours.fr>
 * @license AGPL-3.0
 */
class CRM_DonCorps_CivirulesActions_Activite_Deplacelot extends CRM_Civirules_Action {

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

   //eval(`cv php:boot`);/// pour tester
   //$activityId = 117; /// pour tester

    /// récupère la cible de l'activité (le local)
    $activityContacts = civicrm_api4('ActivityContact', 'get', [
      'select' => [
        'contact_id.display_name',
        'contact_id.id',
      ],
      'where' => [
        ['activity_id', '=', $activityId],
        ['record_type_id:name', '=', 'Activity Targets'],                  
        ['contact_id.contact_sub_type', 'IN', ['CDC', 'Emprunteur']],   // limite la recherche aux lieux de conservation
      ],
      'checkPermissions' => FALSE,
    ]);

    // Si l'activité a été crée depuis la fiche d'un lieu de conservation, on récupère son nom et son id
    if (isset($activityContacts[0])){     
      $target = $activityContacts[0]['contact_id.display_name'];
      $target_id = $activityContacts[0]['contact_id.id'];

    } else {      // Si l'activité a été crée depuis la fiche d'un contact d'autre type, on la supprime 
      CRM_Core_Session::setStatus('Lancer le déplacement depuis le local cible du déplacement : aucune action réalisée', 'alert');

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
      ['record_type_id:name', '=', 'Activity Source'], // source du contact
    ],
    'checkPermissions' => FALSE,
    ]);

  if (isset($activityContacts[0])){     // SI L'ACTIVITÉ A BIEN ETE CRÉE par un contact ON RÉCUPÈRE SON NOM
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

  /// crée le nom du déplacement = sujet de l'activ ité
  $subject=preg_replace('/\s/', '_', $date)."_".preg_replace('/\s/', '_', $source);

  /// récupère les données du déplacement et les transforme en un tableau 
  $details = trim(preg_replace('/<\/?p>/', ' ', $details));  // remplacement des sauts de paragraphe par espaces
  $details = trim(preg_replace('/&nbsp;/', ' ', $details));  // remplacement des sauts de ligne par espaces
  $details = trim(preg_replace('/,+/', ' ', $details));      // remplacemen des virgules par des espaces
  $details = trim(preg_replace('/\s+/', '#', $details));     // remplacement des espaces par #

  $pieces = explode("#", $details); // tableau contenant les numeros de pieces à inventorier, sépare au niveau des #
  sort($pieces);                    // tableau classé par ordre croissant

  $pieces_noUtilisation=array();   // pièces qui ne sont pas rattachées à une utilisation (pas dans la base)
  $pieces_detruites=array();       // pieces détruites, manquante, crematisées
  $pieces_locOK=array();           // pièces rattachées à une localisation 

  foreach($pieces as $piece){
   
    $utilisationDuCorpses = civicrm_api4('Custom_Utilisation_du_corps', 'get', [
      'select' => [
        'id',
        'Lacalisation',
        'Mode_limination_hors_corps_2:name',
        'Protocole_de_recherche_ex_vivo2:label',
      ],
      'where' => [
        ['N_de_pi_ce_ou_de_corps', '=', $piece],
      ],
      'checkPermissions' => FALSE,
    ]);
    
    if(isset($utilisationDuCorpses[0])){      //// la piece existe dans la base
      
      $elim = $utilisationDuCorpses[0]['Mode_limination_hors_corps_2:name'];
      
      // si la piece apparait dans la liste à déplacer alors qu'elle a été détruite ou manquante ou crematisée
      if(($elim=='Cr_mation_comme_pi_ce_anatomiqu')||($elim=='Manquante')||($elim=='Destruction_par_la_m_thode_util')) {
        array_push($pieces_detruites,$piece);

        // On repasse en "non eliminé" et on relocalise dans le bon lieu
        $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
          'values' => [
            'Lacalisation' => $target_id,
            'Mode_limination_hors_corps_2:name' => 'Non_limin_e',
            'Complement_location' => '',
          ],
          'where' => [
            ['id', '=', $utilisationDuCorpses[0]['id']],
          ],
          'checkPermissions' => FALSE,
        ]);
        continue;
      } 
  
      // la piece apparait dans la liste à importer et n'est pas détruite ou absente de la base -> on importe
          array_push($pieces_locOK,$piece);
          $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
          'values' => [
            'Lacalisation' => $target_id,
            'Complement_location' => '',
          ],
          'where' => [
            ['id', '=', $utilisationDuCorpses[0]['id']],
          ],
          'checkPermissions' => FALSE,
            ]);
        
     }else{                                  //// la piece n'existe pas dans la base : ajoutée à $pieces_noUtilisation
       array_push($pieces_noUtilisation,$piece);
       continue;
     } 
  }
   
  ### Prépare le rapport 
  if(count($pieces_locOK)!=0){
  $rapport1="<p>####### ".count($pieces_locOK)." Pièce(s) importée(s) dans ce local : ".implode(", ",$pieces_locOK)."</p>";
  CRM_Core_Session::setStatus(implode(", ",$pieces_locOK), count($pieces_locOK).' Pièce(s) importée(s) dans ce local', 'success');
    } else{
    $rapport1="";
    }
  
  if(count($pieces_detruites)!=0){
    $rapport2="<p>####### ".count($pieces_detruites)." Pièce(s) notée(s) détruite(s) avant déplacement --> Passée(s) en non éliminée(s) et importée(s) dans ce local : ".implode(", ",$pieces_detruites)."</p>";
    CRM_Core_Session::setStatus(implode(", ",$pieces_detruites), count($pieces_detruites).' Pièce(s) notée(s) détruite(s) avant inventaire : recréee(s) - importées dans ce local', 'success');
    } else{
    $rapport2="";
    }
  
  if(count($pieces_noUtilisation)!=0){
    $rapport4="<p>####### ".count($pieces_noUtilisation)." Pièce(s) absente(s) de la base --> Pas de modification : ".implode(", ",$pieces_noUtilisation)."</p>";  
    CRM_Core_Session::setStatus(implode(", ",$pieces_noUtilisation), count($pieces_noUtilisation).' Pièce(s) absente(s) de la base : non modifiée(s)', 'alert'); 
  } else{
    $rapport4="";
    }
  
  $rapport=$rapport4.$rapport2.$rapport1;
   
  /// INSCRIT LE RAPPORT ET LE SUJET DANS L'ACTIVITÉ
  
  $results = civicrm_api4('Activity', 'update', [
    'values' => [
      'details' => $rapport,
      'status_id:name' => 'Completed',
      'subject' => $subject,
    ],
    'where' => [
      ['id', '=', $activityId],
    ],
    'checkPermissions' => FALSE,
  ]);
    //CRM_Core_Error::debug_log_message(print_r($triggerData, TRUE));
  } // fin de process action
} //fin de définition de classe





