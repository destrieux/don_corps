<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CustomGroup_Demandeur_information',
    'entity' => 'CustomGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Demandeur_information',
        'title' => E::ts('Demandeur information'),
        'weight' => 6,
        'collapse_adv_display' => TRUE,
        'created_date' => '2023-05-02 15:02:35',
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'CustomGroup_Demandeur_information_CustomField_Date_d_envoi_d_informations',
    'entity' => 'CustomField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Demandeur_information',
        'name' => 'Date_d_envoi_d_informations',
        'label' => E::ts('Date envoi informations'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'text_length' => 255,
        'date_format' => 'dd/mm/yy',
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'date_d_envoi_d_informations_109',
      ],
      'match' => [
        'name',
        'custom_group_id',
      ],
    ],
  ],
];
