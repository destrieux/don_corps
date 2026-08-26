<?php
eval(`cv php:boot`);
use CRM_CiviDdc_ExtensionUtil as E;

define("CSVFILE", CRM_Core_Config::singleton()->configAndLogDir."/civicrm_check_import.csv"); // définition du ficher de log dans wp-content/uploads/civicrm/ConfigAndLogs

if (is_writable(CSVFILE)) {  // le ficher log existe 
      if (!$fp = fopen(CSVFILE, 'a')) {
          echo "Impossible d'ouvrir le fichier (".CSVFILE.")"."\n";
          exit;
      }

  } else {  // le fichier log n'existe pas  ; le créer
      $fp = fopen(CSVFILE, 'c+b');
      echo "Création nouveau log : ".CSVFILE."\n";
      fclose($fp);
  }


// change le propriétaire et le groupe du fichier de log identique au repertoire de log principal
// dans le cas contraire, les menus ne s'affichent pas 
$own= fileowner(CRM_Core_Config::singleton()->configAndLogDir);
$grp= filegroup(CRM_Core_Config::singleton()->configAndLogDir);
chown(CSVFILE, $own);
chgrp(CSVFILE, $grp);

$fp=fopen(CSVFILE, 'w'); // ouvre le fichier de log w pour le vidr à l'ouverture


echo PHP_EOL.'**** INDIVIDUS NON SUPPRIMES'.PHP_EOL;
    $subtypes =  ['Personnel', 'Donateur', 'Proches', 'Demandeur_d_information', 'Pompes', 'CDC', 'Emprunteur' ];
    $total=0;

    foreach ($subtypes as $subtype){
        $contacts = civicrm_api4('Contact', 'get', [
    'select' => [
        'COUNT(*) AS total',
    ],
    'where' => [
        ['contact_sub_type', '=', $subtype],
        ['is_deleted', '=', FALSE],
    ],
    'limit' => 25,
    'checkPermissions' => FALSE,
    ]);

    if($contacts[0]['total']<>0){
        $total=$total + $contacts[0]['total'];
            $msg= 'Contact / '.$subtype." , ".$contacts[0]['total'].PHP_EOL;
            fwrite($fp, $msg);
            echo $msg;
        }
    }
    $msg= 'TOTAL ,'.$total.PHP_EOL;
    fwrite($fp, $msg);
    echo $msg;

echo PHP_EOL.'**** RELATIONS DEPUIS et VERS CONTACTS NON SUPPRIMES'.PHP_EOL;
    $relationship_types =  ['a pour PAQPF', 'a pour personne de confiance', 'a pour personne de confiance 2', 'Conjoint de', 'Employé de', 'Enfant de', 'Frère ou sœur de'];
    $total=0;

    foreach ($relationship_types as $relationship_type){
        $relationships = civicrm_api4('Relationship', 'get', [
    'select' => [
        'COUNT(*) AS total',
    ],
    'where' => [
        ['relationship_type_id:label', '=', $relationship_type],
        ['contact_id_a.is_deleted', '=', FALSE],
        ['contact_id_b.is_deleted', '=', FALSE],
    ],
    'limit' => 25,
    'checkPermissions' => FALSE,
    ]);

        if($relationships[0]['total']<>0){
            $total=$total + $relationships[0]['total'];
            $msg = 'Relations / '.$relationship_type." , ".$relationships[0]['total'].PHP_EOL;
            fwrite($fp, $msg);
            echo $msg;
        }

    }
    $msg= 'TOTAL ,'.$total.PHP_EOL;
    fwrite($fp, $msg);
    echo $msg;

