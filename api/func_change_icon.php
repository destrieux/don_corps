<?php
eval(`cv php:boot`);
use CRM_DonCorps_ExtensionUtil as E;

 //////////// function change_icon /////////
// Cette fonction est invoquée en post installation installation pour remplacer aussi l'icone du menu
//
// syntaxe : change_icon  ('nom_du_menu_dont les sous_rubriques_sont_a_desactiver', 'icone');
//
// elle est appelée par : function don_corps_civicrm_postinstall()
//////////////

function change_icon2 (){
    $menu = func_get_arg(0);
    $icon = func_get_arg(1);
  
    $navigations = civicrm_api4('Navigation', 'get', [
        'where' => [
          ['name', '=', $menu],
        ],
        'checkPermissions' => FALSE,
      ]);
  
        //print_r($navigations);


    if (isset($navigations[0])){                            // si ce menu
        $results = civicrm_api4('Navigation', 'update', [   // on change l'icone
          'values' => [
            'icon' => $icon,
          ],
          'where' => [
            ['name', '=', $menu],
          ],
          'checkPermissions' => FALSE,
        ]);
     
        
        echo "icone du menu ".$menu." changée pour : ".$icon."\n";
        print_r($results);
        CRM_Core_Session::setStatus('Icone de la rubrique de menu '.$menu.' changée', 'Succès', 'success');
      
  
    } else {
       echo "Pas de sous rubrique pour ".$menu."\n";
       CRM_Core_Session::setStatus('Pas de sous rubrique pour '.$menu, 'Info', 'info');
    }
  
  }    // Fin de définition de la function change_icon ()

    // Changement des icones de menus
    echo PHP_EOL."  -Changement des icones de menus".PHP_EOL;
    change_icon2('Contacts', 'crm-i fa-address-book-o');
    change_icon2('Search', 'crm-i fa-search');
    change_icon2('Contributions','crm-i fa-money-bill-1');
    // Fin du Changement des icones de menus

