<?php
eval(`cv php:boot`);
use CRM_CiviDdc_ExtensionUtil as E;
$exp_dir = '/Users/destri_c/Desktop/importLyon/';       // racine du répertoire d'import export


$ville = 'lyon';
## Pour Lyon
## pas d'export des precisions metiers des personnels
## pas d'export utilsaiton des corps techniques specifiques


$check_option_values = 1 ;
$check_custom_field = 1 ;
$import_organisations = 0 ;
$import_FinancialType = 0 ;
$import_individus = 0 ;
$import_groups = 0 ;
$import_adresses = 0 ;
$import_telephones = 0 ;
$import_email = 0 ;
$import_relationships = 0 ;
$import_utilisations = 0;
$import_arrivee = 0 ;
$import_protinvivo = 0 ;
$import_contributions = 0;
$import_events = 0 ;
$import_participants = 0 ;
$import_activites = 0 ;
$import_notes = 0 ;
$import_documentsContact = 0 ;
$import_documentsVersion = 0 ;
$import_files = 0 ;
$import_tags = 0 ;

function after ($thisv, $inthatv)
{
    if (!is_bool(strpos($inthatv, $thisv)))
    return substr($inthatv, strpos($inthatv,$thisv)+strlen($thisv));
};

function before ($thisv, $inthatv)
  {
      return substr($inthatv, 0, strpos($inthatv, $thisv));
  };



