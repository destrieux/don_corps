<?php
eval(`cv php:boot`);

// 

   
function create_entity2(){
  //////////// function create_entity /////////
  // Cette fonction est invoquée à l'installation pour créer les types de contacts, des options ... préalablement à la création des Custom groups/fields
  // depuis les fichiers managed/CustomGroup_...
  //
  // syntaxe : create_contact_type ($to create)
  //    $to_create =  [                                   // entité à modifier upgrader
  //      'entity' => 'UFJoin',
  //      'values' => [                                   // valeurs pour cette entité
  //        'uf_group_id:name' => $profile_to_update,
  //        'module' => 'Profile',
  //        'is_active' => TRUE,
  //        'module_data' => NULL,
  //      ],
  //    ];
  //
  //////////////
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

      case 'UFJoin':                                             // UF join (utilisation des profiles)
        $check_entity = civicrm_api4($entity, 'get', [    
            'where' => [
                ['uf_group_id:name', "=", $values['uf_group_id:name']],
                [ 'module', "=", $values['module']],
            ],
            'checkPermissions' => FALSE,
        ]);
      break;

      case 'Navigation':                                             // Menus de navigation
        $check_entity = civicrm_api4($entity, 'get', [    
            'where' => [
                ['parent_id:name', "=", $values['parent_id:name']],
                [ 'name', "=", $values['name']],
            ],
            'checkPermissions' => FALSE,
        ]);
      break;

      case 'OptionValue':                                             // Option value
        $check_entity = civicrm_api4($entity, 'get', [    
            'where' => [
                ['option_group_id:name', '=', $values['option_group_id:name']],
                [ 'name', "=", $values['name']],
            ],
            'checkPermissions' => FALSE,
        ]);
      break;

      case 'RelationshipType':                                             // Relations
        $check_entity = civicrm_api4($entity, 'get', [    
            'where' => [
                ['name_a_b', '=', $values['name_a_b']],
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
}// Fin de la définition de la fonction : create entity()



echo  "  -Création des prefixes de contact".PHP_EOL;
$to_create =  [       // Création des prefixes de contact : Mme
  'entity' => 'OptionValue',
  'values' => [
    'label' => 'Mme',
    'value' => '1',
    'name' => 'Mrs.',
    'grouping' => NULL,
    'filter' => 0,
    'is_default' => NULL,
    'weight' => 1,
    'description' => NULL,
    'is_optgroup' => FALSE,
    'is_reserved' => FALSE,
    'is_active' => TRUE,
    'component_id' => NULL,
    'domain_id' => NULL,
    'visibility_id' => NULL,
    'icon' => NULL,
    'color' => NULL,
    'option_group_id:name' => 'individual_prefix',
  ],
];
echo $to_create['values']['label']." : ";
create_entity2($to_create);

$to_create =  [       // Création des prefixes de contact : Melle
  'entity' => 'OptionValue',
  'values' => [
      'label' => 'Melle',
      'value' => '2',
      'name' => 'Ms.',
      'grouping' => NULL,
      'filter' => 0,
      'is_default' => NULL,
      'weight' => 2,
      'description' => NULL,
      'is_optgroup' => FALSE,
      'is_reserved' => FALSE,
      'is_active' => TRUE,
      'component_id' => NULL,
      'domain_id' => NULL,
      'visibility_id' => NULL,
      'icon' => NULL,
      'color' => NULL,
      'option_group_id:name' => 'individual_prefix',
  ],
];
echo $to_create['values']['label']." : ";
create_entity2($to_create);

$to_create =  [       // Création des prefixes de contact : M.
  'entity' => 'OptionValue',
  'values' => [
    'label' => 'M.',
    'value' => '3',
    'name' => 'Mr.',
    'grouping' => NULL,
    'filter' => 0,
    'is_default' => NULL,
    'weight' => 3,
    'description' => NULL,
    'is_optgroup' => FALSE,
    'is_reserved' => FALSE,
    'is_active' => TRUE,
    'component_id' => NULL,
    'domain_id' => NULL,
    'visibility_id' => NULL,
    'icon' => NULL,
    'color' => NULL,
    'option_group_id:name' => 'individual_prefix',
  ],
];
echo $to_create['values']['label']." : ";
create_entity2($to_create);

$to_create =  [       // Création des prefixes de contact : Mx
  'entity' => 'OptionValue',
  'values' => [
    'label' => 'Mx.',
    'value' => '5',
    'name' => 'Mx.',
    'grouping' => NULL,
    'filter' => 0,
    'is_default' => FALSE,
    'weight' => 4,
    'description' => NULL,
    'is_optgroup' => FALSE,
    'is_reserved' => FALSE,
    'is_active' => TRUE,
    'component_id' => NULL,
    'domain_id' => NULL,
    'visibility_id' => NULL,
    'icon' => NULL,
    'color' => NULL,
    'option_group_id:name' => 'individual_prefix',
  ],
];
echo $to_create['values']['label']." : ";
create_entity2($to_create);

$to_create =  [       // Création des prefixes de contact : Dr
  'entity' => 'OptionValue',
  'values' => [
    'label' => 'Dr.',
    'value' => '4',
    'name' => 'Dr.',
    'grouping' => NULL,
    'filter' => 0,
    'is_default' => FALSE,
    'weight' => 4,
    'description' => NULL,
    'is_optgroup' => FALSE,
    'is_reserved' => FALSE,
    'is_active' => FALSE,
    'component_id' => NULL,
    'domain_id' => NULL,
    'visibility_id' => NULL,
    'icon' => NULL,
    'color' => NULL,
    'option_group_id:name' => 'individual_prefix',
  ],
];
echo $to_create['values']['label']." : ";
create_entity2($to_create);