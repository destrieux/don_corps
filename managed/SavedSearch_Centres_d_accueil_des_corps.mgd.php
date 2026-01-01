<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'SavedSearch_Centres_d_accueil_des_corps',
    'entity' => 'SavedSearch',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Centres_d_accueil_des_corps',
        'label' => E::ts('Centres d\'accueil des corps'),
        'api_entity' => 'Contact',
        'api_params' => [
          'version' => 4,
          'select' => [
            'primary_contact_id',
            'sort_name',
            'Contact_Address_contact_id_01.street_address',
            'Contact_Address_contact_id_01.supplemental_address_1',
            'Contact_Address_contact_id_01.postal_code',
            'Contact_Address_contact_id_01.city',
            'phone_primary.phone',
            'email_primary.email',
            'CDC_admin.Directeur_du_Centre_d_accueil',
            'CDC_admin.Pr_sident_du_CESP',
            'CDC_admin.Qualit_du_Pr_sident_du_CESP',
          ],
          'orderBy' => [],
          'where' => [
            [
              'contact_sub_type:name',
              'CONTAINS',
              'CDC',
            ],
          ],
          'groupBy' => [],
          'join' => [
            [
              'Address AS Contact_Address_contact_id_01',
              'LEFT',
              [
                'id',
                '=',
                'Contact_Address_contact_id_01.contact_id',
              ],
            ],
          ],
          'having' => [],
        ],
        'description' => E::ts('Liste des centres de don du corps'),
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'SavedSearch_Centres_d_accueil_des_corps_SearchDisplay_Contact_Search_by_M_Christophe_Destrieux_Table_1',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Contact_Search_by_M_Christophe_Destrieux_Table_1',
        'label' => E::ts('Contact Search by M. Christophe Destrieux Table 1'),
        'saved_search_id.name' => 'Centres_d_accueil_des_corps',
        'type' => 'table',
        'settings' => [
          'description' => E::ts(''),
          'sort' => [
            [
              'Contact_Address_contact_id_01.city',
              'ASC',
            ],
          ],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'sort_name',
              'dataType' => 'String',
              'label' => E::ts('Nom'),
              'sortable' => TRUE,
              'link' => [
                'path' => '',
                'entity' => 'Contact',
                'action' => 'view',
                'join' => '',
                'target' => '_blank',
                'task' => '',
              ],
              'title' => E::ts('Voir Contact'),
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.street_address',
              'dataType' => 'String',
              'label' => E::ts('Adresses'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.supplemental_address_1',
              'dataType' => 'String',
              'label' => E::ts('Complément d\'adresse'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.postal_code',
              'dataType' => 'String',
              'label' => E::ts('Code postal'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'Contact_Address_contact_id_01.city',
              'dataType' => 'String',
              'label' => E::ts('Ville'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'phone_primary.phone',
              'label' => E::ts('Téléphone principal Téléphone'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'email_primary.email',
              'label' => E::ts('Courriel'),
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'CDC_admin.Directeur_du_Centre_d_accueil',
              'label' => E::ts('Directeur du Centre'),
              'sortable' => TRUE,
              'editable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'CDC_admin.Pr_sident_du_CESP',
              'label' => E::ts('Président du CESP'),
              'sortable' => TRUE,
              'editable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'CDC_admin.Qualit_du_Pr_sident_du_CESP',
              'label' => E::ts('Qualité du Président du CESP'),
              'sortable' => TRUE,
              'editable' => TRUE,
            ],
          ],
          'actions' => TRUE,
          'classes' => ['table', 'table-striped'],
          'actions_display_mode' => 'menu',
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
