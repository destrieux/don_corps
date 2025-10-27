<?php
eval(`cv php:boot`);

// 


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

function serialize_custom_fields(){
  $args = func_get_args();
  $rule_name = $args[0];
  $condition_name = $args[1];

  echo "Rule name : ".$rule_name.PHP_EOL;
  echo "Condition name : ".$condition_name.PHP_EOL;
  //print_r($args);

  // Est ce que CiviRulesRuleCondition correspondant à Rule name et Condition name existe ?
  $results = civicrm_api4('CiviRulesRuleCondition', 'get', [
    'select' => ['id'],
    'where' => [
      ['rule_id.name', '=', $rule_name],
      ['condition_id.name', '=', $condition_name],
    ],
    'checkPermissions' => FALSE,
  ]);

  if (isset($results[0])){         
    $CiviRulesRuleCondition_id = $results[0]['id'];       // id de la CiviRulesRuleCondition à modifier
    echo "id de la CiviRulesRuleCondition : ".$CiviRulesRuleCondition_id.PHP_EOL;
  }else{
    echo "CiviRulesRuleCondition nexiste pas ! ".PHP_EOL;
    return;
  }

  unset($args[1]);      // supprime les arguments pour ne garder que les noms de custom fields
  unset($args[0]);


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

    echo $id[0]['id'].PHP_EOL;
    $array[] = $id[0]['id'];                       // on liste les id des champs custom dans $array
  }

  $array = [                                       // on ajoute une colonne Custom_field_id à $array 
    "custom_field_id" => $array
  ];

  $condition_params=serialize($array);             // on l'écrit en sértialisant (foramt atendu par condition_params)
  echo $condition_params.PHP_EOL;

  $results = civicrm_api4('CiviRulesRuleCondition', 'update', [   // on update CiviRulesRuleCondition avec cette condition
    'values' => [
      'condition_params' => $condition_params,
    ],
    'where' => [
      ['id', '=', $CiviRulesRuleCondition_id],
    ],
    'checkPermissions' => FALSE,
  ]);

}

serialize_custom_fields('update_pieces_et_utilisations', 'contact_custom_field_changed', 'Type_de_poi_ce_3', 'Utilisation2');
