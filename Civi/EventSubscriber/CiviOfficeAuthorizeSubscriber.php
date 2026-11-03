<?php

declare(strict_types=1);

namespace Civi\EventSubscriber;

use Civi\API\Event\AuthorizeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Contournement d'un défaut de CiviOffice : le droit est déclaré pour l'action
 * « renderWeb », alors que l'action s'appelle « render ». À retirer quand CiviOffice
 * sera corrigé.
 */
final class CiviOfficeAuthorizeSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return ['civi.api.authorize' => ['onAuthorize', 100]];
  }

  public function onAuthorize(AuthorizeEvent $event): void {
    $request = $event->getApiRequest();
    if (is_object($request)
      && method_exists($request, 'getEntityName')
      && $request->getEntityName() === 'Civioffice'
      && $request->getActionName() === 'render'
      && \CRM_Core_Permission::check('access CiviOffice')
    ) {
      $event->authorize();
    }
  }

}