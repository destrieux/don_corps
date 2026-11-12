<?php
use CRM_DonCorps_ExtensionUtil as E;

// Neutralise l'adresse postale en cas de retour de courrier :
// formulaire Afform + règle CiviRules

// Texte qui relie le formulaire à la règle : écrit dans l'activité par le formulaire,
// recherché par la condition 2. Il doit être identique aux deux endroits, donc non traduit.
$detailsText = 'Retour mail postal pour adresse erronée';

// Type d'activité "modification des coordonnées"
$activityType = civicrm_api4('OptionValue', 'get', [
  'select' => ['value'],
  'where' => [
    ['option_group_id:name', '=', 'activity_type'],
    ['name', '=', 'modification des coordonnées'],
  ],
  'checkPermissions' => FALSE,
])->first();

if (!$activityType) {
  throw new \CRM_Core_Exception("don_corps : type d'activité 'modification des coordonnées' introuvable");
}

// Champ personnalisé "Adresse incorrecte"
$customField = civicrm_api4('CustomField', 'get', [
  'select' => ['id'],
  'where' => [
    ['name', '=', 'Adresse_incorrecte'],
  ],
  'checkPermissions' => FALSE,
])->first();

if (!$customField) {
  throw new \CRM_Core_Exception("don_corps : champ personnalisé 'Adresse_incorrecte' introuvable");
}

return [
  [ // Formulaire de saisie des contacts dont l'adresse est à neutraliser
    'entity' => 'Afform',
    'values' => [
      'name' => 'afformDSactiveAdressePostale',
      'type' => 'form',
      'title' => E::ts("Désactivation de l'adresse postale"),
      'description' => E::ts('Active le champ Adresse Incorrecte'),
      'placement' => ['dashboard_dashlet'],
      'icon' => 'fa-list-alt',
      'permission' => ['access CiviCRM'],
      'create_submission' => TRUE,
      'layout' => [
        [
          '#tag' => 'af-form',
          'ctrl' => 'afform',
          '#children' => [
            [
              '#tag' => 'af-entity',
              'type' => 'Activity',
              'name' => 'Activity1',
              'label' => E::ts('Activité 1'),
              'data' => [
                'source_contact_id' => 'user_contact_id',
                'activity_type_id' => $activityType['value'],
                'status_id' => '2',
                'subject' => $detailsText,
                'details' => $detailsText,
              ],
              'actions' => [
                'create' => TRUE,
                'update' => TRUE,
              ],
              'security' => 'RBAC',
            ],
            [
              '#tag' => 'p',
              'class' => 'af-text',
              '#children' => [
                ['#text' => E::ts("Scannez le code-barres du contact dont l'adresse doit être inactivée. Son identité s'affiche. Validez en tapant Entrée. Une fois les contacts saisis, pressez le bouton Neutraliser l'adresse postale.")],
              ],
            ],
            [
              '#tag' => 'fieldset',
              'af-fieldset' => 'Activity1',
              'class' => 'af-container',
              '#children' => [
                [
                  '#tag' => 'af-field',
                  'name' => 'target_contact_id',
                  'defn' => [
                    'label' => E::ts('Codes-barres (id) des contacts à modifier'),
                    'input_attrs' => [],
                  ],
                ],
              ],
            ],
            [
              '#tag' => 'button',
              'class' => 'af-button btn btn-primary',
              'crm-icon' => 'fa-check',
              'ng-click' => 'afform.submit()',
              'ng-if' => 'afform.showSubmitButton',
              '#children' => [
                ['#text' => E::ts("Neutraliser l'adresse postale")],
              ],
            ],
          ],
        ],
      ],
    ],
  ],
  [ // Règle
    'entity' => 'CiviRulesRule',
    'values' => [
      'name' => 'neutralise_adresse_postale',
      'label' => E::ts('Neutralise adresse postale'),
      'trigger_id:name' => 'new_activity',
      'trigger_params' => 'a:1:{s:11:"record_type";s:1:"0";}',
      'description' => E::ts('Neutralise adresse postale en cas de retour de courrier'),
      'help_text' => E::ts('<p>Si une activité de type "Modification des coordonnées" avec le sujet "Retour mail postal pour adresse erronée" est créée, le champ Adresse erronée passe à OUI.</p>'),
      'is_active' => TRUE,
    ],
  ],
  [ // Condition 1 : activité de type "modification des coordonnées"
    'entity' => 'CiviRulesRuleCondition',
    'values' => [
      'rule_id.name' => 'neutralise_adresse_postale',
      'condition_id.name' => 'activity_of_type',
      'condition_params' => [
        'operator' => '0',
        'activity_type_id' => [$activityType['value']],
      ],
      'is_active' => TRUE,
    ],
  ],
  [ // Condition 2 : ET les détails contiennent le texte du formulaire
    'entity' => 'CiviRulesRuleCondition',
    'values' => [
      'rule_id.name' => 'neutralise_adresse_postale',
      'condition_id.name' => 'contact_has_activity_with_details',
      'condition_link' => 'AND',
      'condition_params' => [
        'operator' => 'contains',
        'text' => $detailsText,
      ],
      'is_active' => TRUE,
    ],
  ],
  [ // Action : Adresse incorrecte = Oui
    'entity' => 'CiviRulesRuleAction',
    'values' => [
      'rule_id.name' => 'neutralise_adresse_postale',
      'action_id.name' => 'set_custom_field',
      'action_params' => [
        'field_id' => $customField['id'],
        'value' => '1',
      ],
      'is_active' => TRUE,
    ],
  ],
];