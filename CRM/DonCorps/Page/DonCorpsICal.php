<?php
/**
 * Génère le flux iCalendar des événements Don Corps.
 *
 * Le flux est construit à partir des événements CiviCRM actifs et
 * inclut les informations utiles issues des champs personnalisés.
 *
 * Le calendrier respecte le format iCalendar (RFC 5545), notamment :
 * - gestion du fuseau Europe/Paris ;
 * - échappement des caractères spéciaux ;
 * - repli des lignes trop longues ;
 * - identifiant unique de chaque événement ;
 * - URL permettant d'accéder à l'événement CiviCRM.
 *
 * Cette classe est appelée directement par le point d'entrée WordPress
 * du flux iCalendar et doit donc pouvoir fonctionner sans session
 * CiviCRM ni contrôle de permissions utilisateur.
 */

class CRM_DonCorps_Page_DonCorpsICal extends CRM_Core_Page {

  private function getCustomFieldDisplayValue($fieldName, $value) {
    /**
     * Convertit une valeur de CustomField en libellé utilisateur.
     *
     * Pour les champs de type liste, recherche le CustomField puis son
     * OptionGroup afin de transformer les valeurs stockées (ex. 1, 2, 3)
     * en libellés (ex. Frais, Congelé, Embaumé).
     *
     * Gère également les champs à valeurs multiples.
     */
    #Retourne le libellé d'une valeur de CustomField de type liste.

      if ($value === null || $value === '') {
          return '';
      }

      // Séparer éventuellement le groupe et le nom du champ
      $parts = explode('.', $fieldName, 2);

      if (count($parts) === 2) {
          $groupName = $parts[0];
          $fieldNameOnly = $parts[1];
      }
      else {
          $groupName = null;
          $fieldNameOnly = $fieldName;
      }

      // Rechercher le CustomField
      $query = \Civi\Api4\CustomField::get()
          ->addSelect('id')
          ->addSelect('option_group_id')
          ->addWhere('name', '=', $fieldNameOnly)
          ->setCheckPermissions(FALSE);

      // Si le groupe est précisé, on l'utilise pour éviter une ambiguïté
      if ($groupName !== null) {
          $query->addWhere('custom_group_id:name', '=', $groupName);
      }

      $fields = $query->execute();

      if ($fields->count() === 0) {
          return $value;
      }

      $field = $fields->first();

      // Pas de groupe d'options :
      // ce n'est probablement pas un champ de type liste.
      if (empty($field['option_group_id'])) {
          return $value;
      }

      // Récupérer les valeurs possibles
      $options = \Civi\Api4\OptionValue::get()
          ->addSelect('value')
          ->addSelect('label')
          ->addWhere(
              'option_group_id',
              '=',
              $field['option_group_id']
          )
          ->addOrderBy('weight', 'ASC')
          ->setCheckPermissions(FALSE)
          ->execute();

      // Construire la correspondance :
      //
      // 1 => Indiférente
      // 2 => Frais
      // 3 => Congelé
      // 4 => Embaumé
      //
      $labels = [];

      foreach ($options as $option) {
          $labels[(string) $option['value']] = $option['label'];
      }

      // Le champ peut être multiple et être retourné
      // sous forme de tableau ou de chaîne séparée par des virgules.
      if (is_array($value)) {
          $values = $value;
      }
      else {
          $values = preg_split(
              '/\s*,\s*/',
              (string) $value,
              -1,
              PREG_SPLIT_NO_EMPTY
          );
      }

      $result = [];

      foreach ($values as $singleValue) {

          $singleValue = trim((string) $singleValue);

          if (isset($labels[$singleValue])) {
              $result[] = $labels[$singleValue];
          }
          else {
              // Si aucune correspondance n'existe,
              // conserver la valeur originale.
              $result[] = $singleValue;
          }
      }

      return implode(', ', $result);
   }

  private function foldIcalLine($line) {
    /**
     * Effectue le "line folding" imposé par le format iCalendar.
     *
     * Les lignes iCalendar de plus de 75 octets sont coupées et poursuivies
     * sur la ligne suivante avec un espace. La coupure est ajustée afin de
     * ne pas interrompre un caractère UTF-8.
     */
    $result = '';

    while (strlen($line) > 75) {

        // Couper sans couper un caractère UTF-8
        $cut = 75;

        while ($cut > 0 && (ord($line[$cut]) & 0xC0) === 0x80) {
            $cut--;
        }

        $result .= substr($line, 0, $cut) . "\r\n ";
        $line = substr($line, $cut);
    }

    return $result . $line;
    }

