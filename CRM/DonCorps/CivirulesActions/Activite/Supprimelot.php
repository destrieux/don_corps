<?php
/**
 * Class for CiviRules soft deleting of contacts
 *
 * @author christophe Destrieux <christophe.destrieux@univ-tours.fr>
 * @license AGPL-3.0
 */
class CRM_DonCorps_CivirulesActions_Activite_Supprimelot extends CRM_Civirules_Action {

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
  
        // ID de l'activité 
        $activityId = $triggerData->getEntityId();

        //eval(`cv php:boot`);/// pour tester
        //$activityId = 108; /// pour tester

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

        if (isset($activities[0])){                         // si l'activité existe on récupère les données
            $date = $activities[0]['activity_date_time'];     // date creation activité
            $details = $activities[0]['details'];             // liste des pièces à détruire

            $activityContacts = civicrm_api4('ActivityContact', 'get', [
                'select' => [
                    'contact_id.display_name',
                    'contact_id.id',
                    ],
                'where' => [
                ['activity_id', '=', $activityId],
                ['record_type_id:name', '=', 'Activity Source'],
                ],
                'checkPermissions' => FALSE,
            ]);

            if (isset($activityContacts[0])){
                $source=$activityContacts[0]['contact_id.id'];    // source de l'activité (créateur) id
                $source_name=$activityContacts[0]['contact_id.display_name'];    // source de l'activité (créateur) name
            }

        }else{
            CRM_Core_Session::setStatus("Pas d'activité");
            return ; 
        }

        /// crée le sujet de l'activité (date_nom_de_la _personne_qui_supprime)
        $subject=preg_replace('/\s/', '_', $date)."_".preg_replace('/\s/', '_', $source_name);
        
        /// récupère la liste des pièces à supprimer, supprime les espaces et sauts de ligne et les transforme en un tableau 
        $details = trim(preg_replace('/<\/?p>/', ' ', $details));  // rempalcement des sauts de paragraphe par espaces
        $details = trim(preg_replace('/&nbsp;/', ' ', $details));  // remplacement des sauts de ligne par espaces
        $details = trim(preg_replace('/,+/', ' ', $details));      // remplacemen des virgules par des espaces
        $details = trim(preg_replace('/\s+/', '#', $details));     // remplacement des espaces par #

        $pieces = explode("#", $details);       // tableau contenant les numeros de pieces à détruire, sépare au niveau des #
        sort($pieces);                          // tableau des pièces à détruire classé par ordre croissant

        
        $pieces_noUtilisation=array();   // pièces manquantes dans la base
        $pieces_a_detruire=array();      // pieces à détruire par l'activité
        $pieces_deja_detruites=array();  // pieces déja détruites
        $pieces_corps=array();           // pièces qui sont des corps : à détruire par tableau de bord
        

        foreach($pieces as $piece){        // pour chaque pièce du champ 'details' de l'activité
    
            $utilisationDuCorpses = civicrm_api4('Custom_Utilisation_du_corps', 'get', [  // on récupère les données de l'utilisation
            'select' => [
                'id',
                'Type_de_poi_ce_3:name',
                'Mode_limination_hors_corps_2:name',
            ],
            'where' => [
                ['N_de_pi_ce_ou_de_corps', '=', $piece],
            ],
            'checkPermissions' => FALSE,
            ]);

            if(isset($utilisationDuCorpses[0])){      //// la piece existe dans la base
                // si la piece a déja été détruite ou manquante ou crematisée : on ne fait rien on signale juste à l'utlisateur
                $elim = $utilisationDuCorpses[0]['Mode_limination_hors_corps_2:name'];

                if(($elim=='Cr_mation_comme_pi_ce_anatomiqu')||($elim=='Manquante')||($elim=='Destruction_par_la_m_thode_util')) {
                array_push($pieces_deja_detruites,$piece);
                continue;
                } 

                // si la pièce est un corps : on ne fait rien et on dit à l'utilisateur de passer par le tableau des corps 
                $types = $utilisationDuCorpses[0]['Type_de_poi_ce_3:name'];
                foreach ($types as $type){

                    if($type=='Corps_entier_tronc'){
                        array_push($pieces_corps,$piece);
                        continue 2;
                    }
                }

                // si la pièce n'est pas manquante, détruite ou crématisée et n'est pas un corps : change statut, date de crémation, localisation
                array_push($pieces_a_detruire,$piece);

                $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
                    'values' => [
                    'Mode_limination_hors_corps_2:name' => 'Cr_mation_comme_pi_ce_anatomiqu',
                    'Date_limination_pi_ce' => $date,
                    'Lacalisation' => '',
                    'Complement_location' => '',
                    ],
                    'where' => [
                        ['N_de_pi_ce_ou_de_corps', '=', $piece],
                    ],
                    'checkPermissions' => FALSE,
                ]);

        
            }else{                                  //// la piece n'existe pas dans la base : ajoutée à $pieces_noUtilisation
            array_push($pieces_noUtilisation,$piece);
            continue;
            } 
        }

        ### Prépare le rapport 
        if(count($pieces_a_detruire)!=0){
            $rapport1="<p>####### ".count($pieces_a_detruire)." Pièce(s) détruite(s) par cette action : ".implode(", ",$pieces_a_detruire)."</p>";
            CRM_Core_Session::setStatus(implode(", ",$pieces_a_detruire), count($pieces_a_detruire).' Pièce(s) détruite(s) par cette action', 'success');
            } else{
            $rapport1="";
        }
        
        if(count($pieces_deja_detruites)!=0){
            $rapport2="<p>####### ".count($pieces_deja_detruites). "Pièces(s) détruite(s) préalablement à cette action --> non modifiée(s) ".implode(", ",$pieces_deja_detruites)."</p>";
            CRM_Core_Session::setStatus(implode(", ",$pieces_deja_detruites), count($pieces_deja_detruites).' Pièce(s) déjà détruite(s) : inchangée(s)', 'success');
            } else{
            $rapport2="";
        }
        
        if(count($pieces_corps)!=0){
            $rapport3="<p>####### ".count($pieces_corps)." Codes barres correspondant à des corps  --> inchangé(s) : ".implode(", ",$pieces_corps)."</p>";
            CRM_Core_Session::setStatus(implode(", ",$pieces_corps), count($pieces_corps).' Codes barres correspondant à des corps : utiliser le tableau de bord et demander crémation', 'alert');
            } else{
            $rapport3="";
        }
        
        if(count($pieces_noUtilisation)!=0){
            $rapport4="<p>####### ".count($pieces_noUtilisation)."Pièce(s) absente(s) de la base --> inchangée(s) : ".implode(", ",$pieces_noUtilisation)."</p>";  
            CRM_Core_Session::setStatus(implode(", ",$pieces_noUtilisation), count($pieces_noUtilisation).' Pièces absentes de la base : inchangées', 'alert'); 
            } else{
            $rapport4="";
        }
    
        $rapport=$rapport1.$rapport2.$rapport3.$rapport4;
        
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

        /// Modifie la cible de l'actité et lui assigne l'id du createur de l'activité 
        /// CAD inscrit l'activité dans la fiche de celui qui crée l'activité
        /// évite le fait que l'activité pourrait être crée dans une fiche donneur ou une organisation)

        $results = civicrm_api4('ActivityContact', 'update', [
            'values' => [
            'contact_id' => $source,
            ],
            'where' => [
            ['activity_id', '=', $activityId],
            ['record_type_id:name', '=', 'Activity Targets'],
            ],
            'checkPermissions' => FALSE,
        ]);

    } // fin de process action

} //fin de définition de classe





