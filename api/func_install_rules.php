<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;

   /// INSTALLATION DE CIVIRULE 
  /*   L'installation des civirules impose la définition de plusieurs choses : 
  * - si elles n'existent pas : des entités dans la base de données (peuvent être utilisées par plusieurs rèlges si besoin): 
  *   - CiviRulesConditions : les conditions à remplir si le triger est déclenché pour que l'action se déroule
  *   - CiviRulesAction : les actions à réaliser si le triger est déclenché et les conditions requises
  *
  * - des regles qui utilisent ces actions et conditions : 
  *   - CiviRulesRule, définition de la règle  (notamment du triger)
  *   - CiviRulesRuleCondition, quelles sont les conditions pour déclencher l'action si le triger est rencontré (met en relation une regle et une conditionp)
  *   - CiviRulesRuleAction, quelle action doit être déclenchée par cette règle (met en relation une regle et une action)
  *
  * - certaines conditions peuvent faire reference à des chamsp customisés installés par des fichierd mgd
  *   (par exemple celles portant sur la modification d'un champ customisé)
  *    les fihciers mgd ne sont actifs qu'apres execution de hook_civicrm_managed ; autrement dit pas dispobibles au moment de l'écriteure des conditions dans la base de donénes
  *   il faut donc créer une condition nulle et la modifier avec le hook post install
  *
  *  Le manuel deconseille l'utilisation de mgd pour les rules ; elles sont donc définies 
  */


      //compile pieces et utilisations
      // NB CiviRulesRuleCondition correspondante est installée dans civicrm post installcar necessite la definition préalable de custom fields definis dans les fichiers.mgd


//////////// fonction create_entity2 () /////////
//  Cette fonction créer ou upgrade les entités
//  Suyntaxe : create_entity2($type d'entité à créer $veleurs pour cette entité)
//
//////////////

      
function create_entity2(){
  $entity = func_get_arg(0)['entity'];     // nom de l'entité à créer (rule, condition....)
  $values = func_get_arg(0)['values'];     // parametres de cette entité
    

  switch ($entity) {
      case 'CiviRulesRuleAction':                           // CiviRulesRuleAction
          $check_entity = civicrm_api4($entity, 'get', [    
            'where' => [
              ['rule_id.name', '=', $values['rule_id.name']],
              ['action_id.name', '=', $values['action_id.name']],
            ],
            'checkPermissions' => FALSE,
          ]);
      break;

      case 'CiviRulesRuleCondition':                        // CiviRulesRuleCondition
          $check_entity = civicrm_api4($entity, 'get', [    
            'where' => [
              ['rule_id.name', '=', $values['rule_id.name']],
              ['condition_id.name', '=', $values['condition_id.name']],
            ],
            'checkPermissions' => FALSE,
          ]);
      break;

      case 'MessageTemplate':
          $check_entity = civicrm_api4($entity, 'get', [    // rmessage template
            'where' => [
              ['msg_title', '=', $values['msg_title']],
            ],
            'checkPermissions' => FALSE,
          ]);
      break;



      default:
        $check_entity = civicrm_api4($entity, 'get', [    // rule, action, condition,OptionValue
          'where' => [
          ['name', '=', $values['name']],
          ],
          'checkPermissions' => FALSE,
        ]);
      break;
  }

  if(isset($check_entity[0])){            // si l'entité existe on l'update
    echo "entité ".$entity." existe - update".PHP_EOL;
    
    $results = civicrm_api4($entity, 'update', [
      'values' => $values,
      'where' => [
        ['id', '=', $check_entity[0]['id']],
      ],
      'checkPermissions' => FALSE,
    ]);

  }else{                                  // si l'entité n'existe pas, on la crée
    echo "entité ".$entity." n'existe pas - creation".PHP_EOL;
    $results = civicrm_api4($entity, 'create', [
      'values' => $values,
      'checkPermissions' => FALSE,
    ]);

  }
 return $results[0]['id']; // retourne l'id de l'entité créée
}


//////////// serialize_custom_fields() /////////
// Cette fonction est invoquée à en post installation pour :
//    - récupérer les champs customs utilisés par les conditions civirules
//    - les écrire en format seialisé
//    - les injecter dans les civirules écrites par le hook don_corps_civicrm_postinstall()
//
// syntaxe : serialize_custom_fields($civirule_rule_name, $civirule_condition_name, $CustomField_name1, $CustomField_name2, $CustomField_name3...);
//
//      $civirule_rule_name :nom de la règle à modifier
//      $civirule_condition_name : nom de la condition à modifier
//      $CustomField_name1, $CustomField_name2, $CustomField_name3... : liste de custom fields  
//
// elle est appelée par : le hook post installation 
//////////////

