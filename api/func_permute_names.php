<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;

## modif Nom de famille -> nom patronymique et surnom -> nom d'usage)
#contactlayout personnel (change aussi les autres)
#profils
# -demanderu d'info (ajoute nom d'usage)
# -Inscription donateur
# -inscription proche donneur (ajouter nom usage)
# -Personnel de centre de don de corps(ajouter nom usage)$
#
#Searchs (ajoute nom d'usage dans les champs et dans les champs de recherche de form)
# - tous contacts ; donerurs vivants ; donneurs DCD
# - tokesn de contacts




function permute_names () {

  echo "##############".PHP_EOL;
  echo "Dans les installations avant avril 2026, les noms de famille sont gérés par deux champs :".PHP_EOL;
  echo "  - last_name : nom d'usage".PHP_EOL;
  echo "  - nick_name : nom de naissance".PHP_EOL.PHP_EOL;
  echo "Ce script permute ces deux champs pour les individus (donneurs, proches, personnels...)".PHP_EOL;
  echo "Après exécution : ".PHP_EOL;
  echo "  - last_name contient le nom de naissance".PHP_EOL;
  echo "  - nick_name contient le nom d'usage".PHP_EOL;
  echo "Dans le cas ou nick_name n'est pas connu inialement, rien n'est modifié".PHP_EOL;
  echo "##############".PHP_EOL;

  ## On récupère les contacts ayant un nom de naissance (nick_name) dans l'installation initiale et non anonymisés
    $contacts = civicrm_api4('Contact', 'get', [
      'select' => [
        'id',
        'last_name',
        'nick_name',
      ],
      'where' => [
        ['contact_type', '=', 'Individual'],
        ['nick_name', '!=', null],
        ['last_name', '!=', 'ANONYMISE'],
      ],
      'checkPermissions' => FALSE,
    ]);

    $total=count($contacts);
    $cnt=1;


  echo PHP_EOL."Confirmez les ".$total." permutations (O/N) ?".PHP_EOL;
        $kb = trim(fgets(STDIN)); // Lire l'entrée et supprimer les espaces inutiles

        if ($kb!='O' AND $kb!='o'){
          echo "Installation non modifiée".PHP_EOL;
          exit;
        }
  




    foreach ($contacts as $contact) {
      echo $cnt."/".$total." (id :".$contact['id'].") : ";
      if ($contact['nick_name']!=$contact['last_name']){   ## on ne traite pas les contacts ou last_name et nick_name sont identiques
        $temp_patro = $contact['nick_name'];
        echo "  Nom famille [de naissance] : ".$contact['last_name']." [".$contact['nick_name']."]";
        
        
        "Nom patronymique".PHP_EOL;
        
        $results = civicrm_api4('Contact', 'update', [
          'values' => [
            'nick_name' => $contact['last_name'],
            'last_name' => $temp_patro,
          ],
          'where' => [
            ['id', '=', $contact['id']],
          ],
          'checkPermissions' => FALSE,
        ]);

        echo " --->  Nom patronymique [d'usage] : ".$results[0]['last_name']." [".$results[0]['nick_name']."]".PHP_EOL;

      } else {
        echo "  Noms patronymique et d'usage identiques (".$contact['last_name']."): non modifié".PHP_EOL;
      }
      ++$cnt;
    }   
  

}




permute_names ();
