<?php

declare(strict_types = 1);

namespace Civi\EventSubscriber;

use Civi\Civioffice\EventSubscriber\AbstractCiviOfficeSearchKitTaskSubscriber;

final class DevisSearchKitTaskSubscriber
  extends AbstractCiviOfficeSearchKitTaskSubscriber {

  protected function getEntityTypes(): iterable {
    return ['Event'];
  }

}