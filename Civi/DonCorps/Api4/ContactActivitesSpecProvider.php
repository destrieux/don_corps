<?php
namespace Civi\DonCorps\Api4;

use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;
use Civi\Api4\Service\Spec\Provider\Generic\SpecProviderInterface;
use CRM_DonCorps_ExtensionUtil as E;

/**
 * Ajoute le champ calculé Contact.liste_activites (lecture seule).
 *
 * @service
 * @internal
 */
class ContactActivitesSpecProvider extends \Civi\Core\Service\AutoService implements SpecProviderInterface {
    // implements SpecProviderInterface : la classe devient un fournisseur de spécifications API4. 
    // CiviCRM l'interroge chaque fois qu'il construit la liste des champs d'une entité.

  public function modifySpec(RequestSpec $spec): void {
    $field = new FieldSpec('liste_activites', 'Contact', 'String');
    $field->setLabel(E::ts('Activités au laboratoire'))
      ->setTitle(E::ts('Activités au laboratoire'))
      ->setDescription(E::ts('Événements « Activité au laboratoire » du contact (date – titre)'))
      ->setType('Extra')
      ->setReadonly(TRUE)
      ->setColumnName('id')
      ->setSqlRenderer([__CLASS__, 'renderListeActivites']);
    $spec->addFieldSpec($field);
  }

  public function applies($entity, $action): bool {
    return $entity === 'Contact' && $action === 'get';
  }

  public static function renderListeActivites(array $field): string {
    return "(
      SELECT GROUP_CONCAT(DISTINCT
               CONCAT_WS(' – ', DATE_FORMAT(e.start_date, '%d/%m/%Y'), e.title)
               ORDER BY e.start_date DESC
               SEPARATOR ' | ')
      FROM civicrm_participant p
      INNER JOIN civicrm_event e ON e.id = p.event_id
      INNER JOIN civicrm_option_group og ON og.name = 'event_type'
      INNER JOIN civicrm_option_value ov
             ON ov.option_group_id = og.id
            AND ov.value = e.event_type_id
      WHERE p.contact_id = {$field['sql_name']}
        AND p.is_test = 0
        AND ov.name = 'Activité au laboratoire'
    )";
  }

}