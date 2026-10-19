<?php

class CRM_DonCorps_Page_DonCorpsICal extends CRM_Core_Page {

  private function getCustomFieldDisplayValue($fieldName, $value) {
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


/* public function run() {

    $ical = [
        'BEGIN:VCALENDAR',
        'CALSCALE:GREGORIAN',
        'PRODID:-//Don Corps//CiviCRM//FR',
        'VERSION:2.0',
        'BEGIN:VTIMEZONE',
        'TZID:Europe/Paris',
        'BEGIN:DAYLIGHT',
        'DTSTART:19810329T020000',
        'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU',
        'TZNAME:UTC+2',
        'TZOFFSETFROM:+0100',
        'TZOFFSETTO:+0200',
        'END:DAYLIGHT',
        'BEGIN:STANDARD',
        'DTSTART:19961027T030000',
        'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU',
        'TZNAME:UTC+1',
        'TZOFFSETFROM:+0200',
        'TZOFFSETTO:+0100',
        'END:STANDARD',
        'END:VTIMEZONE',
        'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        'UID:test-doncorps-1@localhost',
        'DTSTAMP:20261018T180000Z',
        'DTSTART:20261024T080000Z',
        'DTEND:20261024T100000Z',
        'SUMMARY:Test calendrier',
        'END:VEVENT',
        'END:VCALENDAR',
    ];

    header('Content-Type: text/calendar; charset=utf-8');

    echo implode("\r\n", $ical) . "\r\n";

    CRM_Utils_System::civiExit();
} */


     public function run() {   // méthode publique appelée depuis l'extétieur de la classe

    ## Contrôleur du flux iCalendar : 
    ##    - il interroge CiviCRM, 
    ##    - sélectionne les événements  actifs, 
    ##    - récupère leurs champs, 
    ##    - configure les en-têtes HTTP 
    ##    - puis renvoie le calendrier au client.

    $events = \Civi\Api4\Event::get()
        ->addSelect('id')
        ->addSelect('title')
        ->addSelect('start_date')
        ->addSelect('end_date')
        ->addSelect('location')
        ->addSelect('description')
        ->addSelect('Caract_ristiques_de_l_activit_.R_f_rence_Saisine')
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
      
      $output[] = 'STATUS:CONFIRMED';
      $output[] = 'SUMMARY:' . $this->escapeIcal($event['title']); // ajoute le titre de l'évenement
                                              // escapeIcal échappe les caractères spéciaux iCalendar (, ;  \...)
                                              // réécrit en precedant les caractesrs speciaux par
      $output[] = 'TRANSP:OPAQUE';
      $output[] = 'UID:doncorps-' . $event['id'] . '@civicrm';// identifiant unique de l'évenement construit à parti de son id dans civicrm



      # Récupère les champs perso à intégrer au calendrier
      $description = $event['description'] ?? '';
      $reference = $event['Caract_ristiques_de_l_activit_.R_f_rence_Saisine'] ?? '';
      $nombreCorps = $event['Donn_es_techniques.Nombre_de_corps_sans_segmentation'] ?? '';
      $conservation = $event['Donn_es_techniques.Conservation'] ?? '';
      $installation = $event['Donn_es_techniques.Installation_des_corps'] ?? '';
      $remarques = $event['Donn_es_techniques.Remarques'] ?? '';
      $nombre = $event['Participants.Nombre_d_apprenants']." | ".$event['Participants.Nombre_de_formateurs']." | ".$event['Participants.Nombre_de_Staff'] ?? '0';
      

      $descriptionLines = [];

      if (!empty($reference)) {
        $descriptionLines[] = 'Saisine : ' . $reference;
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




/*       if (!empty($event['location'])) {
        $output[] = 'LOCATION:' . $this->escapeIcal($event['location']); // ajoute le lieu au tableau
      } */

      $output[] = 'END:VEVENT';
    }

    $output[] = 'END:VCALENDAR';

    header('Content-Type: text/calendar; charset=utf-8');
    //header('Content-Type: text/plain; charset=utf-8');

    foreach ($output as $line) {
    echo $this->foldIcalLine($line) . "\r\n";
    }
    CRM_Utils_System::civiExit();
  } 




  private function escapeIcal($value) {

    $value = (string) $value;

    $value = str_replace(
      ['\\', ';', ',', "\r\n", "\r", "\n"],
      ['\\\\', '\;', '\,', '\n', '\n', '\n'],
      $value
    );

    return $value;
  }
}