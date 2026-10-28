<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;

$exp_dir = './managed/';       // racine du répertoire d'import export

### Définition des fonctions

  function export_stuffCDC(){
      $entity = func_get_arg(0);      // nom de l'entité à créer (optionvalue, contact.....)
      $subtype = func_get_arg(1);
      $name = func_get_arg(2);        // prefixe du fichier d'export
      $exp_file = $name.".txt";

    switch ($entity) {

      case 'Organization':                                                // Contacts : individuals, organization
        $exports = civicrm_api4('Contact', 'get', [
            'select' => [
                '*',
                'CDC_admin.*',
              ],
            'where' => [
              ['contact_type', '=', $entity],
              ['contact_sub_type', '=', $subtype],
            ],
            //'limit' => 40,
            'checkPermissions' => FALSE,
            'orderBy' => [
                'sort_name' => 'ASC',
              ],
        ]);
        echo PHP_EOL.count($exports)." ".$entity." / ".$subtype." exportés into ".$exp_file.PHP_EOL;
        break;

      case 'Address':


            $adresses = civicrm_api4('Address', 'get', [
              'select' => [
                  '*',
                  'location_type_id',
                  'country_id.name',
                  'contact_id.legal_identifier',
                ],
              'where' => [
                  ['contact_id.contact_sub_type', '=', $subtype],
                ],
              'checkPermissions' => FALSE,
            ]);

          // vérifie que ces adresses appartiennent à des contacts qui ne sont pas dans la corbeille

          $exports=array();
          $error_log=array();                   // chaine contenant les messages d'erreur à loguer
          foreach ($adresses as $adresse){

              $contacts = civicrm_api4('Contact', 'get', [
                  'select' => [
                      'id',
                      'display_name',
                    ],
                  'where' => [
                    ['legal_identifier', '=', $adresse['contact_id.legal_identifier']],
                    ['is_deleted', '=', FALSE],
                  ],
                  'checkPermissions' => FALSE,
                ]);


                if (isset($contacts[0])){// il existe un contact avec cette adresse
                  //echo ".";
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
          echo PHP_EOL.count($exports)." ".$entity." / ".$subtype." exportés into ".$exp_file.PHP_EOL;
        break;

      case 'Phone':
            $phones = civicrm_api4('Phone', 'get', [
              'select' => [
                  '*',
                  'location_type_id:name',
                  'phone_type_id:name',
                  'contact_id.legal_identifier',
                ],
              'where' => [
                  ['contact_id.contact_sub_type', '=', $subtype],
                ],
              'checkPermissions' => FALSE,
              //'limit' => 25,
            ]);
          // vérifie que ces telephones appartiennent à des contacts qui ne sont pas dans la corbeille

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
                    ['legal_identifier', '=', $phone['contact_id.legal_identifier']],
                    ['is_deleted', '=', FALSE],
                  ],
                  'checkPermissions' => FALSE,
                ]);


                if (isset($contacts[0])){// il existe un contact avec ce téléphone
                  //echo $phone['contact_id']." ".$contacts[0]['display_name'].PHP_EOL;
                  //echo ".";
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
          echo PHP_EOL.count($exports)." ".$entity." / ".$subtype." exportés into ".$exp_file.PHP_EOL;
        break;

      case 'Email':
          $emails = civicrm_api4('Email', 'get', [
            'select' => [
                '*',
                'location_type_id:name',
                'contact_id.legal_identifier',
              ],
            'where' => [
                  ['contact_id.contact_sub_type', '=', $subtype],
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
                  ['legal_identifier', '=', $email['contact_id.legal_identifier']],
                  ['is_deleted', '=', FALSE],
                ],
                'checkPermissions' => FALSE,
              ]);


              if (isset($contacts[0])){// il existe un contact avec ce téléphone
                //echo $phone['contact_id']." ".$contacts[0]['display_name'].PHP_EOL;
                //echo ".";
                unset ($email['id']);
                unset ($email['location_type_id']);

                array_push($exports, $email);
                } else {
                $error = "Contact id ".$email['contact_id']."lié au téléphone ".$email['id']." n'existe pas  - Ignorée";
                echo PHP_EOL.$error.PHP_EOL;
                array_push($error_log,$error);
                }
        }
          echo PHP_EOL.count($exports)." ".$entity." / ".$subtype." exportés into ".$exp_file.PHP_EOL;
        break;

    }   // fin du switch

    if (isset($error_log[0])){
      echo PHP_EOL."Erreurs :".PHP_EOL;
      print_r($error_log).PHP_EOL;
    }

    file_put_contents($exp_file, json_encode($exports, JSON_PRETTY_PRINT));
  } // fin de la définition de la fonction export_stuffCDC

  function replaceInfile($file, $find, $replace) {
        if ($find != $replace) {
            //recupere la totalité du fichier
            $str = file_get_contents($file);
            if ($str === false) {
                return false;
            } else {
                //effectue le remplacement dans le texte
                $str = str_replace($find, $replace, $str);
                //remplace dans le fichier
                if (file_put_contents($file, $str) === false) {
                    return false;
                }
            }
        }
        return true;
  } // fin de définition de la fonction replaceInfile



  # Export des UF Groups avec civix export
    $uFGroups = civicrm_api4('UFGroup', 'get', [
        'select' => [
          '*',
        ],
        'where' => [
          ['base_module', '=', 'don_corps'],
        ],
        'checkPermissions' => FALSE,
      ]);

      if(isset ($uFGroups[0])){
        foreach ($uFGroups as $uFGroup){
          echo "exporting UFGroup ".$uFGroup['name']." (".$uFGroup['id'].")".PHP_EOL;
          $cmd = "civix export UFGroup ".$uFGroup['id'];
          echo $cmd.PHP_EOL;
          exec($cmd, $output, $retval);
          echo "Returned with status $retval and output:\n";
          print_r($output);
          unset ($output);

          // on renome le fichier mgd pour qu'il ne soit pas géré par civicrm automatiquement mais uniquement
          // à la demande, sinon almertes sur l'impossiblité de créer des UFFieds en double
          rename('managed/UFGroup_'.$uFGroup['name'].'.mgd.php', 'managed/UFGroup_'.$uFGroup['name'].'.mgd.php.disable');

        }
      }
  # Fin export des UF Groups avec civix export

  # Export des ContactLayouts avec civix export
    $contactLayouts = civicrm_api4('ContactLayout', 'get', [
      'select' => [
        'id',
        'label',
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
      ],
      'checkPermissions' => FALSE,
    ]);

    if(isset($contactLayouts[0])){
          foreach ($contactLayouts as $contactLayout){
            $name='ContactLayout_'.str_replace(' ', '_', $contactLayout['label']);
            echo "exporting Contact Layout ".$contactLayout['label']." (id : ".$contactLayout['id'].")".' as : '.$name.PHP_EOL;
            $cmd = "civix export ContactLayout ".$contactLayout['id'];
            echo $cmd.PHP_EOL;
            exec($cmd, $output, $retval);
            echo "Returned with status $retval and output:\n";
            print_r($output);
            unset ($output);

            $new="'name' => '".$name."'";
            echo $new.PHP_EOL;

            replaceInfile('managed/ContactLayout_1.mgd.php', "'name' => 'ContactLayout_1'", $new);
            rename('managed/ContactLayout_1.mgd.php', 'managed/'.$name.'.mgd.php');
        }

    }
  # Fin de l'export des ContactLayouts avec civix export

