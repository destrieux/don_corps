<?php


/* ATTENTION 
- il existe un bug dans CiviOffice qui refuse d'utiliser les _ dans les noms d'entité (ici Custom_utilistion_du_corps)
- une issue a été ouverte le
- Pour etre moins restirictif, il faut modifier le fichier 
    ext/de.systopia.civioffice/Civi/Civioffice/Api4/Action/Civioffice/RenderWebAction.php

      public function setEntityType(string $entityType): self {
          ##Assertion::alnum($entityType, 'entityType must only contain alpha-numeric characters.');
          $this->entityType = $entityType;
          return $this;
        }

      En 

        public function setEntityType(string $entityType): self {
      Assertion::regex(
      $entityType,
      '/^[A-Za-z0-9_]+$/',
      'entityType contains invalid characters.'
    );
    $this->entityType = $entityType;

    return $this;
  }
 */


declare(strict_types = 1);

namespace Civi\EventSubscriber;

use Civi\Core\Event\GenericHookEvent;
use Civi\Token\AbstractTokenSubscriber;
use Civi\Token\TokenProcessor;
use Civi\Token\TokenRow;

final class CiviOfficeTokenSubscriber extends AbstractTokenSubscriber {

  /**
   * Champs possédant une liste d'options.
   *
   * @var array<string,bool>
   */
  private array $optionFields = [];

  /**
   * Cache des enregistrements déjà lus.
   *
   * @var array<int,array>
   */
  private array $recordCache = [];

  public static function getSubscribedEvents(): array {
    return [
      'civi.civioffice.tokenContext' => 'onCiviOfficeTokenContext',
    ] + parent::getSubscribedEvents();
  }

  public function __construct() {
    $tokens = [];

    $fields = civicrm_api4(
      'Custom_Utilisation_du_corps',
      'getFields',
      [],
      FALSE
    );

    foreach ($fields as $field) {
      $tokens[$field['name']] = $field['title'];
      if (!empty($field['options'])) {
        $this->optionFields[$field['name']] = TRUE;
      }
    }

    parent::__construct(
      'utilisationducorps',
      $tokens
    );
  }

  public function onCiviOfficeTokenContext(GenericHookEvent $event): void {

    if ($event->entity_type === 'Custom_Utilisation_du_corps') {
      $event->context['utilisationId'] = $event->entity_id;
    }
  }

  public function checkActive(TokenProcessor $processor): bool {

    return in_array(
      'utilisationId',
      $processor->context['schema'] ?? [],
      TRUE
    ) || [] !== $processor->getContextValues('utilisationId');
  }

  public function evaluateToken(
    TokenRow $row,
    $entityName,
    $field,
    $prefetch = NULL
    ): void {

      $entityId = $row->context['utilisationId'];

      // Chargement unique de l'enregistrement.
      if (!isset($this->recordCache[$entityId])) {

        $select = ['*'];

        // Ajouter automatiquement les labels des champs à options.
        foreach (array_keys($this->optionFields) as $optionField) {
          $select[] = $optionField . ':label';
        }

        $result = civicrm_api4(
          'Custom_Utilisation_du_corps',
          'get',
          [
            'select' => $select,
            'where' => [
              ['id', '=', $entityId],
            ],
            'limit' => 1,
          ],
          FALSE
        );

        $this->recordCache[$entityId] = $result[0] ?? [];
      }

      $record = $this->recordCache[$entityId];

      // Pour les champs à options, utiliser automatiquement le libellé.
      if (isset($this->optionFields[$field])) {
        $value = $record[$field . ':label'] ?? '';
      }
      else {
        $value = $record[$field] ?? '';
      }

      // Conversion des tableaux en chaîne.
      if (is_array($value)) {
        $value = implode(', ', array_map('strval', $value));
      }

      $row->tokens(
        $entityName,
        $field,
        (string) $value
      ); 

/* $row->tokens(
    $entityName,
    $field,
    'ABC123'
); */

  }

}