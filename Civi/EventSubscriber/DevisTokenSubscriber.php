<?php

declare(strict_types=1);

namespace Civi\EventSubscriber;

use Civi\Core\Event\GenericHookEvent;
use Civi\Token\AbstractTokenSubscriber;
use Civi\Token\Event\TokenValueEvent;
use Civi\Token\TokenProcessor;
use Civi\Token\TokenRow;

/**
 * Tokens « devis » : champs d'un événement (y compris le groupe Participants),
 * utilisables dans les modèles CiviOffice avec la syntaxe {devis.nom_du_token}.
 *
 * Fonctionne avec DevisSearchKitTaskSubscriber, qui affiche l'action
 * « Imprimer avec CiviOffice » sur les recherches SearchKit d'événements.
 */
final class DevisTokenSubscriber extends AbstractTokenSubscriber {

  /**
   * Nom du token => champ API4 de l'événement.
   */
  private const FIELDS = [
    'titre'               => 'title',
    'date_debut'          => 'start_date',
    'date_fin'            => 'end_date',
    'apprenants'          => 'Participants.Nombre_d_apprenants',
    'formateurs'          => 'Participants.Nombre_de_formateurs',
    'staff'               => 'Participants.Nombre_de_Staff',
    'seances'             => 'Participants.Nombre_de_s_ances',
    'total_participants'  => 'Participants.Total_participants',
    'tarif_apprenant'     => 'Participants.Tarif_par_apprenant',
    'tarif_formateur'     => 'Participants.Tarif_par_formateur',
    'tarif_staff'         => 'Participants.Tarif_par_staff',
    'montant_apprenants'  => 'Participants.Montant_Total_Apprenants',
    'montant_formateurs'  => 'Participants.Montant_total_formateurs',
    'montant_staff'       => 'Participants.Montant_total_Staff',
    'montant_total_devis' => 'Participants.Montant_total_devis',
    'mention_devis'       => 'Participants.Mention_faire_apparaitre_sur_le_devis',
    'num_saisine'         => 'Caract_ristiques_de_l_activit_.id_de_saisine.label',
    'deposant_name'       => 'Caract_ristiques_de_l_activit_.id_de_saisine.caracteristiques.D_posant.display_name',
    'deposant_rue'        => 'Caract_ristiques_de_l_activit_.id_de_saisine.caracteristiques.D_posant.address_primary.street_address',
    'deposant_compad1'    => 'Caract_ristiques_de_l_activit_.id_de_saisine.caracteristiques.D_posant.address_primary.supplemental_address_1',
    'deposant_compad2'    => 'Caract_ristiques_de_l_activit_.id_de_saisine.caracteristiques.D_posant.address_primary.supplemental_address_2',
    'deposant_codepost'   => 'Caract_ristiques_de_l_activit_.id_de_saisine.caracteristiques.D_posant.address_primary.postal_code',
    'deposant_ville'      => 'Caract_ristiques_de_l_activit_.id_de_saisine.caracteristiques.D_posant.address_primary.city',
    'deposant_mail'       => 'Caract_ristiques_de_l_activit_.id_de_saisine.caracteristiques.D_posant.email_primary.email',
    'deposant_empl'       => 'Caract_ristiques_de_l_activit_.id_de_saisine.caracteristiques.D_posant.employer_id.organization_name',



  ];

  /**
   * Tokens affichés comme des montants (« 1 250,00 € »).
   */
  private const MONEY = [
    'tarif_apprenant',
    'tarif_formateur',
    'tarif_staff',
    'montant_apprenants',
    'montant_formateurs',
    'montant_staff',
    'montant_total_devis',
  ];

  /**
   * Tokens affichés comme des dates.
   */
  private const DATES = [
    'date_debut',
    'date_fin',
    ];

  public static function getSubscribedEvents(): array {
    return [
      'civi.civioffice.tokenContext' => 'onCiviOfficeTokenContext',
    ] + parent::getSubscribedEvents();
  }

  public function __construct() {
    parent::__construct('devis', [
      'titre'               => 'Titre de l\'événement',
      'date_debut'          => 'Date de début',
      'date_fin'            => 'Date de fin',
      'apprenants'          => 'Nombre d\'apprenants',
      'formateurs'          => 'Nombre de formateurs',
      'staff'               => 'Nombre de staff',
      'seances'             => 'Nombre de séances',
      'total_participants'  => 'Total participants',
      'tarif_apprenant'     => 'Tarif par apprenant',
      'tarif_formateur'     => 'Tarif par formateur',
      'tarif_staff'         => 'Tarif par staff',
      'montant_apprenants'  => 'Montant apprenants',
      'montant_formateurs'  => 'Montant formateurs',
      'montant_staff'       => 'Montant staff',
      'montant_total_devis' => 'Montant total du devis',
      'mention_devis'       => 'Mention à faire apparaitre sur le devis',
      'num_saisine'         => 'Num saisine',
      'deposant_name'       => 'Nom du déposant',
      'deposant_rue'        => 'Rue du déposant',
      'deposant_compad1'    => 'Compl Adresse 1 déposant',
      'deposant_compad2'    => 'Compl Adresse 1 déposant',
      'deposant_codepost'   => 'Code postal déposant',
      'deposant_ville'      => 'Ville déposant',
      'deposant_mail'       => 'Courriel déposant',
      'deposant_empl'       => 'Employeur déposant',
    ]);
  }






  /**
   * Transmet l'id de l'événement aux tokens quand le document porte sur un événement.
   */
  public function onCiviOfficeTokenContext(GenericHookEvent $event): void {
    if ($event->entity_type === 'Event') {
      $event->context['eventId'] = $event->entity_id;
    }
  }

  /**
   * Cette classe n'intervient que si un id d'événement est présent dans le contexte.
   */
  public function checkActive(TokenProcessor $processor): bool {
    return in_array('eventId', $processor->context['schema'] ?? [], TRUE)
      || [] !== $processor->getContextValues('eventId');
  }

  /**
   * Une seule requête pour tous les événements et tous les tokens.
   */
  public function prefetch(TokenValueEvent $e): array {
    $ids = array_filter($e->getTokenProcessor()->getContextValues('eventId'));
    if (!$ids) {
      return [];
    }
    return \Civi\Api4\Event::get(FALSE)
      ->setSelect(array_merge(['id'], array_values(self::FIELDS)))
      ->addWhere('id', 'IN', array_values($ids))
      ->execute()
      ->indexBy('id')
      ->getArrayCopy();
  }

  public function evaluateToken(TokenRow $row, $entityName, $field, $prefetch = NULL): void {
    $eventId = $row->context['eventId'] ?? NULL;
    $value = $prefetch[$eventId][self::FIELDS[$field]] ?? '';

    if ($value !== '' && $value !== NULL) {
    if (in_array($field, self::MONEY, TRUE)) {
        $value = \CRM_Utils_Money::format($value);
    }
    elseif (in_array($field, self::DATES, TRUE)) {
        $value = \CRM_Utils_Date::customFormat($value);
    }
    }

    $row->tokens($entityName, $field, (string) $value);
  }

}