function export_lyon(){
  $opts=func_get_arg(0);

  print_r($opts);   

    # cahnge le titre de l'ancien groupe Informations Personnel en Informations Personnel OLD
    $results = civicrm_api4('CustomGroup', 'update', [
      'values' => [
        'title' => 'Informations Personnel',
      ],
      'where' => [
        ['name', '=', 'infos_personnel'],
      ],
      'checkPermissions' => FALSE,
    ]);

  # Custom group Promesse de don
    if($opts['migrate_Custom_Promesse_don'] == 1)
      {

        $stuff_to_update=array();    // remet à zero le tableau des choses à modifier


        ## Template, option group et option values pour : Pr_venir_personne_r_f_rence_de_la_c_r_monie 
        #   $original_custom_name = 'Promesse_de_don.Pr_venir_la_perosnne_r_f_rente';         // nom du champ dans la base originale
            
        #   $new_custom_name = 'Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie'; // nom du champ conforme à la nouvelle base 
              ## NOTEZ LES . au lieu de _ dans les variables

        #   $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
                                      // ajouter indice [0] si valeur unique
        #  [
        #   ];

          #  create_stuff($tocreate_things, $original_custom_name, $new_custom_name);





        # Custom field, option group et option values pour : Pr_venir_personne_r_f_rence_de_la_c_r_monie 
          $original_custom_name = 'Promesse_de_don.Pr_venir_la_perosnne_r_f_rente';         // nom du champ dans la base originale
          $new_custom_name = 'Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie'; // nom du champ conforme à la nouvelle base 
            ## NOTEZ LES . au lieu de _ dans les variables

          $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
          [
            [ 'name' => 'OptionGroup_Promesse_de_don_Pr_venir_personne_r_f_rence_de_la_c_r_monie',
              'entity' => 'OptionGroup',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'name' => 'Promesse_de_don_Pr_venir_personne_r_f_rence_de_la_c_r_monie',
                  'title' => E::ts('Promesse de don :: Prévenir personne référence de la cérémonie'),
                  'data_type' => 'Int',
                  'is_reserved' => FALSE,
                  'option_value_fields' => ['name', 'label', 'description'],
                  ],
                'match' => ['name'],
                ],
            ],

            [ 'name' => 'OptionGroup_Promesse_de_don_Pr_venir_personne_r_f_rence_de_la_c_r_monie_OptionValue_Volont_inconnue',
              'entity' => 'OptionValue',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'option_group_id.name' => 'Promesse_de_don_Pr_venir_personne_r_f_rence_de_la_c_r_monie',
                  'label' => E::ts('Volonté inconnue'),
                  'value' => '0',
                  'name' => 'Volont_inconnue',
                ],
                'match' => [
                  'option_group_id',
                  'name',
                  'value',
                ],
              ],
            ],

            ['name' => 'OptionGroup_Promesse_de_don_Pr_venir_personne_r_f_rence_de_la_c_r_monie_OptionValue_Oui',
              'entity' => 'OptionValue',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'option_group_id.name' => 'Promesse_de_don_Pr_venir_personne_r_f_rence_de_la_c_r_monie',
                  'label' => E::ts('Oui'),
                  'value' => '1',
                  'name' => 'Oui',
                ],
                'match' => [
                  'option_group_id',
                  'name',
                  'value',
                ],
              ],
            ],

            [ 'name' => 'OptionGroup_Promesse_de_don_Pr_venir_personne_r_f_rence_de_la_c_r_monie_OptionValue_Non',
              'entity' => 'OptionValue',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'option_group_id.name' => 'Promesse_de_don_Pr_venir_personne_r_f_rence_de_la_c_r_monie',
                  'label' => E::ts('Non'),
                  'value' => '2',
                  'name' => 'Non',
                ],
                'match' => [
                  'option_group_id',
                  'name',
                  'value',
                ],
              ],
            ],

            [ 'name' => 'CustomGroup_Promesse_de_don_CustomField_Pr_venir_personne_r_f_rence_de_la_c_r_monie',
              'entity' => 'CustomField',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'custom_group_id.name' => 'Promesse_de_don',
                  'name' => 'Pr_venir_personne_r_f_rence_de_la_c_r_monie',
                  'label' => E::ts('Prévenir référent de cérémonie'),
                  'data_type' => 'Int',
                  'html_type' => 'Select',
                  'default_value' => '0',
                  'text_length' => 255,
                  'note_columns' => 60,
                  'note_rows' => 4,
                  //'column_name' => 'pr_venir_personne_r_f_rence_de_l_69',
                  'option_group_id.name' => 'Promesse_de_don_Pr_venir_personne_r_f_rence_de_la_c_r_monie',
                ],
                'match' => [
                  'name',
                  'custom_group_id',
                ],
              ],
            ]
          ];

          $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);
          
        # Custom group, option group et option values pour :  Souait_affichage_sur_st_le
          $original_custom_name = 'Promesse_de_don.Souait_affichage_sur_st_le';         // nom du champ dans la base originale
              
          $new_custom_name = 'Promesse_de_don.Souhiat_affichage_st_le'; // nom du champ conforme à la nouvelle base 
                ## NOTEZ LES . au lieu de _ dans les variables

          unset($tocreate_things);
          $tocreate_things[0] =   /// définition du custom field, option group, option values (a recuperer depuis manages files)
                                  // ajouter indice [0] si valeur unique
          [ 'name' => 'CustomGroup_Promesse_de_don_CustomField_Souhiat_affichage_st_le',
            'entity' => 'CustomField',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'custom_group_id.name' => 'Promesse_de_don',
                'name' => 'Souhiat_affichage_st_le',
                'label' => E::ts('Souhait affichage stèle'),
                'data_type' => 'Int',
                'html_type' => 'Select',
                'default_value' => '0',
                'text_length' => 255,
                'note_columns' => 60,
                'note_rows' => 4,
                'column_name' => 'souhiat_affichage_st_le_68',
                'option_group_id.name' => 'Promesse_de_don_Souhait_lecture_nom',
              ],
              'match' => [
                'name',
                'custom_group_id',
              ],
            ],
          ];

          $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);

        # Custom group, option group et option values pour : Promesse_de_don.Souhait_lecture_nom
          $original_custom_name = 'Promesse_de_don.Souhait_lecture_nom';         // nom du champ dans la base originale
              
          $new_custom_name = 'Promesse_de_don.Souhait_lecture_nom'; // nom du champ conforme à la nouvelle base 
                ## NOTEZ LES . au lieu de _ dans les variables

          $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
              [[
            'name' => 'OptionGroup_Promesse_de_don_Souhait_lecture_nom',
            'entity' => 'OptionGroup',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'name' => 'Promesse_de_don_Souhait_lecture_nom',
                'title' => E::ts('Promesse de don :: Souhait lecture nom'),
                'data_type' => 'Int',
                'is_reserved' => FALSE,
                'option_value_fields' => ['name', 'label', 'description'],
              ],
              'match' => ['name'],
            ],
          ],

          [
            'name' => 'OptionGroup_Promesse_de_don_Souhait_lecture_nom_OptionValue_Volont_inconnue',
            'entity' => 'OptionValue',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'option_group_id.name' => 'Promesse_de_don_Souhait_lecture_nom',
                'label' => E::ts('Volonté inconnue'),
                'value' => '0',
                'name' => 'Volont_inconnue',
              ],
              'match' => [
                'option_group_id',
                'name',
                'value',
              ],
            ],
          ],
          [
            'name' => 'OptionGroup_Promesse_de_don_Souhait_lecture_nom_OptionValue_Oui',
            'entity' => 'OptionValue',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'option_group_id.name' => 'Promesse_de_don_Souhait_lecture_nom',
                'label' => E::ts('Oui'),
                'value' => '1',
                'name' => 'Oui',
              ],
              'match' => [
                'option_group_id',
                'name',
                'value',
              ],
            ],
          ],
          [
            'name' => 'OptionGroup_Promesse_de_don_Souhait_lecture_nom_OptionValue_Non',
            'entity' => 'OptionValue',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'option_group_id.name' => 'Promesse_de_don_Souhait_lecture_nom',
                'label' => E::ts('Non'),
                'value' => '2',
                'name' => 'Non',
              ],
              'match' => [
                'option_group_id',
                'name',
                'value',
              ],
            ],
          ],
          [
            'name' => 'CustomGroup_Promesse_de_don_CustomField_Souhait_lecture_nom',
            'entity' => 'CustomField',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'custom_group_id.name' => 'Promesse_de_don',
                'name' => 'Souhait_lecture_nom',
                'label' => E::ts('Souhait lecture nom'),
                'data_type' => 'Int',
                'html_type' => 'Select',
                'default_value' => '0',
                'is_searchable' => TRUE,
                'text_length' => 255,
                'note_columns' => 60,
                'note_rows' => 4,
                'column_name' => 'souhait_lecture_nom_67',
                'option_group_id.name' => 'Promesse_de_don_Souhait_lecture_nom',
              ],
              'match' => [
                'name',
                'custom_group_id',
              ],
            ],
          ],
              ];

          $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);

        update_stuff('Contact', $stuff_to_update);
      } # FIN CUSTOM GROUP PROMESSE DE DONS


  # Custom group ARRIVEE DU CORPS
    if($opts['migrate_Arrivee_corps'] == 1)
      {

        $stuff_to_update=array();    // remet à zero le tableau des choses à modifier

      ## dans les anciennes versions ce group et nomé Sérologies
    ## change le titre de l'ancien groupe arrivée du corps en Arrivée du corps OLD
        $results = civicrm_api4('CustomGroup', 'update', [
          'values' => [
            'title' => 'Arrivée du corps OLD',
          ],
          'where' => [
            ['name', '=', 'S_rologies'],
          ],
          'checkPermissions' => FALSE,
        ]); 

    ## crée le custom group Arriv_e_du_corps_new s'il n'existe pas 
        $customGroups = civicrm_api4('CustomGroup', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['name', '=', 'Arriv_e_du_corps_new'],
          ],
          'checkPermissions' => FALSE,
        ]);

        #print_r($customGroups);

    
         if (!isset($customGroups[0]))  

          {             ## sile groupe n'existe pas, on le crée
          
          echo "CREATION Custom group Arriv_e_du_corps_new".PHP_EOL;
          $results = civicrm_api4('CustomGroup', 'create', [
              'values' => [
                'name' => 'Arriv_e_du_corps_new',
                'title' => E::ts('Arrivée du corps'),
                'extends' => 'Individual',
                //'extends_entity_column_value' => ['Donateur'],
                'style' => 'Tab with table',
                'weight' => 4,
                'is_multiple' => TRUE,
                'max_multiple' => 1,
                'collapse_adv_display' => TRUE,
              ],
              'checkPermissions' => FALSE,
            ]);


          } else {      ## si le groupe existe on le MAJ
                    echo "Suppression de toutes les entrées Custom group Arriv_e_du_corps_new".PHP_EOL;
              $results = civicrm_api4('Custom_Arriv_e_du_corps_new', 'delete', [
              'where' => [
                ['id', '>', 0],
              ],
              'checkPermissions' => FALSE,
            ]); 
          } 

    ## custom field, option group et option values pour : Arriv_e_du_corps_new_CustomField_Signataire_certificat_de_d_c_s'
       $original_custom_group = 'S_rologies';
       $original_custom_field = 'Signataire_certificat_de_d_c_s';

       $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
        
       $new_custom_name = 'Arriv_e_du_corps_new.Signataire_certificat_de_d_c_s'; // nom du champ conforme à la nouvelle base 
          ## NOTEZ LES . au lieu de _ dans les variables

          $tocreate_things[0]=        /// définition du custom field, option group, option values (a recuperer depuis manages files)
            [
              'name' => 'CustomGroup_Arriv_e_du_corps_new_CustomField_Signataire_certificat_de_d_c_s',
              'entity' => 'CustomField',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'custom_group_id.name' => 'Arriv_e_du_corps_new',
                  'name' => 'Signataire_certificat_de_d_c_s',
                  'label' => E::ts('Signataire certificat de décès (nom, tel, ville)'),
                  'html_type' => 'Text',
                  'is_required' => TRUE,
                  'text_length' => 255,
                  'note_columns' => 60,
                  'note_rows' => 4,
                ],
                'match' => [
                  'name',
                  'custom_group_id',
                ],
              ],  
            ];


             ## change le label du custom field sinon creation impossible
          $customFields = civicrm_api4('CustomField', 'get', [
            'select' => [
              'id',
              'label',
            ],  
            'where' => [
              ['name', '=', $original_custom_field],
              ['custom_group_id:name', '=', $original_custom_group],
            ],
            'checkPermissions' => FALSE,
          ]);



          if (isset($customFields[0]))
            {
              $results = civicrm_api4('CustomField', 'update', [
                'values' => [
                  'label' => $customFields[0]['label'].'_OLD',
                ],
                'where' => [
                  ['id', '=', $customFields[0]['id']],
                ],
                'checkPermissions' => FALSE,
              ]);
            }
          echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD'.PHP_EOL;


          $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);

      # fin custom groupe ARRIVEE DU CORPS : signateire certificat

    ## custom field, option group et option values pour : Arriv_e_du_corps_new pace
       $original_custom_group = 'S_rologies';
       $original_custom_field = 'Retrait_Stimulateur_piles';

       $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
        
       $new_custom_name = 'Arriv_e_du_corps_new.Retrait_Stimulateur_piles'; // nom du champ conforme à la nouvelle base 
          ## NOTEZ LES . au lieu de _ dans les variables

          $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
            [
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_Retrait_Stimulateur_piles',
                'entity' => 'OptionGroup',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'name' => 'Arriv_e_du_corps_new_Retrait_Stimulateur_piles',
                    'title' => E::ts('Arrivée du corps new :: Retrait Stimulateur à piles'),
                    'data_type' => 'String',
                    'is_reserved' => FALSE,
                    'option_value_fields' => ['name', 'label', 'description'],
                  ],
                  'match' => ['name'],
                ],
              ],

              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_Retrait_Stimulateur_piles_OptionValue_pas_de_pacemaker',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_Retrait_Stimulateur_piles',
                    'label' => E::ts('pas de pacemaker'),
                    'value' => '1',
                    'name' => 'pas_de_pacemaker',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_Retrait_Stimulateur_piles_OptionValue_retrait_du_pacemaker_au_labo',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_Retrait_Stimulateur_piles',
                    'label' => E::ts('retrait du pacemaker au labo'),
                    'value' => '2',
                    'name' => 'retrait_du_pacemaker_au_labo',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_Retrait_Stimulateur_piles_OptionValue_retrait_du_pacemaker_avant_labo',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_Retrait_Stimulateur_piles',
                    'label' => E::ts('retrait du pacemaker avant labo'),
                    'value' => '3',
                    'name' => 'retrait_du_pacemaker_avant_labo',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_Retrait_Stimulateur_piles_OptionValue_A_v_rifier',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_Retrait_Stimulateur_piles',
                    'label' => E::ts('a vérifier'),
                    'value' => '4',
                    'name' => 'A vérifier',
                    'is_default' => TRUE,
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'CustomGroup_Arriv_e_du_corps_new_CustomField_Retrait_Stimulateur_piles',
                'entity' => 'CustomField',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'custom_group_id.name' => 'Arriv_e_du_corps_new',
                    'name' => 'Retrait_Stimulateur_piles',
                    'label' => E::ts('Retrait Stimulateur à piles'),
                    'html_type' => 'Select',
                    'default_value' => '4',
                    'text_length' => 255,
                    'note_columns' => 60,
                    'note_rows' => 4,
                    'option_group_id.name' => 'Arriv_e_du_corps_new_Retrait_Stimulateur_piles',
                    'in_selector' => TRUE,
                  ],
                  'match' => [
                    'name',
                    'custom_group_id',
                  ],
                ],
              ],  
            ];


             ## change le label du custom field sinon creation impossible
          $customFields = civicrm_api4('CustomField', 'get', [
            'select' => [
              'id',
              'label',
            ],  
            'where' => [
              ['name', '=', $original_custom_field],
              ['custom_group_id:name', '=', $original_custom_group],
            ],
            'checkPermissions' => FALSE,
          ]);

          if (isset($customFields[0]))
            {
              $results = civicrm_api4('CustomField', 'update', [
                'values' => [
                  'label' => $customFields[0]['label'].'_OLD',
                ],
                'where' => [
                  ['id', '=', $customFields[0]['id']],
                ],
                'checkPermissions' => FALSE,
              ]);
            }
          echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD'.PHP_EOL;


          $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);

          ## remplace la valeur 5 ('pas de case cochée' par 4 'a verifier')

          foreach ($stuff_to_update as $id => $stuff_to_updat)
            if ($stuff_to_updat['Retrait_Stimulateur_piles']==5)
              {
                $stuff_to_update[$id]['Retrait_Stimulateur_piles']='4';
              }
     # fin custom groupe ARRIVEE DU CORPS : pace

    ## custom field, option group et option values pour : Arriv_e_du_corps_new sero VIH
       $original_custom_group = 'S_rologies';
       $original_custom_field = 'S_rologie_VIH';

       $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
        
       $new_custom_name = 'Arriv_e_du_corps_new.S_rologie_VIH'; // nom du champ conforme à la nouvelle base 
          ## NOTEZ LES . au lieu de _ dans les variables

          $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
            [
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_VIH',
                'entity' => 'OptionGroup',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'name' => 'Arriv_e_du_corps_new_S_rologie_VIH',
                    'title' => E::ts('Arrivée du corps new :: Sérologie VIH'),
                    'data_type' => 'String',
                    'is_reserved' => FALSE,
                    'option_value_fields' => ['name', 'label', 'description'],
                  ],
                  'match' => ['name'],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_VIH_OptionValue_Non_realis_es',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_VIH',
                    'label' => E::ts('non realisée'),
                    'value' => '1',
                    'name' => 'Non_realis_es',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_VIH_OptionValue_Positive',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_VIH',
                    'label' => E::ts('positive'),
                    'value' => '3',
                    'name' => 'Positive',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_VIH_OptionValue_n_gative',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_VIH',
                    'label' => E::ts('négative'),
                    'value' => '2',
                    'name' => 'n_gative',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'CustomGroup_Arriv_e_du_corps_new_CustomField_S_rologie_VIH',
                'entity' => 'CustomField',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'custom_group_id.name' => 'Arriv_e_du_corps_new',
                    'name' => 'S_rologie_VIH',
                    'label' => E::ts('Sérologie VIH'),
                    'html_type' => 'Select',
                    'default_value' => '1',
                    'text_length' => 255,
                    'note_columns' => 60,
                    'note_rows' => 4,
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_VIH',
                    'in_selector' => TRUE,
                  ],
                  'match' => [
                    'name',
                    'custom_group_id',
                  ],
                ],
              ],
            ];


             ## change le label du custom field sinon creation impossible
          $customFields = civicrm_api4('CustomField', 'get', [
            'select' => [
              'id',
              'label',
            ],  
            'where' => [
              ['name', '=', $original_custom_field],
              ['custom_group_id:name', '=', $original_custom_group],
            ],
            'checkPermissions' => FALSE,
          ]);

          if (isset($customFields[0]))
            {
              $results = civicrm_api4('CustomField', 'update', [
                'values' => [
                  'label' => $customFields[0]['label'].'_OLD',
                ],
                'where' => [
                  ['id', '=', $customFields[0]['id']],
                ],
                'checkPermissions' => FALSE,
              ]);
            }
          echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD'.PHP_EOL;


          $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);

     # fin custom groupe ARRIVEE DU CORPS : sero VIH

    ## custom field, option group et option values pour : Arriv_e_du_corps_new sero HBV
       $original_custom_group = 'S_rologies';
       $original_custom_field = 'S_rologie_HBV';

       $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
        
       $new_custom_name = 'Arriv_e_du_corps_new.S_rologie_HBV'; // nom du champ conforme à la nouvelle base 
          ## NOTEZ LES . au lieu de _ dans les variables

          $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
            [
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_HBV',
                'entity' => 'OptionGroup',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'name' => 'Arriv_e_du_corps_new_S_rologie_HBV',
                    'title' => E::ts('Arrivée du corps new :: Sérologie HBV'),
                    'data_type' => 'String',
                    'is_reserved' => FALSE,
                    'option_value_fields' => ['name', 'label', 'description'],
                  ],
                  'match' => ['name'],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_HBV_OptionValue_non_r_alis_e',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_HBV',
                    'label' => E::ts('non réalisée'),
                    'value' => '1',
                    'name' => 'non_r_alis_e',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_HBV_OptionValue_positive',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_HBV',
                    'label' => E::ts('positive'),
                    'value' => '3',
                    'name' => 'positive',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_HBV_OptionValue_n_gative',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_HBV',
                    'label' => E::ts('négative'),
                    'value' => '2',
                    'name' => 'n_gative',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'CustomGroup_Arriv_e_du_corps_new_CustomField_S_rologie_HBV',
                'entity' => 'CustomField',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'custom_group_id.name' => 'Arriv_e_du_corps_new',
                    'name' => 'S_rologie_HBV',
                    'label' => E::ts('Sérologie HBV'),
                    'html_type' => 'Select',
                    'default_value' => '1',
                    'text_length' => 255,
                    'note_columns' => 60,
                    'note_rows' => 4,
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_HBV',
                    'in_selector' => TRUE,
                  ],
                  'match' => [
                    'name',
                    'custom_group_id',
                  ],
                ],
              ],
              
            ];


             ## change le label du custom field sinon creation impossible
          $customFields = civicrm_api4('CustomField', 'get', [
            'select' => [
              'id',
              'label',
            ],  
            'where' => [
              ['name', '=', $original_custom_field],
              ['custom_group_id:name', '=', $original_custom_group],
            ],
            'checkPermissions' => FALSE,
          ]);

          if (isset($customFields[0]))
            {
              $results = civicrm_api4('CustomField', 'update', [
                'values' => [
                  'label' => $customFields[0]['label'].'_OLD',
                ],
                'where' => [
                  ['id', '=', $customFields[0]['id']],
                ],
                'checkPermissions' => FALSE,
              ]);
            }
          echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD'.PHP_EOL;


          $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);

     # fin custom groupe ARRIVEE DU CORPS : sero HBV

    ## custom field, option group et option values pour : Arriv_e_du_corps_new sero COVID
       $original_custom_group = 'S_rologies';
       $original_custom_field = 'S_rologie_COVID';

       $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
        
       $new_custom_name = 'Arriv_e_du_corps_new.S_rologie_COVID'; // nom du champ conforme à la nouvelle base 
          ## NOTEZ LES . au lieu de _ dans les variables

          $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
            [
             [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_COVID',
                'entity' => 'OptionGroup',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'name' => 'Arriv_e_du_corps_new_S_rologie_COVID',
                    'title' => E::ts('Arrivée du corps new :: Sérologie COVID'),
                    'data_type' => 'String',
                    'is_reserved' => FALSE,
                    'option_value_fields' => ['name', 'label', 'description'],
                  ],
                  'match' => ['name'],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_COVID_OptionValue_non_r_alis_e',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_COVID',
                    'label' => E::ts('non réalisée'),
                    'value' => '1',
                    'name' => 'non_r_alis_e',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_COVID_OptionValue_positive',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_COVID',
                    'label' => E::ts('positive'),
                    'value' => '3',
                    'name' => 'positive',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Arriv_e_du_corps_new_S_rologie_COVID_OptionValue_n_gative',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_COVID',
                    'label' => E::ts('négative'),
                    'value' => '2',
                    'name' => 'n_gative',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'CustomGroup_Arriv_e_du_corps_new_CustomField_S_rologie_COVID',
                'entity' => 'CustomField',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'custom_group_id.name' => 'Arriv_e_du_corps_new',
                    'name' => 'S_rologie_COVID',
                    'label' => E::ts('Sérologie COVID'),
                    'html_type' => 'Select',
                    'default_value' => '1',
                    'text_length' => 255,
                    'note_columns' => 60,
                    'note_rows' => 4,
                    'option_group_id.name' => 'Arriv_e_du_corps_new_S_rologie_COVID',
                  ],
                  'match' => [
                    'name',
                    'custom_group_id',
                  ],
                ],
              ],
              
            ];


             ## change le label du custom field sinon creation impossible
          $customFields = civicrm_api4('CustomField', 'get', [
            'select' => [
              'id',
              'label',
            ],  
            'where' => [
              ['name', '=', $original_custom_field],
              ['custom_group_id:name', '=', $original_custom_group],
            ],
            'checkPermissions' => FALSE,
          ]);

          if (isset($customFields[0]))
            {
              $results = civicrm_api4('CustomField', 'update', [
                'values' => [
                  'label' => $customFields[0]['label'].'_OLD',
                ],
                'where' => [
                  ['id', '=', $customFields[0]['id']],
                ],
                'checkPermissions' => FALSE,
              ]);
            }
          echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD'.PHP_EOL;


          $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);

     # fin custom groupe ARRIVEE DU CORPS : sero COVID

    ## custom field, option group et option values pour : Arriv_e_du_corps_new PCR COVID
      $original_custom_group = 'S_rologies';
      $original_custom_field = 'S_rologie_VIH';

      $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
      
      $new_custom_name = 'Arriv_e_du_corps_new.PCR_COVID_nasopharyng_e'; // nom du champ conforme à la nouvelle base 
        ## NOTEZ LES . au lieu de _ dans les variables

        $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
          [
             [
              'name' => 'OptionGroup_Arriv_e_du_corps_new_PCR_COVID_nasopharyng_e',
              'entity' => 'OptionGroup',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'name' => 'Arriv_e_du_corps_new_PCR_COVID_nasopharyng_e',
                  'title' => E::ts('Arrivée du corps :: PCR COVID nasopharyngée'),
                  'data_type' => 'String',
                  'is_reserved' => FALSE,
                  'option_value_fields' => ['name', 'label', 'description'],
                ],
                'match' => ['name'],
              ],
            ],
            [
              'name' => 'OptionGroup_Arriv_e_du_corps_new_PCR_COVID_nasopharyng_e_OptionValue_non_r_alis_e',
              'entity' => 'OptionValue',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'option_group_id.name' => 'Arriv_e_du_corps_new_PCR_COVID_nasopharyng_e',
                  'label' => E::ts('non réalisée'),
                  'value' => '1',
                  'name' => 'non_r_alis_e',
                ],
                'match' => [
                  'option_group_id',
                  'name',
                  'value',
                ],
              ],
            ],
            [
              'name' => 'OptionGroup_Arriv_e_du_corps_new_PCR_COVID_nasopharyng_e_OptionValue_positive',
              'entity' => 'OptionValue',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'option_group_id.name' => 'Arriv_e_du_corps_new_PCR_COVID_nasopharyng_e',
                  'label' => E::ts('positive'),
                  'value' => '3',
                  'name' => 'positive',
                ],
                'match' => [
                  'option_group_id',
                  'name',
                  'value',
                ],
              ],
            ],
            [
              'name' => 'OptionGroup_Arriv_e_du_corps_new_PCR_COVID_nasopharyng_e_OptionValue_n_gative',
              'entity' => 'OptionValue',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'option_group_id.name' => 'Arriv_e_du_corps_new_PCR_COVID_nasopharyng_e',
                  'label' => E::ts('négative'),
                  'value' => '2',
                  'name' => 'n_gative',
                ],
                'match' => [
                  'option_group_id',
                  'name',
                  'value',
                ],
              ],
            ],
            [
              'name' => 'CustomGroup_Arriv_e_du_corps_new_CustomField_PCR_COVID_nasopharyng_e',
              'entity' => 'CustomField',
              'cleanup' => 'unused',
              'update' => 'unmodified',
              'params' => [
                'version' => 4,
                'values' => [
                  'custom_group_id.name' => 'Arriv_e_du_corps_new',
                  'name' => 'PCR_COVID_nasopharyng_e',
                  'label' => E::ts('PCR COVID nasopharyngée'),
                  'html_type' => 'Select',
                  'default_value' => '1',
                  'text_length' => 255,
                  'note_columns' => 60,
                  'note_rows' => 4,
                  'option_group_id.name' => 'Arriv_e_du_corps_new_PCR_COVID_nasopharyng_e',
                ],
                'match' => [
                  'name',
                  'custom_group_id',
                ],
              ],
            ],
          ];


            ## change le label du custom field sinon creation impossible
        $customFields = civicrm_api4('CustomField', 'get', [
          'select' => [
            'id',
            'label',
          ],  
          'where' => [
            ['name', '=', $original_custom_field],
            ['custom_group_id:name', '=', $original_custom_group],
          ],
          'checkPermissions' => FALSE,
        ]);

        if (isset($customFields[0]))
          {
            $results = civicrm_api4('CustomField', 'update', [
              'values' => [
                'label' => $customFields[0]['label'].'_OLD',
              ],
              'where' => [
                ['id', '=', $customFields[0]['id']],
              ],
              'checkPermissions' => FALSE,
            ]);
          }
        echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD'.PHP_EOL;


        $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);

        ## Correction du champ PCR COVID nasopharyngée qui utilise la meme liste de valeurs que sero VIH

        $serologies = civicrm_api4('Custom_S_rologies', 'get', [
          'select' => [
            'PCR_COVID',
          ],

          'orderBy' => [
              'id' => 'ASC',
          ],
          'checkPermissions' => FALSE,
        ]);

        foreach ($serologies as $serologie)
          {
              $stuff_to_update[$serologie['id']]['PCR_COVID_nasopharyng_e']=$serologie['PCR_COVID'];
          }
     # fin custom groupe ARRIVEE DU CORPS : PCR COVID


   


      $entity='Custom_Arriv_e_du_corps_new';


      
      echo PHP_EOL."MAJ des champs créés pour ".count($stuff_to_update)." ".$entity."(s)".PHP_EOL;

      foreach ($stuff_to_update as $id => $stuff_to_updat)
        {
          echo '.';

          
          $contact_id=$stuff_to_updat['entity_id'];
          //echo $contact_id.PHP_EOL;

          $arriveCorps = civicrm_api4('Custom_Arriv_e_du_corps_new', 'get', [ // on récupère les arrivées du corps new
                                                                              // pour le contact entity.id
              'select' => [
                'id',
              ],
                'where' => [
                ['entity_id.id', '=', $contact_id],
              ],
              'checkPermissions' => FALSE,
            ]);

            if(count($arriveCorps)==0)
              {             ## il n'existe aucune entréee arrivée du corps : on la crée

            
            $results = civicrm_api4('Custom_Arriv_e_du_corps_new', 'create', [
              'values' => $stuff_to_updat,
              'checkPermissions' => FALSE,
            ]);



              } else {      ## il existe une entréee arrivée du corps : on la MAJ
                $results = civicrm_api4($entity, 'update', [    // mise à jour de chaque occurence de l'entité
                  'values' => $stuff_to_update,
                  'where' => [
                    ['id', '=', $arrivees[0][$id],
                    ],
                  ],
                  'checkPermissions' => FALSE,
                ]);
              }
      }
      echo PHP_EOL;

   } # fin custom groupe ARRIVEE DU CORPS

  # Custom group utilisation
    if ($opts['migrate_utilisation_corps'] == 1)
      {
        $stuff_to_update=array();    // remet à zero le tableau des choses à modifier

      ## custom field, option group et option values pour : utilisation du corps / type de pièce 
       $original_custom_group = 'Utilisation_du_corps';
       $original_custom_field = 'type_de_pi_ce';

       $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
        
       $new_custom_name = 'Utilisation_du_corps.Type_de_poi_ce_3'; // nom du champ conforme à la nouvelle base 
          ## NOTEZ LES . au lieu de _ dans les variables

       $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
                                  // ajouter indice [0] si valeur unique
        [        
          [ 'name' => 'OptionGroup_Utilisation_du_corps_Type_de_poi_ce_3',
            'entity' => 'OptionGroup',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'name' => 'Utilisation_du_corps_Type_de_poi_ce_3',
                'title' => E::ts('Utilisation du corps :: Type de poièce 3'),
                'data_type' => 'String',
                'is_reserved' => FALSE,
                'option_value_fields' => ['name', 'label', 'description'],
              ],
              'match' => ['name'],
            ],
          ],

          [ 'name' => 'CustomGroup_Utilisation_du_corps_CustomField_Type_de_poi_ce_3',
            'entity' => 'CustomField',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'custom_group_id.name' => 'Utilisation_du_corps',
                'name' => 'Type_de_poi_ce_3',
                'label' => E::ts('Type de pièce'),
                'html_type' => 'Select',
                'default_value' => '1',
                'text_length' => 255,
                'note_columns' => 60,
                'note_rows' => 4,
                'option_group_id.name' => 'Utilisation_du_corps_Type_de_poi_ce_3',
                'serialize' => 1,
                'in_selector' => TRUE,
              ],
              'match' => [
                'name',
                'custom_group_id',
              ],
            ],
          ],
        
          [ 'name' => 'OptionGroup_Utilisation_du_corps_Type_de_poi_ce_3_OptionValue_Corps_entier',
            'entity' => 'OptionValue',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'option_group_id.name' => 'Utilisation_du_corps_Type_de_poi_ce_3',
                'label' => E::ts('Corps entier'),
                'value' => '1',
                'name' => 'Corps_entier',
              ],
              'match' => [
                'option_group_id',
                'name',
                'value',
              ],
            ],
          ],

          [ 'name' => 'OptionGroup_Utilisation_du_corps_Type_de_poi_ce_3_OptionValue_Membre_sup_rieur',
            'entity' => 'OptionValue',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'option_group_id.name' => 'Utilisation_du_corps_Type_de_poi_ce_3',
            'label' => E::ts('Membre supérieur'),
            'value' => '2',
            'name' => 'Membre_sup_rieur',
              ],
              'match' => [
                'option_group_id',
                'name',
                'value',
              ],
            ],
          ],

          [ 'name' => 'OptionGroup_Utilisation_du_corps_Type_de_poi_ce_3_OptionValue_Membre_inf_rieur',
            'entity' => 'OptionValue',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'option_group_id.name' => 'Utilisation_du_corps_Type_de_poi_ce_3',
            'label' => E::ts('Membre inférieur'),
            'value' => '3',
            'name' => 'Membre_inf_rieur',
              ],
              'match' => [
                'option_group_id',
                'name',
                'value',
              ],
            ],
          ],

          [ 'name' => 'OptionGroup_Utilisation_du_corps_Type_de_poi_ce_3_OptionValue_Extr_mit_c_phalique',
            'entity' => 'OptionValue',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'option_group_id.name' => 'Utilisation_du_corps_Type_de_poi_ce_3',
            'label' => E::ts('Face'),
            'value' => '4',
            'name' => 'Extr_mit_c_phalique',
              ],
              'match' => [
                'option_group_id',
                'name',
                'value',
              ],
            ],
          ],
  
          [ 'name' => 'OptionGroup_Utilisation_du_corps_Type_de_poi_ce_3_OptionValue_Enc_phale',
            'entity' => 'OptionValue',
            'cleanup' => 'unused',
            'update' => 'unmodified',
            'params' => [
              'version' => 4,
              'values' => [
                'option_group_id.name' => 'Utilisation_du_corps_Type_de_poi_ce_3',
            'label' => E::ts('Encéphale'),
            'value' => '5',
            'name' => 'Enc_phale',
              ],
              'match' => [
                'option_group_id',
                'name',
                'value',
              ],
            ],
          ],
        ];

          ## change le label du custom field sinon creation impossible
          $customFields = civicrm_api4('CustomField', 'get', [
            'select' => [
              'id',
              'label',
            ],  
            'where' => [
              ['name', '=', $original_custom_field],
              ['custom_group_id:name', '=', $original_custom_group],
            ],
            'checkPermissions' => FALSE,
          ]);

          if (isset($customFields[0]))
            {
              $results = civicrm_api4('CustomField', 'update', [
                'values' => [
                  'label' => $customFields[0]['label'].'_OLD',
                ],
                'where' => [
                  ['id', '=', $customFields[0]['id']],
                ],
                'checkPermissions' => FALSE,
              ]);
            }
          echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD';
          //print_r($customFields[0]);


          $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);
          

      ## FIN DE custom field, option group et option values pour : utilisation du corps / type de pièce 

      ## custom field, option group et option values pour : utilisation du corps / côte 
            $original_custom_group = 'Utilisation_du_corps';
            $original_custom_field = 'C_t_';

            $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
              
            $new_custom_name = 'Utilisation_du_corps.cote2'; // nom du champ conforme à la nouvelle base 
                ## NOTEZ LES . au lieu de _ dans les variables

            $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
                                        // ajouter indice [0] si valeur unique
              [
 


                [ 'name' => 'OptionGroup_Utilisation_du_corps_cote2',
                  'entity' => 'OptionGroup',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'name' => 'Utilisation_du_corps_cote2',
                      'title' => E::ts('Utilisation du corps :: cote2'),
                      'data_type' => 'String',
                      'is_reserved' => FALSE,
                      'option_value_fields' => ['name', 'label', 'description'],
                    ],
                    'match' => ['name'],
                  ],
                ],
               [
                  'name' => 'CustomGroup_Utilisation_du_corps_CustomField_cote2',
                  'entity' => 'CustomField',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'custom_group_id.name' => 'Utilisation_du_corps',
                      'name' => 'cote2',
                      'label' => E::ts('Côté'),
                      'html_type' => 'Select',
                      'default_value' => '3',
                      'is_required' => TRUE,
                      'is_searchable' => TRUE,
                      'text_length' => 255,
                      'note_columns' => 60,
                      'note_rows' => 4,
                      'option_group_id.name' => 'Utilisation_du_corps_cote2',
                      'serialize' => 1,
                      'in_selector' => TRUE,
                    ],
                    'match' => [
                      'name',
                      'custom_group_id',
                    ],
                  ],
                ],

                [
                  'name' => 'OptionGroup_Utilisation_du_corps_cote2_OptionValue_Droit',
                  'entity' => 'OptionValue',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'option_group_id.name' => 'Utilisation_du_corps_cote2',
                      'label' => E::ts('Droit'),
                      'value' => '1',
                      'name' => 'Droit',
                    ],
                    'match' => [
                      'option_group_id',
                      'name',
                      'value',
                    ],
                  ],
                ],
                [
                  'name' => 'OptionGroup_Utilisation_du_corps_cote2_OptionValue_Gauche',
                  'entity' => 'OptionValue',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'option_group_id.name' => 'Utilisation_du_corps_cote2',
                      'label' => E::ts('Gauche'),
                      'value' => '2',
                      'name' => 'Gauche',
                    ],
                    'match' => [
                      'option_group_id',
                      'name',
                      'value',
                    ],
                  ],
                ],
                [
                  'name' => 'OptionGroup_Utilisation_du_corps_cote2_OptionValue_Non_applicable',
                  'entity' => 'OptionValue',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'option_group_id.name' => 'Utilisation_du_corps_cote2',
                      'label' => E::ts('Non applicable'),
                      'value' => '3',
                      'name' => 'Non_applicable',
                    ],
                    'match' => [
                      'option_group_id',
                      'name',
                      'value',
                    ],
                  ],
                ],
              ];

                ## change le label du custom field sinon creation impossible
                $customFields = civicrm_api4('CustomField', 'get', [
                  'select' => [
                    'id',
                    'label',
                  ],  
                  'where' => [
                    ['name', '=', $original_custom_field],
                    ['custom_group_id:name', '=', $original_custom_group],
                  ],
                  'checkPermissions' => FALSE,
                ]);

                if (isset($customFields[0]))
                  {
                    $results = civicrm_api4('CustomField', 'update', [
                      'values' => [
                        'label' => $customFields[0]['label'].'_OLD',
                      ],
                      'where' => [
                        ['id', '=', $customFields[0]['id']],
                      ],
                      'checkPermissions' => FALSE,
                    ]);
                  }
                echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD';
                //print_r($customFields[0]);


          $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);
      ## FIN DE custom field, option group et option values pour : utilisation du corps / côté 

      ## custom field, option group et option values pour : utilisation du corps / utilisation 2 
            $original_custom_group = 'Utilisation_du_corps';
            $original_custom_field = 'Utilisation';

            $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
              
            $new_custom_name = 'Utilisation_du_corps.Utilisation2'; // nom du champ conforme à la nouvelle base 
                ## NOTEZ LES . au lieu de _ dans les variables

            $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
                                        // ajouter indice [0] si valeur unique
              [

   

                [ 'name' => 'OptionGroup_Utilisation_du_corps_Utilisation2',
                  'entity' => 'OptionGroup',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'name' => 'Utilisation_du_corps_Utilisation2',
                      'title' => E::ts('Utilisation du corps :: Utilisation2'),
                      'data_type' => 'String',
                      'is_reserved' => FALSE,
                      'option_value_fields' => ['name', 'label', 'description'],
                    ],
                    'match' => ['name'],
                  ],
                ],
                             [
                  'name' => 'CustomGroup_Utilisation_du_corps_CustomField_Utilisation2',
                  'entity' => 'CustomField',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'custom_group_id.name' => 'Utilisation_du_corps',
                      'name' => 'Utilisation2',
                      'label' => E::ts('Utilisation'),
                      'html_type' => 'Select',
                      'default_value' => '8',
                      'text_length' => 255,
                      'note_columns' => 60,
                      'note_rows' => 4,
                      'option_group_id.name' => 'Utilisation_du_corps_Utilisation2',
                      'serialize' => 1,
                      'in_selector' => TRUE,
                    ],
                    'match' => [
                      'name',
                      'custom_group_id',
                    ],
                  ],
                ],
                [ 'name' => 'OptionGroup_Utilisation_du_corps_Utilisation2_OptionValue_Enseignement_C1',
                  'entity' => 'OptionValue',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'option_group_id.name' => 'Utilisation_du_corps_Utilisation2',
                      'label' => E::ts('Enseignement'),
                      'value' => '1',
                      'name' => 'Enseignement_C1',
                      'description' => NULL,
                    ],
                    'match' => [
                      'option_group_id',
                      'name',
                      'value',
                    ],
                  ],
                ],
                [ 'name' => 'OptionGroup_Utilisation_du_corps_Utilisation2_OptionValue_Enseignement_C2',
                  'entity' => 'OptionValue',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'option_group_id.name' => 'Utilisation_du_corps_Utilisation2',
                      'label' => E::ts('Enseignement C2'),
                      'value' => '2',
                      'name' => 'Enseignement_C2',
                      'description' => NULL,
                    ],
                    'match' => [
                      'option_group_id',
                      'name',
                      'value',
                    ],
                  ],
                ],
                [ 'name' => 'OptionGroup_Utilisation_du_corps_Utilisation2_OptionValue_Enseignement_C3',
                  'entity' => 'OptionValue',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'option_group_id.name' => 'Utilisation_du_corps_Utilisation2',
                      'label' => E::ts('Enseignement C3'),
                      'value' => '3',
                      'name' => 'Enseignement_C3',
                      'description' => NULL,
                    ],
                    'match' => [
                      'option_group_id',
                      'name',
                      'value',
                    ],
                  ],
                ],
                [ 'name' => 'OptionGroup_Utilisation_du_corps_Utilisation2_OptionValue_Recherche',
                  'entity' => 'OptionValue',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'option_group_id.name' => 'Utilisation_du_corps_Utilisation2',
                      'label' => E::ts('Recherche'),
                      'value' => '5',
                      'name' => 'Recherche',
                    ],
                    'match' => [
                      'option_group_id',
                      'name',
                      'value',
                    ],
                  ],
                ],
                [ 'name' => 'OptionGroup_Utilisation_du_corps_Utilisation2_OptionValue_Workshop',
                  'entity' => 'OptionValue',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'option_group_id.name' => 'Utilisation_du_corps_Utilisation2',
                      'label' => E::ts('Workshop'),
                      'value' => '4',
                      'name' => 'Workshop',
                      'description' => NULL,
                    ],
                    'match' => [
                      'option_group_id',
                      'name',
                      'value',
                    ],
                  ],
                ],
                [
                  'name' => 'OptionGroup_Utilisation_du_corps_Utilisation2_OptionValue_Ind_termin_',
                  'entity' => 'OptionValue',
                  'cleanup' => 'unused',
                  'update' => 'unmodified',
                  'params' => [
                    'version' => 4,
                    'values' => [
                      'option_group_id.name' => 'Utilisation_du_corps_Utilisation2',
                      'label' => E::ts('En attente utilisation'),
                      'value' => '8',
                      'name' => 'Ind_termin_',
                    ],
                    'match' => [
                      'option_group_id',
                      'name',
                      'value',
                    ],
                  ],
                ],
              ];

                ## change le label du custom field sinon creation impossible
                $customFields = civicrm_api4('CustomField', 'get', [
                  'select' => [
                    'id',
                    'label',
                  ],  
                  'where' => [
                    ['name', '=', $original_custom_field],
                    ['custom_group_id:name', '=', $original_custom_group],
                  ],
                  'checkPermissions' => FALSE,
                ]);

                if (isset($customFields[0]))
                  {
                    $results = civicrm_api4('CustomField', 'update', [
                      'values' => [
                        'label' => $customFields[0]['label'].'_OLD',
                      ],
                      'where' => [
                        ['id', '=', $customFields[0]['id']],
                      ],
                      'checkPermissions' => FALSE,
                    ]);
                  }
                echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD';
                //print_r($customFields[0]);

            $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);
      ## FIN DE custom field, option group et option values pour : utilisation du corps / Utlisation 2

      ## custom field, option group et option values pour : utilisation du corps / Protocole_de_recherche_ex_vivo2 
            $original_custom_group = 'Utilisation_du_corps';
            $original_custom_field = 'Protocole';

            $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
              
            $new_custom_name = 'Utilisation_du_corps.Protocole_de_recherche_ex_vivo2'; // nom du champ conforme à la nouvelle base 
                ## NOTEZ LES . au lieu de _ dans les variables

            $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
                                        // ajouter indice [0] si valeur unique
              [


                  [
                      'name' => 'OptionGroup_Utilisation_du_corps_Protocole_de_recherche_ex_vivo2',
                      'entity' => 'OptionGroup',
                      'cleanup' => 'unused',
                      'update' => 'unmodified',
                      'params' => [
                        'version' => 4,
                        'values' => [
                          'name' => 'Utilisation_du_corps_Protocole_de_recherche_ex_vivo2',
                          'title' => E::ts('Utilisation du corps :: Protocole de recherche ex vivo2'),
                          'data_type' => 'String',
                          'is_reserved' => FALSE,
                          'option_value_fields' => ['name', 'label', 'description'],
                        ],
                        'match' => ['name'],
                      ],
                    ],
                                      [
                    'name' => 'CustomGroup_Utilisation_du_corps_CustomField_Protocole_de_recherche_ex_vivo2',
                    'entity' => 'CustomField',
                    'cleanup' => 'unused',
                    'update' => 'unmodified',
                    'params' => [
                      'version' => 4,
                      'values' => [
                        'custom_group_id.name' => 'Utilisation_du_corps',
                        'name' => 'Protocole_de_recherche_ex_vivo2',
                        'label' => E::ts('Protocole de recherche ex vivo'),
                        'html_type' => 'Select',
                        'default_value' => '11',
                        'text_length' => 255,
                        'note_columns' => 60,
                        'note_rows' => 4,
                        'option_group_id.name' => 'Utilisation_du_corps_Protocole_de_recherche_ex_vivo2',
                        'serialize' => 1,
                        'in_selector' => TRUE,
                      ],
                      'match' => [
                        'name',
                        'custom_group_id',
                      ],
                    ],
                  ],
  
                    [
                      'name' => 'OptionGroup_Utilisation_du_corps_Protocole_de_recherche_ex_vivo2_OptionValue_Fibratlas',
                      'entity' => 'OptionValue',
                      'cleanup' => 'unused',
                      'update' => 'unmodified',
                      'params' => [
                        'version' => 4,
                        'values' => [
                          'option_group_id.name' => 'Utilisation_du_corps_Protocole_de_recherche_ex_vivo2',
                          'label' => E::ts('Fibratlas'),
                          'value' => '1',
                          'name' => 'Fibratlas',
                        ],
                        'match' => [
                          'option_group_id',
                          'name',
                          'value',
                        ],
                      ],
                    ],
 
                    [
                      'name' => 'OptionGroup_Utilisation_du_corps_Protocole_de_recherche_ex_vivo2_OptionValue_Pas_de_protocole',
                      'entity' => 'OptionValue',
                      'cleanup' => 'unused',
                      'update' => 'unmodified',
                      'params' => [
                        'version' => 4,
                        'values' => [
                          'option_group_id.name' => 'Utilisation_du_corps_Protocole_de_recherche_ex_vivo2',
                          'label' => E::ts('Pas de protocole'),
                          'value' => '11',
                          'name' => 'Pas_de_protocole',
                        ],
                        'match' => [
                          'option_group_id',
                          'name',
                          'value',
                        ],
                      ],
                    ],
              ];

                ## change le label du custom field sinon creation impossible
                $customFields = civicrm_api4('CustomField', 'get', [
                  'select' => [
                    'id',
                    'label',
                  ],  
                  'where' => [
                    ['name', '=', $original_custom_field],
                    ['custom_group_id:name', '=', $original_custom_group],
                  ],
                  'checkPermissions' => FALSE,
                ]);

                if (isset($customFields[0]))
                  {
                    $results = civicrm_api4('CustomField', 'update', [
                      'values' => [
                        'label' => $customFields[0]['label'].'_OLD',
                      ],
                      'where' => [
                        ['id', '=', $customFields[0]['id']],
                      ],
                      'checkPermissions' => FALSE,
                    ]);
                  }
                echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD';
                //print_r($customFields[0]);


                  $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);

      ## FIN DE custom field, option group et option values pour : Protocole_de_recherche_ex_vivo2

      ## custom field, option group et option values pour : utilisation du corps / Site_inject_ 
            $original_custom_group = 'Utilisation_du_corps';
            $original_custom_field = 'Site_d_injection';

            $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
              
            $new_custom_name = 'Utilisation_du_corps.Site_inject_'; // nom du champ conforme à la nouvelle base 
                ## NOTEZ LES . au lieu de _ dans les variables

            $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
                                        // ajouter indice [0] si valeur unique
              [


              [
                'name' => 'OptionGroup_Utilisation_du_corps_Site_inject_',
                'entity' => 'OptionGroup',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'name' => 'Utilisation_du_corps_Site_inject_',
                    'title' => E::ts('Utilisation du corps :: Site injecté'),
                    'data_type' => 'String',
                    'is_reserved' => FALSE,
                    'option_value_fields' => ['name', 'label', 'description'],
                  ],
                  'match' => ['name'],
                ],
              ],
               [
                'name' => 'CustomGroup_Utilisation_du_corps_CustomField_Site_inject_',
                'entity' => 'CustomField',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'custom_group_id.name' => 'Utilisation_du_corps',
                    'name' => 'Site_inject_',
                    'label' => E::ts('Site injecté'),
                    'html_type' => 'Select',
                    'text_length' => 255,
                    'note_columns' => 60,
                    'note_rows' => 4,
                    'option_group_id.name' => 'Utilisation_du_corps_Site_inject_',
                    'serialize' => 1,
                  ],
                  'match' => [
                    'name',
                    'custom_group_id',
                  ],
                ],
              ],

              [
                'name' => 'OptionGroup_Utilisation_du_corps_Site_inject_OptionValue_A_Basilaire',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Site_inject_',
                    'label' => E::ts('A. Basilaire'),
                    'value' => '1',
                    'name' => 'A_Basilaire',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Site_inject_OptionValue_A_Carotide_interne_droite',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Site_inject_',
                    'label' => E::ts('A. carotide interne D'),
                    'value' => '2',
                    'name' => 'A_Carotide_interne_droite',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Site_inject_OptionValue_A_Carotide_interne_gauche',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Site_inject_',
                    'label' => E::ts('A. carotide interne G'),
                    'value' => '3',
                    'name' => 'A_Carotide_interne_gauche',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Site_inject_OptionValue_A_F_morale_droite',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Site_inject_',
                    'label' => E::ts('A. Fémorale D'),
                    'value' => '6',
                    'name' => 'A_F_morale_droite',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Site_inject_OptionValue_A_F_morale_gauche',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Site_inject_',
                    'label' => E::ts('A. Fémorale G'),
                    'value' => '7',
                    'name' => 'A_F_morale_gauche',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Site_inject_OptionValue_A_Vert_brale_droite',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Site_inject_',
                    'label' => E::ts('A. vertébrale D'),
                    'value' => '4',
                    'name' => 'A_Vert_brale_droite',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Site_inject_OptionValue_A_Vert_brale_gauche',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Site_inject_',
                    'label' => E::ts('A. vertébrale G'),
                    'value' => '5',
                    'name' => 'A_Vert_brale_gauche',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Site_inject_OptionValue_V_Jugulaire_interne_droite',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Site_inject_',
                    'label' => E::ts('V. jugulaire interne D'),
                    'value' => '8',
                    'name' => 'V_Jugulaire_interne_droite',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Site_inject_OptionValue_V_Jugulaire_interne_gauche',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Site_inject_',
                    'label' => E::ts('V. jugulaire interne G'),
                    'value' => '9',
                    'name' => 'V_Jugulaire_interne_gauche',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],

              ];

                ## change le label du custom field sinon creation impossible
                $customFields = civicrm_api4('CustomField', 'get', [
                  'select' => [
                    'id',
                    'label',
                  ],  
                  'where' => [
                    ['name', '=', $original_custom_field],
                    ['custom_group_id:name', '=', $original_custom_group],
                  ],
                  'checkPermissions' => FALSE,
                ]);

                if (isset($customFields[0]))
                  {
                    $results = civicrm_api4('CustomField', 'update', [
                      'values' => [
                        'label' => $customFields[0]['label'].'_OLD',
                      ],
                      'where' => [
                        ['id', '=', $customFields[0]['id']],
                      ],
                      'checkPermissions' => FALSE,
                    ]);
                  }
                echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD';
                //print_r($customFields[0]);


                $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);
      ## FIN DE custom field, option group et option values pour : Site_inject_

      ## custom field, option group et option values pour : utilisation du corps / Imagerie2
            $original_custom_group = 'Utilisation_du_corps';
            $original_custom_field = 'Imagerie';

            $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
              
            $new_custom_name = 'Utilisation_du_corps.Imagerie2'; // nom du champ conforme à la nouvelle base 
                ## NOTEZ LES . au lieu de _ dans les variables

            $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
                                        // ajouter indice [0] si valeur unique
              [

                [
                    'name' => 'OptionGroup_Utilisation_du_corps_Imagerie2',
                    'entity' => 'OptionGroup',
                    'cleanup' => 'unused',
                    'update' => 'unmodified',
                    'params' => [
                      'version' => 4,
                      'values' => [
                        'name' => 'Utilisation_du_corps_Imagerie2',
                        'title' => E::ts('Utilisation du corps :: Imagerie2'),
                        'data_type' => 'String',
                        'is_reserved' => FALSE,
                        'option_value_fields' => ['name', 'label', 'description'],
                      ],
                      'match' => ['name'],
                    ],
                  ],
    


                  [
                    'name' => 'OptionGroup_Utilisation_du_corps_Imagerie2_OptionValue_IRM_ex_vivo_3T',
                    'entity' => 'OptionValue',
                    'cleanup' => 'unused',
                    'update' => 'unmodified',
                    'params' => [
                      'version' => 4,
                      'values' => [
                        'option_group_id.name' => 'Utilisation_du_corps_Imagerie2',
                        'label' => E::ts('IRM ex vivo 3T'),
                        'value' => '3',
                        'name' => 'IRM_ex_vivo_3T',
                      ],
                      'match' => [
                        'option_group_id',
                        'name',
                        'value',
                      ],
                    ],
                  ],
                  [
                    'name' => 'OptionGroup_Utilisation_du_corps_Imagerie2_OptionValue_IRM_ex_vivo_7T',
                    'entity' => 'OptionValue',
                    'cleanup' => 'unused',
                    'update' => 'unmodified',
                    'params' => [
                      'version' => 4,
                      'values' => [
                        'option_group_id.name' => 'Utilisation_du_corps_Imagerie2',
                        'label' => E::ts('IRM ex vivo 7T'),
                        'value' => '4',
                        'name' => 'IRM_ex_vivo_7T',
                      ],
                      'match' => [
                        'option_group_id',
                        'name',
                        'value',
                      ],
                    ],
                  ],

                  [
                    'name' => 'CustomGroup_Utilisation_du_corps_CustomField_Imagerie2',
                    'entity' => 'CustomField',
                    'cleanup' => 'unused',
                    'update' => 'unmodified',
                    'params' => [
                      'version' => 4,
                      'values' => [
                        'custom_group_id.name' => 'Utilisation_du_corps',
                        'name' => 'Imagerie2',
                        'label' => E::ts('Imagerie'),
                        'html_type' => 'Select',
                        'text_length' => 255,
                        'note_columns' => 60,
                        'note_rows' => 4,
                        'option_group_id.name' => 'Utilisation_du_corps_Imagerie2',
                        'serialize' => 1,
                      ],
                      'match' => [
                        'name',
                        'custom_group_id',
                      ],
                    ],
                  ],
             

              ];

                ## change le label du custom field sinon creation impossible
                $customFields = civicrm_api4('CustomField', 'get', [
                  'select' => [
                    'id',
                    'label',
                  ],  
                  'where' => [
                    ['name', '=', $original_custom_field],
                    ['custom_group_id:name', '=', $original_custom_group],
                  ],
                  'checkPermissions' => FALSE,
                ]);

                if (isset($customFields[0]))
                  {
                    $results = civicrm_api4('CustomField', 'update', [
                      'values' => [
                        'label' => $customFields[0]['label'].'_OLD',
                      ],
                      'where' => [
                        ['id', '=', $customFields[0]['id']],
                      ],
                      'checkPermissions' => FALSE,
                    ]);
                  }
                echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD';
                //print_r($customFields[0]);


                $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);
      ## FIN DE custom field, option group et option values pour : Imagerie2

      ## custom field, option group et option values pour : utilisation du corps / Mode_limination_hors_corps_2
        $original_custom_group = 'Utilisation_du_corps';
        $original_custom_field = 'Devenir_des_pi_ces';

        $original_custom_name = $original_custom_group.'.'.$original_custom_field;         // nom du champ dans la base originale
          
        $new_custom_name = 'Utilisation_du_corps.Mode_limination_hors_corps_2'; // nom du champ conforme à la nouvelle base 
            ## NOTEZ LES . au lieu de _ dans les variables


        $tocreate_things =        /// définition du custom field, option group, option values (a recuperer depuis manages files)
                                    // ajouter indice [0] si valeur unique
          [
            [
                'name' => 'OptionGroup_Utilisation_du_corps_Mode_limination_hors_corps_2',
                'entity' => 'OptionGroup',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'name' => 'Utilisation_du_corps_Mode_limination_hors_corps_2',
                    'title' => E::ts('Utilisation du corps :: Mode élimination (hors corps)2'),
                    'data_type' => 'String',
                    'is_reserved' => FALSE,
                    'option_value_fields' => ['name', 'label', 'description'],
                  ],
                  'match' => ['name'],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Mode_limination_hors_corps_2_OptionValue_Non_limin_e',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Mode_limination_hors_corps_2',
                    'label' => E::ts('Non éliminée'),
                    'value' => '5',
                    'name' => 'Non_limin_e',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Mode_limination_hors_corps_2_OptionValue_Conservation_illimit_e',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Mode_limination_hors_corps_2',
                    'label' => E::ts('Conservation illimitée'),
                    'value' => '4',
                    'name' => 'Conservation_illimit_e',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Mode_limination_hors_corps_2_OptionValue_Cr_mation_comme_pi_ce_anatomiqu',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Mode_limination_hors_corps_2',
                    'label' => E::ts('Crémation / Op funéraires'),
                    'value' => '1',
                    'name' => 'Cr_mation_comme_pi_ce_anatomiqu',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Mode_limination_hors_corps_2_OptionValue_Demander_cr_mation',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Mode_limination_hors_corps_2',
                    'label' => E::ts('Demander crémation'),
                    'value' => '7',
                    'name' => 'Demander_cr_mation',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Mode_limination_hors_corps_2_OptionValue_Cr_mation_demand_e',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Mode_limination_hors_corps_2',
                    'label' => E::ts('Crémation demandée'),
                    'value' => '8',
                    'name' => 'Cr_mation_demand_e',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Mode_limination_hors_corps_2_OptionValue_Destruction_par_la_m_thode_util',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Mode_limination_hors_corps_2',
                    'label' => E::ts('Destruction par la méthode utilisée'),
                    'value' => '3',
                    'name' => 'Destruction_par_la_m_thode_util',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'OptionGroup_Utilisation_du_corps_Mode_limination_hors_corps_2_OptionValue_Manquante',
                'entity' => 'OptionValue',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'option_group_id.name' => 'Utilisation_du_corps_Mode_limination_hors_corps_2',
                    'label' => E::ts('Manquante'),
                    'value' => '6',
                    'name' => 'Manquante',
                  ],
                  'match' => [
                    'option_group_id',
                    'name',
                    'value',
                  ],
                ],
              ],
              [
                'name' => 'CustomGroup_Utilisation_du_corps_CustomField_Mode_limination_hors_corps_2',
                'entity' => 'CustomField',
                'cleanup' => 'unused',
                'update' => 'unmodified',
                'params' => [
                  'version' => 4,
                  'values' => [
                    'custom_group_id.name' => 'Utilisation_du_corps',
                    'name' => 'Mode_limination_hors_corps_2',
                    'label' => E::ts('Mode élimination'),
                    'html_type' => 'Select',
                    'default_value' => '5',
                    'is_required' => TRUE,
                    'text_length' => 255,
                    'note_columns' => 60,
                    'note_rows' => 4,
                    'option_group_id.name' => 'Utilisation_du_corps_Mode_limination_hors_corps_2',
                    'in_selector' => TRUE,
                  ],
                  'match' => [
                    'name',
                    'custom_group_id',
                  ],
                ],
              ],
          ];

            ## change le label du custom field sinon creation impossible
            $customFields = civicrm_api4('CustomField', 'get', [
              'select' => [
                'id',
                'label',
              ],  
              'where' => [
                ['name', '=', $original_custom_field],
                ['custom_group_id:name', '=', $original_custom_group],
              ],
              'checkPermissions' => FALSE,
            ]);

            if (isset($customFields[0]))
              {
                $results = civicrm_api4('CustomField', 'update', [
                  'values' => [
                    'label' => $customFields[0]['label'].'_OLD',
                  ],
                  'where' => [
                    ['id', '=', $customFields[0]['id']],
                  ],
                  'checkPermissions' => FALSE,
                ]);
              }
            echo "Modification Custom field label --> ".$customFields[0]['label'].'_OLD';
            //print_r($customFields[0]);


            $stuff_to_update=create_stuff($tocreate_things, $original_custom_name, $new_custom_name, $stuff_to_update);
      ## FIN DE custom field, option group et option values pour : Mode_limination_hors_corps_2

      update_stuff('Custom_Utilisation_du_corps', $stuff_to_update);

      } #fin if migrate_utilisation_corps == 1)
    
}

