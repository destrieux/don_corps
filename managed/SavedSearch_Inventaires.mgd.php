<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Inventaires',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Inventaires',
        'label' => E::ts('Inventaires'),
        'api_entity' => 'Custom_Utilisation_du_corps',
        'api_params' => [
          'version' => 4,
          'select' => [
            'entity_id.sort_name',
            'Custom_Utilisation_du_corps_Contact_entity_id_01.Prise_en_charge_au_d_c_s.N_de_d_c_s',
            'N_de_pi_ce_ou_de_corps',
            'Type_de_poi_ce_3:label',
            'Sortie',
            'Date_de_retour',
            'Lacalisation.display_name',
            'Custom_Utilisation_du_corps_Contact_entity_id_01.Devenir_du_corps.devenir_effectif_du_corps:label',
            'Inventaires:label',
            'id',
          ],
          'orderBy' => [],
          'where' => [
            [
              'OR',
              [
                [
                  'Inventaires:name',
                  'IS NOT EMPTY',
                ],
                ['Lacalisation', 'IS NOT EMPTY'],
              ],
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Contact AS Custom_Utilisation_du_corps_Contact_entity_id_01',
              'LEFT',
              [
                'entity_id',
                '=',
                'Custom_Utilisation_du_corps_Contact_entity_id_01.id',
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
    'name' => 'SavedSearch_Inventaires_SearchDisplay_Inventaires_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Inventaires_Table_1',
        'label' => E::ts('Inventaires'),
        'saved_search_id.name' => 'Inventaires',
        'type' => 'table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'entity_id.sort_name',
              'dataType' => 'String',
              'label' => E::ts('Nom trié'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => 'entity_id',
                'target' => '_blank',
              ],
              'title' => E::ts('Voir Id. de l\'entité'),
            ],
            [
              'type' => 'field',
              'key' => 'Custom_Utilisation_du_corps_Contact_entity_id_01.Prise_en_charge_au_d_c_s.N_de_d_c_s',
              'dataType' => 'String',
              'label' => E::ts('N° de décès'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => 'Custom_Utilisation_du_corps_Contact_entity_id_01',
                'target' => '_blank',
              ],
              'title' => E::ts('Voir Utilisation du corps Contact'),
            ],
            [
              'type' => 'field',
              'key' => 'Type_de_poi_ce_3:label',
              'dataType' => 'String',
              'label' => E::ts('Type de pièce'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'N_de_pi_ce_ou_de_corps',
              'dataType' => 'String',
              'label' => E::ts('Code Barres'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Lacalisation.display_name',
              'dataType' => 'String',
              'label' => E::ts('Localisation Théorique'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Inventaires:label',
              'dataType' => 'String',
              'label' => E::ts('Inventaires'),
              'sortable' => TRUE,
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
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
