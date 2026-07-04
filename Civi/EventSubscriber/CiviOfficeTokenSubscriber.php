<?php


/* ATTENTION 
- il existe un bug dans CiviOffice qui refuse d'utiliser les _ dans les noms d'entité (ici Custom_utilistion_du_corps)
- une issue a été ouverte le 27/6/26. 
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

declare(strict_types=1);

namespace Civi\EventSubscriber;

use Civi\Core\Event\GenericHookEvent;
use Civi\Token\AbstractTokenSubscriber;
use Civi\Token\TokenProcessor;
use Civi\Token\TokenRow;

final class CiviOfficeTokenSubscriber extends AbstractTokenSubscriber {

  /**
   * Champs utilisant des listes d'options.
   */
  private array $optionFields = [
    'Type_de_poi_ce_3',
    'cote2',
    'Utilisation2',
    'Protocole_de_recherche_ex_vivo2',
    'M_dium_inject_',
    'Bain_de_conservation',
    'Imagerie2',
    'Klingler2',
  ];

  public static function getSubscribedEvents(): array {
    return [
      'civi.civioffice.tokenContext' => 'onCiviOfficeTokenContext',
    ] + parent::getSubscribedEvents();
  }

  public function __construct() {

    parent::__construct('utilisationducorps', [

      'N_de_pi_ce_ou_de_corps' => 'N° de pièce',

      'N_de_pi_ce_ou_de_corps_barcode' => 'N° de pièce (code-barres)',

      'Type_de_poi_ce_3' => 'Type de pièce',

      'cote2' => 'Côté',

      'Utilisation2' => 'Utilisation',

      'Protocole_de_recherche_ex_vivo2' => 'Protocole ex vivo',

      'Compl_ment' => 'Complément',

      'M_dium_inject_' => 'Médium injecté',

      'Imagerie2' => 'Imagerie',

      'Klingler2' => 'Klingler',

      'Bain_de_conservation' => 'Bain de conservation',

    ]);
  }

  public function onCiviOfficeTokenContext(GenericHookEvent $event): void {

    if ($event->entity_type === 'Custom_Utilisation_du_corps') {
      $event->context['utilisationId'] = $event->entity_id;
    }

  }

  public function checkActive(TokenProcessor $processor): bool {

    return
      in_array('utilisationId', $processor->context['schema'] ?? [], TRUE)
      || [] !== $processor->getContextValues('utilisationId');

  }

  public function evaluateToken(
  TokenRow $row,
  $entityName,
  $field,
  $prefetch = NULL
): void {

  $entityId = $row->context['utilisationId'];

  // Le token "barcode" dépend du champ N_de_pi_ce_ou_de_corps
  $select = ($field === 'N_de_pi_ce_ou_de_corps_barcode')
    ? 'N_de_pi_ce_ou_de_corps'
    : $field;

  if (in_array($select, $this->optionFields, TRUE)) {
    $select .= ':label';
  }

  $result = civicrm_api4(
    'Custom_Utilisation_du_corps',
    'get',
    [
      'select' => [$select],
      'where' => [
        ['id', '=', $entityId],
      ],
      'limit' => 1,
    ],
    FALSE
  );

  $value = $result[0][$select] ?? '';

  if (is_array($value)) {
    $value = implode(', ', $value);
  }

  // Calcul du token dérivé
  if ($field === 'N_de_pi_ce_ou_de_corps_barcode') {
    $value = '*' . $value . '*';
  }

  $row->tokens(
    $entityName,
    $field,
    (string) $value
  );
}

}