function update_stuff()
  {
    $entity=func_get_arg(0);
    $stuff_to_updates=func_get_arg(1);

    echo PHP_EOL."MAJ des champs créés pour ".count($stuff_to_updates)." ".$entity."(s)".PHP_EOL;

    foreach ($stuff_to_updates as $id => $stuff_to_update)
      {
        echo '.';
        $results = civicrm_api4($entity, 'update', [    // mise à jour de chaque occurence de l'entité
          'values' => $stuff_to_update,
          'where' => [
            ['id', '=', $id],
          ],
          'checkPermissions' => FALSE,
        ]); 
      }
      echo PHP_EOL;
  }

function create_stuff()     ## cette fonction crée dans la bdd originale des champs requis
                            ## et y recopie la valeur originale 
                            ## Arguments : 
                            ##   $tocreate_things : sortie des managed files
                            ##   $original_custom_name : nom du custom name dans la base originale
                            ##   $new_custom_name : nom du custom name dans la nouvelle base
  {
    $stuff_to_update_partial=array();

    $tocreate_things=func_get_arg(0);
    $original_custom_name=func_get_arg(1);
    $new_custom_name=func_get_arg(2);
    $stuff_to_update=func_get_arg(3);

    $new_postfix = after ('.', $new_custom_name);   
    $new_prefix = before ('.', $new_custom_name);  



    $original_postfix = after ('.', $original_custom_name);   
    $original_prefix = before ('.', $original_custom_name);  
 
    echo PHP_EOL;

    foreach ($tocreate_things as $tocreate_thing)

      {
        switch ($tocreate_thing['entity']){
          case 'OptionValue' :
            $entities = civicrm_api4($tocreate_thing['entity'], 'get', [
              'where' => [
                ['option_group_id:name', '=', $tocreate_thing['params']['values']['option_group_id.name']],
                ['name', '=', $tocreate_thing['params']['values']['name']],
                ],
              'checkPermissions' => FALSE,
            ]);

            break;

          case 'CustomField' :
            $entities = civicrm_api4($tocreate_thing['entity'], 'get', [
              'where' => [
                ['custom_group_id:name', '=', $tocreate_thing['params']['values']['custom_group_id.name']],
                ['name', '=', $tocreate_thing['params']['values']['name']],
                ],
              'checkPermissions' => FALSE,
            ]);

            break;   

          default:
            $entities = civicrm_api4($tocreate_thing['entity'], 'get', [
              'where' => [
                ['name', '=', $tocreate_thing['params']['values']['name']],
              ],
              'checkPermissions' => FALSE,
            ]);
            break;

        }

        if (isset($entities[0]))
          {
            echo "MAJ ".$tocreate_thing['entity']." : ".$tocreate_thing['params']['values']['name'].PHP_EOL;
            //print_r($tocreate_thing['params']['values']);

            $results = civicrm_api4($tocreate_thing['entity'], 'update', [
              'values' => $tocreate_thing['params']['values'],
              'where' => [
                ['id', '=', $entities[0]['id']],
              ],
              'checkPermissions' => FALSE,
            ]);


          } else {
            echo "Création ".$tocreate_thing['entity']." : ".$tocreate_thing['params']['values']['name'].PHP_EOL;
            $results = civicrm_api4($tocreate_thing['entity'], 'create', [
              'values' => $tocreate_thing['params']['values'],
              'checkPermissions' => FALSE,
            ]);
          }
      }

      switch ($new_prefix){
        case 'Utilisation_du_corps':
          
          $list_utilisations = civicrm_api4('Custom_Utilisation_du_corps', 'get', [       // récupère la liste des utilisations à mettre à jour
            'select' => [
              'id',
              $original_postfix.':name',
              $original_postfix,
            ],

            'orderBy' => [
              'id' => 'ASC',
            ],

            'checkPermissions' => FALSE,
            //'limit' => 5,
          ]);

           foreach ($list_utilisations as $list_utilisation)
            {  
              $utilisation_id=$list_utilisation['id'];
              $stuff_to_update[$utilisation_id][$new_postfix]=$list_utilisation[$original_postfix];
            }

          return($stuff_to_update);
          break;

        case 'Arriv_e_du_corps_new':
          
          $list_arrivees = civicrm_api4('Custom_S_rologies', 'get', [       // récupère la liste des utilisations à mettre à jour
            'select' => [
              'id',
              $original_postfix,
              'entity_id',
            ],

            'orderBy' => [
              'id' => 'ASC',
            ],

            'checkPermissions' => FALSE,

          ]);

            foreach ($list_arrivees as $list_arrivee)
              //print_r($list_arrivee);
            {  
              $arrivee_id=$list_arrivee['id'];
              $stuff_to_update[$arrivee_id][$new_postfix]=$list_arrivee[$original_postfix];
              $stuff_to_update[$arrivee_id]['entity_id']=$list_arrivee['entity_id'];
            }

          return($stuff_to_update);
          break;  

        default : // cas des contacts
          $list_contacts = civicrm_api4('Contact', 'get', [       // récupère la liste des contacts à mettre à jour
            'select' => [
              'id',
              $original_custom_name,
              $original_custom_name.':label',
            ],
            'where' => [
              ['contact_sub_type', '=', 'Donateur'],
              ['is_deleted', '=', FALSE],
            ],
            'orderBy' => [
            'id' => 'ASC',
            ],
            'checkPermissions' => FALSE,
            //'limit' => 5,
          ]);


          foreach ($list_contacts as $list_contact)
            {  
              $id = $list_contact['id'];      // id du contact à modifier
              $old=$list_contact[$original_custom_name];
              $old_label=$list_contact[$original_custom_name.':label'];

              //echo "contact id : ".$id."  -  ".$original_custom_name." : ".$old.' ('.$old_label.') --> ';
              
              if($old==NULL)    // si la valeur n'est pas définie : passe à 0
                {
                  $old = 0;
                }

              $contact_id=$list_contact['id'];

              $stuff_to_update[$contact_id][$new_custom_name]=$old;
            }
            return($stuff_to_update);
            break;
      }






  } # FIN DE FONCTION CREATE STUFF


