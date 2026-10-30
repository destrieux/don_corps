<?php
// Civi/DonCorps/Hook/Custom.php
namespace Civi\DonCorps\Hook;
    // Elle indique que toutes les classes déclarées dans ce fichier 
    // appartiennent au « dossier virtuel » Civi\DonCorps\Hook.
    // La classe s’appelle Custom dans le fichier, 
    //  mais son nom complet est \Civi\DonCorps\Hook\Custom (namespace\class)
class Custom {

  public static function handle(string $op, int $groupID, int $entityID): void {
    /* 
    - \Civi\DonCorps\Hook\Custom est le nom complet de la classe, 
        qui correspond au fichier Civi/DonCorps/Hook/Custom.php. 
    -  Le \ initial indique qu’on part de la racine des namespaces.
    - ::handle(...) appelle sa méthode statique handle.
    - (int) $groupID et (int) $entityID convertissent en entier. 
        CiviCRM peut fournir ces valeurs sous forme de texte ("23"), 
        et la méthode handle déclare des paramètres de type int, 
        donc la conversion évite une erreur de type.
    - $params n’est pas transmis : le code n’en a pas besoin, 
        puisque handle() relit les valeurs en base avec l’API4. 
        Cela évite de dépendre de la structure interne de $params, qui est difficile à manier.
    */

    if (!in_array($op, ['create', 'edit'], TRUE) || !$entityID) {
      return;
    } // verifie que l'opérateur est create ou edit et qu'il y a un custom field de défini 

    $group = \Civi\Api4\CustomGroup::get(FALSE)
      ->addSelect('name')
      ->addWhere('id', '=', $groupID)
      ->execute()->first();

    if (!$group || $group['name'] !== 'Participants') { // ne lance la fonction que qi le champps appartient au groupe participants
      return;
    }

    $event = \Civi\Api4\Event::get(FALSE)
      ->addSelect(
        'Participants.Nombre_d_apprenants',
        'Participants.Nombre_de_formateurs',
        'Participants.Nombre_de_Staff',
        'Participants.Total_participants',
        'Participants.Nombre_de_s_ances'
      )
      ->addWhere('id', '=', $entityID)
      ->execute()->first();
    if (!$event) {
      return;
    }

    $total = ((int) ($event['Participants.Nombre_d_apprenants'] ?? 0)
           + (int) ($event['Participants.Nombre_de_formateurs'] ?? 0)
           + (int) ($event['Participants.Nombre_de_Staff'] ?? 0))
           * (int) ($event['Participants.Nombre_de_s_ances'] ?? 0);

    // Garde-fou : on n'écrit que si la valeur change, sinon la mise à jour
    // redéclenche ce hook en boucle.
    if ((int) ($event['Participants.Total_participants'] ?? -1) === $total) {
      return;
    }

    \Civi\Api4\Event::update(FALSE)
      ->addWhere('id', '=', $entityID)
      ->addValue('Participants.Total_participants', $total)
      ->execute();
  }

}