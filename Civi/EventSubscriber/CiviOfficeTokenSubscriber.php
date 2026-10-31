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

declare(strict_types=1);  // typage strict : "14" est considere comme du texte et non en entier

namespace Civi\EventSubscriber; // dossier virtuel de la classe
                                // le fichier doit se trouver dans Civi/EventSubscriber/

use Civi\Core\Event\GenericHookEvent;     // définbition des alias : TokenRow pointe Civi\Token\TokenRow
use Civi\Token\AbstractTokenSubscriber;
use Civi\Token\TokenProcessor;
use Civi\Token\TokenRow;

## La classe   
## final : personne ne peut hériter de cette classe.
## extends AbstractTokenSubscriber : elle reprend la classe de base de CiviCRM pour les fournisseurs de tokens, 
## qui gère l’enregistrement des tokens et appelle vos méthodes au bon moment.
final class CiviOfficeTokenSubscriber extends AbstractTokenSubscriber {

  ## Liste des champs à options
    private array $optionFields = [
      //Propriété (variable) privée qui liste les champs dont la valeur stockée est un code, 
      //et dont on veut le libellé affiché. 
      // Elle sert dans evaluateToken pour ajouter :label.

      'Type_de_poi_ce_3',
      'cote2',
      'Utilisation2',
      'Protocole_de_recherche_ex_vivo2',
      'M_dium_inject_',
      'Bain_de_conservation',
      'Imagerie2',
      'Klingler2',
    ];

  ## Evenements écoutés
    //Cette méthode dit à CiviCRM : « quand l’événement civi.civioffice.tokenContext 
    // se produit, appelle ma méthode onCiviOfficeTokenContext ». 
    // Le + parent::getSubscribedEvents() ajoute les abonnements 
    // de la classe de base (ceux qui déclarent et évaluent les tokens). 
    // Sans cette addition, les tokens n’apparaîtraient pas.

    public static function getSubscribedEvents(): array {

      return [
        'civi.civioffice.tokenContext' => 'onCiviOfficeTokenContext',
      ] + parent::getSubscribedEvents();
    }

  ## Le constructeur
    //parent::__construct(...) appelle le constructeur de la classe de base avec deux informations.
    // - 'utilisationducorps' est le préfixe des tokens : ils s’écriront {utilisationducorps.Type_de_poi_ce_3}.
    // - Le tableau associe chaque nom technique ('N_de_pi_ce_ou_de_corps') à son libellé affiché dans la liste des tokens ('N° de pièce').
    // - N_de_pi_ce_ou_de_corps_barcode est un second token, dérivé du premier, pour les codes-barres.

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
  ## Contexte : Appelée par CiviOffice quand il prépare un document. 
    ## $event contient le type et l’id de l’enregistrement traité.

    public function onCiviOfficeTokenContext(GenericHookEvent $event): void {
      if ($event->entity_type === 'Custom_Utilisation_du_corps') {
        $event->context['utilisationId'] = $event->entity_id;
      }
      //Si le document porte sur cette entité, on range son id dans le contexte 
      // sous la clé utilisationId. C’est une valeur que les tokens retrouveront ensuite.

    }
  ## ACTIVATION  
    // CiviCRM demande : « cette classe doit-elle intervenir pour ce traitement ? ». 
    // La réponse est oui si :
    //  - utilisationId figure dans le schéma déclaré du contexte (?? [] donne une liste vide s’il n’y en a pas), 
    //  - ou une valeur existe déjà pour utilisationId.
    //  TRUE en dernier argument de in_array impose une comparaison stricte. Pour un document sur un événement, la réponse est non, donc cette classe reste inactive.

    public function checkActive(TokenProcessor $processor): bool {
      return
        in_array('utilisationId', $processor->context['schema'] ?? [], TRUE)
        || [] !== $processor->getContextValues('utilisationId');

    }

  ## CALCUL DUN TOKEN
    // Appelée pour chaque token de chaque ligne. 
    // $row est la ligne en cours, 
    // $entityName le préfixe (utilisationducorps), 
    // $field le nom du token. 
    // $prefetch n’est pas utilisé ici.

      public function evaluateToken(
      TokenRow $row,
      $entityName,
      $field,
      $prefetch = NULL
    ): void {

  $entityId = $row->context['utilisationId']; // Récupère l’id placé dans le contexte à l’étape précédente.

  // Le token "barcode" dépend du champ N_de_pi_ce_ou_de_corps
  // Civioffice gère mal les polices : si deux instances du meme token apparaissent
  // sule la police de la premiere est considéree
  // ici on crée un second token pour les codes barres
  
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