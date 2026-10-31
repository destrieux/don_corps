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
        'Participants.Nombre_de_s_ances',
        'Participants.Tarif_par_apprenant',
        'Participants.Tarif_par_formateur',
        'Participants.Tarif_par_staff',
        'Participants.Montant_Total_Apprenants',
        'Participants.Montant_total_formateurs',
        'Participants.Montant_total_Staff',
        'Participants.Montant_total_devis'
      )
      ->addWhere('id', '=', $entityID)
      ->execute()->first();
    if (!$event) {
      return;
    }

   $champsCalcules = [
  'Participants.Total_participants',
  'Participants.Montant_Total_Apprenants',
  'Participants.Montant_total_formateurs',
  'Participants.Montant_total_Staff',
  'Participants.Montant_total_devis',
];

$event = \Civi\Api4\Event::get(FALSE)
  ->addSelect(
    'Participants.Nombre_d_apprenants',
    'Participants.Nombre_de_formateurs',
    'Participants.Nombre_de_Staff',
    'Participants.Nombre_de_s_ances',
    'Participants.Tarif_par_apprenant',
    'Participants.Tarif_par_formateur',
    'Participants.Tarif_par_staff',
    'Participants.Total_participants',
    'Participants.Montant_Total_Apprenants',
    'Participants.Montant_total_formateurs',
    'Participants.Montant_total_Staff',
    'Participants.Montant_total_devis'
  )
  ->addWhere('id', '=', $entityID)
  ->execute()->first();
if (!$event) {
  return;
}

$apprenants = (int) ($event['Participants.Nombre_d_apprenants'] ?? 0);
$formateurs = (int) ($event['Participants.Nombre_de_formateurs'] ?? 0);
$staff      = (int) ($event['Participants.Nombre_de_Staff'] ?? 0);
$seances    = (int) ($event['Participants.Nombre_de_s_ances'] ?? 0);

$total = ($apprenants + $formateurs + $staff) * $seances;

$montantApprenants = $apprenants * (float) ($event['Participants.Tarif_par_apprenant'] ?? 0) * $seances;
$montantFormateurs = $formateurs * (float) ($event['Participants.Tarif_par_formateur'] ?? 0) * $seances;
$montantStaff      = $staff      * (float) ($event['Participants.Tarif_par_staff'] ?? 0)      * $seances;
$totalGeneral      = $montantApprenants + $montantFormateurs + $montantStaff;

$nouvelles = [
  'Participants.Total_participants'        => $total,
  'Participants.Montant_Total_Apprenants'  => round($montantApprenants, 2),
  'Participants.Montant_total_formateurs'  => round($montantFormateurs, 2),
  'Participants.Montant_total_Staff'       => round($montantStaff, 2),
  'Participants.Montant_total_devis'       => round($totalGeneral, 2),
];

// On ne garde que les champs dont la valeur a changé
$aEcrire = [];
foreach ($nouvelles as $champ => $valeur) {
  $actuelle = $event[$champ] ?? NULL;
  if ($actuelle === NULL || abs((float) $actuelle - (float) $valeur) > 0.004) {
    $aEcrire[$champ] = $valeur;
  }
}

// Rien n'a changé : on sort sans écrire (évite la boucle)
if (!$aEcrire) {
  return;
}

$update = \Civi\Api4\Event::update(FALSE)->addWhere('id', '=', $entityID);
foreach ($aEcrire as $champ => $valeur) {
  $update->addValue($champ, $valeur);
}
$update->execute();
  }

}