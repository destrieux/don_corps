<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'OptionGroup_document_type',
    'entity' => 'OptionGroup',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'document_type',
        'title' => E::ts('Type de document'),
        'option_value_fields' => ['name', 'label', 'description'],
      ],
      'match' => ['name'],
    ],
  ],
  [
    'name' => 'OptionGroup_document_type_OptionValue_General',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'document_type',
        'label' => E::ts('Général'),
        'value' => '1',
        'name' => 'General',
        'is_default' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_document_type_OptionValue_Promesse_de_don',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'document_type',
        'label' => E::ts('Promesse de don'),
        'value' => '2',
        'name' => 'Promesse de don',
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_document_type_OptionValue_RGPD',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'document_type',
        'label' => E::ts('RGPD'),
        'value' => '3',
        'name' => 'RGPD',
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_document_type_OptionValue_Tous_documents_incription',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'document_type',
        'label' => E::ts('Tous documents incription'),
        'value' => '4',
        'name' => 'Tous documents incription',
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_document_type_OptionValue_Assurance',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'document_type',
        'label' => E::ts('Assurance'),
        'value' => '5',
        'name' => 'Assurance',
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_document_type_OptionValue_Courriers',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'document_type',
        'label' => E::ts('Courriers'),
        'value' => '6',
        'name' => 'Courriers',
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup_document_type_OptionValue_Extrait_de_certificat_de_d_c_s',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'document_type',
        'label' => E::ts('Extrait de certificat de décès'),
        'value' => '7',
        'name' => 'Extrait de certificat de décès',
      ],
      'match' => [
        'option_group_id',
        'name',
        'value',
      ],
    ],
  ],
];
