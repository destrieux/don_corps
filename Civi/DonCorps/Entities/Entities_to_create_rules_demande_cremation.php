<?php
use CRM_DonCorps_ExtensionUtil as E;

// Envoyer mail si demande crémation : action, condition et règle CiviRules

// Champ personnalisé surveillé par la condition 1
$customField = civicrm_api4('CustomField', 'get', [
  'select' => ['id'],
  'where' => [
    ['name', '=', 'Mode_limination_hors_corps_2'],
  ],
  'checkPermissions' => FALSE,
])->first();

if (!$customField) {
  throw new \CRM_Core_Exception("don_corps : champ personnalisé 'Mode_limination_hors_corps_2' introuvable");
}

// Modèle de message utilisé par l'action d'envoi
$msgTemplate = civicrm_api4('MessageTemplate', 'get', [
  'select' => ['id'],
  'where' => [
    ['msg_title', '=', 'Demander crémation au secrétariat'],
  ],
  'checkPermissions' => FALSE,
])->first();

if (!$msgTemplate) {
  throw new \CRM_Core_Exception("don_corps : modèle de message 'Demander crémation au secrétariat' introuvable");
}
$msgId = $msgTemplate['id'];

return [
  [ // Action : change le mode d'élimination
    'entity' => 'CiviRulesAction',
    'values' => [
      'name' => 'changer_elimination_pour_cremation_demandee',
      'label' => E::ts("Change le mode d'élimination pour crémation demandée"),
      'class_name' => 'CRM_DonCorps_CivirulesActions_Contact_Changeelimination',
      'is_active' => TRUE,
    ],
  ],
  [ // Condition : mode d'élimination = demander crémation
    'entity' => 'CiviRulesCondition',
    'values' => [
      'name' => 'demander_cremation_du_contact',
      'label' => E::ts("Vérifie si le mode d'élimination est demander crémation"),
      'class_name' => 'CRM_DonCorps_CivirulesConditions_Contact_Demandercrema',
      'is_active' => TRUE,
    ],
  ],
  [ // Règle
    'entity' => 'CiviRulesRule',
    'values' => [
      'name' => 'envoyer_mail_si_demande_cremation',
      'label' => E::ts('Envoyer mail si demande crémation'),
      'trigger_id:name' => 'changed_individual_custom_data',
      'description' => E::ts('Envoie un mail aux PF si un corps passe en "demander crémation" et le passe en crémation demandée (pas de délai)'),
      'is_active' => TRUE,
    ],
  ],
  [ // Condition 1 : le champ mode d'élimination a changé
    'entity' => 'CiviRulesRuleCondition',
    'values' => [
      'rule_id.name' => 'envoyer_mail_si_demande_cremation',
      'condition_id.name' => 'contact_custom_field_changed',
      'condition_params' => [
        'custom_field_id' => [$customField['id']],
      ],
      'is_active' => TRUE,
    ],
  ],
  [ // Condition 2 : ET le mode est "demander crémation"
    'entity' => 'CiviRulesRuleCondition',
    'values' => [
      'rule_id.name' => 'envoyer_mail_si_demande_cremation',
      'condition_id.name' => 'demander_cremation_du_contact',
      'condition_link' => 'AND',
      'is_active' => TRUE,
    ],
  ],
  [ // Action 1 : envoi du mail
    'entity' => 'CiviRulesRuleAction',
    'values' => [
      'rule_id.name' => 'envoyer_mail_si_demande_cremation',
      'action_id.name' => 'emailapi_send',
      'action_params' => [
        'from_name' => 'Techniciens labo anatomie',
        'from_email' => 'techlabos@domain.fr',
        'template_id' => $msgId,
        'disable_smarty' => FALSE,
        'location_type_id' => '',
        'from_email_option' => '',
        'alternative_receiver_address' => 'pompes@domain.fr',
        'cc' => 'votre.labo@domain.fr',
        'bcc' => '',
        'file_on_case' => FALSE,
      ],
      'is_active' => TRUE,
    ],
  ],
  [ // Action 2 : passage en crémation demandée
    'entity' => 'CiviRulesRuleAction',
    'values' => [
      'rule_id.name' => 'envoyer_mail_si_demande_cremation',
      'action_id.name' => 'changer_elimination_pour_cremation_demandee',
      'is_active' => TRUE,
    ],
  ],
];