echo PHP_EOL.'**** ADRESSES DES CONTACTS NON SUPPRIMES'.PHP_EOL;
    $subtypes =  ['Personnel', 'Donateur', 'Proches', 'Demandeur_d_information', 'Pompes', 'CDC', 'Emprunteur' ];
    $total=0;

    foreach ($subtypes as $subtype){
        $addresses = civicrm_api4('Address', 'get', [
        'select' => [
            'COUNT(*) AS total',
        ],
        'where' => [
            ['contact_id.contact_sub_type', '=', $subtype],
            ['contact_id.is_deleted', '=', FALSE],
        ],
        'limit' => 25,
        'checkPermissions' => FALSE,
        ]);

        if($addresses[0]['total']<>0){
            $total=$total + $addresses[0]['total']; 
            $msg = 'Adresses pour '.$subtype." , ".$addresses[0]['total'].PHP_EOL;
            fwrite($fp, $msg);
            echo $msg;
        }
    }
    $msg = 'TOTAL ,'.$total.PHP_EOL;
    fwrite($fp, $msg);
    echo $msg;

echo PHP_EOL.'**** COURRIELS DES CONTACTS NON SUPPRIMES'.PHP_EOL;
    $total=0;
    foreach ($subtypes as $subtype){
        $emails = civicrm_api4('Email', 'get', [
        'select' => [
            'COUNT(*) AS total',
        ],
        'where' => [
            ['contact_id.contact_sub_type', '=', $subtype],
            ['contact_id.is_deleted', '=', FALSE],
        ],
        'limit' => 25,
        'checkPermissions' => FALSE,
        ]);

        if($emails[0]['total']<>0){
            $total=$total + $emails[0]['total'];
            $msg = 'Courriels pour '.$subtype." , ".$emails[0]['total'].PHP_EOL;
            fwrite($fp, $msg);
            echo $msg;
        }
        
    }
    $msg = 'TOTAL ,'.$total.PHP_EOL;
    fwrite($fp, $msg);
    echo $msg;


echo PHP_EOL.'**** TELEPHONES DES CONTACTS NON SUPPRIMES'.PHP_EOL;
    $total=0;
    foreach ($subtypes as $subtype){
        $phone = civicrm_api4('Phone', 'get', [
        'select' => [
            'COUNT(*) AS total',
        ],
        'where' => [
            ['contact_id.contact_sub_type', '=', $subtype],
            ['contact_id.is_deleted', '=', FALSE],
        ],
        'limit' => 25,
        'checkPermissions' => FALSE,
        ]);
        if($phone[0]['total']<>0){
            $total=$total + $phone[0]['total'];
            $msg = 'Courriels pour '.$subtype." , ".$phone[0]['total'].PHP_EOL;
            fwrite($fp, $msg);
            echo $msg;
        }
    }
    $msg = 'TOTAL ,'.$total.PHP_EOL;
    fwrite($fp, $msg);
    echo $msg;

echo PHP_EOL.'**** ARRIVEES DE CORPS POUR CONTACTS NON SUPPRIMES'.PHP_EOL;
    $total=0;
    try {
    $arrivees = civicrm_api4('Custom_Arriv_e_du_corps_new', 'get', [
        'select' => [
            'COUNT(*) AS total',
        ],
        'where' => [
            ['entity_id.is_deleted', '=', FALSE],
        ],
        'limit' => 25,
        'checkPermissions' => FALSE,
        ]);

    } catch (Exception $e){
        $arrivees[0]['total']=0;
    }
    if($arrivees[0]['total']<>0){
    $msg = 'Arrivees de corps , '.$arrivees[0]['total'].PHP_EOL;
    fwrite($fp, $msg);
    echo $msg;
    }


echo PHP_EOL.'**** EVENTS ACTIFS'.PHP_EOL;
    $total=0;
    try {
     $events = civicrm_api4('Event', 'get', [
        'select' => [
            'COUNT(*) AS total',
        ],
        'where' => [
             ['is_active', '=', TRUE],
        ],
        'limit' => 25,
        'checkPermissions' => FALSE,
        ]);

    } catch (Exception $e){
        $events[0]['total']=0;
    }
    if($events[0]['total']<>0){
        $msg = 'Events , '.$events[0]['total'].PHP_EOL;
        fwrite($fp, $msg);
        echo $msg;
    }


