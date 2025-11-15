<?php

class CRM_Myextension_Hook {

  /**
   * Reformater les dates avant export Excel
   */
  public static function civicrm_alterExportOutput(&$rows, $exportMode) {
    if (empty($rows)) {
      return;
    }

    // Colonnes qui contiennent des dates — à adapter selon ton instance
    $dateColumns = [
      'Birth Date',
      'Membership Start Date',
      'Membership End Date',
      'Contribution Receive Date',
    ];

    foreach ($rows as &$row) {
      foreach ($row as $key => &$value) {
        if (in_array($key, $dateColumns, true) && !empty($value)) {
          $ts = strtotime($value);
          if ($ts) {
            $value = date('d/m/Y', $ts); // Format français
          }
        }
      }
    }
  }
}