function export_stuff(){
    $entity = func_get_arg(0);      // nom de l'entité à créer (optionvalue, contact.....)
    $subtype = func_get_arg(1);     
    $name = func_get_arg(2);        // prefixe du fichier d'export
    $exp_file = $name.".txt";
    echo "Preparation export ".$entity." into ".$exp_file.PHP_EOL;

  switch ($entity) {
    case 'OptionValue':

        $exports=array();
            foreach ($subtype as $option_val_tocheck){
                //echo $option_val_tocheck.PHP_EOL;
                $option_val_tocheck = explode('.', $option_val_tocheck);

                if (count($option_val_tocheck)==2){
                  $custom_group = $option_val_tocheck[0];
                  $custom_field = $option_val_tocheck[1];
                  echo "custom_group:".$custom_group."     custom_field :".$custom_field.PHP_EOL;

                  //echo "il y en a 2".PHP_EOL;
                  $customFields = civicrm_api4('CustomField', 'get', [
                    'select' => [
                    'option_group_id:name',
                    ],
                    'where' => [
                    ['custom_group_id:name', '=', $custom_group],
                    ['name', '=', $custom_field],
                    ],
                    'checkPermissions' => FALSE,
                  ]);

                  $option_group_name = $customFields[0]['option_group_id:name'];

                //echo $customFields[0]['option_group_id.name'].PHP_EOL;
                } else {
                  $option_group_name = $option_val_tocheck[0] ;
                }

                //echo $customFields[0]['option_group_id.name'].PHP_EOL;

                $optionValues = civicrm_api4('OptionValue', 'get', [
                    'select' => [
                        'option_group_id.name',
                        '*',
                    ],
                    'where' => [
                    ['option_group_id:name', '=', $option_group_name],
                    ],
                    'checkPermissions' => FALSE,
                ]);

            array_push($exports, $optionValues);
        }

        break;
        
    case 'FinancialType':                                             // type d'opérations (dons...)

      $exports = civicrm_api4('FinancialType', 'get', [
        'where' => [
          ['is_active', '=', TRUE],
        ],
        'checkPermissions' => FALSE,
      ]);
      echo count($exports)." financial types exportés".PHP_EOL;
      break;


    case 'Individual':                                                // Contacts : individuals, organization
      $exports = civicrm_api4('Contact', 'get', [
            'select' => [
                '*',
               'custom.*',
               'prefix_id:name',
               'suffix_id:name',
               'communication_style_id:name',
              ],
            'where' => [
                ['contact_type', '=', $subtype],
            ],
            //'limit' => 40,
            'checkPermissions' => FALSE,
            'orderBy' => [
                'sort_name' => 'ASC',
              ],
        ]);
        //print_r($exports);
        echo count($exports)." contacts indivsus exportés".PHP_EOL;
        break;

      case 'Group':                                                // Groups 
        $exports = civicrm_api4('Group', 'get', [                  // Récupère ls groupes non liés à un recherche Searchkit
          'where' => [
            ['saved_search_id', 'IS NULL'],
          ],
          'checkPermissions' => FALSE,
        ]);

        if(isset($exports[0])) {               // il existe des groupes non liés à une recherche Searchkit à exporter
          //print_r($exports);
          echo count($exports)." groupes exportés".PHP_EOL; 

        } else {
              $error = "Pas de groupe non lié à une recherche Searckit ";
              echo PHP_EOL.$error.PHP_EOL;
              array_push($error_log,$error);
        }

      break;

      case 'GroupContact':                                         // Groups contacts
        $Groups = civicrm_api4('Group', 'get', [                   // Récupère ls groupes non liés à un recherche Searchkit
          'where' => [
            ['saved_search_id', 'IS NULL'],
          ],
          'checkPermissions' => FALSE,
        ]);

        $exports=array();
        $error_log=array();                   // chaine contenant les messages d'erreur à loguer

        if(isset($Groups[0])) {               // il existe des groupes non liés à une recherche Searchkit à exporter
          foreach($Groups as $Group){
            $groupContacts = civicrm_api4('GroupContact', 'get', [
              'where' => [
                ['group_id', '=', $Group['id']],
              ],
              'checkPermissions' => FALSE,
            ]);
            
            if(isset($groupContacts[0])){      // il y a des contacts attachés à ce groupe
              foreach ($groupContacts as $groupContact){
                $contacts = civicrm_api4('Contact', 'get', [    // recherche si le contact correspondant n'est pas supprimé
                  'select' => [
                      'id',
                      'display_name',
                    ],
                  'where' => [
                    ['is_deleted', '=', FALSE],
                    ['id','=',$groupContact['contact_id']],
                  ],
                  'checkPermissions' => FALSE,
                ]);
                
                if(isset($contacts[0])){
                  echo "Groupe ".$Group['name']." (id: ".$Group['id'].")  --> Export du GroupContact ".$groupContact['id']." correspondant au Contact ".$contacts[0]['display_name']." (id: ".$contacts[0]['id'].")".PHP_EOL;
                  array_push($exports,$groupContact);
                }else{
                  $error = "Groupe ".$Group['name']." (id: ".$Group['id'].")  --> Pas d'export du GroupContact ".$groupContact['id']." en l'absence de Contact correspondant (".$groupContact['contact_id'].")";
                  //echo PHP_EOL.$error.PHP_EOL;
                  array_push($error_log,$error);
                }
              }
            } else {
              $error = "Pas de GroupContact attachés au groupe ".$Group['name']." (id: ".$Group['id'].")";
              //echo PHP_EOL.$error.PHP_EOL;
              array_push($error_log,$error);
            }
          }
        } else {
              $error = "Pas de groupe non lié à une recherche Searckit ";
              //echo PHP_EOL.$error.PHP_EOL;
              array_push($error_log,$error);
        }
          //print_r($exports);
          echo count($exports)." GroupContacts exportés".PHP_EOL;
          break;

    
    case 'Organization':                                                // Contacts : individuals, organization
      $exports = civicrm_api4('Contact', 'get', [
          'select' => [
              '*',
              'custom.*',
            ],
          'where' => [
              ['contact_type', '=', $subtype],
          ],
          //'limit' => 40,
          'checkPermissions' => FALSE,
          'orderBy' => [
              'sort_name' => 'ASC',
            ],
      ]);
      //print_r($exports);
      echo PHP_EOL.count($exports)." Organisations exportées ".PHP_EOL;

      break;

    case 'CustomField':
        $exports = civicrm_api4('CustomField', 'get', [
            'select' => [
              'custom_group_id:name',
              '*',
            ],
            'checkPermissions' => FALSE,
          ]);

        break;
    case 'Address':
          $adresses = civicrm_api4('Address', 'get', [
            'select' => [
                '*',
                'location_type_id',
                'country_id.name',
                'master_id.contact_id',
              ],
            'checkPermissions' => FALSE,
            //'limit' => 25,
          ]);
        // check if these adresses belong to any contact non ANONYMISE et pas dana la corbeille

        $exports=array();
        $error_log=array();                   // chaine contenant les messages d'erreur à loguer
        foreach ($adresses as $adresse){
            
            //echo $adresse[id].PHP_EOL;
            $contacts = civicrm_api4('Contact', 'get', [
                'select' => [
                    'id',
                    'display_name',
                  ],
                'where' => [
                  ['id', '=', $adresse['contact_id']],
                  ['sort_name', 'NOT LIKE', 'ANONYMISE, Anonymisé'],
                  ['is_deleted', '=', FALSE],
                ],
                'checkPermissions' => FALSE,
              ]);


              if (isset($contacts[0])){// il existe un contact avec cette adresse 
                //echo $adresse['contact_id']." ".$contacts[0]['display_name'].PHP_EOL; 
                // verifier si bien ok pour les lieux de cérémonies pour qui les contact id sont nulles    
                echo ".";   
                unset ($adresse['id']);    
                unset ($adresse['country_id']);  
                unset ($adresse['master_id']) ;

                array_push($exports, $adresse);   
                } else {
                
                $error = "Contact id ".$adresse['contact_id']."lié à l'adresse ".$adresse['id']." n'existe pas  - Ignorée";
                echo PHP_EOL.$error.PHP_EOL;
                array_push($error_log,$error);

                }
        }
        echo PHP_EOL.count($exports)." adresses  exportées".PHP_EOL;
      break;

    case 'Phone':
          $phones = civicrm_api4('Phone', 'get', [
            'select' => [
                '*',
                'location_type_id:name',
                'phone_type_id:name',
              ],
            'checkPermissions' => FALSE,
            //'limit' => 25,
          ]);
        // check if these phones belong to any contact non ANONYMISE et pas dana la corbeille

        $exports=array();
        $error_log=array();                   // chaine contenant les messages d'erreur à loguer
        foreach ($phones as $phone){                       // pour chaque telephone de la base originale
            
            //echo $phone[id].PHP_EOL;
            $contacts = civicrm_api4('Contact', 'get', [    // liste les contacts correpondant au contact_id du telephone, non anonymisé et non supprimés
                'select' => [
                    'id',
                    'display_name',
                  ],
                'where' => [
                  ['id', '=', $phone['contact_id']],
                  ['sort_name', 'NOT LIKE', 'ANONYMISE, Anonymisé'],
                  ['is_deleted', '=', FALSE],
                ],
                'checkPermissions' => FALSE,
              ]);


              if (isset($contacts[0])){// il existe un contact avec ce téléphone 
                //echo $phone['contact_id']." ".$contacts[0]['display_name'].PHP_EOL;     
                echo ".";   
                unset ($phone['id']);    
                unset ($phone['location_type_id']);  
                unset ($phone['phone_type_id']) ;

                array_push($exports, $phone);   
                } else {
                $error = "Contact id ".$phone['contact_id']."lié au téléphone ".$phone['id']." n'existe pas  - Ignorée";
                echo PHP_EOL.$error.PHP_EOL;
                array_push($error_log,$error);
                }
        }
        echo PHP_EOL.count($exports)." téléphones  exportés".PHP_EOL;
      break;

    case 'Email':
        $emails = civicrm_api4('Email', 'get', [
          'select' => [
              '*',
              'location_type_id:name',
            ],
          'checkPermissions' => FALSE,
          //'limit' => 25,
        ]);
      // check if these emails belong to any contact non ANONYMISE et pas dana la corbeille

      $exports=array();
      $error_log=array();                   // chaine contenant les messages d'erreur à loguer
      foreach ($emails as $email){                       // pour chaque telephone de la base originale
          
          //echo $phone[id].PHP_EOL;
          $contacts = civicrm_api4('Contact', 'get', [    // liste les contacts correpondant au contact_id du telephone, non anonymisé et non supprimés
              'select' => [
                  'id',
                  'display_name',
                ],
              'where' => [
                ['id', '=', $email['contact_id']],
                ['sort_name', 'NOT LIKE', 'ANONYMISE, Anonymisé'],
                ['is_deleted', '=', FALSE],
              ],
              'checkPermissions' => FALSE,
            ]);


            if (isset($contacts[0])){// il existe un contact avec ce téléphone 
              //echo $phone['contact_id']." ".$contacts[0]['display_name'].PHP_EOL;     
              echo ".";   
              unset ($email['id']);    
              unset ($email['location_type_id']);  
              
              array_push($exports, $email);   
              } else {
              $error = "Contact id ".$email['contact_id']."lié au courriel ".$email['id']." n'existe pas  - Ignorée";
              echo PHP_EOL.$error.PHP_EOL;
              array_push($error_log,$error);
              }
      }
      echo PHP_EOL.count($exports)." emails exportés".PHP_EOL;
      break;

    case 'Relationship':
      $relationships = civicrm_api4($entity, 'get', [
        'select' => [
            '*',
            'relationship_type_id:name',
          ],
        'checkPermissions' => FALSE,
        //'limit' => 25,
      ]);
      // check if these relationships belong to any contact non ANONYMISE et pas dana la corbeille
      $exports=array();
      $error_log=array();                   // chaine contenant les messages d'erreur à loguer

      foreach ($relationships as $relationship){                       // pour chaque relation de la base originale
          
          //echo $relationship[id].PHP_EOL;
          $contacts = civicrm_api4('Contact', 'get', [    // liste les contacts impliqués dans la relation, non anonymisé et non supprimés
              'select' => [
                  'id',
                  'display_name',
                ],
              'where' => [
                ['OR', [['id', '=', $relationship['contact_id_a']],['id', '=', $relationship['contact_id_b']]]],
                ['sort_name', 'NOT LIKE', 'ANONYMISE, Anonymisé'],
                ['is_deleted', '=', FALSE],
              ],
              'checkPermissions' => FALSE,
            ]);

            //print_r($contacts);

            //echo "compte : ".count($contacts).PHP_EOL;

            if (count($contacts)==2){// il existe une paire de contacts pour cette relation 
              //echo "Contacts ".$contacts[0]['display_name']." et ".$contacts[1]['display_name']." liés par relation :".$relationship['relationship_type_id:name'].PHP_EOL;     
              echo ".";   
              unset ($relationship['relationship_type_id']);    
              //unset ($relationship['id']);    
              
              array_push($exports, $relationship);   
              } else {
              $error = "Pas de paire de contacts pour la relation ".$relationship['relationship_type_id:name']." (".$relationship['id'].")";
              echo PHP_EOL.$error.PHP_EOL;
              array_push($error_log,$error);
            
            
            } 
      }
      echo PHP_EOL.count($exports)." relations exportés ".PHP_EOL;
      break;

    case 'Custom_Utilisation_du_corps':
      $utilisations = civicrm_api4($entity, 'get', [
        'select' => [
            'id',
            'N_de_pi_ce_ou_de_corps',
            'Compl_ment',
            'D_lai_en_heure_entre_d_c_s_h_0_et_injection',
            'D_lai_en_heure_entre_d_c_s_h_0_et_pr_l_vement_ventuel',
            'Date_de_retour',
            'Sortie',
            'Date_limination_pi_ce',
            'Remarques',

            'Type_de_poi_ce_3:name',
            'cote2:name',
            'Utilisation2:name',
            'Protocole_de_recherche_ex_vivo2:name',
            'Site_inject_:name',
            'M_dium_inject_:name',
            'Imagerie2:name',
            'Inclusion_en_paraffine2:name',
            'Klingler2:name',
            'Mode_limination_hors_corps_2:name',
            'Inventaires:name',

            'Lacalisation',
            'Complement_location',
            'Pr_par_par',
            'entity_id',

          ],
        'checkPermissions' => FALSE,
        //'limit' => 15,
      ]);


      // verifie que les contacts impliqués (entity id, prepare par, localisation) existent bien
      $exports=array();
      $error_log=array();                   // chaine contenant les messages d'erreur à loguer

      foreach ($utilisations as $utilisation){                       // pour chaque utilisation de la base originale
          
          $contacts = civicrm_api4('Contact', 'get', [    // liste les contacts correpondant entity id, prepare par, localisation
              'select' => [
                  'id',
                  'display_name',
                ],
              'where' => [
                ['id', '=', $utilisation['entity_id']],
                ['is_deleted', '=', FALSE],
              ],
              'checkPermissions' => FALSE,
            ]);

            //echo "compte : ".count($contacts).PHP_EOL;

            if (count($contacts)==1){// le contact existe 
              //echo "Contact : ".$contacts[0]['display_name']." impliqué dans l'utlisation :".$utilisation['id'].PHP_EOL;     
              echo ".";   
              unset ($utilisation['id']);    
              
              array_push($exports, $utilisation);   

              } else {
              $error = "Pas de contact lié à l'utilisation ".$utilisation['id']." : ".$contacts[0]['display_name']." (".$contacts[0]['id'].") [Loc : ".$utilisation['Lacalisation']."] [Prep : ".$utilisation['Pr_par_par']."] [Donneur : ".$utilisation['entity_id']."]";
              array_push($error_log,$error);  
            
            } 
      }
      echo PHP_EOL.count($exports)." Utilisation du corps exportées ".PHP_EOL;
      break;

    case 'Custom_Arriv_e_du_corps_new':
          $arrivees = civicrm_api4($entity, 'get', [
            'select' => ['*'],
            'checkPermissions' => FALSE,
            //'limit' => 15,
          ]);


          // verifie que le contacts associé (entity id) existe bien
          $exports=array();
          $error_log=array();                   // chaine contenant les messages d'erreur à loguer

          foreach ($arrivees as $arrivee){     // pour chaque arrivée de la base originale
              
              $contacts = civicrm_api4('Contact', 'get', [    // liste les contacts correpondant entity id, prepare par, localisation
                  'select' => [
                      'id',
                      'display_name',
                    ],
                  'where' => [
                    ['id', '=', $arrivee['entity_id']],
                    ['is_deleted', '=', FALSE],
                  ],
                  'checkPermissions' => FALSE,
                ]);

                //echo "compte : ".count($contacts).PHP_EOL;

                if (isset($contacts[0])){// le contact existe 
                  //echo "Contact : ".$contacts[0]['display_name']." impliqué dans l'utlisation :".$utilisation['id'].PHP_EOL;     
                  echo ".";   
                  unset ($arrivee['id']);    
                  
                  array_push($exports, $arrivee);   

                  } else {
                  $error = "Pas de contact lié à l'arrivee ".$arrivee['id'].PHP_EOL;
                  array_push($error_log,$error);  
                
                } 
          }
          echo PHP_EOL.count($exports)." Arrivées du corps exportées ".PHP_EOL;
          break;
    case 'Contribution':
      $total =0;
      $contributions = civicrm_api4($entity, 'get', [
        'select' => [
          '*',
          'financial_type_id:name',
          'payment_instrument_id:name',
          'contribution_status_id:name',
          ],
        'checkPermissions' => FALSE,
        //'limit' => 15,
      ]);


      // verifie que le contacts lié existe bien
      $exports=array();
      $error_log=array();                   // chaine contenant les messages d'erreur à loguer

      foreach ($contributions as $contribution){           // pour chaque contribution de la  base originale
          
          $contacts = civicrm_api4('Contact', 'get', [    // liste les contacts correspondant (contact_id)
              'select' => [
                  'id',
                  'display_name',
                ],
              'where' => [
                ['id', '=', $contribution['contact_id']],
                ['is_deleted', '=', FALSE],
                ['sort_name', 'NOT LIKE', 'ANONYMISE, Anonymisé'],
              ],
              'checkPermissions' => FALSE,
            ]);

            //echo "compte : ".count($contacts).PHP_EOL;

            if (count($contacts)==1){// le contact existe 
              //echo "Contact : ".$contacts[0]['display_name']." impliqué dans la contribution :".$contribution['id']." (".$contribution['total_amount'].$contribution['currency'].")".PHP_EOL;     
              echo ".";   
              //unset ($contribution['id']);    
              unset ($contribution['financial_type_id']);   
              unset ($contribution['payment_instrument_id']);   
              unset ($contribution['contribution_status_id']);   
              array_push($exports, $contribution);   
              $total = $total + ($contribution['total_amount']); 

            } else {
              $error = "Pas de contact non anonymisé lié à la contribution ".$contribution['id'];
              array_push($error_log, $error); 
              } 
      }
      echo "TOTAL : ".$total.PHP_EOL;
      break;

    case 'Event':
        $total =0;

        $events = civicrm_api4('Event', 'get', [
          'select' => [
            '*',
            'event_type_id:name',
            'financial_type_id:name',
            'participant_listing_id:name',
            'default_role_id:name',
            'address.street_address',
            'address.supplemental_address_1',
            'address.postal_code',
            'address.city',
            'phone.phone',
            'email.email',
          ],
          'where' => [
            ['OR', [['is_template', '=', TRUE], ['is_template', '=', FALSE]]],
          ],
          'join' => [
            ['LocBlock AS loc_block', 'LEFT', ['loc_block.id', '=', 'loc_block_id']],
            ['Address AS address', 'LEFT', ['address.id', '=', 'loc_block.address_id']],
            ['Phone AS phone', 'LEFT', ['phone.id', '=', 'loc_block.phone_id']],
            ['Email AS email', 'LEFT', ['email.id', '=', 'loc_block.email_id']],
          ],
          'checkPermissions' => FALSE,
        ]);

        $exports=array();
        foreach ($events as $event){           // pour chaque evenement de la  base originale
            
                echo "Event : ".$event['id'].PHP_EOL;     
                //echo ".";   
                //unset ($event['id']);    
                unset ($event['event_type_id']);   
                unset ($event['participant_listing_id']);  
                unset ($event['financial_type_id']); 
                unset ($event['default_role_id']); 
                unset ($event['created_id']); 
                array_push($exports, $event);   
              
        }
        echo PHP_EOL.count($exports)." Events exportées ".PHP_EOL;

        break;

    case 'Participant':
      $total =0;
      $participants = civicrm_api4($entity, 'get', [
        'select' => [
          '*',
          'status_id:name',
          'role_id:name',
          ],
        'checkPermissions' => FALSE,
        //'limit' => 15,
      ]);


      // verifie que le contacts lié existe bien
      $exports=array();
      $error_log=array();                   // chaine contenant les messages d'erreur à loguer

      foreach ($participants as $participant){           // pour chaque participant de la  base originale
          
          $contacts = civicrm_api4('Contact', 'get', [    // liste les contacts correspondant (contact_id)
              'select' => [
                  'id',
                  'display_name',
                ],
              'where' => [
                ['id', '=', $participant['contact_id']],
                ['is_deleted', '=', FALSE],
                //['sort_name', 'NOT LIKE', 'ANONYMISE, Anonymisé'],
              ],
              'checkPermissions' => FALSE,
            ]);

            //echo "compte : ".count($contacts).PHP_EOL;

            if (count($contacts)==1){// le contact existe 
              //echo "Contact : ".$contacts[0]['display_name']." participant à l'evenement :".$participant['id'].PHP_EOL;     
              echo ".";   
              //unset ($participant['id']);    
              unset ($participant['role_id']);   
              unset ($participant['status_id']);   
              unset ($participant['created_id']);   
              unset ($participant['transferred_to_contact_id']);   
              unset ($participant['discount_id']);    
              unset ($participant['registered_by_id']);                         
              array_push($exports, $participant);   

            } else {
              $error = "Contact id ".$participant['contact_id']." n'existe pas  - Ignorée";
              echo PHP_EOL.$error.PHP_EOL;
              array_push($error_log,$error);
              } 
      }
      echo PHP_EOL.count($exports)." participants exportés".PHP_EOL;
      break;

    case 'Activity':
      /* 
       Les types d'activités utilisés sont les suivantes ; certaines sont liées à d'autres entités codées par "component_id
      - 'activity_type_id' => 2, 'activity_type_id:label' => 'Appel téléphonique', 'activity_type_id:name' => 'Phone Call', 
      - 'activity_type_id' => 3, 'activity_type_id:label' => 'Courriel', 'activity_type_id:name' => 'Email',
      - 'activity_type_id' => 5, 'activity_type_id:label' => 'Inscription à événement', 'activity_type_id:name' => 'Event Registration'
      - 'activity_type_id' => 6, 'activity_type_id:label' => 'Contribution', 'activity_type_id:name' => 'Contribution', 
      - 'activity_type_id' => 19, 'activity_type_id:label' => 'Mailing', 'activity_type_id:name' => 'Bulk Email', 
      - 'activity_type_id' => 22, 'activity_type_id:label' => 'Imprimer/fusionner le document', 'activity_type_id:name' => 'Print PDF Letter',
      - 'activity_type_id' => 46, 'activity_type_id:label' => 'Paiement', 'activity_type_id:name' => 'Payment',
      - 'activity_type_id' => 47, 'activity_type_id:label' => 'Remboursement', 'activity_type_id:name' => 'Refund',
      - 'activity_type_id' => 49, 'activity_type_id:label' => 'Facture téléchargée', 'activity_type_id:name' => 'Downloaded Invoice',
      - 'activity_type_id' => 51, 'activity_type_id:label' => 'Contact fusionné', 'activity_type_id:name' => 'Contact Merged',
      - 'activity_type_id' => 52, 'activity_type_id:label' => 'Contact supprimé par fusion', 'activity_type_id:name' => 'Contact Deleted by Merge',
      - 'activity_type_id' => 56, 'activity_type_id:label' => 'Document ajouté au dossier','activity_type_id:name' => 'Document ajouté au dossier',
      - 'activity_type_id' => 57, 'activity_type_id:label' => 'Modification des coordonnées', 'activity_type_id:name' => 'Modification des coordonnées',
      - 'activity_type_id' => 58, 'activity_type_id:label' => 'Retrait effets personnels', 'activity_type_id:name' => 'Retrait effets personnels',
      - 'activity_type_id:name', '=', 'Retrait de pace maker',

      Chaque activité (Activity) est reliée : 
        - à des contacts via une entité ActivityContact qui précise : 
            - id du contact (contact_id), 
            - id de l'activité (activity_id)
            - rôle du contact pour cette activité (record_type_id)
                1, "label": "Assignés à l'activité", "name": "Activity Assignees",
                2, "label": "Origine de l'activité", "name": "Activity Source",
                3, "label": "Cibles de l'activité", "name": "Activity Targets",
            Donc deux contacts au moins (source et Target) doivent être reliés à une activité : le créateur (source) et le contact concerné
            Les targets peuvent etre multipls (envois en nombre...)
            Pour que ces fichiers soient créées on présice source, tatget et assignee à la creation de l'activité
        
        - à une autre entité, par exemple une contribution, une cérémonie... 
            c'est le cas pour 
             - 'activity_type_id' => 5,'Event Registration',"component_id:name": "CiviEvent",
             - 'activity_type_id' => 6, 'Contribution',  "component_id:name": "CiviContribute",
             - 'activity_type_id' => 46, 'Payment', "component_id:name": "CiviContribute",
             - 'activity_type_id' => 47, 'Refund', "component_id:name": "CiviContribute",
             POur ces activités liées, l'activité est crée automatiquement à l'import des autres entités.

      
        
       */
      
      $total =0;
      $activity_tokeep=array();           // liste des activités à conserver = 
                                          // - non supprimées
                                          // - liées à un contact source et à un contact target

      $error_log=array();                   // chaine contenant les messages d'erreur à loguer

      $activities = civicrm_api4('Activity', 'get', [   // récupère la liste des activités non supprimées
        'select' => [                                   // qui ne correspondant pas à un evenement ou une contribution (gérées directement à la création de ces entités)
          '*',                                          // et avec source et target
          'activity_type_id:name',
          'status_id:name',
          'priority_id:name',
          'engagement_level:name',
          'source_contact_id',
          'target_contact_id',
          'assignee_contact_id',
        ],
        'where' => [
          ['is_deleted', '=', FALSE],             /// ajouter ici les activités à exporter
          ['OR', [['activity_type_id:name', '=', 'Retrait de pace maker'],['activity_type_id:name', '=', 'Phone Call'], ['activity_type_id:name', '=', 'Email'], ['activity_type_id:name', '=', 'Bulk Email'], ['activity_type_id:name', '=', 'Print PDF Letter'], ['activity_type_id:name', '=', 'Payment'], ['activity_type_id:name', '=', 'Refund'], ['activity_type_id:name', '=', 'Downloaded Invoice'], ['activity_type_id:name', '=', 'Contact Merged'], ['activity_type_id:name', '=', 'Contact Deleted by Merge'], ['activity_type_id:name', '=', 'Document ajouté au dossier'], ['activity_type_id:name', '=', 'Modification des coordonnées'], ['activity_type_id:name', '=', 'Retrait effets personnels']]],
          ['source_contact_id', '<>', NULL],
          ['target_contact_id', '<>', NULL],
          //['id','IN',[36083, 37433, 37375, 37485]],
        ],
        'orderBy' => [
          'parent_id' => 'ASC',
        ],
        'checkPermissions' => FALSE,
      ]);



      foreach($activities as $activity){               // Pour chacune des activités
        //echo "Activité : ".$activity['id'].PHP_EOL;
        echo ".";
        
        if ($activity['assignee_contact_id'] != NULL){
          $assignee=array();
          foreach ($activity['assignee_contact_id'] as $assignee_id){ // on verifie que chaque contact assignees existe bien dans la base et n'est pas annulé
            $assignees = civicrm_api4('Contact', 'get', [     
              'select' => [                                  
                'id',    
              ],
              'where' => [
                ['id', '=', $assignee_id],
                ['is_deleted', '=', FALSE],
              ],
              'checkPermissions' => FALSE,
            ]);
          
            if ($assignees[0]['id'] != NULL){                 // si le contact existe on concatene son id avec $assignees
              array_push ($assignee, $assignees[0]['id']);
            } else {

                $error = "Contact Assigné (id: ".$assignee_id.") manquant supprimé de l'activité : ".$assignee_id;
                echo PHP_EOL.$error.PHP_EOL;
                array_push($error_log,$error);
            }
          }
          $activity['assignee_contact_id']=$assignee;         // pour l'ensemble des contact : on remplace l'ancienne par la nouvelle liste
          
        }
        

        $target=array();
        foreach ($activity['target_contact_id'] as $target_id){ // on verifie que chaque contact target existe bien dans la base et n'est pas annulé
          $targets = civicrm_api4('Contact', 'get', [     
            'select' => [                                  
              'id',    
            ],
            'where' => [
              ['id', '=', $target_id],
              ['is_deleted', '=', FALSE],
            ],
            'checkPermissions' => FALSE,
          ]);
        
          if (isset($targets[0])){                 // si le contact existe on concatene son id avec $target
            array_push ($target, $targets[0]['id']);
          } else {
            $error = "Contact Target (id: ".$target_id.") manquant supprimé de l'activité : ".$activity['id'];
            echo PHP_EOL.$error.PHP_EOL;
            array_push($error_log,$error);
        }
        }
        $activity['target_contact_id']=$target;         // pour l'ensemble des contact : on remplace l'ancienne par la nouvelle liste
        

        //print_r($target).PHP_EOL;

        $source = civicrm_api4('Contact', 'get', [
          'where' => [
            ['id', '=', $activity['source_contact_id']],
            ['is_deleted', '=', FALSE],
          ],
          'checkPermissions' => FALSE,
        ]);
        $produit=count($source)*count($target);
        
      if($produit!=0){                  // si'l existe bien une source et au moins un target
          //$activity['original_id']=$activity['id'];
          //unset ($activity['id']);
          unset ($activity['activity_type_id']);
          unset ($activity['status_id']);
          unset ($activity['engagement_level']);
          unset ($activity['priority_id']);
          unset ($activity['source_record_id']);
          
          array_push($activity_tokeep, $activity);// on pousse cette activité contact(s) dans $activity_tokeep, liste des activity  à exporter
      }else{
        $error="ACTIVITÉ Non exportée car contact manquant : S : ".count($source)."     T: ".count($targets)."  S*T :".$produit;
        echo PHP_EOL.$error.PHP_EOL;
        array_push($error_log,$error);
      }
      }
        $exports = $activity_tokeep;
      //print_r($exports);
      echo PHP_EOL.count($exports)." activités exportées".PHP_EOL;
      break;
    case 'File':
      /*
      - les files ont chacun un id et contiennent le lien vers le fichier sur le disque
      - les Entity files relient les fichiers à l'élément ['entity_id] d'une table ["entity_table"]
        cette table peut être 
            1- "civicrm_activity"
            2- "civicrm_document_version"
            3- "civicrm_msg_template"
            4- "civicrm_note"
            5- "civicrm_value_promesse_de_d_17"
            6- "civicrm_value_test_10"
        Seuls 1-4 sont gérés ; les autres, dont la fonction n'est pas claire, sont ignorés.

      */
      $total =0;
      $file_tokeep=array();           // liste des fichiers à conserver
      $entity_file_tokeep=array();    // liste des entityfiles (lien aux notes, activités...) à conserver
      $error_log=array();  
                                     
      $entityFiles = civicrm_api4('EntityFile', 'get', [   // recupère les lien entre fichier et entité : note, activité ou dossier)
        'select' => [
          '*',
        ],
        //'where' => [
          //['entity_table:name', '=', 'civicrm_activity'],
          //['entity_table:name', '=', 'civicrm_note'],
          //['entity_table', '=', 'civicrm_document_version'],
          //['entity_table', '=', 'civicrm_msg_template'],
          //['entity_table', '=', 'civicrm_value_promesse_de_d_17'],
          
        //],
        'checkPermissions' => FALSE,
        //'limit' => 25,
        //'offset' => 175,
      ]);

  

      foreach ($entityFiles as $entityFile){          // pour chaque entity file (lien entre fichier et entité : note, activité ou case)
        
        $error=array(); 
        switch ($entityFile['entity_table']){         // target indique l'entité considérée ; note, activite ou case
          case 'civicrm_activity':
            $target = "Activity";
            break;

          case 'civicrm_note':
            $target = "Note";
            break;

          case 'civicrm_case':
            $target = "Case";
            break;

          case 'civicrm_document_version':
            $target = "DocumentVersion";
            break;

          case 'civicrm_msg_template':
            $target = "MessageTemplate";
            break;

          default : // dans les autres cas on ignore 
            continue 2;     // continue passe à l'element suivant dans la boucle swithcn ; 2 fait passer au suivanr dans le foreach situe plus haut

        }
        //echo $entityFile['id']."   ".$entityFile['entity_id'].PHP_EOL;
        $entity_check = civicrm_api4($target, 'get', [
          'where' => [
            ['id', '=', $entityFile['entity_id']],
          ],
          'checkPermissions' => FALSE,
        ]);

        if (!isset($entity_check[0]['id'])){         // si l'entité liée au fichier n'existe pas
          //print_r($entityFile);
          $error= "Entity file : ".$entityFile['id']." : ".$target." id ".$entityFile['entity_id']." n'existe pas dans la base originale";
          echo PHP_EOL.$error.PHP_EOL;
          array_push($error_log,$error);
        }

        $files = civicrm_api4('File', 'get', [
          'where' => [
            ['id', '=',  $entityFile['file_id']],
          ],
          'checkPermissions' => FALSE,
        ]);

        if (!isset($files[0]['id'])){         // si le fichier n'existe pas
          //print_r($entityFile);
          $error= "Entity file : ".$entityFile['id']." : le fichier id ".$entityFile['file_id']." n'existe pas dans la base originale";
          echo PHP_EOL.$error.PHP_EOL;
          array_push($error_log,$error);
        }

        if ((!isset($entity_check[0]['id'])) OR (!isset($files[0]['id']))){   // si le fichier ou l'entité manquent
          $error= "Entity file : ".$entityFile['id']." non crée";
          echo PHP_EOL.$error.PHP_EOL;
          array_push($error_log,$error);

        }else{
          array_push($entity_file_tokeep, $entityFile);         // on ajoute cet entityfile à ceux à importer
          array_push($file_tokeep, $files[0]);                  // on ajoute ce fichier à ceux à importer
          echo "Entity file ".$entityFile['id']." exportée pour ".$target." ".$entityFile['entity_id']." et fichier ".$entityFile['file_id'].PHP_EOL;
        }

      }

      $exp_file_entity = $name."_entityFiles.txt";
      file_put_contents($exp_file_entity, json_encode($entity_file_tokeep, JSON_PRETTY_PRINT));
      $exports = $file_tokeep;
      echo PHP_EOL.count($exports)." entity file exportés".PHP_EOL;

      $files = civicrm_api4('File', 'get', [
        'select' => [
          '*',
          'entity_file.*',
        ],
        'join' => [
          ['EntityFile AS entity_file', 'LEFT'],
        ],
        'where' => [
          ['entity_file.file_id', 'IS NULL'],
        ],
        'checkPermissions' => FALSE,
      ]);

      echo "NB ".count($files)." fichiers orphelins non importés".PHP_EOL;
      break;

    case 'Note':
      $notes_tokeep=array();            // liste des notes à conserver (contacts existants)
      $error_log=array();               // liste des erreurs
      $notes = civicrm_api4($entity, 'get', [
        'checkPermissions' => FALSE,
      ]);

      foreach ($notes as $note){
        $error=array();                   /// messages erreur pour cette note
        switch ($note['entity_table']){
          case 'civicrm_contact':
            $target = 'Contact';
            $checked_entities = civicrm_api4($target, 'get', [    // on recherche les contacts correspondants à entity_id
              'select' => [
                'id',
              ],
              'where' => [
                ['id', '=', $note['entity_id']],
                ['is_deleted', '=', FALSE],
              ],
              'checkPermissions' => FALSE,
            ]);
    
            if (isset($checked_entities[0]['id'])){         // si un contact existe, on ajoute la note à la liste à exporter
              array_push($notes_tokeep,$note);
              echo ".";
            }else{
              $error="Note ".$note['id']." non conservée : ".$target." id ".$note['entity_id']." pour entitmanque";
              echo PHP_EOL.$error.PHP_EOL;
              array_push($error_log,$error);
            }
            break;

          case 'civicrm_relationship':     
            $target = 'Relationship';
            $checked_entities = civicrm_api4($target, 'get', [   // on vérifie que la relation existe
              'select' => [
                'id',
              ],
              'where' => [
                ['id', '=', $note['entity_id']],
              ],
              'checkPermissions' => FALSE,
            ]);

            if (isset($checked_entities[0]['id'])){
              $contacts = civicrm_api4('Contact', 'get', [   // on vérifie que le cntact lié à cette relation existe
                'select' => [
                  'id',
                ],
                'where' => [
                  ['id', '=', $note['contact_id']],
                  ['is_deleted', '=', FALSE],
                ],
                'checkPermissions' => FALSE,
              ]);

              if (isset($contacts[0]['id'])){
                array_push($notes_tokeep,$note);
                echo ".";
              }else{
                $error="Note ".$note['id']." non conservée : contact ".$note['contact_id']." manque ou supprimé";
                echo PHP_EOL.$error.PHP_EOL;
                array_push($error_log,$error);
              }

            } else {
              $error="Note ".$note['id']." non conservée : ".$target." id ".$note['entity_id']." manque ou supprimé";
              echo PHP_EOL.$error.PHP_EOL;
              array_push($error_log,$error);
            }

            break;

          case 'civicrm_participant':
            $target = 'Participant';
            $checked_entities = civicrm_api4($target, 'get', [   // on vérifie que l'inscription du participant existe
              'select' => [
                'id',
                'contact_id',
              ],
              'where' => [
                ['id', '=', $note['entity_id']],
              ],
              'checkPermissions' => FALSE,
            ]);

            if (isset($checked_entities[0]['id'])){
              $contacts = civicrm_api4('Contact', 'get', [   // on vérifie que le cntact lié à cette relation existe
                'select' => [
                  'id',
                ],
                'where' => [
                  ['id', '=', $checked_entities[0]['contact_id']],
                  ['is_deleted', '=', FALSE],
                ],
                'checkPermissions' => FALSE,
              ]);

              if (isset($contacts[0]['id'])){
                array_push($notes_tokeep,$note);
                echo ".";
              }else{
                $error="Note ".$note['id']." non conservée : id du participant ".$note['contact_id']." manque ou supprimé";
                echo PHP_EOL.$error.PHP_EOL;
                array_push($error_log,$error);
              }

            } else {
              $error="Note ".$note['id']." non conservée : ".$target." id ".$note['entity_id']." manque ou supprimé";
              echo PHP_EOL.$error.PHP_EOL;
              array_push($error_log,$error);
            }

            break;

          case 'civicrm_contribution':
            $target = 'Contribution';
            $checked_entities = civicrm_api4($target, 'get', [   // on vérifie que ce don existe
              'select' => [
                'id',
                'contact_id',
              ],
              'where' => [
                ['id', '=', $note['entity_id']],
              ],
              'checkPermissions' => FALSE,
            ]);

            if(isset($checked_entities[0]['id'])){
              $contacts = civicrm_api4('Contact', 'get', [   // on vérifie que le cntact auquel la note se rattache existe et pas supprimé
                'select' => [
                  'id',
                ],
                'where' => [
                  ['id', '=', $note['contact_id']],
                  ['is_deleted', '=', FALSE],
                ],
                'checkPermissions' => FALSE,
              ]);
  
              if (isset($contacts[0]['id'])){
                array_push($notes_tokeep,$note);
                echo ".";
              }else{
                $error="Note ".$note['id']." non conservée : id du participant ".$note['contact_id']." manque ou supprimé";
                echo PHP_EOL.$error.PHP_EOL;
                array_push($error_log,$error);
              }
            }else{
              $error="Note ".$note['id']." non conservée : ".$target." id ".$note['entity_id']." manque ou supprimé";
              echo PHP_EOL.$error.PHP_EOL;
              array_push($error_log,$error);
            }

            break;

          case 'civicrm_note':
            $target = 'Note';
            break;
        }
      }
      $exports = $notes_tokeep;
      echo PHP_EOL.count($exports)." notes  exportées".PHP_EOL;
      break;

    case 'DocumentContact':
      $documentContacts = civicrm_api4('DocumentContact', 'get', [
        'select' => [
          '*',
          'document.subject',
          'document.type_id:name',
          'document.status_id:name',
          'document.added_by',
          'document.updated_by',
          'document.date_added',
          'document.date_updated',
        ],
        'join' => [
          ['Document AS document', 'LEFT'],
        ],
        //'where' => [
        //  ['id', '>', 1975],
        //],
        //'limit' => 25,
        'checkPermissions' => FALSE,
      ]);

      $exports=array();

      foreach ($documentContacts as $documentContact){           // pour chaque evenement de la  base originale
        echo PHP_EOL."Traitement DocumentContact id : ".$documentContact['id'].PHP_EOL;

        $contacts = civicrm_api4('Contact', 'get', [        // on recherche le contact associé à contact id (celui auquel le docment se rapporte)
          'select' => [
            'id',
          ],
          'where' => [
            ['id', '=', $documentContact['contact_id']],
          ],
          'checkPermissions' => FALSE,
        ]);

        if (isset($contacts[0]['id'])){           // le contact existe 
          echo " Contact lié existe id : ".$documentContact['contact_id'].PHP_EOL;  
        } else {
          $error="DocumentContact id : ".$documentContact['id']." : contact associé ".$documentContact['contact_id']." n'existe pas";
          echo $error.PHP_EOL;
          array_push($error_log,$error);
        }

        $documents = civicrm_api4('Document', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['id', '=', $documentContact['document_id']],
          ],
          'checkPermissions' => FALSE,
        ]);

        if (isset($documents[0]['id'])){           // le document existe 
          echo " Document lié existe id : ".$documentContact['document_id'].PHP_EOL;  
        } else {
          $error="DocumentContact id : ".$documentContact['id']." : document associé ".$documentContact['document_id']." n'existe pas";
          echo $error.PHP_EOL;
          array_push($error_log,$error);
        }

        if (isset($documents[0]['id']) AND isset($contacts[0]['id'])){
          echo " Export de DocumentContact id : ".$documentContact['id'].PHP_EOL;
          array_push($exports, $documentContact);
        } else {
          $error="DocumentContact id : ".$documentContact['id']." Non exporté";
          echo $error.PHP_EOL;
          array_push($error_log,$error);
        }
      }
      echo PHP_EOL.count($exports)." DocumentContacts  exportées".PHP_EOL;

      $documents = civicrm_api4('Document', 'get', [
        'select' => [
          'id',
        ],
        'join' => [
          ['DocumentContact AS document_contact', 'LEFT'],
        ],
        'where' => [
          ['document_contact.contact_id', 'IS NULL'],
        ],
        'checkPermissions' => FALSE,
      ]);

      //print_r($documents);
      echo "NB. ".count($documents)." documents orphelins non exportés".PHP_EOL;


      break;


    case 'DocumentVersion':
      $documentVersions = civicrm_api4('DocumentVersion', 'get', [
        //'limit' => 25,
        'checkPermissions' => FALSE,
      ]);

      $exports=array();

      foreach ($documentVersions as $documentVersion){           // pour chaque document version de la  base originale
        $documents = civicrm_api4('Document', 'get', [
          'select' => [
            'id',
          ],
          'where' => [
            ['id', '=', $documentVersion['document_id']],
          ],
          'checkPermissions' => FALSE,
        ]);

        if (isset($documents[0]['id'])){           // le document existe ; on crée l'entrée 
          echo "Créafion DocumentVersion id : ".$documentVersion['id']." avec document ".$documentVersion['document_id'].PHP_EOL;  
          array_push($exports, $documentVersion);   

        } else {
          $error="DocumentVersion id : ".$documentVersion['id']." : document associé ".$documentVersion['document_id']." n'existe pas";
          echo $error.PHP_EOL;
          array_push($error_log,$error);

          $error="DocumentVersion id : ".$documentVersion['id']." : Non exporté";
          echo $error.PHP_EOL;
          array_push($error_log,$error);
        }
      }
      echo PHP_EOL.count($exports)." DocumentContacts  exportées".PHP_EOL;
      break;

    case 'Custom_Protocoles_in_vivo':
      $protocolesInVivos = civicrm_api4('Custom_Protocoles_in_vivo', 'get', [
        'select' => [
          'id',
          'Intitul_du_protocole:name',
          'identifiant_dans_le_s_protocole_s_',
          'date_d_inclusion_in_vivo',
          'entity_id',
        ],
        'checkPermissions' => FALSE,
      ]);

      $exports=array();
      foreach ($protocolesInVivos as $protocolesInVivo){
        echo "Protocole in vivo id : ".$protocolesInVivo['id']." (".$protocolesInVivo['Intitul_du_protocole:name'].") ";
        $contacts = civicrm_api4('Contact', 'get', [    // liste les contacts correspondant (contact_id)
          'select' => [
              'id',
            ],
          'where' => [
            ['id', '=', $protocolesInVivo['entity_id']],
            ['is_deleted', '=', FALSE],
          ],
          'checkPermissions' => FALSE,
        ]);

        if (isset($contacts[0]['id'])){                     // si le contact lié existe et n'est pas à la corbeille
          echo "pour Contact id :".$protocolesInVivo['entity_id']." Exporté".PHP_EOL;
          array_push($exports,$protocolesInVivo);
        } else {                                        // si ce contact n'existe pas ou à la corbeille
          echo "Contact lié (".$protocolesInVivo['entity_id'].") n'existe pas ou à la corbeille. PAS D'EXPORT".PHP_EOL;
          $error="Protocole in vivo id : ".$protocolesInVivo['id']." (".$protocolesInVivo['Intitul_du_protocole:name']." Contact lié (".$protocolesInVivo['entity_id'].") n'exsite pas ou à la corbeille. PAS D'EXPORT";
          array_push($error_log,$error);
        }

      }
      echo PHP_EOL.count($exports)." Protocoles in vivo exportées ".PHP_EOL;
      break;

    case 'Tag':
      $error=array();                                     // chaine des messages d'erreur
      $exports=array();                                   // chaine des elements à exporter

      $tags = civicrm_api4('Tag', 'get', [                    // on récupère les tags
        'select' => [
          '*',
        ],
        'checkPermissions' => FALSE,
      ]);

      if(isset($tags[0])){          // si des tags existent
        $exports=$tags;
      }else{                        // en l'absence de Tag
        $error = "Pas de tag à exporter";
      }
      echo PHP_EOL.count($exports)." tags  exportés".PHP_EOL;
      break;

    case 'EntityTag':
      $error=array();                                     // chaine des messages d'erreur
      $exports=array();                                   // chaine des elements à exporter

      $entityTags = civicrm_api4('EntityTag', 'get', [        // on récupère les entités liées au tags
        'select' => [
          '*',
        ],
        'checkPermissions' => FALSE,
      ]);

      foreach ($entityTags as $entityTag){
        switch ($entityTag['entity_table']){         // target indique l'entité considérée ; note, activite ou case
          
          case 'civicrm_file':
            $target = "File";
            break;

          case 'civicrm_activity':
            $target = "Activity";
            break;

          case 'civicrm_contact':
            $target = "Contact";
            break;

          /*case 'civicrm_case':                // ignore tags pour civicases (non utilisés)
            $target = "Case";
            break;*/

          /*case 'ccivicrm_saved_search':       // ignore tags pour saved search (tags à l'installation)
            $target = "SavedSearch";
            break;*/

          default : // dans les autres cas on ignore 
            continue 2;     // continue passe à l'element suivant dans la boucle swithcn ; 2 fait passer au suivanr dans le foreach situe plus haut
        }

        $entity_check = civicrm_api4($target, 'get', [      // on recherche une entité avec l'id définie dans le entitytag
          'where' => [
            ['id', '=', $entityTag['entity_id']],
          ],
          'limit' => 1,
          'checkPermissions' => FALSE,
        ]);

        if (!isset($entity_check[0]['id'])){             // si l'entité n'existe pas
          $error= "Entity tag : ".$entityTag['id']." non importée : l'entité ".$target." avec l'id ".$entityTag['entity_id']." n'existe pas dans la base originale";
          echo PHP_EOL.$error.PHP_EOL;
          array_push($error_log,$error);

        } else {                                        // si l'entité existe
           $tags = civicrm_api4('Tag', 'get', [         // on recherche un tag avec l'id définie dans le entitytag
            'where' => [
              ['id', '=', $entityTag['tag_id']],
            ],
            'limit' => 1,
            'checkPermissions' => FALSE,
          ]);

          if (!isset($tags[0]['id'])){                   // si le tag n'existe pas
            $error= "Entity tag : ".$entityTag['id']." non importée : le tag avec l'id ".$entityTag['tag_id']." n'existe pas dans la base originale";
            echo PHP_EOL.$error.PHP_EOL;
            array_push($error_log,$error);
  
          } else {                                       // si le tag et l'entité existent
            array_push($exports, $entityTag);
            echo ".";
          }
        }
      }
      echo PHP_EOL.count($exports)." tags exportés".PHP_EOL;
      break;

      
  }   // fin du swith 

  if (isset($error_log)){
    echo PHP_EOL."Erreurs :".PHP_EOL;
    print_r($error_log).PHP_EOL;
  }
  
  file_put_contents($exp_file, json_encode($exports, JSON_PRETTY_PRINT));
}



