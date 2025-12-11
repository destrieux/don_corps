<?php
use CRM_CiviDdc_ExtensionUtil as E;

/**
 * PieceAnatomique.Relocate API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_piece_anatomique_Relocate_spec(&$spec) {
  $spec['magicword']['api.required'] = 0;
}

/**
 * PieceAnatomique.Relocate API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws API_Exception
 */
function civicrm_api3_piece_anatomique_Relocate($params) {

// liste les pièces et corps détruits, manquants, crématisés (I.E qui ne devraient pas avoir de loc) et qui ont une localisation
$utilisationDuCorpses = civicrm_api4('Custom_Utilisation_du_corps', 'get', [
  'select' => [
    'N_de_pi_ce_ou_de_corps',
    'contact.Devenir_du_corps.Date_op_rations_fun_raires',
    'contact.Devenir_du_corps.Date_de_sortie_d_finitive',
    'Mode_limination_hors_corps_2:name',
    'Lacalisation',
    'Type_de_poi_ce_3:name',
  ],
  'where' => [
    ['Mode_limination_hors_corps_2:name', 'IN', ['Manquante', 'Destruction_par_la_m_thode_util', 'Cr_mation_comme_pi_ce_anatomiqu']],
    ['Lacalisation', 'IS NOT NULL'],
  ],
  'orderBy' => [
    'N_de_pi_ce_ou_de_corps' => 'ASC',
  ],
  'checkPermissions' => FALSE,
]);



$today_tstp=strtotime("now"); // timestamp de la date du jour

foreach ($utilisationDuCorpses as $utilisationDuCorps)  // traite chacun des corps et pieces
  {
  unset($param_array);    // vide la table des variables à supprimer

  $type_piece = $utilisationDuCorps['Type_de_poi_ce_3:name'][0];
  $num_corps = $utilisationDuCorps['N_de_pi_ce_ou_de_corps'];
  $date_op_fun =$utilisationDuCorps['contact.Devenir_du_corps.Date_op_rations_fun_raires'];
  $date_op_fun_tstp=strtotime($date_op_fun); // timestamp de la date des op funeraires

  $date_sortieDef = $utilisationDuCorps['contact.Devenir_du_corps.Date_de_sortie_d_finitive'];
  $date_sortieDef_tstp = strtotime($date_sortieDef); // timestamp de la date de sortie definitive

  $piece_id =$utilisationDuCorps['id'];
  $localisation = $utilisationDuCorps['Lacalisation'];
  $mode_elim =$utilisationDuCorps['Mode_limination_hors_corps_2:name'];


  echo "AVANT"."\n";
  echo "type de pieces : ".$type_piece."\n";
  echo "code barres : ".$num_corps."\n";
  echo "date op fun : ".$date_op_fun."\n";
  echo "piece id    : ".$piece_id."\n";
  echo "localisation: ".$localisation."\n";
  echo "mode elimination : ".$mode_elim."\n";



  if (((($date_op_fun_tstp <= $today_tstp) and ($date_op_fun_tstp != NULL)) or (($date_sortieDef_tstp <= $today_tstp) and ($date_sortieDef_tstp != NULL))) and ($type_piece == 'Corps_entier_tronc') and (($mode_elim == 'Non_limin_e') or ($mode_elim == 'Demander_cr_mation') or ($mode_elim == 'Cr_mation_demand_e')))     
  // si :
  //  - il s'agit d'un corps
  //  - date opérations funéraires antérieure à la date du jour (non nulle) OU date sorte définitive  antérieure à la date du jour (non nulle)
  //  - ET mode mode elimination "non eliminée" ou "demander cremation" ou "crémation demandée" 
  // alors : passe piece en cremation 
  // 
    {
    echo "Now :".$today_tstp."\n";
    echo "OpFun :".$date_op_fun_tstp."\n";
    echo "SortieDef :".$date_sortieDef_tstp."\n";
    echo "-------"."\n";
    echo "piece id    : ".$piece_id."\n";
    echo "code barres : ".$num_corps."\n";
    echo "date op fun : ".$date_op_fun."\n";
    echo "mode elimination : ".$mode_elim."\n";
    echo "localisation: ".$localisation."\n";
    echo "operations funeraires réalisées ou sortie définitive et pas d'élimination ou demande de cremation --> passe piece en cremation"."\n";

    $mode_elim = 'Cr_mation_comme_pi_ce_anatomiqu';  // modifie le mode d'éliminiation

    $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
      'values' => [
        'Mode_limination_hors_corps_2:name' =>  $mode_elim,
       ],
       'where' => [
           ['id', '=', $piece_id],
         ],
       'checkPermissions' => FALSE,
     ]);
    }

  if (($mode_elim == 'Cr_mation_comme_pi_ce_anatomiqu') or ($mode_elim == 'Manquante') or ($mode_elim == 'Destruction_par_la_m_thode_util')) 
    // si : 
    //  - le mode d'élimination est  : "Crémation_comme_pièce_anatomique" ou "Destruction_par_la_méthode_utilisé" ou "manquante"
    // alors : supprime la localisation
  {
    echo "-------"."\n";
    echo "piece id    : ".$piece_id."\n";
    echo "code barres : ".$num_corps."\n";
    echo "date op fun : ".$date_op_fun."\n";
    echo "mode elimination : ".$mode_elim."\n";
    echo "localisation: ".$localisation."\n";
    echo "si cremation ou destruction ou manquante --> supprimer la loca"."\n";

    $results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
      'values' => [
        'Lacalisation' => '',
       ],
       'where' => [
           ['id', '=', $piece_id],
         ],
       'checkPermissions' => FALSE,
     ]);
    }
  }
}
