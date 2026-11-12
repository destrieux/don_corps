<?php
// Réglages CiviCRM appliqués à l'installation par Params::apply()
return [
  // ---- Dates et heures ----
  'dateformatDatetime'       => '%e %B %Y',
  // 'dateformatDatetime'    => '%e %B %Y %H:%M',
  'dateformatFull'           => '%e %B %Y',
  'dateformatTime'           => '%H:%M',
  'dateformatFinancialBatch' => 'dd-mm-yyyy',
  'dateformatshortdate'      => '%d/%m/%Y',
  'dateInputFormat'          => 'dd/mm/yy',
  'timeInputFormat'          => '2',
  'weekBegins'               => '1',

  // ---- Langue, monnaie, pays ----
  'uiLanguages'               => ['fr_FR'],
  'partial_locales'           => '0',
  'defaultCurrency'           => 'EUR',
  'monetaryDecimalPoint'      => ',',
  'monetaryThousandSeparator' => ' ',
  'moneyformat'               => '%a %c ',
  'defaultContactCountry'     => '1076',   // France

  // ---- Adresses ----
  'address_format'           => "{contact.address_name}\r\n{contact.street_address}\r\n{contact.supplemental_address_1}\r\n{contact.supplemental_address_2}\r\n{contact.supplemental_address_3}\r\n{contact.postal_code}{ }{contact.city}\r\n{contact.country}",
  'mailing_format'           => "{contact.address_name}\r\n{contact.street_address}\r\n{contact.supplemental_address_1}\r\n{contact.supplemental_address_2}\r\n{contact.supplemental_address_3}\r\n{contact.postal_code}{ }{contact.city}\r\n{contact.country}",
  'hideCountryMailingLabels' => '1',
  'address_options'          => ['1', '2', '3', '4', '5', '6', '10'],

  // ---- Fiche contact et recherche ----
  'contact_edit_options' => ['8', '7', '2', '5', '1', '4', '6', '12', '14', '16'],
  'quicksearch_options'  => [
    'sort_name',
    'first_name',
    'Promesse_de_don.N_de_don',
    'Prise_en_charge_au_d_c_s.N_de_d_c_s',
    'Annulation.N_annulation',
    'Utilisation_du_corps.N_de_pi_ce_ou_de_corps',
  ],
  'contact_ajax_check_similar' => '0',
  'includeWildCardInName'      => '0',
  'includeEmailInName'         => '0',
  'searchPrimaryDetailsOnly'   => '0',
  'smartGroupCacheTimeout'     => 0,

  // ---- Apparence et composants ----
  'theme_backend'     => 'greenwich',
  'enable_components' => ['CiviEvent', 'CiviContribute', 'CiviMail', 'CiviReport'],
];