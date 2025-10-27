<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;


function update_search2(){
    //////////// update_search() /////////
    // Cette fonction est invoquée à en post installation pour changer les icones de certains tabs de layouts et en inactiver d'autres
    //
    // syntaxe : update_search($searchname, $inactive_tabs);
    //
    //      $$searchname : nom der la recherche
    //      $search_api_params : parametres des api à modifier
    //      $tag [optionnel] : tag à ajouter 
    //
    // elle est appelée par : function don_corps_civicrm_postinstall()
    //////////////
    ##### update des requetes
      $nbargs = func_num_args();
      $searchname = func_get_arg(0);              // nom de la recherche sauvegardée à modifier
      $search_api_params = func_get_arg(1);       // parametres de l'api
      
      if (func_num_args()==3){                    // s'il y a 3 arguments, c'est qu'il y a un tag
          $tags = civicrm_api4('Tag', 'get', [
            'select' => [
              'id',
            ],
            'where' => [
              ['name', '=', func_get_arg(2)],
            ],
            'checkPermissions' => FALSE,
          ]);
    
          if ($tags[0]['id']!=0){
            $tag= func_get_arg(2);                  // valeur du tag s'il existe sinon nul
          } else {
            $tag="";
          }
        
      }
      //echo "Tag : ".$tag.PHP_EOL;
      
      $savedSearches = civicrm_api4('SavedSearch', 'get', [
        'select' => [
          'id',
        ],
        'where' => [
          ['name', '=', $searchname],
        ],
        'checkPermissions' => FALSE,
      ]);
    
      //echo "id : ".$savedSearches[0]['id'].PHP_EOL;
    
      if($savedSearches[0]['id']!=0){      // si la requete existe on l'update
        $results = civicrm_api4('SavedSearch', 'update', [
          'values' => [
            'api_params' => $search_api_params,
          ],
          'where' => [
            ['id', '=', $savedSearches[0]['id']],
          ],
          'checkPermissions' => FALSE,
        ]);
  
        if ($results['is_error']) {
          echo "    Erreur lors de la MAJ de la requete ".$searchname.PHP_EOL;
          CRM_Core_Session::setStatus('Erreur lors de la MAJ de la requete '.$searchname, 'Erreur', 'error');
        } else {
          echo "     - Requete ".$searchname." MAJ".PHP_EOL;
          CRM_Core_Session::setStatus('MAJ réussie de la requete '.$searchname, 'Succès', 'success');
        }
    
        if ($tag!=""){                                       /// la requete existe et le tag est non nul
    
          $entityTags = civicrm_api4('EntityTag', 'get', [
            'select' => [
              'id',
            ],
            'where' => [
              ['entity_table', '=', 'civicrm_saved_search'],
              ['entity_id', '=', $results[0]['id']],
            ],
            'checkPermissions' => FALSE,
          ]);
    
          if ($entityTags[0]['id']==0) {       // si le tag n'est pas associé à ce contact
              $tags = civicrm_api4('EntityTag', 'create', [      /// associe le tag purge à la reqauete
                'values' => [
                  'entity_table' => 'civicrm_saved_search',
                  'tag_id.name' => $tag,
                  'entity_id' => $results[0]['id'],
                ],
                'checkPermissions' => FALSE,
              ]);
          echo "      & Tag ajouté".PHP_EOL;
          }          
    
        }
      } else {                                              // si la requete n'exste pas
        echo "La requete ".$searchname." n'existe pas".PHP_EOL;
      }
  }// Fin de la définition de la fonction : update_search() 
  


$searchname = 'Tokens_for_contact';                   // % requete tokens : tokens des contacts     
$api_params =[ 'version' => 4, 'select' => [ 'Promesse_de_don.N_de_don', 'deceased_date', 'Promesse_de_don.Centre_de_don:label', 'Compl_m_nt_tat_civil.Ville_de_naissance', 'Promesse_de_don.Devenir_souhait_:label', 'Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie:label', 'Promesse_de_don.Souhait_lecture_nom:label', 'Promesse_de_don.Souhiat_affichage_st_le:label', 'Promesse_de_don.Refus_personne_referente', 'id', 'CURDATE()', ], 'orderBy' => [], 'where' => [], 'groupBy' => [], 'join' => [], 'having' => [], ];
update_search2($searchname, $api_params, 'tokens');