// export des OptionValues
  if ($check_option_values == 1){
    if ($ville=='lyon')
      {
      $opts =[
        'migrate_Custom_Promesse_don' => 1,
        'migrate_Arrivee_corps' => 1,
        'migrate_utilisation_corps' => 1,
      ];
  
      export_lyon($opts);

      exit;
      }


    $exp_file = $exp_dir."03_option_values";
    $subtype  =[                       // tableau des valeurs d'option (choix multiples...) pour verifier correspondance entre Source et Cibles
        //'Compl_m_nt_tat_civil.Civilit_user',
        
        'Ant_c_dents_m_dicaux.Stimulateur_pile',
        'Ant_c_dents_m_dicaux.Pathologie_cible',
        
        'Promesse_de_don.Centre_de_don',
        'Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie',
        'Promesse_de_don.Souhait_lecture_nom',
        'Promesse_de_don.Souhiat_affichage_st_le',
        'Promesse_de_don.Devenir_souhait_',
        
        'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps',
        'Prise_en_charge_au_d_c_s.Lieu_de_d_c_s',
        
        'Devenir_du_corps.CESP',
        'Devenir_du_corps.devenir_effectif_du_corps',
        'Devenir_du_corps.Souhait_funeraire_personne_ref_rente',
        
        'Transfert_vers_autre_centre.CDC_de_transfert',
        
        'Arriv_e_du_corps_new.Retrait_Stimulateur_piles',
        'Arriv_e_du_corps_new.S_rologie_VIH',
        'Arriv_e_du_corps_new.S_rologie_HBV',
        'Arriv_e_du_corps_new.S_rologie_COVID',
        'Arriv_e_du_corps_new.PCR_COVID',
        'Arriv_e_du_corps_new.PCR_COVID_nasopharyng_e',
        
        'infos_personnel.M_tier',
        'infos_personnel.Cat_gorie',
        'infos_personnel.BAP',
        'infos_personnel.Contrat',
        
        'champs_caches.toutes_utilisations',
        'champs_caches.toutes_pieces',

        'Utilisation_du_corps.Type_de_poi_ce_3',
        'Utilisation_du_corps.cote2',
        'Utilisation_du_corps.Utilisation2',
        
        'Utilisation_du_corps.Protocole_de_recherche_ex_vivo2',
        'Utilisation_du_corps.Site_inject_',
        'Utilisation_du_corps.M_dium_inject_',
        'Utilisation_du_corps.Imagerie2',    
        'Utilisation_du_corps.Inclusion_en_paraffine2',
        'Utilisation_du_corps.Klingler2',
        'Utilisation_du_corps.Mode_limination_hors_corps_2',
        'Utilisation_du_corps.Inventaires',
        'event_type',
        'participant_role',
        'activity_type',
        'activity_contacts',
        'activity_status',
        'document_status',
        'document_type',

        'Protocoles_in_vivo.intitul_du_protocole',


        //'contribution_status',  
        //'payment_instrument',   
        // 'financial_type',  

        ];

        if ($ville=='lyon')
          {
            $subtype  =[                       // tableau des valeurs d'option (choix multiples...) pour verifier correspondance entre Source et Cibles            
            'Ant_c_dents_m_dicaux.Stimulateur_pile',
            'Ant_c_dents_m_dicaux.Pathologie_cible',
            
            'Promesse_de_don.Centre_de_don',
            'Promesse_de_don.Pr_venir_personne_r_f_rence_de_la_c_r_monie',
            'Promesse_de_don.Souhait_lecture_nom',
            'Promesse_de_don.Souhiat_affichage_st_le',
            'Promesse_de_don.Devenir_souhait_',
            
            'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps',
            'Prise_en_charge_au_d_c_s.Lieu_de_d_c_s',
            
            'Devenir_du_corps.CESP',
            'Devenir_du_corps.devenir_effectif_du_corps',
            'Devenir_du_corps.Souhait_funeraire_personne_ref_rente',
            
            'Transfert_vers_autre_centre.CDC_de_transfert',
            
            'Arriv_e_du_corps_new.Retrait_Stimulateur_piles',
            'Arriv_e_du_corps_new.S_rologie_VIH',
            'Arriv_e_du_corps_new.S_rologie_HBV',
            'Arriv_e_du_corps_new.S_rologie_COVID',
            'Arriv_e_du_corps_new.PCR_COVID',
            'Arriv_e_du_corps_new.PCR_COVID_nasopharyng_e',

            'Utilisation_du_corps.Type_de_poi_ce_3',
            'Utilisation_du_corps.cote2',
            'Utilisation_du_corps.Utilisation2',
            
            'Utilisation_du_corps.Protocole_de_recherche_ex_vivo2',
            'Utilisation_du_corps.Site_inject_',
            'Utilisation_du_corps.M_dium_inject_',
            'Utilisation_du_corps.Imagerie2',    
            'Utilisation_du_corps.Mode_limination_hors_corps_2',
            'Utilisation_du_corps.Inventaires',
            'event_type',
            'participant_role',
            'activity_type',
            'activity_contacts',
            'activity_status',
            'document_status',
            'document_type',

            'Protocoles_in_vivo.intitul_du_protocole',


            //'contribution_status',  
            //'payment_instrument',   
            // 'financial_type',  

            ];
          }

       //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
       export_stuff('OptionValue',$subtype, $exp_file);
      }