function serialize_custom_fields2(){
  $args = func_get_args();
  
  // Si des custom filds ne sont pas passés à la fonction : return
  if (count($args)==0){
    echo "Pas de custom field passé à la fonction ".PHP_EOL;
    return;
  }

  // Est ce que les custom fields existent
  foreach ($args as $custom){
    //echo "custom recherché : ".$custom.PHP_EOL;
      $id = civicrm_api4('CustomField', 'get', [   // on récupère l'id du custom field
        'select' => [
          'id',
        ],
        'where' => [
          ['name', '=', $custom],
        ],
        'checkPermissions' => FALSE,
      ]);

    //echo $id[0]['id'].PHP_EOL;
    $array[] = $id[0]['id'];                       // on liste les id des champs custom dans $array
  }

  $array = [                                       // on ajoute une colonne Custom_field_id à $array 
    "custom_field_id" => $array
  ];
  
  return serialize($array);                           // retourne la valeur en sértialisant (foramt atendu par condition_params)

}  // fin de la fonction serialize_custom_fields



echo PHP_EOL."   - Civirule : Compile pieces et utilisations".PHP_EOL;
      
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
create_entity2($to_create);

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
create_entity2($to_create);

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
create_entity2($to_create);

                  //compile pieces et utilisations : Rule condition
$condition_params=serialize_custom_fields2('Type_de_poi_ce_3', 'Utilisation2');

$to_create =  [      
  'entity' => 'CiviRulesRuleCondition',
  'values' => [
    'rule_id.name' => 'update_pieces_et_utilisations',
    'condition_id.name' => 'contact_custom_field_changed',
    'is_active' => TRUE,
    'condition_params' => $condition_params, 
  ],
];
create_entity2($to_create);



// Copie code barre corps
echo PHP_EOL."   - Civirule : Copie code barre corps".PHP_EOL;

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
create_entity2($to_create);

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
create_entity2($to_create);

$condition_params=serialize_custom_fields2('N_de_pi_ce_ou_de_corps');

$to_create =  [       // Copie code barre corps : Rule Condition
  'entity' => 'CiviRulesRuleCondition',
  'values' => [
    'rule_id.name' => 'copie_code_barre_corps',
    'condition_id.name' => 'contact_custom_field_changed',
    'is_active' => TRUE,
    'condition_params' => $condition_params,
    ],
  ];
create_entity2($to_create);

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
create_entity2($to_create);


//envoyer_mail_si_demande_cremation
echo PHP_EOL."   - Civirule : Envoyer_mail_si_demande_cremation".PHP_EOL;



$to_create =  [     //envoyer_mail_si_demande_cremation : Message template à envoyer 
  'entity' => 'MessageTemplate',
  'values' => [
    'msg_title' => 'Demander crémation au secrétariat',
    'msg_subject' => 'Merci de demander crémation pour : {contact.display_name}',
    'msg_text' => NULL,
    'msg_html' => '<p>Bonjour</p>
<p>Merci de prévoir le transfert et de nous communiquer la date de crémation de :</p>
<p>{contact.display_name}</p>
<p>né(e)&nbsp;{contact.nick_name} le {contact.birth_date} à </p>
<p>décédé(e) le {contact.deceased_date} à </p>
<p><br />
Nous restons à votre disposition pour tout renseignement complémentaire</p>
<p>Les techniciens du laboratoire de {domain.city}</p>
<p>&nbsp;</p>',
    'is_active' => TRUE,
    'workflow_id' => NULL,
    'workflow_name' => NULL,
    'is_default' => TRUE,
    'is_reserved' => FALSE,
    'is_sms' => FALSE,
    'pdf_format_id' => 0,
  ],
];

$msg_id = serialize(create_entity2($to_create));  // récupère l'id du message qui vient d'être créé et sera utilisé dans CiviRulesRuleAction (1)



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
create_entity2($to_create);

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
create_entity2($to_create);

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
create_entity2($to_create);

                  // envoyer_mail_si_demande_cremation : Rule Condition_1