echo PHP_EOL.'**** PARTICIPANTS NON SUPPRIMES POUR EVENTS ACTIFS'.PHP_EOL;
    $total=0;
    try {
     $participants = civicrm_api4('Participant', 'get', [
        'select' => [
            'COUNT(*) AS total',
        ],
        'where' => [
    ['event_id.is_active', '=', TRUE],
    ['contact_id.is_deleted', '=', FALSE],
        ],
        'limit' => 25,
        'checkPermissions' => FALSE,
        ]);

    } catch (Exception $e){
        $participants[0]['total']=0;
    }
    if($participants[0]['total']<>0){
        $msg = 'Participants , '.$participants[0]['total'].PHP_EOL;
        fwrite($fp, $msg);
        echo $msg;
    }



echo PHP_EOL.'**** UTLISATIONS DU CORPS POUR CONTACTS NON SUPPRIMES'.PHP_EOL;
    $total=0;
    try {
     $uses = civicrm_api4('Custom_Utilisation_du_corps', 'get', [
        'select' => [
            'COUNT(*) AS total',
        ],
        'where' => [
    ['entity_id.is_deleted', '=', FALSE],
        ],
        'limit' => 25,
        'checkPermissions' => FALSE,
        ]);

    } catch (Exception $e){
        $uses[0]['total']=0;
    }
    if($uses[0]['total']<>0){
        $msg = 'Utilisations , '.$uses[0]['total'].PHP_EOL;
        fwrite($fp, $msg);
        echo $msg;
    }

echo PHP_EOL.'**** PROTOCOLES IN VIVO POUR CONTACTS NON SUPPRIMES'.PHP_EOL;
    $total=0;
    try {
     $protos = civicrm_api4('Custom_Protocoles_in_vivo', 'get', [
        'select' => [
            'COUNT(*) AS total',
        ],
        'where' => [
    ['entity_id.is_deleted', '=', FALSE],
        ],
        'limit' => 25,
        'checkPermissions' => FALSE,
        ]);

    } catch (Exception $e){
        $protos[0]['total']=0;
    }
    if($protos[0]['total']<>0){
        $msg = 'Protocoles , '.$protos[0]['total'].PHP_EOL;
        fwrite($fp, $msg);
        echo $msg;
    }

echo PHP_EOL.'**** DONS DE CONTACTS NON SUPPRIMES'.PHP_EOL;
    $total=0;
    try {
     $dons = civicrm_api4('Contribution', 'get', [
        'select' => [
            'COUNT(*) AS total',
        ],
        'where' => [
    ['contact_id.is_deleted', '=', FALSE],
        ],
        'limit' => 25,
        'checkPermissions' => FALSE,
        ]);

    } catch (Exception $e){
        $dons[0]['total']=0;
    }
    if($dons[0]['total']<>0){
        $msg = 'Dons , '.$dons[0]['total'].PHP_EOL;
        fwrite($fp, $msg);
        echo $msg;
    }



echo PHP_EOL.'**** ACTIVITES IMPLIQUANT DES CONTACTS NON SUPPRIMES'.PHP_EOL;
    $total=0;

    $types = civicrm_api4('OptionValue', 'get', [
    'select' => [
        'value',
        'label',
    ],
    'where' => [
        ['option_group_id:name', '=', 'activity_type'],
    ],
    'orderBy' => [
        'name' => 'ASC',
    ],
    'checkPermissions' => FALSE,
    ]);



    $total=0;

    foreach ($types as $type){
        $activities = civicrm_api4('Activity', 'get', [
        'select' => [
            'COUNT(*) AS total',
        ],
        'where' => [
            ['activity_type_id', '=', $type['value']],
            ['is_deleted', '=', FALSE],
            ['source_contact_id', 'IS NOT NULL'],
            ['target_contact_id', 'IS NOT NULL'],
        ],
        'limit' => 25,
        'checkPermissions' => FALSE,
        ]);
        
        if($activities[0]['total']<>0){
            $total=$total + $activities[0]['total'];
            $msg = 'Activity Contact pour activité '.$type['label']." , ".$activities[0]['total'].PHP_EOL;
            fwrite($fp, $msg);
            echo $msg;
        }

    }
    echo 'TOTAL ,'.$total.PHP_EOL;

fclose($fp); // ferme le fichier de log

echo  PHP_EOL.'********* Fichier csv : '.CSVFILE.PHP_EOL;