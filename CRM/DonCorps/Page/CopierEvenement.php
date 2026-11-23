<?php
use CRM_DonCorps_ExtensionUtil as E;

/**
 * Duplique un événement puis ouvre la copie dans le formulaire Form Builder.
 */
class CRM_DonCorps_Page_CopierEvenement extends CRM_Core_Page {

  public function run() {
    $id = CRM_Utils_Request::retrieve('id', 'Positive', $this, TRUE);

    // Copie native : champs personnalisés, tarifs, etc. (sans participants)
    $copie = CRM_Event_BAO_Event::copy($id);

    CRM_Core_Session::setStatus(
      E::ts('Événement dupliqué : %1', [1 => $copie->title]),
      E::ts('Copie créée'),
      'success'
    );

    // Ouvre la copie dans le formulaire Form Builder
    $url = CRM_Utils_System::url(
      'civicrm/NouvelleActivite',   // ← route de ton formulaire Form Builder
      NULL, FALSE,
      '?Event1=' . $copie->id          // ← nom de l'entité Event dans le formulaire
    );
    CRM_Utils_System::redirect($url);
  }

}