     public function run() {   // méthode publique appelée depuis l'extétieur de la classe
        /**
         * Génère et retourne le flux iCalendar.
         *
         * Interroge les événements CiviCRM actifs, construit un VEVENT pour
         * chaque événement et ajoute les informations spécifiques au Don Corps.
         *
         * Le flux est retourné directement au client avec le type MIME
         * `text/calendar`, afin d'être utilisable notamment comme abonnement
         * dans Apple Calendar.
         */

    $events = \Civi\Api4\Event::get()
        ->addSelect('id')
        ->addSelect('title')
        ->addSelect('start_date')
        ->addSelect('end_date')
        ->addSelect('location')
        ->addSelect('description')
        ->addSelect('Caract_ristiques_de_l_activit_.id_de_saisine.caracteristiques.Demandeur.display_name')
        ->addSelect('Caract_ristiques_de_l_activit_.id_de_saisine.label')
        ->addSelect('Caract_ristiques_de_l_activit_.En_attente_de_confirmation')
        ->addSelect('Donn_es_techniques.Nombre_de_corps_sans_segmentation')
        ->addSelect('Donn_es_techniques.Conservation')
        ->addSelect('Donn_es_techniques.Installation_des_corps')
        ->addSelect('Donn_es_techniques.Remarques')
        ->addSelect('Participants.Nombre_d_apprenants')
        ->addSelect('Participants.Nombre_de_formateurs')
        ->addSelect('Participants.Nombre_de_Staff')
        ->setCheckPermissions(FALSE)
        ->addWhere('is_active', '=', TRUE)
        ->addOrderBy('start_date', 'ASC')
        ->execute();


    ## Construction du tableau $output contenant un ligne iCalendar par élément
    $output = [];
    $output[] = 'BEGIN:VCALENDAR';        // Début du calendrier ; [] : ajoute un nouvel élément à la fin du tableau
    $output[] = 'CALSCALE:GREGORIAN';     // calendirer grégorien
    $output[] = 'PRODID:-//Don Corps//CiviCRM//FR';  // identifie le logiciel ou le système qui a produit le calendrier.
    $output[] = 'VERSION:2.0';            // Format utilisé par iCalendar (std pour apple, outlook...)
    $output[] = 'METHOD:PUBLISH';         // publication du calendrier
    $output[] = 'BEGIN:VTIMEZONE';        // déclaration du fuseau horaire
    $output[] = 'TZID:Europe/Paris';      // fuseau de Paris
    $output[] = 'BEGIN:DAYLIGHT';         // début de la période d'été
    $output[] = 'DTSTART:19810329T020000';    // date historique de la définition de la regle
    $output[] = 'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU'; // chaque année, en mars (3), le dernier sunday (-1SU) du mois
    $output[] = 'TZNAME:UTC+2';           // UTC+2 : nom de l'heure
    $output[] = 'TZOFFSETFROM:+0100';     // avant le changement d'heure : paris est en UTC+1
    $output[] = 'TZOFFSETTO:+0200';       // apres le changement d'heure : paris est en UTC+2
    $output[] = 'END:DAYLIGHT';           // fin définition heure d'été
    $output[] = 'BEGIN:STANDARD';         // définition heuyre d'hiver
    $output[] = 'DTSTART:19961027T030000';
    $output[] = 'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU';
    $output[] = 'TZNAME:UTC+1';
    $output[] = 'TZOFFSETFROM:+0200';
    $output[] = 'TZOFFSETTO:+0100';
    $output[] = 'END:STANDARD';
    $output[] = 'END:VTIMEZONE';

    foreach ($events as $event) {

      $output[] = 'BEGIN:VEVENT';// début de l'évenement
      $output[] = 'CLASS:PUBLIC';//
      $start = new DateTime(
          $event['start_date'],
          new DateTimeZone('Europe/Paris')
      );

      $end = new DateTime(
          $event['end_date'],
          new DateTimeZone('Europe/Paris')
      );

      $output[] = 'DTSTART;TZID=Europe/Paris:' .
          $start->format('Ymd\THis');

      $output[] = 'DTEND;TZID=Europe/Paris:' .
          $end->format('Ymd\THis');
      $output[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');          // date de génération du fichier en gmt
      
      $confirmation = $event['Caract_ristiques_de_l_activit_.En_attente_de_confirmation'] ?? '';
      if ($confirmation == '0'){
        $output[] = 'STATUS:TENTATIVE';
        $output[] = 'TRANSP:TRANSPARENT';
        $confirmation ="Non";
      }else{
        $output[] = 'STATUS:CONFIRMED';
        $output[] = 'TRANSP:OPAQUE';
        $confirmation ="Oui";
      }

      $output[] = 'SUMMARY:' . $this->escapeIcal($event['title']); // ajoute le titre de l'évenement
                                              // escapeIcal échappe les caractères spéciaux iCalendar (, ;  \...)
                                              // réécrit en precedant les caractesrs speciaux par
      
      $eventUrl = home_url('/?page=CiviCRM&q=civicrm/event/info&id=' . $event['id']);
      $output[] = 'URL:'. $this->escapeIcal($eventUrl);
          

      $output[] = 'UID:doncorps-' . $event['id'] . '@civicrm';// identifiant unique de l'évenement construit à parti de son id dans civicrm



      # Récupère les champs perso à intégrer au calendrier
        // Prépare les informations spécifiques Don Corps qui seront
        // regroupées dans la propriété DESCRIPTION du VEVENT.
      $description = $event['description'] ?? '';
      $reference = $event['Caract_ristiques_de_l_activit_.id_de_saisine.label'] ?? '';
      $reference = str_replace('-', '‑', $reference);
      $porteur = $event['Caract_ristiques_de_l_activit_.id_de_saisine.caracteristiques.Demandeur.display_name'] ?? '';
      $nombreCorps = $event['Donn_es_techniques.Nombre_de_corps_sans_segmentation'] ?? '';
      $conservation = $event['Donn_es_techniques.Conservation'] ?? '';
      $installation = $event['Donn_es_techniques.Installation_des_corps'] ?? '';
      $remarques = $event['Donn_es_techniques.Remarques'] ?? '';
      $nombre = $event['Participants.Nombre_d_apprenants']." | ".$event['Participants.Nombre_de_formateurs']." | ".$event['Participants.Nombre_de_Staff'] ?? '0';
      

        // Les champs sont regroupés en plusieurs lignes afin de conserver
        // une présentation lisible dans les applications de calendrier.
      $descriptionLines = [];
      if (!empty($reference)) {
        $descriptionLines[] = 'Saisine : ' . $reference;
      }
      $descriptionLines[]= 'Confirmé : '  .$confirmation;


      if (!empty($porteur)) {
        $descriptionLines[] = 'Porteur : ' . $porteur;
      }

      if (!empty($nombreCorps)) {
        $descriptionLines[] = 'Nb de corps : ' . $nombreCorps;
      }

      if (!empty($conservation)) {
          $conservationLabel = $this->getCustomFieldDisplayValue(
              'Donn_es_techniques.Conservation',
              $conservation
          );
          $descriptionLines[] =
              'Conservation : ' . $conservationLabel;
      }

      if (!empty($installation)) {
        $descriptionLines[] = 'Position : ' . $installation;
      }

      if (!empty($event['Participants.Nombre_d_apprenants'])) {
        $descriptionLines[] = 'Nombre apprenants | Formateurs | Staff : ' . $nombre;
      }

      if (!empty($remarques)) {
        $descriptionLines[] = 'Remarques : ' . $remarques;
      }

      if (!empty($description)) {
        $descriptionLines[] = $description;
      }

      if (!empty($descriptionLines)) {
        $output[] = 'DESCRIPTION:' .
          $this->escapeIcal(implode("\n", $descriptionLines));
      }

      $output[] = 'END:VEVENT';
    }

    $output[] = 'END:VCALENDAR';

    header('Content-Type: text/calendar; charset=utf-8');
    //header('Content-Type: text/plain; charset=utf-8');  // POUR TESTER AFFICHAGE AU LIEU GENERATION DE CALENDIRER

    foreach ($output as $line) {
    echo $this->foldIcalLine($line) . "\r\n";
    }
    CRM_Utils_System::civiExit();
  } 




  private function escapeIcal($value) {
    /**
     * Échappe une valeur destinée à une propriété iCalendar.
     *
     * Les caractères spéciaux du format iCalendar (\, ; ,) sont échappés
     * et les retours à la ligne sont convertis en séquences `\n`.
     *
     * Cette fonction est utilisée pour éviter qu'une valeur provenant de
     * CiviCRM ne modifie accidentellement la structure du fichier iCalendar.
     */
    $value = (string) $value;

    $value = str_replace(
      ['\\', ';', ',', "\r\n", "\r", "\n"],
      ['\\\\', '\;', '\,', '\n', '\n', '\n'],
      $value
    );

    return $value;
  }
}