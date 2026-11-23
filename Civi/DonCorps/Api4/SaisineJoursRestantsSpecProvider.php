<?php
namespace Civi\DonCorps\Api4;

use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;
use Civi\Api4\Service\Spec\Provider\Generic\SpecProviderInterface;
use CRM_DonCorps_ExtensionUtil as E;

/**
 * Champs calculés sur Saisine :
 * - jours_restants : jours entre aujourd'hui et la date de fin de validité
 * - etat_validite  : ok / bientot / expire
 *
 * @service
 * @internal
 */
class SaisineJoursRestantsSpecProvider extends \Civi\Core\Service\AutoService implements SpecProviderInterface {

  const GROUPE = 'caracteristiques';
  const CHAMP_DEBUT = 'Date_d_but_de_validit_';
  const CHAMP_FIN = 'Date_fin_de_validit_';

  public function modifySpec(RequestSpec $spec): void {
    $jours = new FieldSpec('jours_restants', 'Saisine', 'Integer');
    $jours->setLabel(E::ts('Jours restants (fin de validité)'))
      ->setTitle(E::ts('Jours restants (fin de validité)'))
      ->setType('Extra')
      ->setReadonly(TRUE)
      ->setColumnName('id')
      ->setSqlRenderer([__CLASS__, 'renderJoursRestants']);
    $spec->addFieldSpec($jours);

    $etat = new FieldSpec('etat_validite', 'Saisine', 'String');
    $etat->setLabel(E::ts('État de validité'))
      ->setTitle(E::ts('État de validité'))
      ->setDescription(E::ts('ok : > 90 jours ou période < 7 jours ; bientot : 0 à 90 jours ; expire : date dépassée'))
      ->setType('Extra')
      ->setReadonly(TRUE)
      ->setColumnName('id')
      ->setSqlRenderer([__CLASS__, 'renderEtat']);
    $spec->addFieldSpec($etat);
  }

  public function applies($entity, $action): bool {
    return $entity === 'Saisine' && $action === 'get';
  }

  /**
   * Renvoie [table, colonne] SQL d'un champ du groupe, ou NULL s'il n'existe pas.
   */
  private static function colonne(string $champ): ?array {
    $id = \CRM_Core_BAO_CustomField::getCustomFieldID($champ, self::GROUPE);
    if (!$id) {
      return NULL;
    }
    [$table, $column] = \CRM_Core_BAO_CustomField::getTableColumnGroup($id);
    return [$table, $column];
  }

  public static function renderJoursRestants(array $field): string {
    $fin = self::colonne(self::CHAMP_FIN);
    if (!$fin) {
      return 'NULL';
    }
    [$table, $colFin] = $fin;
    return "(
      SELECT DATEDIFF(cv.`$colFin`, CURDATE())
      FROM `$table` cv
      WHERE cv.entity_id = {$field['sql_name']}
      LIMIT 1
    )";
  }

  public static function renderEtat(array $field): string {
    $fin = self::colonne(self::CHAMP_FIN);
    $debut = self::colonne(self::CHAMP_DEBUT);
    if (!$fin || !$debut) {
      return 'NULL';
    }
    [$table, $colFin] = $fin;
    [, $colDebut] = $debut;

    return "(
      SELECT CASE
        WHEN cv.`$colFin` IS NULL THEN NULL
        WHEN cv.`$colDebut` IS NOT NULL
             AND DATEDIFF(cv.`$colFin`, cv.`$colDebut`) < 7 THEN 'ok'
        WHEN DATEDIFF(cv.`$colFin`, CURDATE()) > 90 THEN 'ok'
        WHEN DATEDIFF(cv.`$colFin`, CURDATE()) >= 0 THEN 'bientot'
        ELSE 'expire'
      END
      FROM `$table` cv
      WHERE cv.entity_id = {$field['sql_name']}
      LIMIT 1
    )";
  }

}