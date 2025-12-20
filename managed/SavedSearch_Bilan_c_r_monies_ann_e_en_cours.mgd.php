<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_c_r_monies_ann_e_en_cours',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_c_r_monies_ann_e_en_cours',
        'label' => E::ts('Bilan : cérémonies année en cours'),
        'api_entity' => 'Event',
        'api_params' => [
          'version' => 4,
          'select' => [
            'start_date',
            'loc_block_id.address_id.street_address',
            'loc_block_id.address_id.city',
          ],
          'orderBy' => [],
          'where' => [
            ['start_date', '=', 'this.year'],
            ['is_active', '=', TRUE],
          ],
          'groupBy' => [],
          'join' => [
            [
              'LocBlock AS Event_LocBlock_loc_block_id_01',
              'LEFT',
              [
                'loc_block_id',
                '=',
                'Event_LocBlock_loc_block_id_01.id',
              ],
            ],
            [
              'Address AS Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01',
              'LEFT',
              [
                'Event_LocBlock_loc_block_id_01.address_id',
                '=',
                'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.id',
              ],
              [
                'Event_LocBlock_loc_block_id_01_LocBlock_Address_address_id_01.is_primary',
                '=',
                TRUE,
              ],
            ],
          ],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Bilan_c_r_monies_ann_e_en_cours_SearchDisplay_Bilan_c_r_monies_ann_e_en_cours_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_c_r_monies_ann_e_en_cours_Table_1',
        'label' => E::ts('Bilan : cérémonies année en cours Table 1'),
        'saved_search_id.name' => 'Bilan_c_r_monies_ann_e_en_cours',
        'type' => 'table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [
            ['start_date', 'ASC'],
          ],
          'limit' => 0,
          'pager' => FALSE,
          'placeholder' => 0,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'start_date',
              'dataType' => 'Timestamp',
              'label' => E::ts('Date'),
              'sortable' => FALSE,
              'rewrite' => '',
            ],
            [
              'type' => 'field',
              'key' => 'loc_block_id.address_id.street_address',
              'label' => E::ts('Lieu'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'loc_block_id.address_id.city',
              'label' => E::ts(''),
              'sortable' => TRUE,
            ],
          ],
          'actions' => FALSE,
          'classes' => [],
          'headerCount' => TRUE,
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
