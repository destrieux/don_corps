<?php
namespace Civi\DonCorps\Install; //Le nom complet de la classe est \Civi\DonCorps\Install\CustomFieldFilter, 
                                 //et il correspond au fichier Civi/DonCorps/Install/CustomFieldFilter.php.

class SearchUpdateToken {        // Nom de la classe

  /**
   * Modifie le tag d'une recherche, non exporté aveec mgd files
   * @return bool TRUE si le tag a été déposé
   */

  public static function updateToken(string $exp_file): bool {
      // public : fonction appellable depuis n'importe ou 
      // static : on l'appelle directement sur la classe
      // string $exp_file : 1er parametre qui doit être du texte : nom du fichier contenant les tags à mofiider
      // bool : type de retour : la méthode renvoie true ou false

    if (!file_exists($exp_file)) {
      \Civi::log()->warning("don_corps : le fichier ".$exp_file." n'existe pas - tags des searches non mofifies");
      return FALSE;
    } else {
      $json = file_get_contents($exp_file);
      $tags_searches = json_decode($json, true);

      foreach ($tags_searches as $tags_search){
        // vérification que le tag et le search existent bien 
        $search = \Civi\Api4\SavedSearch::get(FALSE)
          ->addSelect('id')
          ->addWhere('name', '=', $tags_search['search'])
          ->execute()
          ->first();

        if (!$search) {
          \Civi::log()->warning("don_corps : recherche '{$tags_search['search']}' introuvable - tag non modifié.");
          return FALSE;
        }

        $tag = \Civi\Api4\Tag::get(FALSE)
          ->addSelect('id','name')
          ->addWhere('label', '=', $tags_search['tag'])
          ->execute()
          ->first();

        if (!$tag) {
          \Civi::log()->warning("don_corps : tag '{$tags_search['tag']}' introuvable - tag de la recherche '{$tags_search['search']}' non modifié.");
          return FALSE;
        }

        // le tag et le search existent : on vérifie si le tag est déja associé au search
        $entity_tag = \Civi\Api4\EntityTag::get(FALSE)
          ->addSelect('id')
          ->addWhere('entity_table', '=', 'civicrm_saved_search')
          ->addWhere('entity_id', '=', $search['id'])
          ->execute()
          ->first();

        if($entity_tag){
          //\Civi::log()->info("don_corps : tag '{$tags_search['tag']}' déja associé à la recherche '{$tags_search['search']}' non modifié.");
        }else{
          $entity_tag = \Civi\Api4\EntityTag::create(FALSE)
          ->addValue('entity_table','civicrm_saved_search')
          ->addValue('tag_id.name', $tag['name'])
          ->addValue('entity_id', $search['id'])
          ->execute();
          //\Civi::log()->info("don_corps : nouveau tag '{$tags_search['tag']}' associé à la recherche '{$tags_search['search']}.");
        }
      }
      return TRUE;
    }
  }
}