### Fin de l'export des contactlayouts

### Export des Tags
  $tags = civicrm_api4('Tag', 'get', [
      'select' => [
        'id',
        'name',
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
      ],
      'checkPermissions' => FALSE,
    ]);

    if(isset ($tags[0])){
      foreach ($tags as $tag){
        echo "exporting tag ".$tag['name']." (".$tag['id'].")".PHP_EOL;
        $cmd = "civix export Tag ".$tag['id'];
        echo $cmd.PHP_EOL;
        exec($cmd, $output, $retval);
        echo "Returned with status $retval and output:\n";
        print_r($output);
        unset ($output);
      }
    }
### Fin de l'export des Tags


### Export des Saved searches non crées avec civix export afform (pour tokens et purges)
  $savedSearches = civicrm_api4('SavedSearch', 'get', [
      'select' => [
        'id',
        'label',
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
        ['tags:label', 'IN', ['Requêtes utilisées pour les purges', 'tokens']],
      ],
      'orderBy' => [
        'name' => 'ASC',
      ],
      'checkPermissions' => FALSE,
    ]);

  if(isset ($savedSearches[0])){
    foreach ($savedSearches as $savedSearch){
      echo "exporting Search ".$savedSearch['label']." (".$savedSearch['id'].")".PHP_EOL;
      $cmd = "civix export SavedSearch ".$savedSearch['id'];
      echo $cmd.PHP_EOL;
      exec($cmd, $output, $retval);
      echo "Returned with status $retval and output:\n";
      print_r($output);
      unset ($output);
    }
  }

### Fin de l'export des Saved searches non crées avec civix export afform

### Export du Groupe archives
  $groups = civicrm_api4('Group', 'get', [
      'select' => [
        'id',
        'title',
      ],
      'where' => [
        ['title', '=', 'Archives'],
      ],
      'checkPermissions' => FALSE,
    ]);

  if(isset ($groups[0])){
    foreach ($groups as $group){
      echo "exporting Group ".$group['title']." (".$group['id'].")".PHP_EOL;
      $cmd = "civix export Group ".$group['id'];
      echo $cmd.PHP_EOL;
      exec($cmd, $output, $retval);
      echo "Returned with status $retval and output:\n";
      print_r($output);
      unset ($output);
    }
  }
