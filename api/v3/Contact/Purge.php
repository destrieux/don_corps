<?php
use CRM_CiviDdc_ExtensionUtil as E;

/**
 * Contact.Purge API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_contact_Purge_spec(&$spec): void  {
  $spec['magicword']['api.required'] = 0;
}

/**
 * Contact.Purge API 1.3
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
function civicrm_api3_contact_Purge($params) {

  // V1.2      CES TROIS CHAMPS ONT ETE MODIFIES APRES INSTALLATION LYON 1/4/24
//            $suppr_list_array["Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie"] = "";
//            $suppr_list_array["Promesse_de_don.Souhait_lecture_nom"] = "";
//            $suppr_list_array["Promesse_de_don.Souhiat_affichage_st_le"] = "";





function purge_help()
  {
  echo " Cette API réalise la purge de la base du centre de don du corps."."\n";
  echo " Elle appelle une fonction [purge(nom_du_groupe, à_purger1, _purger2, _purger3,... )] qui permet le parametrage de la purge"."\n";
  echo " Par défaut les options sont celles requises par l'article 4 de l'arrêté du 11 juillet 2023 "."\n";
  echo " L'utilsateur peut changer les données à purger en utilisant les parametres purger[1-n]"."\n";
  echo "\n";
  echo " Usage : purge(nom_du_groupe, à_purger1, _purger2, _purger3,... )"."\n";
  echo "\n";
  echo " nom_du_groupe : applique la purge aux contacts de ce groupe"."\n";
  echo "                 en general les groupes sont définis par des groupes dynamiques issus de requetes Searchkit"."\n";
  echo "                 ils peuvent correspondre à tout type de groupe de contacts"."\n";
  echo "\n";
  echo " options : définissent les actions à appliquer pour chaque type de données relatives aux contacts du groupe"."\n";
  echo "  - Delete      : Détruit le contact ATTENTION DANGER : NE LAISSE AUCUNE TRACE DANS LA BASE"."\n";
  echo "                  A N'UTILISER QUE POUR DEMANDEURS ET PROCHES NON RATTACHÉS"."\n";
  echo "  - Anon        : Nom, Nom de naissance, Prénoms, Date envoi des informations"."\n";
  echo "  - DN          : Date de naissance mais conserve l'année"."\n";
  echo "  - LieuN       : Lieu de naissance"."\n";
  echo "  - Genre       : Genre"."\n";
  echo "  - ATCD        : antécédents médicaux, cause du DC, signataire du certificat de DC"."\n";
  echo "  - ATCDnoProt  : antécédents médicaux, cause du DC, signataire du certificat de DC EN L'ABSENCE de protocole ex vivo"."\n";
  echo "  - Coord       : adresses postales et electroniques, téléphones"."\n";
  echo "  - Doc         : documents (promesse don...), activités (appels téléphoniques) et notes"."\n";
  echo "  - Relat       : relations du contact (personne de confiance, PAQF...)"."\n";
  echo "                  souhait de la personne référente en matière de restitution, pompes funebres qu elle a désignées"."\n";
  echo "  - DateDC      : date de décès (mais conserve l'année), met 'est décédé' à 1"."\n";
  echo "  - DC          : lieu de décès, lieu de prise en charge, infos complementaires sur prise en charge, met 'est décédé' à 1"."\n";
  echo "  - Choix       : choix exprimés dans la déclaration : restitution, preference type d’opération funéraire, "."\n";
  echo "                  opposition invitation proches, inscription mémorielle"."\n";
  echo "  - Accueil     : informations concernant l'accueil ou le transfert du corps : "."\n";
  echo "                  date d’accueil, refus d'accueillir le corps, décision de transfert, établissement de transfert, date de transfert"."\n";
  echo "  - Usage       : informations sur l'utilisation du corps : n° anonymisation, projets, durée conservation, "."\n";
  echo "                  date sortie corps pour projet, entité bénéficiaire,"."\n";
  echo "                  date de retour, impossibilité restauration corps, suspension acces au corps"."\n";
  echo "                  NB: la purge épargne les pièces pour lesquelles une conservation sans limite est autorisée ou qui n'ont pas été détruites et celles incluses dans un protocole de recherche ex vivo"."\n";
  echo "  -Opp_fun      : type d’opération funéraire, impossibilité de restitution du corps, date de sortie du corps en vue des opérations funéraires,"."\n";
  echo "                  date opérations funéraires, "."\n";
  echo "                  date de sortie du corps de l’établissement auquel le corps a été transféré vers l’établissement qui a délivré la carte,"."\n";
  echo "                  date d’accueil du corps par l’établissement ayant délivré la carte en vue de la restitution du corps ou des cendres,"."\n";
  echo "                  date de restitution du corps ou des cendres"."\n";
  echo "  -Group        : supprime les groupes du contact et le rattache au groupe archive"."\n";
  echo "  -Event        : supprime les evenements auxquels le contact est inscrit"."\n";
  echo "\n"; 

  } // fin definition fonction purge_help



function purge()
{

//////  Préparation logs
//$logfile = "/var/www/html/wordpress/wp-content/uploads/civicrm/civi_ddc_logs/civicrm_ddc_purge.log";  // définir ici le chemin des logs

$logfile = CRM_Core_Config::singleton()->configAndLogDir."/civicrm_ddc_purge.log"; // remonte le chemin des logs par défaut

if (is_writable($logfile)) {  // le ficher log existe ; on l'ouvre

    // Dans notre exemple, nous ouvrons le fichier $logfile en mode d'ajout
    // Le pointeur de fichier est placé à la fin du fichier
    // c'est là que $logtext sera placé
    if (!$fp = fopen($logfile, 'a')) {
         echo "Impossible d'ouvrir le fichier ($logfile)"."\n";
         exit;
    }


} else {  // le fichier log n'existe pas  ; le créer
    $fp = fopen($logfile, 'c+b');
    echo "Création nouveau log : ".$logfile."\n";
}

$today = date("Y-m-d H:i:s"); // utilisé pour horodatage
#fwrite($fp, $today);


  // récupère le nom du groupe et vérifie s'il existe
  $grp = func_get_arg(0);
  $groups = civicrm_api4('Group', 'get', [
    'where' => [
      ['title', '=', $grp],
    ],
    'checkPermissions' => FALSE
  ]);

  foreach ($groups as $row) {
    $grp_id = $row['id'];
  };

  if (isset($grp_id)){
    echo "\n"."Traitement Groupe :".$grp."\n";
  }else {
    echo "\n"."Le groupe :".$grp." n'existe pas"."\n"."\n";
    purge_help();
    return;
  }

  // récupère les variables à supprimer
  $numargs = func_num_args();
  unset($suppr_list_array);    // vide la table des variables à supprimer
  $supp_contact = 0;
  $mod_contact = 0;
  $tag_atcd = 0;
  $supp_usage = 0;
  $supp_adresses = 0;
  $supp_docs = 0;
  $supp_relats = 0;
  $supp_group =0;
  $supp_event =0;


//  print_r($suppr_list_array);
  for ($i = 1; $i < $numargs; $i++) {
      $arg = func_get_arg($i);

//      echo $i." : ".$arg."\n"; // debug

        switch ($arg) {
          case 'Delete':
            echo "    - Suppression totale des contacts"."\n";
            $supp_contact = 1;
            break;

          case 'Anon':
            echo "    - Anonymisation"."\n";
            $mod_contact = 1;
            $suppr_list_array["first_name"] = "Anonymisé";
            $suppr_list_array["middle_name"] = "Anonymisé";
            $suppr_list_array["nick_name"] = "Anonymisé";
            $suppr_list_array["last_name"] = "ANONYMISE";
            $suppr_list_array["Demandeur_information.Date_d_envoi_d_informations"] = "";
            break;

          case 'DN':
            echo "    - Suppression date de naissance"."\n";
            $mod_contact = 1;
            $suppr_list_array["birth_date"] = "";
            break;

          case 'LieuN':
            echo "    - Suppression lieu de naissance"."\n";
            $mod_contact = 1;
            $suppr_list_array["Compl_m_nt_tat_civil.Ville_de_naissance"] = "";
            break;

          case 'Genre':
            echo "    - Suppression genre"."\n";
            $mod_contact = 1;
            $suppr_list_array["gender_id"] = "";
            break;

          case 'ATCD':
            echo "    - Suppression antécédents"."\n";
            $mod_contact = 1;
            $tag_atcd = 1;
            $suppr_list_array["Ant_c_dents_m_dicaux.Ant_c_dents_m_dico_chirurgicaux"] = "";
            $suppr_list_array["Ant_c_dents_m_dicaux.Cause_du_d_c_s_si_connue"] = "";
            $suppr_list_array["Arriv_e_du_corps_new.Signataire_certificat_de_d_c_s"] = "";
            $suppr_list_array["Ant_c_dents_m_dicaux.Stimulateur_pile"] = "";
            $suppr_list_array["Ant_c_dents_m_dicaux.Pathologie_cible:name"] = "";         
            break;


          case 'Choix':
            echo "    - Suppression choix exprimés sur déclaration de don"."\n";
            $mod_contact = 1;
            $suppr_list_array["Promesse_de_don.Refus_personne_referente"] = "";
            $suppr_list_array["Promesse_de_don.Devenir_souhait_"] = "";

            //       CES TROIS CHAMPS ONT ETE MODIFIES APRES INSTALLATION LYON 1/4/24
            $suppr_list_array["Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie"] = "";
            $suppr_list_array["Promesse_de_don.Souhiat_affichage_st_le"] = "";
            //$suppr_list_array["Promesse_de_don.Pr_venir_la_perosnne_r_f_rente"] = "";
            $suppr_list_array["Promesse_de_don.Souhait_lecture_nom"] = "";
            //$suppr_list_array["Promesse_de_don.Souait_affichage_sur_st_le"] = "";
            break;

          case 'DC':
            echo "    - Suppression infos décès sauf date"."\n";
            $mod_contact = 1;
            $suppr_list_array["is_deceased"] = TRUE;
            $suppr_list_array["Prise_en_charge_au_d_c_s.Lieu_de_d_c_s"] = "";
            $suppr_list_array["Prise_en_charge_au_d_c_s.Ville_de_d_c_s"] = "";
            $suppr_list_array["Prise_en_charge_au_d_c_s.Pr_cision_lieu_prise_en_charge"] = "";
            break;

          case 'DateDC':
            echo "    - Suppression date de décès"."\n";
            $mod_contact = 1;
            $suppr_list_array["deceased_date"] = "";
            $suppr_list_array["is_deceased"] = TRUE;
            break;


          case 'Accueil';
            echo "    - Suppression des informations portant sur l'accueil/transfert du corps"."\n";
            $mod_contact = 1;
            $suppr_list_array["Prise_en_charge_au_d_c_s.Date_d_arriv_e_au_CDC"] = "";
            $suppr_list_array["Transfert_vers_autre_centre.CDC_de_transfert"] = "";
            $suppr_list_array["Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps"] = "";
            $suppr_list_array["Transfert_vers_autre_centre.Date_de_transfert_vers_un_autre_CDC"] = "";
            break;

          case 'Opp_fun';
            echo "    - Suppression des informations portant sur les opérations funéraires"."\n";
            $mod_contact = 1;
            $suppr_list_array["Devenir_du_corps.devenir_effectif_du_corps"] = "";
            $suppr_list_array["Devenir_du_corps.Date_de_sortie_d_finitive"] = "";
            $suppr_list_array["Devenir_du_corps.Date_op_rations_fun_raires"] = "";
            $suppr_list_array["Devenir_du_corps.Date_approximative_de_r_alisation_des_op_rations_fun_raires"] = "";
            $suppr_list_array["Transfert_vers_autre_centre.Date_de_retour_du_corps_cendres"] = "";
            $suppr_list_array["Transfert_vers_autre_centre.Date_de_r_c_ption_du_corps_ou_des_cendres"] = "";
            $suppr_list_array["Devenir_du_corps.Date_de_restitution"] = "";
            break;

          case 'Coord':
            echo "    - Suppression adresses, emails, téléphones"."\n";
            $supp_adresses = 1;
            break;

          case 'Doc':
            echo "    - Suppression documents, notes"."\n";
            $supp_docs = 1;
            $mod_contact = 1;
            $suppr_list_array["Promesse_de_don.RGPD_sign_e"] = "";
            break;

          case 'Relat':
            echo "    - Suppression relations"."\n";
            $supp_relats = 1;
            $mod_contact = 1;
            $suppr_list_array["Devenir_du_corps.Pompes_fun_bres_mandat_es_par_proches"] = "";
            $suppr_list_array["Devenir_du_corps.Souhait_funeraire_personne_ref_rente"] = "";

            break;

          case 'Usage':
            echo "    - Suppression données usage corps/pièces: avis CESP"."\n";
            $supp_usage = 1;
            $mod_contact = 1;
            $suppr_list_array["Devenir_du_corps.CESP"] = "";
            $suppr_list_array["Devenir_du_corps.ref_avis_CESP"] = "";
            $suppr_list_array["Devenir_du_corps.D_cision_de_suspension_acc_s_au_corps"] = "";
            break;

          case 'Group':
            echo "    - Suppression Groupes"."\n";
            $supp_group = 1;
            break;

          case 'Event':
            echo "    - Suppression Evenements"."\n";
            $supp_event = 1;
            break;

          default:
            purge_help();
            return;
            break;

        }
  }

echo "\n";



// Suppression des contacts
if ($supp_contact == 1){

  $result = civicrm_api4('Contact', 'get', [  /// liste les contacts avant de les supprimer
    'where' => [
       ['groups:label', 'IN', [$grp]],
    ],
    'checkPermissions' => FALSE,
  ]);

  // Boucle pour chacun des contacts du groupe : affiche le contact et le log
  foreach ($result as $contact) {
    $id = $contact['id'];
    echo " - Suppression Contact :".$contact['display_name']." (".$id.")\n";
    $logtext = $today.",".$grp.",".$id.",".$contact['last_name'].",".$contact['first_name']."\n";
#    echo $logtext;
    fwrite($fp, $logtext);
  }


  $result = civicrm_api4('Contact', 'delete', [  // supprime effectivement les contacts
    'where' => [
       ['groups:label', 'IN', [$grp]],
    ],
    'checkPermissions' => FALSE,
  ]);

  return;
} // Fin boucle Suppression des contacts




// Modification des contacts sans destruction
$result = civicrm_api4('Contact', 'get', [
  'where' => [
     ['groups:label', 'IN', [$grp]],
  ],
  'checkPermissions' => FALSE,
]);


foreach ($result as $contact) {
  $id = $contact['id'];
  $name = $contact['display_name'];
  echo " - Traitement Contact :".$name." (id:".$id.")"."\n";

  $logtext = $today.",".$grp.",".$id.",".$contact['last_name'].",".$contact['first_name']."\n";
# echo $logtext;
  fwrite($fp, $logtext);


  if ($contact["birth_date"]){                // copie l'année de naissance seule sans la date dans un champ Compl_m_nt_tat_civil.Ann_e_naissance
//    $birthyear = ($contact['birth_date']);
    $birthyear = substr($contact['birth_date'],0,4);
#    echo "année naissance : ".$birthyear."\n";
    $results = civicrm_api4('Contact', 'update', [
        'values' => [
          'Compl_m_nt_tat_civil.Ann_e_naissance' => $birthyear
          ],

        'where' => [
          ['id', '=', $id],
        ],
        'checkPermissions' => FALSE,
      ]);
  }

  if ($contact["deceased_date"]){                // copie l'année de DC seule sans la date dans un champ Compl_m_nt_tat_civil.Ann_e_naissance
    $dcyear = substr($contact['deceased_date'],0,4);
#    echo "année DC : ".$dcyear."\n";
    $results = civicrm_api4('Contact', 'update', [
        'values' => [
          'Compl_m_nt_tat_civil.Ann_e_de_d_c_s_auto_' => $dcyear
          ],

        'where' => [
          ['id', '=', $id],
        ],
        'checkPermissions' => FALSE,
      ]);
  }

//print_r($suppr_list_array);

  // Modification table contact
    if ($mod_contact == 1){
      echo "     Tables: contact";
      $results = civicrm_api4('Contact', 'update', [
        'values' =>
          $suppr_list_array,
        'where' => [
          ['id', '=', $id],
        ],
        'checkPermissions' => FALSE,
      ]);

      if ($tag_atcd==1){  // si les ATCD sonnt modifies : mise tag 'ATCD_Purges'

        $entityTags = civicrm_api4('EntityTag', 'get', [   // on regarde si ce contact a déja le tag ATCD Purges
          'where' => [
            ['entity_table', '=', 'civicrm_contact'],
            ['entity_id', '=', $id],
            ['tag_id:name', '=', 'ATCD Purges'],
          ],
          'checkPermissions' => FALSE,
        ]);

        if(!isset($entityTags[0])){  // si le tag ATCD Purges n'est pas mis
            $results = civicrm_api4('EntityTag', 'create', [  // on le met pour éviter purges itératives
              'values' => [
              'entity_table' => 'civicrm_contact',
              'entity_id' => $id,
              'tag_id:name' => 'ATCD Purges',
              ],
              'checkPermissions' => FALSE,
            ]);
        } // fin de verification / creation tag
      }

  } // fin modification table contact

  // Destruction coordonnées de ce contact
    if($supp_adresses == 1){
    // Destruction adresses de ce contact
       echo ", adresses";
       $results = civicrm_api4('Address', 'delete', [
        'where' => [
          ['contact_id', '=', $id],
        ],
        'checkPermissions' => FALSE,
      ]);

    // Destruction de tous les mails de ce contact
   		 echo ", mails";
       $results = civicrm_api4('Email', 'delete', [
        'where' => [
         ['contact_id', '=', $id],
        ],
        'checkPermissions' => FALSE,
       ]);

  	// Destruction de tous les téléphones de ce contact
 		   echo ", téléphones";
       $results = civicrm_api4('Phone', 'delete', [
        'where' => [
          ['contact_id', '=', $id],
        ],
      'checkPermissions' => FALSE,
      ]);

    }  // fin boucle modification coordonnées


  // Destruction documents, notes activités de ce contact
    if($supp_docs == 1){

   	// Destruction de toutes les notes de ce contact
      echo ", notes";
      $results = civicrm_api4('Note', 'delete', [
        'where' => [
          ['entity_id', '=', $id],
        ],
        'checkPermissions' => FALSE,
      ]);

   	// Destruction de tous les documents de ce contact
      echo ", documents";
      $results = civicrm_api4('DocumentContact', 'delete', [
        'where' => [
          ['contact_id', '=', $id],
        ],
        'checkPermissions' => FALSE,
      ]);

    // Destruction des activités liées à ce contact
      echo ", activités";
      $results = civicrm_api4('ActivityContact', 'delete', [
         'where' => [
           ['contact_id', '=', $id],
        ],
         'checkPermissions' => FALSE,
       ]);
    }  // fin boucle modification notes et documents

  // Destruction relations de ce contact
    if($supp_relats==1){
      echo ", relations";
      $results = civicrm_api4('Relationship', 'delete', [
        'where' => [
          ['contact_id_a.id', '=', $id],
        ],
        'checkPermissions' => FALSE,
      ]);

      $results = civicrm_api4('Relationship', 'delete', [
        'where' => [
          ['contact_id_b.id', '=', $id],
        ],
        'checkPermissions' => FALSE,
      ]);


      $entityTags = civicrm_api4('EntityTag', 'get', [
        'where' => [
          ['entity_table', '=', 'civicrm_contact'],
          ['entity_id', '=', $id],
          ['tag_id:name', '=', 'Relations Purgees'],
        ],
        'checkPermissions' => FALSE,
      ]);

      foreach ($entityTags as $row) {
        $tag_id = $row['id'];
      }

      // si $tag_id non nul, le tag est déja mis pas

      //if(!$tag_id){  // si le tag n'est pas mis
      if(!isset($tag_id)){  // si le tag n'est pas mis
          $results = civicrm_api4('EntityTag', 'create', [  // Tag le contact pour éviter purges itératives
            'values' => [
             'entity_table' => 'civicrm_contact',
             'entity_id' => $id,
             'tag_id:name' => 'Relations Purgees',
             ],
            'checkPermissions' => FALSE,
          ]);
      } // fin de verification / creation tag


    }   // Fin boucle destruction relations de ce contact


  // Destruction usages de ce contact
    if($supp_usage==1){
      echo ", utilisation";

    $results = civicrm_api4('Custom_Utilisation_du_corps', 'delete', [
      'where' => [
        ['entity_id', '=', $id],
        ['Mode_limination_hors_corps_2:name', '<>', 'Non_limin_e'],             // ne supprime pas les corps/pieces non éliminées
        ['Mode_limination_hors_corps_2:name', '<>', 'Conservation_illimit_e'],  // ne supprime pas les corps/pieces conservées avec délai ilimité
        ['Mode_limination_hors_corps_2:name', '<>', 'Demander_cr_mation'],      // ne supprime pas les corps/pieces dont la crémation est à demander
        ['Mode_limination_hors_corps_2:name', '<>', 'Cr_mation_demand_e'],      // ne supprime pas les corps/pieces dont la crémation est demandée
        ['Protocole_de_recherche_ex_vivo2:name', '=', 'Pas_de_protocole'],      // ne supprime pas les corps/pieces inclus ds protocole
        //['Date_limination_pi_ce', 'IS NOT EMPTY'],
      ],
      'checkPermissions' => FALSE,
    ]);

     }   // Fin boucle usage de ce contact

  // Modification des groupes
    if ($supp_group == 1){
       	// Destruction de l'appartenance de ce contact à des groupes
   	echo ", groupes";
    $results = civicrm_api4('GroupContact', 'delete', [
      'where' => [
        ['contact_id.id', '=', $id],
      ],
      'checkPermissions' => FALSE,
    ]);

  	// Rattachement de ce contact au groupe Archives
    $results = civicrm_api4('GroupContact', 'create', [
      'values' => [
        'contact_id' => $id,
        'status' => 'Added',
        'group_id:label' => 'Archives',
      ],
      'checkPermissions' => FALSE,
    ]);

    } // fin modification des groupes


   	// Destruction des inscriptions à des evenements pour ce contact
   	if ($supp_event == 1){
   	  echo ", évenements";
      $results = civicrm_api4('Participant', 'delete', [
       'where' => [
       ['contact_id', '=', $id],
       ],
        'checkPermissions' => FALSE,
      ]);
   	} // fin Destruction des inscriptions à des evenements pour ce contact


 		// crée une note informant des modifications
   	echo ", création note"."\n";
 		unset($note_array);
 		$note_array["entity_id"] = $id;
 		$note_array["note"] = "Purge automatique";
 		$note_array["subject"] = $grp;
 		$note_array["contact_id"] = '1';

    $results = civicrm_api4('Note', 'create', [
      'values' =>
          $note_array,
      'checkPermissions' => FALSE,
    ]);
} // end for each contact loop

fclose($fp);  // ferme le fichier de log

} // fin definition fonction de purge

///////////////////////////

// CREATION DU GROUPE Archives s'il n'existe pas

$groups = civicrm_api4('Group', 'get', [
  'where' => [
    ['title', '=', 'Archives'],
  ],
  'checkPermissions' => FALSE
]);

//echo $groups['id'];

foreach ($groups as $row) {
$arch = $row['id'];
 }

if ($arch <> 0) {
  echo "Le groupe Archive existe (id: ",$arch,") - pas de création","\n";
} else {
  echo "Le groupe Archive n'existe pas - création","\n";

    $results = civicrm_api4('Group', 'create', [
    'values' => [
      'title' => 'Archives',
      'description' => 'Donneurs ayant été purgés',
      'is_active' => TRUE,
    ],
    'checkPermissions' => FALSE,
  ]);

} // endif creation groupe Archives


// CREATION DU TAG Relations_purgees s'il n'existe pas

$tags = civicrm_api4('Tag', 'get', [
  'where' => [
    ['name', '=', 'Relations Purgees'],
  ],
  'checkPermissions' => FALSE,
]);


foreach ($tags as $row) {
$tag_id = $row['id'];
 };

if ($tag_id <> 0) {
  echo "Le tag  Relations Purgees existe (id: ",$tag_id,") - pas de création","\n";
} else {
  echo "Le tag  Relations Purgees n'existe pas - création","\n";

  $results = civicrm_api4('Tag', 'create', [
    'values' => [
      'name' => 'Relations Purgees',
      'description' => 'Donneurs dont les relations ont été purgées - évite des purges multiples',
    ],
    'checkPermissions' => FALSE,
  ]);

} // endif creation CREATION DU TAG Relations_purgees


// CREATION DU TAG ATCD purges s'il n'existe pas
unset ($tags);
unset ($tag_id);

$tags = civicrm_api4('Tag', 'get', [
  'where' => [
    ['name', '=', 'ATCD Purges'],
  ],
  'checkPermissions' => FALSE,
]);


foreach ($tags as $row) {
$tag_id = $row['id'];
 };

if ($tag_id <> 0) {
  echo "Le tag  ATCD Purges existe (id: ",$tag_id,") - pas de création","\n";
} else {
  echo "Le tag  ATCD Purges n'existe pas - création","\n";

  $results = civicrm_api4('Tag', 'create', [
    'values' => [
      'name' => 'ATCD Purges',
      'description' => 'Donneurs non inclus dans protocole dont les ATCD ont été purges - évite des purges multiples',
    ],
    'checkPermissions' => FALSE,
  ]);

} // endif creation CREATION DU TAG ATCD purges



#exit; // a decommenter pour juste crere groupes et tags



// PURGE DES DEMANDEURS D'INFORMATION N'AYANT PAS DONNE SUITE APRES UN AN #############################################
//
// décret du 11 juillet 2023, ARTICLE 4 - I
//    "Les données à caractère personnel et informations mentionnées au 1 de l’article 3 [nom, prénom, coordonnées] sont conservées
//    "pendant un délai d’un an à compter de l’envoi du document d’information."
//

purge('demandeurs plus un an', 'Delete');


// FIN DE LA PURGE DES DEMANDEURS D'INFORMATION N'AYANT PAS DONNE SUITE APRES UN AN ###################################


// PURGE DES DONNEURS 5 ANS APRES OPERATIONS FUNERAIRES ###############################################################
//
// arrêté du 11 juillet 2023, ARTICLE 4 - II
//    "Les données à caractère personnel et informations mentionnées au 2o de l’article 3 sont conservées"
//    "pendant un délai de 5 ans à compter de la date des opérations funéraires ou de la restitution du corps ou des cendres:"
//      "- données mentionnées aux a:
//          -nom, prénom [Anon]
//          -sexe [Genre]
//          -date de naissance [DN]
//
//      "- données mentionnées au b : adresse postale et electronique, telephone [Coord]
//
//      "- déclaration de don, [Doc]
//
//      "- données mentionnées au d :
//          -personne référente,[Relat]
//          -restitution de son corps ou de ses cendres, [Choix]
//          -préférence sur le type d’opération funéraire, [Choix]
//          -opposition éventuelle à l’invitation de la personne référente, de la famille ou des proches à la cérémonie du souvenir [Choix]
//          -inscription mémorielle [Choix]
//
//      "- données mentionnées au e : antécédents médicaux [ATCD] 
              // ajouter ATCD dans les options pour suppression immédiate
              // sinon son sont supprimés dans un  second temps (cf. purge('ATCD Anonymises sans protocole','ATCDnoProt');)
              // pour conserver les ATCD de personnes incluses ds protocole
//
//      "- données mentionnées au f :
//          -date de DC, [DC]
//          -extrait de certificat de DC [Doc]
//
//      "- données mentionnées au g :
//          - date d’accueil du corps, [Accueil]
//          - décision refus d'accueillir le corps,[Accueil]
//          - décision de transfert, nom de l’établissement auquel le corps est transféré[Accueil]
//          - date de transfert[Accueil]
//
//      "- données mentionnées au h : numéro unique attribué au corps, numéro unique attribué aux pièces anatomiques en cas de segmentation du corps
//
//      "- données mentionnées au i : Données relatives à l’utilisation du corps du donneur:
//          - projets de formation ou de recherche,
//          - durée de conservation du corps ou des pièces anatomiques en cas de dépassement de la durée règlementaire
//          - date de sortie du corps de la structure d’accueil
//          - entité bénéficiaire,
//          - date de retour prévue,
//          - décision de suspension ou d’interdiction d’accès au corps
//          - le cas échéant impossibilité de restauration du corps;
//
//      "- données mentionnées au j : Données relatives aux opérations funéraires ou à la restitution du corps ou des cendres:
//          - type d’opération funéraire retenu par l’établissement,
//          - le cas échéant impossibilité de restitution du corps
//          - date de sortie du corps en vue des opérations funéraires,
//          - date de réalisation des opérations funéraires,
//          - date de sortie du corps de l’établissement auquel le corps a été transféré vers l’établissement qui a délivré la carte,
//          - date d’accueil du corps par l’établissement ayant délivré la carte en vue de la restitution du corps ou des cendres,
//          - date de restitution du corps ou des cendres;

 //purge('Op funeraires de plus de 5 ans','Anon','Genre','DN','LieuN','Coord','Doc','Relat','ATCDnoProt','Choix','DC','DateDC','Accueil','Usage','Opp_fun','Group','Event');

 purge('Op funeraires de plus de 5 ans','Anon','DN','LieuN','Coord','Doc','Relat','Choix','DC','DateDC','Accueil','Opp_fun','Group','Event');

// FIN DE LA PURGE DES DONNEURS 5 ANS APRES OPERATIONS FUNERAIRES #####################################################


// PURGE DES DONNEURS ANNULES #########################################################################################
//
// décret du 11 juillet 2023, ARTICLE 4 - III
//    "En cas de révocation du consentement au don du corps,
//    "les données sont supprimées à compter de l’enregistrement de la révocation.
//    "Le numéro de carte de donneur est rendu anonyme. Il ne peut être réutilisé."
//

purge('dons annulés sans donneurs en archive deja purges','Anon','Genre','DN','LieuN','Coord','Doc','Relat','ATCD','Choix','Group','Event');
// FIN DE LA PURGE DES DONNEURS ANNULES ###############################################################################


// PURGE DES PROCHES DONT LE DONNEUR EST DCD DEPUIS PLUS D'UN AN ######################################################
//
// décret du 11 juillet 2023, ARTICLE 4 - IV
//    "Les données à caractère personnel et informations mentionnées aux 3o et 4o de l’article 3
//    "sont conservées pendant un délai d’un an à compter de la date des opérations funéraires ou de la restitution du corps ou des cendres
//    "ou supprimées à la date fixée au VIII lorsque l’établissement n’est pas informé du décès du donneur."
//
// Ces données sont celles relatives à la personne référente, à la famille ou aux proches:
//    - données d’identité: nom, prénom;[Anon pour le proche]
//    - Coordonnées: adresse postale, adresse électronique, téléphone;[Coord pour le proche]
//    - Date de réception du document d’information; (courrier)
//    - choix de demander la restitution du corps ou des cendres,[Relat pour le donneur]
//    - préférence sur le type d’opération funéraire souhaité, [Relat pour le donneur]
//    - Données relatives à la cérémonie du souvenir, [Event pour le donneur]
//    - coordonnées de l’opérateur de pompes funèbres choisi par la personne référente; [Relat pour le donneur]
//    - Date d’information de la date de la cérémonie du souvenir (courrier)
//
// La purge se fait en deux etapes
//  1/ les relations des donneurs decedes depuis plus d'un an sont supprimées (traité ici)
//  2/ les contacts de sous type "Proche" sans relation sont supprimés (traité à l'étape PURGE DES PROCHES SANS RELATION)
//

purge('op funeraires plus un an','Relat','Event');  // suppression des relations des donneurs décédés depuis plus d'un an
// FIN DE LA PURGE PURGE DES PROCHES DONT LE DONNEUR EST DCD DEPUIS PLUS D'UN AN ######################################


// PURGE DES DONNEURS REFUSES DECEDES DEPUIS PLUS D'UN AN #############################################################
//
// décret du 11 juillet 2023, ARTICLE 4 - V
//    "En cas de refus d’accueil d’un corps fondé sur les circonstances du décès ou l’état de conservation du corps,
//    "les données mentionnées aux 2o à 4o de l’article 3 sont conservées pendant un délai d’un an après le décès.
//      2 : données relatives au donneur (traité ici), y compris les relations de ce donneur
//      3 et 4 : données relatives aux proches (traité à l'étape PURGE DES PROCHES SANS RELATION)

purge('dons refuses plus un an','Anon','Genre','DN','LieuN','Coord','Doc','Relat','ATCD','Choix','DC','DateDC','Accueil','Usage','Opp_fun','Group','Event');
// FIN DE LA PURGE DES DONNEURS REFUSES DECEDES DEPUIS PLUS D'UN AN  ##################################################


// PURGE DES PERSONNELS PARTIS DEPUIS PLUS DE 5 ANS ####################################################################
//
// décret du 11 juillet 2023, ARTICLE 4 - VI.
//    "Les données à caractère personnel et informations mentionnées au 5o de l’article 3 sont conservées
//    "pendant un délai de 5 ans à compter, suivant le cas, de la fin des fonctions,
//    "de la fin de l’habilitation ou de l’autorisation ou de la fin du projet.
//
// Il s'agit des données relatives aux personnels: (delete)
//    - responsable de la structure d’accueil des corps,
//    - personnels habilités,
//    - personnels techniques de la structure d’accueil des corps,
//    - personnels concernés par les activités d’enseignement médical et de recherche,
//    - personnels titulaires d’une autorisation expresse délivrée par le responsable de la structure d’accueil des corps
//
//    - nom, prénom,
//    - qualité, service d’affectation,
//    - adresse électronique professionnelle, téléphone professionnel;

purge('Personnels partis plus 5 ans','Delete');
// FIN DE LA PURGE DES PERSONNELS PARTIS DEPUIS PLUS DE 5 ANS  #########################################################

// PURGE DES DONNEES RELATIVES AUX ENTITES EXTERIEURES #################################################################
//
// décret du 11 juillet 2023, ARTICLE 4 - VI.
//    "VII. – Les données à caractère personnel et informations mentionnées au 6o de l’article 3
//    "sont conservées pendant un délai de 5 ans à compter de l’échéance de la convention prévue à l’article R. 1261
//
// il s'agit des données relatives aux entités distinctes de l’établissement autorisé:
//     - nom du responsable du projet de formation ou de recherche, prénom, qualité, adresse électronique professionnelle.
//

// POUR LE MOMENT CES DONNÉES NE SONT PAS COLLECTÉES DANS CETTE BASE



// FIN DE LA PURGE DES PURGE DES DONNEES RELATIVES AUX ENTITES EXTERIEURES  ###########################################

// PURGE DES DONNEURS DE PLUS DE 120 ANS ##############################################################################
//
// décret du 11 juillet 2023, ARTICLE 4 - VIII
//    "Les données correspondant à un donneur dont l’établissement n’aurait pas été informé du décès sont supprimées"
//    "à la date à laquelle le donneur atteint l’âge théorique de cent-vingt ans."
//    "Le numéro de carte de donneur est rendu anonyme. Il ne peut être réutilisé."
//

purge('don plus 120 ans','Anon','Genre','DN','LieuN','Coord','Doc','Relat','ATCD','Choix','DC','DateDC','Accueil','Usage','Opp_fun','Group','Event');
// FIN DE LA PURGE DES DONNEURS DE PLUS DE 120 ANS ##############################################################################


// PURGE DES ATCD en l'absence de protocole ex vivo ##############################################################################
//
// préserve les ATCD des donneurs inclus dans sun protocole ex vivo
//    Les donneurs sont anonymisés par les autres appels de la fonction purge
//    Le groupe 'ATCD Anonymises sans protocole' contient les contacts
//          - préalalement anonymisés,
//          - et non inclus dans un protocole
//          - et non tagés 'ATCD Purges'
//    La fonction purge supprime les ATCD et pose un tag 'ATCD Purges'
//

purge('Archives sans protocole in ni ex vivo','ATCD','Usage',);
// FIN DE LA PURGE DES DONNEURS DE PLUS DE 120 ANS ##############################################################################




// PURGE DES PROCHES SANS RELATION ####################################################################################
// cette étape supprime les proches dont toutes les relations ont été supprimées
purge('Proches sans relation','Delete');

// FIN DE LA PURGE DES PROCHES SANS RELATION ##########################################################################


  }
