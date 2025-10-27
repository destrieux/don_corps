<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Bilan_nombre_de_demandeurs_d_information',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_nombre_de_demandeurs_d_information',
        'label' => E::ts('Bilan : nombre de demandeurs d\'information année A-1'),
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
              'previous.year',
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
    'name' => 'SavedSearch_Bilan_nombre_de_demandeurs_d_information_SearchDisplay_Bilan_nombre_de_demandeurs_d_information_List_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Bilan_nombre_de_demandeurs_d_information_List_1',
        'label' => E::ts('Bilan : nombre de demandeurs d\'information année A-1List 1'),
        'saved_search_id.name' => 'Bilan_nombre_de_demandeurs_d_information',
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
              'label' => E::ts('Nombre de demandes de dossier d\'information (A -1) :'),
              'title' => E::ts(NULL),
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
