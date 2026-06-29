<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;


function create_rules(){

$msg="  - Création des Rules".PHP_EOL;
      $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
      fwrite($fp, $msg);
      echo $msg;

      $msg="      -> Civirule : Déplace lot de pièces ".PHP_EOL;
        fwrite($fp, $msg);
        if (VERBOSE==1){
          echo $msg;
        }
        fclose($fp);

        $to_create =  [        //Déplace lot : Déclaration de l'Action
          'entity' => 'CiviRulesAction',
          'values' => [
          'name' => 'deplacelot',
          'label' => 'Déplace un lot de pièces anatomiques',
          'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Deplacelot',
            'is_active' => TRUE,
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
          ],
        ];
        create_entity($to_create);

        $to_create =  [         //Déplace lot : Rule
                    
          'entity' => 'CiviRulesRule',
          'values' => [
            'trigger_id:name' => 'new_activity',
            'name' => 'déplace_un_lot_de_pièces_anatomiques_',
            'label' =>'Déplace un lot de pièces anatomiques ',
            'trigger_params' => 'a:1:{s:11:"record_type";s:1:"3";}',
            'is_active' => TRUE,
            'description' => 'Déplace un lot de pièces vers le local depuis lequel une activité déplacer pièces anatomiques est créée',
            'help_text' => "<p>Déplace un lot de pièces identifiées par leurs codes-Barres.</p>\r\n\r\n<p>Lorsqu'une action de type Déplacement de lot de pièce anatomique est créée, elle déplace les pièces figurant dans le champ détails de l'activité vers le contact depuis lequel l'activité est crée (local de conservation).</p>\r\n\r\n<p>Les pièces manquantes ou déjà détruites sont localisées dans cette pièce de stockage et leur statut est modifié en <em>Non Eliminé.</em></p>\r\n",
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
            'is_debug' => FALSE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [    //Déplace lot : Rule Action
          'entity' => 'CiviRulesRuleAction',
          'values' => [
            'action_params' => NULL,
            'delay' => NULL,
            'ignore_condition_with_delay' => 0,
            'is_active' => TRUE,
            'rule_id.name' => 'déplace_un_lot_de_pièces_anatomiques_',
            'action_id.name' => 'deplacelot',
          ],
        ];
        create_entity($to_create);

        //Déplace lot : Rule condition

        $weight = civicrm_api4('OptionValue', 'get', [ // récupère le weight du type de l'activité qui est utilisé comme activity_type_id
            'select' => [
                'weight',
            ],
            'where' => [
                ['name', '=', 'Déplacer un lot de pièces/corps'],
            ],
            'checkPermissions' => FALSE,
            ]);

        $activity_type_id = $weight[0]['weight'];
        
        $to_create =  [                   
          'entity' => 'CiviRulesRuleCondition',
          'values' => [
          'condition_link' => NULL,
          'is_active' => TRUE,
          'condition_id.name' => 'activity_of_type',
          'rule_id.name' => 'déplace_un_lot_de_pièces_anatomiques_',
          'condition_link' => NULL,
          'condition_params' => [
            'operator' => '0',
            'activity_type_id' => [
                $activity_type_id,
            ],
            ],
          ],
        ];
        create_entity($to_create);

      $msg="      -> Civirule : Supprime lot de pièces ".PHP_EOL;
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        fclose($fp);
          if (VERBOSE==1){
            echo $msg;
          }

        $to_create =  [        //Supprime lot : Déclaration de l'Action
          'entity' => 'CiviRulesAction',
          'values' => [
          'name' => 'supprimelot',
          'label' => 'Supprime un lot de pièces anatomiques',
          'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Supprimelot',
            'is_active' => TRUE,
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
          ],
        ];
        create_entity($to_create);

        $to_create =  [         //Supprime Lot : Rule
                  
          'entity' => 'CiviRulesRule',
          'values' => [
            'trigger_id:name' => 'new_activity',
            'name' => 'supprime_lot_de_pièces',
            'label' => E::ts('Supprime lot de pièces'),
            'trigger_params' => 'a:1:{s:11:"record_type";s:1:"3";}',
            'is_active' => TRUE,
            'description' => E::ts('Supprime un lot de pièces identifiées par leur code Barres.'),
            'help_text' => "<p>Lorsqu'une action de type Suppression de lot de pièce anatomique est créée, elle supprime les pièces figurant dans le champ détails de l'activité.</p>\r\n\r\n<p>Si un code-barres de corps est saisi, l'utilisateur est invité à utiliser le tableau de bord des corps.&nbsp;Les pièces manquantes ou déja détruites sont ignorées.</p>\r\n\r\n<p>Sinon, la pièce est passée en \"Crémation\" et sa localisation est supprimée.</p>\r\n\r\n<p>Un rapport remplace les données du champ Détails</p>\r\n\r\n<p>&nbsp;</p>\r\n",
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
            'is_debug' => FALSE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [    //Supprime lot : Rule Action
          'entity' => 'CiviRulesRuleAction',
          'values' => [
            'action_params' => NULL,
            'delay' => NULL,
            'ignore_condition_with_delay' => 0,
            'is_active' => TRUE,
            'rule_id.name' => 'supprime_lot_de_pièces',
            'action_id.name' => 'supprimelot',
          ],
        ];
        create_entity($to_create);
    

        //Supprime lot : Rule condition
        $weight = civicrm_api4('OptionValue', 'get', [ // récupère le weight du type de l'activité qui est utilisé comme activity_type_id
            'select' => [
                'weight',
            ],
            'where' => [
                ['name', '=', "Suppression d'une pièces"],
            ],
            'checkPermissions' => FALSE,
            ]);

        $activity_type_id = $weight[0]['weight'];

        $to_create =  [                   
          'entity' => 'CiviRulesRuleCondition',
          'values' => [
            'condition_link' => NULL,
            'is_active' => TRUE,
            'rule_id.name' => 'supprime_lot_de_pièces',
            'condition_id.name' => 'activity_of_type',
            'condition_link' => NULL,
            'condition_params' => [
                'operator' => '0',
                'activity_type_id' => [
                    $activity_type_id,
                ],
            ],
            #'condition_params' => 'a:2:{s:8:"operator";s:1:"0";s:16:"activity_type_id";a:1:{i:0;s:2:"61";}}',
          ],
        ];
        create_entity($to_create);

      $msg="      -> Civirule : Inventaire ".PHP_EOL;
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        fclose($fp);
          if (VERBOSE==1){
            echo $msg;
          }

        $to_create =  [        //Inventaire : Déclaration de l'Action
          'entity' => 'CiviRulesAction',
          'values' => [
            'name' => 'creeinventaire',
            'label' => 'Crée un inventaire de pièces anatomiques',
            'class_name' => 'CRM_DonCorps_CivirulesActions_Activite_Creeinventaire',
            'is_active' => TRUE,
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
            ],
          ];
        create_entity($to_create);

        $to_create =  [         //Inventaire : Rule
          'entity' => 'CiviRulesRule',
          'values' => [
            'trigger_id:name' => 'new_activity',
            'name' => "création_d'inventaire",
            'label' => "Création d'inventaire",
            'trigger_params' => "a:1:{s:11:\"record_type\";s:1:\"3\";}",
            'is_active' => TRUE,
            'description' => 'Crée un nouvel inventaire',
            'help_text' => "<p>Lorsqu'une activité de type inventaire est créée depuis un lieu de conservation, un rapport remplace la liste des pièces dans le champ détail ; les pièces sont éventuellement relocalisées et leur statut est corrigé. Le champ 'Inventaires' des pièces et des corps concernés est mis à jour.</p>\r\n\r\n<p>&nbsp;</p>\r\n",
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
            'is_debug' => FALSE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [    //Inventaire : Rule Action
          'entity' => 'CiviRulesRuleAction',
          'values' => [
            'action_params' => NULL,
            'delay' => NULL,
            'ignore_condition_with_delay' => 0,
            'is_active' => TRUE,
            'rule_id.name' => "création_d'inventaire",
            'action_id.name' => 'creeinventaire'
          ],
        ];
        create_entity($to_create);

        //Inventaire : Rule condition
        $weight = civicrm_api4('OptionValue', 'get', [ // récupère le weight du type de l'activité qui est utilisé comme activity_type_id
            'select' => [
                'weight',
            ],
            'where' => [
                ['name', '=', "Inventaire"],
            ],
            'checkPermissions' => FALSE,
        ]);

        $activity_type_id = $weight[0]['weight'];
        
        $to_create =  [                   
          'entity' => 'CiviRulesRuleCondition',
          'values' => [
          'condition_link' => NULL,
          'condition_params' => [
                'operator' => '0',
                'activity_type_id' => [
                    $activity_type_id,
                ],
            ],
            #'condition_params' => 'a:2:{s:8:"operator";s:1:"0";s:16:"activity_type_id";a:1:{i:0;s:2:"60";}}',
            'is_active' => TRUE,
            'rule_id.name' => "création_d'inventaire",
            'condition_id.name' => 'activity_of_type',
          ],
        ];
        create_entity($to_create);

    
      $msg="      -> Civirule : MAJ civilités ".PHP_EOL;
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        fclose($fp);
          if (VERBOSE==1){
            echo $msg;
          } 
        $to_create =  [        //Corriger_civililite : Déclaration de l'Action
            'entity' => 'CiviRulesAction',
            'values' => [
              'name' => 'Corriger_civililite',
              'label' => 'Corriger la civilite',
              'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_FixCivilite',
              'is_active' => TRUE,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
            ],
        ];
        create_entity($to_create);

        $to_create =  [         //Corriger_civililite : Rule
                
          'entity' => 'CiviRulesRule',
          'values' => [
            'trigger_id:name' => 'changed_contact_custom_data',
            'name' => 'maj_genre_',
            'label' => 'MAJ Genre ',
            'trigger_params' => NULL,
            'is_active' => TRUE,
            'description' => E::ts('Met à jour le genre et les formules de politesse'),
            'help_text' => NULL,
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
            'is_debug' => FALSE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [    //Corriger_civililite : Rule Action
          'entity' => 'CiviRulesRuleAction',
          'values' => [
            'action_params' => NULL,
            'delay' => NULL,
            'ignore_condition_with_delay' => 0,
            'is_active' => TRUE,
            'rule_id.name' => 'maj_genre_',
            'action_id.name' => 'Corriger_civililite',
          ],
        ];
        create_entity($to_create);

        //Corriger_civililite : Rule condition
        #$condition_params=serialize_custom_fields('Civilit_user');

        $customFields = civicrm_api4('CustomField', 'get', [ // récupère l'id du champ personnalisé
            'select' => [
                'id',
            ],
            'where' => [
                ['name', '=', 'Civilit_user'],
            ],
            'checkPermissions' => FALSE,
            ]);

        $custom_field_id = $customFields[0]['id'];

        $to_create =  [      
          'entity' => 'CiviRulesRuleCondition',
          'values' => [
            'condition_link' => NULL,
            'condition_params' => [
                'custom_field_id' => [
                    $custom_field_id,
                ],
                ],
            #'condition_params' => $condition_params,
            'is_active' => TRUE,
            'rule_id.name' => 'maj_genre_',
            'condition_id.name' => 'contact_custom_field_changed',
          ],
        ];
        create_entity($to_create);
                    

    
      $msg="      -> Civirule : Compile pieces et utilisations".PHP_EOL;
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        fclose($fp);
          if (VERBOSE==1){
            echo $msg;
          }   

        $to_create =  [        //compile pieces et utilisations : Déclaration de l'Action
            'entity' => 'CiviRulesAction',
            'values' => [
                'name' => 'compile_pieces_et_utlisations',
                'label' => "Compile les pièces et utilisations d'un corps",
                'class_name' => 'CRM_DonCorps_CivirulesActions_Piece_Compilepiecesutilisations',
                'is_active' => TRUE,
                'created_date' => date('Y-m-d'),
                'created_user_id' => 1,
                'modified_date' => NULL,
                'modified_user_id' => NULL,
            ],
        ];
        create_entity($to_create);

        $to_create =  [         //compile pieces et utilisations : Rule
          'entity' => 'CiviRulesRule',
          'values' => [
              'name' => 'update_pieces_et_utilisations',
              'label' => "update pieces et utilisations",
              'is_active' => TRUE,
              'trigger_id:name' => 'changed_individual_custom_data',
              'trigger_params' => NULL,
              'description' => "Update la liste des pieces utilisées et des utilisations d'un corps",
              'help_text' => NULL,
              'created_date' => date('Y-m-d'),
              'created_user_id' => 1,
              'modified_date' => NULL,
              'modified_user_id' => NULL,
              'is_debug' => FALSE,
          ],
        ];
        create_entity($to_create);

        $to_create =  [    //compile pieces et utilisations : Rule Action
          'entity' => 'CiviRulesRuleAction',
          'values' => [
            'action_params' => NULL,
            'delay' => NULL,
            'ignore_condition_with_delay' => 0,
            'is_active' => TRUE,
            'rule_id.name' => 'update_pieces_et_utilisations',
            'action_id.name' => 'compile_pieces_et_utlisations',
          ],
        ];
        create_entity($to_create);

        //compile pieces et utilisations : Rule condition
        //$condition_params=serialize_custom_fields('Type_de_poi_ce_3', 'Utilisation2');
        $customFields = civicrm_api4('CustomField', 'get', [ // récupère l'id du champ personnalisé
            'select' => [
                'id',
            ],
            'where' => [
                ['OR', [['name', '=', 'Utilisation2'], ['name', '=', 'Type_de_poi_ce_3']]],
            ],
            'checkPermissions' => FALSE,
            ]);


        $custom_field_id = array_column($customFields->getArrayCopy(), 'id');

        $to_create =  [      
          'entity' => 'CiviRulesRuleCondition',
          'values' => [
            'rule_id.name' => 'update_pieces_et_utilisations',
            'condition_id.name' => 'contact_custom_field_changed',
            'is_active' => TRUE,
            #'condition_params' => $condition_params, 
            'condition_params' => [
                'custom_field_id' => $custom_field_id
                ],
          ],
        ];
        create_entity($to_create);


      $msg="      -> Civirule : Copie code barre corps".PHP_EOL;
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        fclose($fp);
          if (VERBOSE==1){
            echo $msg;
          }  
        $to_create =  [         //Copie code barre corps : Déclaration de Action
          'entity' => 'CiviRulesAction',
          'values' => [
            'name' => 'contact_CopyBarCode',
            'label' => "Copie du code barre du corps d'un donneur dans le champ cache piece principale",
            'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_CopyBarCode',
            'is_active' => TRUE,
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
          ],
        ];
        create_entity($to_create);

        $to_create =  [       // Copie code barre corps : Rule
          'entity' => 'CiviRulesRule',
          'values' => [
            'name' => 'copie_code_barre_corps',
            'label' => "Copie code barre corps",
            'is_active' => TRUE,
            'trigger_id:name' => 'changed_individual_custom_data',
            'trigger_params' => NULL,
            'description' => 'Copie le code barre du corps du donneur dans le champ caché "piece principale" lors des modif de n° de piece',
            'help_text' => NULL,
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
            'is_debug' => FALSE,
          ],
        ];
        create_entity($to_create);

        // Copie code barre corps : Rule Condition

        $customFields = civicrm_api4('CustomField', 'get', [ // récupère l'id du champ personnalisé
            'select' => [
                'id',
            ],
            'where' => [
                ['name', '=', 'N_de_pi_ce_ou_de_corps']
            ],
            'checkPermissions' => FALSE,
            ]);


        $custom_field_id = array_column($customFields->getArrayCopy(), 'id');


        //$condition_params=serialize_custom_fields('N_de_pi_ce_ou_de_corps');

        $to_create =  [       
          'entity' => 'CiviRulesRuleCondition',
          'values' => [
            'rule_id.name' => 'copie_code_barre_corps',
            'condition_id.name' => 'contact_custom_field_changed',
            'is_active' => TRUE,
            //'condition_params' => $condition_params,
            'condition_params' => [
                'custom_field_id' => $custom_field_id
                ],
            ],
          ];
        create_entity($to_create);

        $to_create =  [        //Copie code barre corps : Rule Action
          'entity' => 'CiviRulesRuleAction',
          'values' => [
            'action_params' => NULL,
            'delay' => NULL,
            'ignore_condition_with_delay' => 0,
            'is_active' => TRUE,
            'rule_id.name' => 'copie_code_barre_corps',
            'action_id.name' => 'contact_CopyBarCode',
          ],
        ];
        create_entity($to_create);


      $msg="      -> Civirule : Envoyer_mail_si_demande_cremation".PHP_EOL;
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        fclose($fp);
          if (VERBOSE==1){
            echo $msg;
          }  

        $to_create =  [     //envoyer_mail_si_demande_cremation : Message template à envoyer 
          'entity' => 'MessageTemplate',
          'values' => [
            'msg_title' => 'Demander crémation au secrétariat',
            'msg_subject' => 'Merci de demander crémation pour : {contact.display_name}',
            'msg_text' => NULL,
            'msg_html' => '<p>Bonjour</p>
              <p>Merci de prévoir le transfert et de nous communiquer la date de crémation de :</p>
              <p>{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label} {contact.first_name} {Tokens_for_contact_Champs_de_fu.CONCAT_WS_last_name_nick_name}</p>
              <p>né(e)&nbsp; le {contact.birth_date} à {Tokens_for_contact_Champs_de_fu.Compl_m_nt_tat_civil.Ville_de_naissance}</p>
              <p>décédé(e) le {contact.deceased_date} à {Tokens_for_contact_Champs_de_fu.Prise_en_charge_au_d_c_s.Ville_de_d_c_s}</p>
              <p><br />
              Nous restons à votre disposition pour tout renseignement complémentaire</p>
              <p>Les techniciens du laboratoire de {domain.city}</p>',
            'is_active' => TRUE,
            'workflow_id' => NULL,
            'workflow_name' => NULL,
            'is_default' => TRUE,
            'is_reserved' => FALSE,
            'is_sms' => FALSE,
            'pdf_format_id' => 0,
          ],
        ];

        #$msg_id = serialize(create_entity($to_create));  // récupère l'id du message qui vient d'être créé et sera utilisé dans CiviRulesRuleAction (1)
        $msg_id = create_entity($to_create);  // récupère l'id du message qui vient d'être créé et sera utilisé dans CiviRulesRuleAction (1)


        $to_create =  [         //envoyer_mail_si_demande_cremation : Déclaration de l'Action
          'entity' => 'CiviRulesAction',
          'values' => [
            'name' => 'changer_elimination_pour_cremation_demandee',
            'label' => "Change le mode d'élimination pour crémation demandée",
            'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_Changeelimination',
            'is_active' => TRUE,
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
          ],
        ];
        create_entity($to_create);

        $to_create =  [         //envoyer_mail_si_demande_cremation : Déclaration de la condition
          'entity' => 'CiviRulesCondition',
          'values' => [
            'name' => 'demander_cremation_du_contact',
            'label' => "Vérifie si le mode d'élimination est demander crémation",
            'class_name' => 'CRM_DonCorps_CivirulesConditions_Contact_Demandercrema',
            'is_active' => TRUE,
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
          ],
        ];
        create_entity($to_create);

        $to_create =  [  // envoyer_mail_si_demande_cremation : Rule
          'entity' => 'CiviRulesRule',
          'values' => [
            'name' => 'envoyer_mail_si_demande_cremation',
            'label' => 'Envoyer mail si demande cremation',
            'is_active' => TRUE,
            'trigger_id:name' => 'changed_individual_custom_data',
            'trigger_params' => NULL,
            'description' => 'Envoi un mail aux PF si un corps passe en "demander crémation" et le passe en crémation demandée (pas de délai)',
            'help_text' => NULL,
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
            'is_debug' => FALSE,
          ],
        ];
        create_entity($to_create);

        // envoyer_mail_si_demande_cremation : Rule Condition_1


        $customFields = civicrm_api4('CustomField', 'get', [ // récupère l'id du champ personnalisé
            'select' => [
                'id',
            ],
            'where' => [
                ['name', '=', 'Mode_limination_hors_corps_2']
            ],
            'checkPermissions' => FALSE,
            ]);


        $custom_field_id = array_column($customFields->getArrayCopy(), 'id');


        //$condition_params=serialize_custom_fields('Mode_limination_hors_corps_2');
        $to_create =  [
          'entity' => 'CiviRulesRuleCondition',
          'values' => [
            'condition_link' => NULL,
            'rule_id.name' => 'envoyer_mail_si_demande_cremation',
            'condition_id.name' => 'contact_custom_field_changed',
            'is_active' => TRUE,
            //'condition_params' => $condition_params,  
            'condition_params' => [
                'custom_field_id' => $custom_field_id
            ],
          ],
        ];
        create_entity($to_create);

        $to_create =  [       // envoyer_mail_si_demande_cremation : Rule Condition_2
          'entity' => 'CiviRulesRuleCondition',
          'values' => [
            'condition_link' => 'AND',
            'rule_id.name' => 'envoyer_mail_si_demande_cremation',
            'condition_id.name' => 'demander_cremation_du_contact',
            'is_active' => TRUE,
            'condition_params' => NULL, 
          ],
        ];
        create_entity($to_create);

        $to_create =  [      //envoyer_mail_si_demande_cremation : Rule Action 1
          'entity' => 'CiviRulesRuleAction',
          'values' => [    
            'action_params' => [
                'from_name' => 'Techniciens labo anatomie',
                'from_email' => 'dons.corps@med.univ-tours.fr',
                'template_id' => $msg_id,
                'disable_smarty' => FALSE,
                'location_type_id' => '',
                'from_email_option' => '',
                'alternative_receiver_address' => 'pompes@domain.fr',
                'cc' => 'votre.labo@domain.fr',
                'bcc' => '',
                'file_on_case' => FALSE,
            ],
            #'action_params' => 'a:10:{s:9:"from_name";s:25:"Techniciens labo anatomie";s:10:"from_email";s:28:"dons.corps@med.univ-tours.fr";s:11:"template_id";'.$msg_id.'s:14:"disable_smarty";b:0;s:16:"location_type_id";s:0:"";s:17:"from_email_option";s:0:"";s:28:"alternative_receiver_address";s:23:"destrieux@univ-tours.fr";s:2:"cc";s:0:"";s:3:"bcc";s:0:"";s:12:"file_on_case";b:0;}',    
            'delay' => NULL,
            'ignore_condition_with_delay' => 0,
            'is_active' => TRUE,
            'rule_id.name' => 'envoyer_mail_si_demande_cremation',
            'action_id.name' => 'emailapi_send',
          ],
        ];
        create_entity($to_create);

        $to_create =  [       //envoyer_mail_si_demande_cremation : Rule Action 2
          'entity' => 'CiviRulesRuleAction',
          'values' => [
            'action_params' => NULL,
            'delay' => NULL,
            'ignore_condition_with_delay' => 0,
            'is_active' => TRUE,
            'rule_id.name' => 'envoyer_mail_si_demande_cremation',
            'action_id.name' => 'changer_elimination_pour_cremation_demandee',
          ],
        ];
        create_entity($to_create);


      $msg="      -> Civirule : Neutralise adresse postale en cas de retour de courrier".PHP_EOL;
        $fp=fopen(LOGFILE, 'a'); // ouvre le fichier de log
        fwrite($fp, $msg);
        fclose($fp);
          if (VERBOSE==1){
            echo $msg;
          }  
        //reécupère l'id de l'action "modification des coordonnées"
        $optionValues = civicrm_api4('OptionValue', 'get', [
          'select' => [
            'value',
          ],
          'where' => [
            ['name', '=', 'modification des coordonnées'],
          ],
          'checkPermissions' => FALSE,
        ]);

        $id_activité_modif_coord = $optionValues[0]['value'];

        $activité_modif_coord_id = array_column($optionValues->getArrayCopy(), 'value');

        $to_create =  [       //  créer action "modification des coordonnées"
          'entity' => 'Afform',
          'values' => [
            'type' => 'form',
            'requires' => NULL,
            'entity_type' => NULL,
            'join_entity' => NULL,
            'title' => E::ts("Désactivation de l'adresse postale"),
            'description' => E::ts('Active le champ Adresse Incorrecte'),
            'placement' => [
              'dashboard_dashlet',
            ],
            'summary_contact_type' => NULL,
            'summary_weight' => NULL,
            'icon' => 'fa-list-alt',
            'server_route' => '',
            'is_public' => FALSE,
            'permission' => [
              'access CiviCRM',
            ],
            'permission_operator' => 'AND',
            'redirect' => NULL,
            'submit_enabled' => TRUE,
            'submit_limit' => NULL,
            'create_submission' => TRUE,
            'manual_processing' => FALSE,
            'allow_verification_by_email' => FALSE,
            'email_confirmation_template_id' => NULL,
            'navigation' => NULL,
            'modified_date' => date('Y-m-d'),
            'layout' => [
              [
                '#tag' => 'af-form',
                'ctrl' => 'afform',
                '#children' => [
                  [
                    '#tag' => 'af-entity',
                    'data' => [
                      'source_contact_id' => 'user_contact_id',
                      'activity_type_id' => $id_activité_modif_coord,
                      'status_id' => '2',
                      'details' => 'Retour mail postal pour adresse erronée',
                      'subject' => 'Retour mail postal pour adresse erronée',
                    ],
                    'type' => 'Activity',
                    'name' => 'Activity1',
                    'label' => E::ts('Activité 1'),
                    'actions' => [
                      'create' => TRUE,
                      'update' => TRUE,
                    ],
                    'security' => 'RBAC',
                  ],
                  [
                    '#tag' => 'p',
                    'class' => 'af-text',
                    '#children' => [
                      [
                        '#text' => "Scannez le code barre du contact dont l'adresse doit être inactivée. Son identité s'affiche. Validez en tapant entrée. Une fois les contacts saisis, pressez le bouton Neutraliser l'adresse postale.",
                      ],
                    ],
                  ],
                  [
                    '#text' => '
          ',
                  ],
                  [
                    '#tag' => 'fieldset',
                    'af-fieldset' => 'Activity1',
                    'class' => 'af-container',
                    '#children' => [
                      [
                        '#tag' => 'af-field',
                        'name' => 'target_contact_id',
                        'defn' => [
                          'label' => E::ts('Codes barres (id) des contacts à modifier'),
                          'input_attrs' => [],
                        ],
                      ],
                    ],
                  ],
                  [
                    '#tag' => 'button',
                    'class' => 'af-button btn btn-primary',
                    'crm-icon' => 'fa-check',
                    'ng-click' => 'afform.submit()',
                    'ng-if' => 'afform.showSubmitButton',
                    '#children' => [
                      [
                        '#text' => "Neutraliser l'adresse postale",
                      ],
                    ],
                  ],
                  [
                    '#text' => '
        ',
                  ],
                ],
              ],
            ],
            'name' => 'afformDSactiveAdressePostale',
          ],
        ];
        create_entity($to_create);

        // la variable $mail_content_triger sera utilisée pour la création de la condition 2
        $mail_content_triger = serialize($to_create['values']['layout'][0]['#children'][0]['data']['details']);

        //$mail_content_triger = $to_create['values']['layout'][0]['#children']['data']['details'];
        $to_create =  [                                                       // passer l'adresse en erroné : Rule
          'entity' => 'CiviRulesRule',
          'values' => [
            'name' => 'neutralise_adresse_postale',
            'label' => E::ts('Neutralise adresse postale'),
            'trigger_id:name' => 'new_activity',
            'trigger_params' => 'a:1:{s:11:"record_type";s:1:"0";}',
            'is_active' => TRUE,
            'description' => E::ts('Neutralise adresse postale en cas de retour de courrier'),
            'help_text' => '<p>Si une activité de type "Modification des coordonnées" avec le sujet "Retour mail postal pour adresse erronnée" est créee&nbsp;</p>
            <p>passage à OUI de adresse erronée</p>
            ',
            'created_date' => date('Y-m-d'),
            'created_user_id' => 1,
            'modified_date' => NULL,
            'modified_user_id' => NULL,
            'is_debug' => FALSE,
          ],
        ];
        create_entity($to_create);


        $to_create =  [
          'entity' => 'CiviRulesRuleCondition',
          'values' => [
            'condition_link' => NULL,
            //'condition_params' => 'a:2:{s:8:"operator";s:1:"0";s:16:"activity_type_id";a:1:{i:0;'.$id_activité_modif_coord_ser.'}}',
            'condition_params' => [
                'operator' => '0',
                'activity_type_id' => $activité_modif_coord_id,
            ],
            'is_active' => TRUE,
            'rule_id.name' => 'neutralise_adresse_postale',
            'condition_id.name' => 'activity_of_type',
          ], 
        ];
        create_entity($to_create);

        $to_create =  [                                                     // passer l'adresse en erroné : Rule Condition 1
          'entity' => 'CiviRulesRuleCondition',
          'values' => [
              'condition_link' => 'AND',
              //'condition_params' => 'a:2:{s:8:"operator";s:8:"contains";s:4:"text";'.$mail_content_triger.'}',
              'condition_params' => [
                    'operator' => 'contains',
                    'text' => E::ts('Retour mail postal pour adresse erronée'),
                ],
              'is_active' => TRUE,
              'rule_id.name' => 'neutralise_adresse_postale',
              'condition_id.name' => 'contact_has_activity_with_details',
            ],
        ];
        create_entity($to_create);

          // passer l'adresse en erroné : recupere l'id du champ adresse erronée 
          $customFields = civicrm_api4('CustomField', 'get', [
            'select' => [
              'id',
            ],
            'where' => [
              ['name', '=', 'Adresse_incorrecte'],
            ],
            'checkPermissions' => FALSE,
          ]);

          #$custom_adresse_incorrecte = serialize ($customFields[0]['id']);
          

        $to_create =  [                                  // passer l'adresse en erroné : Rule Action 1
          'entity' => 'CiviRulesRuleAction',
          'values' => [    
            #'action_params' => 'a:2:{s:8:"field_id";'.$custom_adresse_incorrecte.'s:5:"value";s:1:"1";}',
            'action_params' => [
                'field_id' => $customFields[0]['id'],
                'value' => '1',
            ],
            'delay' => NULL,
            'ignore_condition_with_delay' => 0,
            'is_active' => TRUE,
            'rule_id.name' => 'neutralise_adresse_postale',
            'action_id.name' => 'set_custom_field',
          ],
        ];
        create_entity($to_create);
}     // fin de la création des Rules

create_rules();