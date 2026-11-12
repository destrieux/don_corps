<?php
namespace Civi\DonCorps\ICal;

use CRM_DonCorps_ExtensionUtil as E;

/**
 * Flux iCalendar personnalisé pour les événements Don Corps.
 *
 * Un abonnement personnel est créé depuis la page CiviCRM
 * « Mon abonnement iCalendar ». Une URL contenant un token aléatoire
 * de 256 bits est générée pour chaque contact.
 *
 * Le token n'est jamais stocké en clair : seul son hash SHA-256 est
 * enregistré dans la table civicrm_don_corps_ical_token.
 *
 * Le flux est exposé par WordPress via le paramètre `doncorps_ical`.
 * Avant de générer le calendrier, le token est vérifié et doit être actif.
 * L'accès au flux se fait ensuite sans contrôle de permissions CiviCRM,
 * pour permettre l'abonnement depuis Apple Calendar ou toute autre
 * application iCalendar.
 *
 * Un token peut être révoqué depuis la page d'abonnement. La génération
 * d'un nouveau token désactive l'ancien.
 *
 * Voir aussi :
 * - xml/Menu/don_corps.xml : déclare les routes des deux pages ci-dessous ;
 * - CRM_DonCorps_Page_DonCorpsICal (civicrm/event/doncorpsical) : génère le flux iCalendar ;
 * - CRM_DonCorps_Page_DonCorpsICalToken (civicrm/doncorps/ical-token) : gestion de l'abonnement ;
 * - templates/CRM/DonCorps/Page/DonCorpsIcalToken.tpl : template de la page d'abonnement ;
 * - sql/install.sql : création de la table des tokens (via Calendar::installTable()).
 */
class Calendar {

  /**
   * Branche le flux sur WordPress.
   *
   * À l'étape template_redirect (juste avant que WordPress charge le template
   * de la page), WordPress appelle Calendar::feed().
   */
  public static function register(): void {
    if (function_exists('add_action')) {
      add_action('template_redirect', [self::class, 'feed']);
    }
  }

  /**
   * Crée la table de stockage des tokens (civicrm_don_corps_ical_token).
   * À appeler depuis l'upgrader, à l'installation.
   *
   * @throws \CRM_Core_Exception si le fichier SQL est introuvable
   */
  public static function installTable(): void {
    \Civi::log()->info('don_corps : création de la table de stockage des tokens iCalendar');

    $sqlFile = E::path('sql/install.sql');
    if (!is_file($sqlFile)) {
      throw new \CRM_Core_Exception("don_corps : fichier SQL introuvable : $sqlFile");
    }

    \CRM_Utils_File::sourceSQLFile(NULL, $sqlFile);
  }

  /**
   * Expose le flux iCalendar via WordPress.
   *
   * Cette interception par template_redirect est nécessaire car, sous
   * WordPress, une URL CiviCRM classique peut être interceptée par le CMS
   * et renvoyer une page HTML au lieu du flux text/calendar.
   *
   * Le token est validé avant tout accès aux données CiviCRM.
   */
  public static function feed(): void {
    if (!isset($_GET['doncorps_ical'])) {
      return;
    }

    $token = (string) ($_GET['token'] ?? '');
    if ($token === '') {
      self::deny('Accès refusé');
    }

    $tokenId = self::findActiveToken($token);
    if (!$tokenId) {
      self::deny('Token invalide');
    }

    \CRM_Core_DAO::executeQuery(
      'UPDATE civicrm_don_corps_ical_token SET last_access_date = NOW() WHERE id = %1',
      [1 => [$tokenId, 'Integer']]
    );

    (new \CRM_DonCorps_Page_DonCorpsICal())->run();
    exit;
  }

  /**
   * Renvoie l'id du token actif correspondant, ou NULL.
   */
  private static function findActiveToken(string $token): ?int {
    $id = \CRM_Core_DAO::singleValueQuery(
      'SELECT id FROM civicrm_don_corps_ical_token
       WHERE token_hash = %1 AND is_active = 1
       LIMIT 1',
      [1 => [hash('sha256', $token), 'String']]
    );
    return $id ? (int) $id : NULL;
  }

  /**
   * Répond 403 avec un message texte et arrête l'exécution.
   */
  private static function deny(string $message): void {
    status_header(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
  }

}