<?php
// Civi/DonCorps/Install/CustomFieldFilter.php
namespace Civi\DonCorps\Install; //Le nom complet de la classe est \Civi\DonCorps\Install\CustomFieldFilter, 
                                 //et il correspond au fichier Civi/DonCorps/Install/CustomFieldFilter.php.

class CustomFieldFilter {        // Nom de la classe

  /**
   * Modifie le filtre d'un champ personnalisé de type "référence de contact" sur un groupe.
   * Invoquée après l'installation des champs personalisés qui filtrent les contacts
   * syntaxe :  (Custom_field_name Groupe_servant_de_filtre
   * @return bool TRUE si le filtre a été appliqué
   */



  public static function applyGroupFilter(string $customFieldName, string $groupName): bool {
      // public : fonction appellable depuis n'importe ou 
      // static : on l'appelle directement sur la classe
      // string $customFieldName : 1er parametre qui doit être du texte
      // bool : type de retour : la méthode renvoie true ou false

    $field = \Civi\Api4\CustomField::get(FALSE)
    ->addSelect('id')
    ->addWhere('name', '=', $customFieldName)
    ->execute()
    ->first();
    
        // \Civi\Api4\CustomField::get(FALSE) : appelle la méthode get sur la classe CustomField
        // FALSE : ne teste pas les permissions
        // first : ne retourne que la premiere valeur (ancien $fields[0]['id']
        //         ou bien NULL si cette valeur n'existe pas 


    if (!$field) {
      \Civi::log()->warning("don_corps : champ personnalisé '{$customFieldName}' introuvable.");
      return FALSE;
    }

    $group = \Civi\Api4\Group::get(FALSE)
      ->addSelect('id')
      ->addWhere('name', '=', $groupName)
      ->execute()
      ->first();

    if (!$group) {
      \Civi::log()->warning("don_corps : groupe '{$groupName}' introuvable.");
      return FALSE;
    }

    try {
      \Civi\Api4\CustomField::update(FALSE)
        ->addWhere('id', '=', $field['id'])
        ->addValue('filter', 'action=lookup&group=' . $group['id'])
        ->execute();
    }
    catch (\Throwable $e) {
      \Civi::log()->error("don_corps : échec du filtre sur '{$customFieldName}' : " . $e->getMessage());
      return FALSE;
    }
        // $e->getMessage() : récupère l'erreur et ajoute aux logs


    \Civi::log()->info("don_corps : champ '{$customFieldName}' filtré par le groupe '{$groupName}'.");
    return TRUE;
  }

}