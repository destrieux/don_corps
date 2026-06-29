<?php

declare(strict_types = 1);

namespace Civi\EventSubscriber;

use Civi\Civioffice\EventSubscriber\AbstractCiviOfficeSearchKitTaskSubscriber;

final class CiviOfficeSearchKitTaskSubscriber
  extends AbstractCiviOfficeSearchKitTaskSubscriber {

  /**
   * Entités SearchKit pour lesquelles l'action
   * "Imprimer avec CiviOffice" doit apparaître.
   */
  protected function getEntityTypes(): iterable {
    return [
      'Custom_Utilisation_du_corps',
    ];
  }

}