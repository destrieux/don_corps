<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_refus_ou_non_reception_corps_A_1',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_refus_ou_non_reception_corps_A_1',
        'label' => E::ts('Bilan : refus ou non reception corps A-1'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'Prise_en_charge_au_d_c_s.Motif_de_refus_du_corps:label',
            'COUNT(id) AS COUNT_id',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'Donateur',
            ],
            ['deceased_date', '=', 'previous.year'],
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
    'name' => 'SavedSearch_Bilan_refus_ou_non_reception_corps_A_1_SearchDisplay_refus_A_1_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'refus_A_1_Table_1',
        'label' => E::ts('refus A-1 Table 1'),
        'saved_search_id.name' => 'Bilan_refus_ou_non_reception_corps_A_1',
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
              'label' => E::ts('Motifs de refus du corps'),
              'sortable' => FALSE,
              'tally' => [
                'fn' => NULL,
              ],
            ],
            [
              'type' => 'field',
              'key' => 'COUNT_id',
              'label' => E::ts('Nombre'),
              'sortable' => FALSE,
              'tally' => [
                'fn' => 'SUM',
              ],
            ],
          ],
          'actions' => FALSE,
          'classes' => [],
          'tally' => [
            'label' => E::ts('Nombre total de corps refusés ou non reçus l\'année précédente'),
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