// export de custom fields
  if ($check_custom_field == 1){
      $exp_file = $exp_dir."02_CustomField";  
      export_stuff('CustomField','CustomField', $exp_file);
    }


// export organisations
  if ($import_organisations ==1){
      $exp_file = $exp_dir."05_organisations";
      $subtype = 'Organization';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff('Organization',$subtype, $exp_file);
    }

// export financial types

  if($import_FinancialType ==1){
      $exp_file = $exp_dir."12_FinancialType";
      $subtype =  'FinancialType';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff('FinancialType',$subtype, $exp_file);
    }



    // export individus

  if($import_individus ==1){
      $exp_file = $exp_dir."10_individuals";
      $subtype =  'Individual';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff('Individual',$subtype, $exp_file);
    }
// export groups
  if($import_groups ==1){
    $exp_file = $exp_dir.'28_Groups';                              // nom du fichier à exporter sans le suffixe : juste les groupes
    export_stuff('Group','Group', $exp_file);
    echo PHP_EOL.$exp_file." exporté".PHP_EOL;

    $exp_file = $exp_file.'_Groups_Contacts';                     // nom du fichier à exporter sans le suffixe : les groupes attachés aux contacts
    export_stuff('GroupContact','GroupContact', $exp_file);
    echo PHP_EOL.$exp_file." exporté".PHP_EOL;
  }
 