$condition_params=serialize_custom_fields2('Mode_limination_hors_corps_2');
$to_create =  [
  'entity' => 'CiviRulesRuleCondition',
  'values' => [
    'condition_link' => NULL,
    'rule_id.name' => 'envoyer_mail_si_demande_cremation',
    'condition_id.name' => 'contact_custom_field_changed',
    'is_active' => TRUE,
    'condition_params' => $condition_params,  
  ],
];
create_entity2($to_create);

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
create_entity2($to_create);

$to_create =  [      //envoyer_mail_si_demande_cremation : Rule Action 1
  'entity' => 'CiviRulesRuleAction',
  'values' => [    
    'action_params' => 'a:10:{s:9:"from_name";s:25:"Techniciens labo anatomie";s:10:"from_email";s:28:"dons.corps@med.univ-tours.fr";s:11:"template_id";'.$msg_id.'s:14:"disable_smarty";b:0;s:16:"location_type_id";s:0:"";s:17:"from_email_option";s:0:"";s:28:"alternative_receiver_address";s:23:"destrieux@univ-tours.fr";s:2:"cc";s:0:"";s:3:"bcc";s:0:"";s:12:"file_on_case";b:0;}',    
    'delay' => NULL,
    'ignore_condition_with_delay' => 0,
    'is_active' => TRUE,
    'rule_id.name' => 'envoyer_mail_si_demande_cremation',
    'action_id.name' => 'emailapi_send',
  ],
];
create_entity2($to_create);

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
create_entity2($to_create);

// Rule Neutralise adresse postale en cas de retour de courrier
echo PHP_EOL."   - Civirule : Neutralise adresse postale en cas de retour de courrier".PHP_EOL;

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
//echo $id_activité_modif_coord.PHP_EOL;

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
create_entity2($to_create);

// la variable $mail_content_triger sera utilisée pour la création de la condition 2
$mail_content_triger = serialize($to_create['values']['layout'][0]['#children'][0]['data']['details']);

//$mail_content_triger = $to_create['values']['layout'][0]['#children']['data']['details'];
$to_create =  [                                                       // passer l'adresse en erroné : Rule
  'entity' => 'CiviRulesRule',
  'values' => [
    'name' => 'neutralise_adresse_postale',
    'label' => E::ts('Neutralise adresse postale'),
    'trigger_id' => 1,
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
create_entity2($to_create);


$id_activité_modif_coord_ser = serialize($id_activité_modif_coord); // passer l'adresse en erroné : Rule Condition 1
$to_create =  [
  'entity' => 'CiviRulesRuleCondition',
  'values' => [
    'condition_link' => NULL,
    'condition_params' => 'a:2:{s:8:"operator";s:1:"0";s:16:"activity_type_id";a:1:{i:0;'.$id_activité_modif_coord_ser.'}}',
    'is_active' => TRUE,
    'rule_id.name' => 'neutralise_adresse_postale',
    'condition_id.name' => 'activity_of_type',
  ], 
];
create_entity2($to_create);

$to_create =  [                                                     // passer l'adresse en erroné : Rule Condition 1
  'entity' => 'CiviRulesRuleCondition',
  'values' => [
      'condition_link' => 'AND',
      'condition_params' => 'a:2:{s:8:"operator";s:8:"contains";s:4:"text";'.$mail_content_triger.'}',
      'is_active' => TRUE,
      'rule_id.name' => 'neutralise_adresse_postale',
      'condition_id.name' => 'contact_has_activity_with_details',
    ],
];
create_entity2($to_create);

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

  $custom_adresse_incorrecte = serialize ($customFields[0]['id']);

$to_create =  [                                                     // passer l'adresse en erroné : Rule Action 1
  'entity' => 'CiviRulesRuleAction',
  'values' => [    
    'action_params' => 'a:2:{s:8:"field_id";'.$custom_adresse_incorrecte.'s:5:"value";s:1:"1";}',
    'delay' => NULL,
    'ignore_condition_with_delay' => 0,
    'is_active' => TRUE,
    'rule_id.name' => 'neutralise_adresse_postale',
    'action_id.name' => 'set_custom_field',
  ],
];
create_entity2($to_create);

echo PHP_EOL."   - Civirule : MAJ civilités ".PHP_EOL;
          
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
$condition_params=serialize_custom_fields('Civilit_user');
echo $condition_params.PHP_EOL;
$to_create =  [      
  'entity' => 'CiviRulesRuleCondition',
  'values' => [
    'condition_link' => NULL,
    'condition_params' => $condition_params,
    'is_active' => TRUE,
    'rule_id.name' => 'maj_genre_',
    'condition_id.name' => 'contact_custom_field_changed',
  ],
];
create_entity($to_create);