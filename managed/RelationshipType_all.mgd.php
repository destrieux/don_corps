<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'RelationshipType_a_pour_PAQF',
    'entity' => 'RelationshipType',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name_a_b' => 'a pour PAQPF',
        'label_a_b' => E::ts('a pour PAQPF'),
        'name_b_a' => 'est la PAQPF',
        'label_b_a' => E::ts('est la PAQPF'),
        'description' => E::ts('Personne ayant qualité pour pourvoir aux funérailles'),
        'contact_type_a' => 'Individual',
        'contact_type_b' => 'Individual',
        'reltoken.display_reltokens' => TRUE,
      ],
      'match' => [E::ts('name_a_b'), 'name_b_a'],
    ],
  ],

 [
    'name' => 'RelationshipType_Confiance',
    'entity' => 'RelationshipType',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name_a_b' => 'a pour personne de confiance 2',
        'label_a_b' => E::ts('a pour personne de confiance 2'),
        'name_b_a' => 'est la personne de confiance 2',
        'label_b_a' => E::ts('est la personne de confiance 2'),
        'description' => E::ts('personne de confiance alternative'),
        'contact_type_a' => 'Individual',
        'contact_type_b' => 'Individual',
        'reltoken.display_reltokens' => TRUE,
      ],
      'match' => [E::ts('name_a_b'), 'name_b_a'],
    ],
  ],

[
    'name' => 'RelationshipType_Confiance2',
    'entity' => 'RelationshipType',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name_a_b' => 'a pour personne de confiance',
        'label_a_b' => E::ts('a pour personne de confiance'),
        'name_b_a' => 'est la personne de confiance de',
        'label_b_a' => E::ts('est la personne de confiance de'),
        'description' => E::ts('personne confiance'),
        'contact_type_a' => 'Individual',
        'contact_type_b' => 'Individual',
        'reltoken.display_reltokens' => TRUE,
      ],
      'match' => [E::ts('name_a_b'), 'name_b_a'],
    ],
  ],


];