// export adresses
  if($import_adresses == 1 ){
      $exp_file = $exp_dir."15_adresses";
      $subtype =  'Address';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff($subtype,$subtype, $exp_file);
    }
// export telephone
  if($import_telephones==1){
      $exp_file = $exp_dir."20_telephone";
      $subtype =  'Phone';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff($subtype,$subtype, $exp_file);
    }
// export Email
  if($import_email==1){
      $exp_file = $exp_dir."25_Email";
      $subtype =  'Email';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff($subtype,$subtype, $exp_file);
    }
// export Relationship
  if($import_relationships ==1){
      $exp_file = $exp_dir."30_Relationship";
      $subtype =  'Relationship';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff($subtype,$subtype, $exp_file);
    }
// export Custom_Utilisation_du_corps
  if ($import_utilisations ==1){
      $exp_file = $exp_dir."40_Custom_Utilisation_du_corps";
      $subtype =  'Custom_Utilisation_du_corps';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff($subtype,$subtype, $exp_file);
  }


// export Custom_arrivée_du_corps
  if ($import_arrivee ==1){
      $exp_file = $exp_dir."42_Custom_Arrivee_du_corps";
      $subtype =  'Custom_Arriv_e_du_corps_new';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff($subtype,$subtype, $exp_file);
  }
