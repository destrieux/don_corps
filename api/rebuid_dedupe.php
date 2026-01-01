<?php

eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;


echo "- Reglage des règles de dédoublonage".PHP_EOL;
    #####
    # Lors de l acréation des dedupe rules en utilisant les fichier mgd, les champs sont mal mappés
    # en raison d'une référence non au nom des champs mais de la tabloe et de la colonne dans la bdd, variabl ed'une installaiton ) l'autre
    # ici, on récupère les valeurs de table et de colonne dans la nouvelle bdd à parir du nom du champ
    # et on modifie la regle de dédoublonage
    #
    # dans $custom_dedupes lister le name des custom fields utilisés dans les regles de dédoublonage
    #####

    ### Passage des regles existantes pour les individus et organizations à l'utilisation "general"
        # "Supervised" : lancée si création depuis l'interface utilisateur
        # "Unsupervised" : lancée si creation online ou import de contact
        echo '  - Passage des usages de tous les groupes de règles de dédoublonage pour les individus à General'.PHP_EOL;
        $results = civicrm_api4('DedupeRuleGroup', 'update', [
        'values' => [
            'used' => 'General',
        ],
        'where' => [
            ['OR', [['contact_type', '=', 'Organization'], ['contact_type', '=', 'Individual']]],
        ],
        'checkPermissions' => FALSE,
    ]);

    ### individuals : création de la table $custom_dedupes qui contient les valeurs de table et de colonne pour chque champ

        $custom_dedupes = ['N_annulation', 'N_de_don', 'N_de_d_c_s'];
        $custom_table=array();

        foreach($custom_dedupes as $custom_dedupe){
            //echo $custom_dedupe.PHP_EOL;
            $customFields = civicrm_api4('CustomField', 'get', [
                'select' => [
                    'column_name',
                    'custom_group_id.table_name',
                ],
                'where' => [
                    ['name', '=', $custom_dedupe],
                ],
                'checkPermissions' => FALSE,
            ]);

            if(isset($customFields[0])){
                $custom_table[$custom_dedupe]['table']=$customFields[0]['custom_group_id.table_name'];
                $custom_table[$custom_dedupe]['column']=$customFields[0]['column_name'];
            }
        }

    ### individuals : création règle supervisée de dédoublonage utilisant les n° de don d'annulation et de deces
        $rule_name = 'num_don_annulation_deces';
        $dedupeRuleGroups = civicrm_api4('DedupeRuleGroup', 'get', [
            'select' => [
                'id',
            ],
            'where' => [
                ['name', '=', $rule_name],
            ],
            'checkPermissions' => FALSE,
        ]);

        if(isset($dedupeRuleGroups[0])){
            $results = civicrm_api4('DedupeRuleGroup', 'update', [
                'values' => [
                    'contact_type' => 'Individual',
                    'threshold' => 10,
                    'used' => 'Supervised',
                    'title' => E::ts('num don ou annulation ou deces (supervisée)'),
                ],
                'where' => [
                    ['id', '=', $dedupeRuleGroups[0]['id']],
                ],
                'checkPermissions' => FALSE,
                ]);
                echo '  - MAJ groupe de règles de dédoublonage';

                $dedupeRules = civicrm_api4('DedupeRule', 'get', [
                    'where' => [
                        ['dedupe_rule_group_id.name', '=', $rule_name],
                    ],
                    'checkPermissions' => FALSE,
                    ]);

                if(isset($dedupeRules[0])){
                    $results = civicrm_api4('DedupeRule', 'delete', [
                    'where' => [
                        ['dedupe_rule_group_id.name', '=', $rule_name],
                    ],
                    'checkPermissions' => FALSE,
                    ]);
                    echo ' et suppression des règles attachées';
                }

        } else {
            $results = civicrm_api4('DedupeRuleGroup', 'create', [
                'values' => [
                    'contact_type' => 'Individual',
                    'threshold' => 10,
                    'used' => 'Supervised',
                    'title' => E::ts('num don ou annulation ou deces (supervisée)'),
                    'name' => $rule_name,
                ],
                'checkPermissions' => FALSE,
                ]);

            echo '  - Création groupe de règles de dédoublonage';
        }
        echo " : ".$rule_name.' (id : '.$results['0']['id'].')'.PHP_EOL ;
        
        foreach($custom_dedupes as $custom_dedupe){
            $results = civicrm_api4('DedupeRule', 'create', [
            'values' => [
                'dedupe_rule_group_id.name' => $rule_name,
                'rule_table' => $custom_table[$custom_dedupe]['table'],
                'rule_field' => $custom_table[$custom_dedupe]['column'],
                'rule_weight' => '10',
            ],
            'checkPermissions' => FALSE,
            ]);
            echo "      --> rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'].PHP_EOL;
        }



    ### individuals : création règle automatique de dédoublonage utilisant les n° de don d'annulation et de deces
        $rule_name = 'numeros_don_annulation_dc_2';
        $dedupeRuleGroups = civicrm_api4('DedupeRuleGroup', 'get', [
            'select' => [
                'id',
            ],
            'where' => [
                ['name', '=', $rule_name],
            ],
            'checkPermissions' => FALSE,
        ]);

        if(isset($dedupeRuleGroups[0])){
            $results = civicrm_api4('DedupeRuleGroup', 'update', [
                'values' => [
                    'contact_type' => 'Individual',
                    'threshold' => 10,
                    'used' => 'Unsupervised',
                    'title' => E::ts('num don ou annulation ou deces (automatique)'),
                ],
                'where' => [
                    ['id', '=', $dedupeRuleGroups[0]['id']],
                ],
                'checkPermissions' => FALSE,
                ]);
                echo '  - MAJ groupe de règles de dédoublonage';

                $dedupeRules = civicrm_api4('DedupeRule', 'get', [
                    'where' => [
                        ['dedupe_rule_group_id.name', '=', $rule_name],
                    ],
                    'checkPermissions' => FALSE,
                    ]);

                if(isset($dedupeRules[0])){
                    $results = civicrm_api4('DedupeRule', 'delete', [
                    'where' => [
                        ['dedupe_rule_group_id.name', '=', $rule_name],
                    ],
                    'checkPermissions' => FALSE,
                    ]);
                    echo ' et suppression des règles attachées';
                }

        } else {
            $results = civicrm_api4('DedupeRuleGroup', 'create', [
                'values' => [
                    'contact_type' => 'Individual',
                    'threshold' => 10,
                    'used' => 'Supervised',
                    'title' => E::ts('num don ou annulation ou deces (automatique)'),
                    'name' => $rule_name,
                ],
                'checkPermissions' => FALSE,
                ]);

            echo '  - Création groupe de règles de dédoublonage';
        }
        echo " : ".$rule_name.' (id : '.$results['0']['id'].')'.PHP_EOL ;
        
        foreach($custom_dedupes as $custom_dedupe){
            $results = civicrm_api4('DedupeRule', 'create', [
            'values' => [
                'dedupe_rule_group_id.name' => $rule_name,
                'rule_table' => $custom_table[$custom_dedupe]['table'],
                'rule_field' => $custom_table[$custom_dedupe]['column'],
                'rule_weight' => '10',
            ],
            'checkPermissions' => FALSE,
            ]);
            echo "      --> rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'].PHP_EOL;
        }



    ### Organisation : création de la table $custom_dedupes qui contient les valeurs de table et de colonne pour chque champ

            $custom_dedupes = ['name', 'email'];
            $custom_table=array();

            $custom_table['name']['table']='civicrm_contact';
            $custom_table['name']['column']='organization_name';
            $custom_table['email']['table']='civicrm_email';
            $custom_table['email']['column']='email';
            

    ### Organisation : création règle supervisée de dédoublonage utilisant le nom ou courriel
        $rule_name = 'OrganizationSupervised';
        $dedupeRuleGroups = civicrm_api4('DedupeRuleGroup', 'get', [
            'select' => [
                'id',
            ],
            'where' => [
                ['name', '=', $rule_name],
            ],
            'checkPermissions' => FALSE,
        ]); 

        if(isset($dedupeRuleGroups[0])){
            $results = civicrm_api4('DedupeRuleGroup', 'update', [
                'values' => [
                    'contact_type' => 'Organization',
                    'threshold' => 20,
                    'used' => 'Supervised',
                    'title' => E::ts('Nom ou courriel (supervisée)'),
                ],
                'where' => [
                    ['id', '=', $dedupeRuleGroups[0]['id']],
                ],
                'checkPermissions' => FALSE,
                ]);
                echo '  - MAJ groupe de règles de dédoublonage';

                $dedupeRules = civicrm_api4('DedupeRule', 'get', [
                    'where' => [
                        ['dedupe_rule_group_id.name', '=', $rule_name],
                    ],
                    'checkPermissions' => FALSE,
                    ]);

                if(isset($dedupeRules[0])){
                    $results = civicrm_api4('DedupeRule', 'delete', [
                    'where' => [
                        ['dedupe_rule_group_id.name', '=', $rule_name],
                    ],
                    'checkPermissions' => FALSE,
                    ]);
                    echo ' et suppression des règles attachées';
                }

        } else {
            $results = civicrm_api4('DedupeRuleGroup', 'create', [
                'values' => [
                    'contact_type' => 'Organization',
                    'threshold' => 20,
                    'used' => 'Supervised',
                    'title' => E::ts('Nom ou courriel (supervisée)'),
                    'name' => $rule_name,
                ],
                'checkPermissions' => FALSE,
                ]);

            echo '  - Création groupe de règles de dédoublonage';
        }
        echo " : ".$rule_name.' (id : '.$results['0']['id'].')'.PHP_EOL ;
        
        foreach($custom_dedupes as $custom_dedupe){
            $results = civicrm_api4('DedupeRule', 'create', [
            'values' => [
                'dedupe_rule_group_id.name' => $rule_name,
                'rule_table' => $custom_table[$custom_dedupe]['table'],
                'rule_field' => $custom_table[$custom_dedupe]['column'],
                'rule_weight' => '10',
            ],
            'checkPermissions' => FALSE,
            ]);
            echo "      --> rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'].PHP_EOL;
        }



    ### Organization : création règle automatique de dédoublonage utilisant le nom ou courriel
        $rule_name = 'OrganizationUnsupervised';
        $dedupeRuleGroups = civicrm_api4('DedupeRuleGroup', 'get', [
            'select' => [
                'id',
            ],
            'where' => [
                ['name', '=', $rule_name],
            ],
            'checkPermissions' => FALSE,
        ]);

        if(isset($dedupeRuleGroups[0])){
            $results = civicrm_api4('DedupeRuleGroup', 'update', [
                'values' => [
                    'contact_type' => 'Organization',
                    'threshold' => 20,
                    'used' => 'Unsupervised',
                    'title' => E::ts('Nom ou courriel (automatique)'),
                ],
                'where' => [
                    ['id', '=', $dedupeRuleGroups[0]['id']],
                ],
                'checkPermissions' => FALSE,
                ]);
                echo '  - MAJ groupe de règles de dédoublonage';

                $dedupeRules = civicrm_api4('DedupeRule', 'get', [
                    'where' => [
                        ['dedupe_rule_group_id.name', '=', $rule_name],
                    ],
                    'checkPermissions' => FALSE,
                    ]);

                if(isset($dedupeRules[0])){
                    $results = civicrm_api4('DedupeRule', 'delete', [
                    'where' => [
                        ['dedupe_rule_group_id.name', '=', $rule_name],
                    ],
                    'checkPermissions' => FALSE,
                    ]);
                    echo ' et suppression des règles attachées';
                }

        } else {
            $results = civicrm_api4('DedupeRuleGroup', 'create', [
                'values' => [
                    'contact_type' => 'Organization',
                    'threshold' => 10,
                    'used' => 'Supervised',
                    'title' => E::ts('Nom ou courriel (automatique)'),
                    'name' => $rule_name,
                ],
                'checkPermissions' => FALSE,
                ]);

            echo '  - Création groupe de règles de dédoublonage';
        }
        echo " : ".$rule_name.' (id : '.$results['0']['id'].')'.PHP_EOL ;
        
        foreach($custom_dedupes as $custom_dedupe){
            $results = civicrm_api4('DedupeRule', 'create', [
            'values' => [
                'dedupe_rule_group_id.name' => $rule_name,
                'rule_table' => $custom_table[$custom_dedupe]['table'],
                'rule_field' => $custom_table[$custom_dedupe]['column'],
                'rule_weight' => '10',
            ],
            'checkPermissions' => FALSE,
            ]);
            echo "      --> rule_table = ".$results[0]['rule_table']." - rule_field = ".$results[0]['rule_field']." - rule_weight = ".$results[0]['rule_weight'].PHP_EOL;
        }



    /// Fin de Reglage des règles de dédoublonage
