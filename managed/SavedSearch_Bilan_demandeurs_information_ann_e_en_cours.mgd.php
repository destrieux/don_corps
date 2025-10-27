<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_demandeurs_information_ann_e_en_cours',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_demandeurs_information_ann_e_en_cours',
        'label' => E::ts('Bilan : demandeurs information année en cours'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'COUNT(Demandeur_information.Date_d_envoi_d_informations) AS COUNT_Demandeur_information_Date_d_envoi_d_informations',
          ],
          'orderBy' => [],
          'where' => [
            [
              'Demandeur_information.Date_d_envoi_d_informations',
              '=',
              'this.year',
            ],
          ],
          'groupBy' => ['contact_type'],
          'join' => [],
          'having' => [],
        ],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Bilan_demandeurs_information_ann_e_en_cours_SearchDisplay_Bilan_demandeurs_information_ann_e_en_cours_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_demandeurs_information_ann_e_en_cours_List_1',
        'label' => E::ts('Bilan : demandeurs information année en cours List 1'),
        'saved_search_id.name' => 'Bilan_demandeurs_information_ann_e_en_cours',
        'type' => 'list',
        'settings' => [
          'style' => 'ul',
          'limit' => 0,
          'sort' => [
            ['sort_name', 'ASC'],
          ],
          'pager' => FALSE,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'COUNT_Demandeur_information_Date_d_envoi_d_informations',
              'dataType' => 'Integer',
              'label' => E::ts('Nombre de demandes d\'information année en cours :'),
            ],
          ],
          'placeholder' => 0,
        ],
      ],
      'match' => [
        'saved_search_id',
        'name',
      ],
    ],
  ],
];