// export Protocoles in vivo
  if ($import_protinvivo ==1){
  $exp_file = $exp_dir."45_Custom_ProtocolesInVivo";
  $subtype =  'Custom_Protocoles_in_vivo';
  export_stuff('Custom_Protocoles_in_vivo','Custom_Protocoles_in_vivo', $exp_file);
  }

// export Contribution
  if($import_contributions == 1){
  $exp_file = $exp_dir."50_Contribution";
  $subtype =  'Contribution';
  //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
  export_stuff('Contribution','Contribution', $exp_file);
  }
// export Event
  if($import_events ==1){
      $exp_file = $exp_dir."60_Event";
      $subtype =  'Event';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff('Event','Event', $exp_file);
    }

// export Participant
  if($import_participants ==1){
      $exp_file = $exp_dir."70_Participant";
      $subtype =  'Participant';
      //echo "Exporting ".$subtype." into ".$exp_file.PHP_EOL;
      export_stuff('Participant','Participant', $exp_file);
    }
// export Activité et activityContacts
  if($import_activites ==1){
    $exp_file = $exp_dir.'80_Activites';                     // nom du fichier à exporter sans le suffixe 
    export_stuff('Activity','Activity', $exp_file);
    echo $exp_file." exporté".PHP_EOL;
  }
// export notes
  if($import_notes ==1){
    $exp_file = $exp_dir.'100_Notes';                     // nom du fichier à exporter sans le suffixe 
    export_stuff('Note','Note', $exp_file);
    echo PHP_EOL.$exp_file." exporté".PHP_EOL;
  }
// export documents
// export documentsContact
  if($import_documentsContact ==1){
    $exp_file = $exp_dir.'115_DocumentsContact';                     // nom du fichier à exporter sans le suffixe 
    export_stuff('DocumentContact','DocumentContact', $exp_file);
    echo $exp_file." exporté".PHP_EOL;
    }

// export documentsVersion
  if($import_documentsVersion ==1){
    $exp_file = $exp_dir.'120_DocumentsVersion';                     // nom du fichier à exporter sans le suffixe 
    export_stuff('DocumentVersion','DocumentVersion', $exp_file);
    echo $exp_file." exporté".PHP_EOL;
    }

// export files
  if($import_files ==1){
    $exp_file = $exp_dir.'90_Files';                     // nom du fichier à exporter sans le suffixe 
    export_stuff('File','File', $exp_file);
    echo $exp_file." exporté".PHP_EOL;
  }

// export tags
if($import_tags ==1){
  $exp_file = $exp_dir.'130_Tags';                            // nom du fichier à exporter sans le suffixe 
  export_stuff('Tag','Tag', $exp_file);
  echo $exp_file." exporté".PHP_EOL;

  $exp_file = $exp_file."_entityFiles";                    // nom du fichier à exporter sans le suffixe 
  export_stuff('EntityTag','EntityTag', $exp_file);
  echo $exp_file." exporté".PHP_EOL;
}


