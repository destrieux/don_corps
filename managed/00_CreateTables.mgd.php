<?php

/**
 * Crée les tables d'entités avant la réconciliation des entités gérées.
 *
 * Les .mgd.php sont lus avant toute création de champ. Sans ce fichier, une mise
 * à jour qui ajoute un champ dépendant de civicrm_saisine échoue (erreur 1215),
 * car l'Upgrader crée la table après la réconciliation.
 */
try {
  if (!\CRM_Core_DAO::checkTableExists('civicrm_saisine')) {
    \CRM_DonCorps_ExtensionUtil::schema()->createEntityTable('schema/Saisine.entityType.php');
    \Civi::log()->info('don_corps : table civicrm_saisine créée depuis 00_CreateTables.mgd.php');
  } else {
    \Civi::log()->info('don_corps : table civicrm_saisine existe deja.mgd.php');
  }
}
catch (\Throwable $e) {
  \Civi::log()->error('don_corps : création de civicrm_saisine impossible : ' . $e->getMessage());
}

// Aucune entité gérée dans ce fichier : il ne sert qu'à l'effet de bord ci-dessus.
return [];