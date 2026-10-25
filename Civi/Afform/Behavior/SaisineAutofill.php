<?php

namespace Civi\DonCorps\Afform\Behavior;

use Civi\Afform\AbstractBehavior;
use Civi\Afform\Event\AfformEntitySortEvent;
use Civi\Afform\Event\AfformPrefillEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SaisineAutofill extends AbstractBehavior implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      'civi.afform.sort.prefill' => 'onAfformSortPrefill',
      'civi.afform.prefill' => ['onAfformPrefill', 99],
    ];
  }

  public static function getEntities(): array {
    return ['Event', 'Saisine'];
  }

  public static function getTitle(): string {
    return 'Saisine Autofill';
  }

  public static function getDescription(): string {
    return 'Relie automatiquement Saisine1 à la Saisine sélectionnée dans Event1.';
  }

  public static function onAfformSortPrefill(AfformEntitySortEvent $event): void {
    $entities = $event->getFormDataModel()->getEntities();

    if (
      isset($entities['Event1']) &&
      isset($entities['Saisine1']) &&
      $entities['Event1']['type'] === 'Event' &&
      $entities['Saisine1']['type'] === 'Saisine'
    ) {
      $event->addDependency('Saisine1', 'Event1');
    }
  }

  public static function onAfformPrefill(AfformPrefillEvent $event): void {
    if ($event->getEntityName() !== 'Saisine1') {
      return;
    }

    $apiRequest = $event->getApiRequest();

    $eventIds = $event->getEntityIds('Event1');

    if (empty($eventIds)) {
      return;
    }

    $eventId = reset($eventIds);

    $result = \Civi\Api4\Event::get(FALSE)
      ->addWhere('id', '=', $eventId)
      ->addSelect('Caract_ristiques_de_l_activit_.Saisine')
      ->execute()
      ->first();

    $saisineId = $result['Caract_ristiques_de_l_activit_.Saisine'] ?? NULL;

    if (!$saisineId) {
      return;
    }

    $saisineEntity = $event->getEntity();

    $apiRequest->loadEntity(
      $saisineEntity,
      [['id' => $saisineId]],
      'update'
    );
  }

}