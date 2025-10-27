<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_refus_ou_non_reception_corps',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_refus_ou_non_reception_corps',
        'label' => E::ts('Bilan : refus/non reception'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:label',
            'COUNT(last_name) AS COUNT_last_name',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Donateur',
            ],
            ['deceased_date', '=', 'this.year'],
            [
              'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:name',
              '!=',
              'Pas_de_refus',
            ],
          ],
          'groupBy' => [
            'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps',
          ],
          'join' => [],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Bilan_refus_ou_non_reception_corps_SearchDisplay_Bilan_refus_ou_non_reception_corps_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_refus_ou_non_reception_corps_Table_1',
        'label' => E::ts('refus / non reception corps Table 1'),
        'saved_search_id.name' => 'Bilan_refus_ou_non_reception_corps',
        'type' => 'table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [
            ['sort_name', 'ASC'],
          ],
          'limit' => 0,
          'pager' => FALSE,
          'placeholder' => 0,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:label',
              'label' => E::ts('Motif de refus ou de non réception d\'un corps'),
              'sortable' => FALSE,
              'tally' => [
                'fn' => NULL,
              ],
            ],
            [
              'type' => 'field',
              'key' => 'COUNT_last_name',
              'label' => E::ts('Nombre'),
              'sortable' => FALSE,
              'tally' => [
                'fn' => 'SUM',
              ],
            ],
          ],
          'actions' => FALSE,
          'classes' => [],
          'button' => NULL,
          'editableRow' => [
            'disable' => TRUE,
          ],
          'tally' => [
            'label' => E::ts('Nombre total de corps refusés ou non reçus dans l\'année en cours'),
          ],
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
