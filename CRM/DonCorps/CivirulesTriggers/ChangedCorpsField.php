<?php

//use CRM_Civirules_Trigger_Post;
//use CRM_Civirules_TriggerData_TriggerData;

/**
 * Déclencheur CiviRules : quand le champ personnalisé "N° de pièce ou de corps" change.
 */
class CRM_DonCorps_CivirulesTriggers_ChangedCorpsField extends CRM_Civirules_Trigger_Post {

  /**
   * Nom machine du champ personnalisé.
   */
  protected $customFieldName = 'custom_351';

  /**
   * Entité concernée : Contact.
   */
  public function getEntity() {
    return 'Contact';
  }

  /**
   * Nom interne du déclencheur (doit correspondre à triggers.json).
   */
  public function getTriggerCondition() {
    return 'changed_corps';
  }

  /**
   * Déclenche uniquement si la valeur du champ a changé.
   */
  public function isValidTrigger($entityId, $entityData, $context) {
    if (!isset($context['oldData']) || !isset($context['newData'])) {
      return false;
    }

    $oldValue = $context['oldData'][$this->customFieldName] ?? null;
    $newValue = $context['newData'][$this->customFieldName] ?? null;

    return $oldValue !== $newValue;
  }

  /**
   * Données passées à la règle.
   */
  public function getValues($entityId) {
    return ['id' => $entityId];
  }
}