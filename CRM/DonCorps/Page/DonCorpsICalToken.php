<?php
/**
 * Page de gestion de l'abonnement iCalendar personnel.
 *
 * Permet à l'utilisateur connecté de :
 * - générer une URL iCalendar personnelle ;
 * - consulter l'existence d'un abonnement actif ;
 * - révoquer son abonnement.
 *
 * Chaque URL est associée au contact CiviCRM connecté et protégée
 * par un token aléatoire dont seul le hash SHA-256 est conservé en base.
 */

class CRM_DonCorps_Page_DonCorpsICalToken extends CRM_Core_Page {

    public function run() {

        $session = CRM_Core_Session::singleton();

        $contactId = $session->getLoggedInContactID();

        if (!$contactId) {
            CRM_Core_Error::statusBounce(
                ts('Vous devez être connecté pour gérer votre abonnement iCalendar.')
            );
        }

        $icalUrl = NULL;

        /*
         * Révocation du token actuel
         */
        if (!empty($_POST['revoke_ical_token'])) {

            CRM_Core_DAO::executeQuery(
                'UPDATE civicrm_don_corps_ical_token
                 SET is_active = 0
                 WHERE contact_id = %1
                   AND is_active = 1',
                [
                    1 => [$contactId, 'Integer'],
                ]
            );

            CRM_Core_Session::setStatus(
                'Votre abonnement iCalendar a été révoqué.',
                'Abonnement iCalendar',
                'success'
            );
        }

        /*
         * Génération d'un nouveau token
         */
        if (!empty($_POST['generate_ical_token'])) {

            $token = bin2hex(random_bytes(32));

            $tokenHash = hash('sha256', $token);

            /*
             * Désactive les anciens tokens
             */
            CRM_Core_DAO::executeQuery(
                'UPDATE civicrm_don_corps_ical_token
                 SET is_active = 0
                 WHERE contact_id = %1
                   AND is_active = 1',
                [
                    1 => [$contactId, 'Integer'],
                ]
            );

            /*
             * Crée le nouveau token
             */
            CRM_Core_DAO::executeQuery(
                'INSERT INTO civicrm_don_corps_ical_token
                    (contact_id, token_hash, created_date, is_active)
                 VALUES
                    (%1, %2, NOW(), 1)',
                [
                    1 => [$contactId, 'Integer'],
                    2 => [$tokenHash, 'String'],
                ]
            );

            $baseUrl = home_url('/');

            $icalUrl = add_query_arg(
                [
                    'doncorps_ical' => 1,
                    'token' => $token,
                ],
                $baseUrl
            );
        }

        /*
         * Vérifie s'il existe encore un abonnement actif
         */
        $dao = CRM_Core_DAO::executeQuery(
            'SELECT id, created_date, last_access_date
             FROM civicrm_don_corps_ical_token
             WHERE contact_id = %1
               AND is_active = 1
             ORDER BY id DESC
             LIMIT 1',
            [
                1 => [$contactId, 'Integer'],
            ]
        );

        $hasActiveToken = $dao->fetch();

        $this->assign('icalUrl', $icalUrl);
        $this->assign('hasActiveToken', (bool) $hasActiveToken);

        parent::run();
    }
}