### Fin de l'export du Groupe archives

### Export des groupes de champs personnalisés (custom groups)
  $customGroups = civicrm_api4('CustomGroup', 'get', [
      'select' => [
        'id',
        'name',
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
      ],
      'orderBy' => [
        'id' => 'ASC',
      ],
      'checkPermissions' => FALSE,
    ]);

    if(isset ($customGroups[0])){
      foreach ($customGroups as $customGroup){
        echo "exporting CustomGroup ".$customGroup['name']." (".$customGroup['id'].")".PHP_EOL;
        $cmd = "civix export CustomGroup ".$customGroup['id'];
        echo $cmd.PHP_EOL;
        exec($cmd, $output, $retval);
        echo "Returned with status $retval and output:\n";
        print_r($output);
        unset ($output);
      }
    }
### Fin de l'export des groupes de champs personnalisés (custom groups)

### Export des options de choix multiples (Option groups)
  $optionGroups = civicrm_api4('OptionGroup', 'get', [
      'select' => [
        'id',
        'name',
      ],
      'where' => [
        ['OR', [['name', '=', 'event_type'],['name', '=', 'document_type'], ['name', '=', 'activity_type'], ['name', '=', 'email_greeting'], ['name', '=', 'gender'], ['name', '=', 'postal_greeting']]],
      ],
      'checkPermissions' => FALSE,
    ]);

    if(isset ($optionGroups[0])){
      foreach ($optionGroups as $optionGroup){
        echo "exporting optionGroups ".$optionGroup['name']." (".$optionGroup['id'].")".PHP_EOL;
        $cmd = "civix export OptionGroup ".$optionGroup['id'];
        echo $cmd.PHP_EOL;
        exec($cmd, $output, $retval);
        echo "Returned with status $retval and output:\n";
        print_r($output);
        unset ($output);
      }
    }
### Fin de l'export des options de choix multiples (Option groups)

### Export des formulaires (afforms)
 # Lorsqu'on exporte un formulaire lié à une recherche, 
 # la recherche est sauvegardée dans le meme temps
 # Il est inutile de le faire 
  $afforms = civicrm_api4('Afform', 'get', [
    'where' => [
      ['base_module', '=', 'don_corps'],
    ],
    'orderBy' => [
      'name' => 'ASC',
    ],
    'checkPermissions' => FALSE,
    'select' => [
      'name',
    ],
  ]);

  if(isset ($afforms[0])){
    foreach ($afforms as $afform){
      echo "exporting afform ".$afform['name'].PHP_EOL;
      $cmd = "civix export Afform ".$afform['name'];
      echo $cmd.PHP_EOL;
      exec($cmd, $output, $retval);
      echo "Returned with status $retval and output:\n";
      print_r($output);
      unset ($output);
    }
  }
### Fin de l'export des formulaires (afforms)

### Export des menus de navigation
  // remove navigation menus mgd files created by afform (sinon doublons)
  $navigations = scandir($exp_dir);  // récupère la liste des fichiers contenus dans managed

  foreach($navigations as $navigation){
      if(preg_match("/Navigation/i",$navigation)){
          $navigation='./managed/'.$navigation;
          echo "Suppression de : ".$navigation.PHP_EOL;
          unlink($navigation);

      }
  }

  // récupère la liste des menus de navigation crés par l'extension
  $navigations = civicrm_api4('Navigation', 'get', [
      'select' => [
        'id',
        'label',
      ],
      'where' => [
        ['base_module', '=', 'don_corps'],
        ['parent_id', 'IS EMPTY'],
      ],
      'checkPermissions' => FALSE,
    ]);

  // exporte ces menus de navigation crées par l'extension
  if(isset ($navigations[0])){
    foreach ($navigations as $navigation){
      echo "exporting navigation menu ".$navigation['label'].' ('.$navigation['id'].')'.PHP_EOL;
      $cmd = "civix export Navigation ".$navigation['id'];
      echo $cmd.PHP_EOL;
      exec($cmd, $output, $retval);
      echo "Returned with status $retval and output:\n";
      print_r($output);
      unset ($output);
    }
  }
### Fin de l'export des menus de navigation

### Exporte les CDC dans le repertoire managed/




  // export organisations
        $exp_file = $exp_dir."05_organisations";
        export_stuffCDC('Organization','CDC', $exp_file);

  // export adresses
        $exp_file = $exp_dir."15_adresses";
        export_stuffCDC('Address','CDC', $exp_file);

  // export telephone
        $exp_file = $exp_dir."20_telephone";
        export_stuffCDC('Phone','CDC', $exp_file);

  // export Email
        $exp_file = $exp_dir."25_Email";
        export_stuffCDC('Email','CDC', $exp_file);

### Fin de l'export des CDC dans le repertoire managed/



