<?php
use CRM_DonCorps_ExtensionUtil as E;

return [
  [
    'name' => 'CiviRulesRule_envoyer_mail_si_demande_cremation',
    'entity' => 'CiviRulesRule',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'envoyer_mail_si_demande_cremation',
        'label' => E::ts('Envoyer mail si demande cremation'),
        'trigger_id.name' => 'changed_individual_custom_data',
        'description' => E::ts('Envoi un mail aux PF si un corps passe en "demander crémation" et le passe en crémation demandée (pas de délai)'),
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_envoyer_mail_si_demande_cremation_CiviRulesRuleAction_1',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'envoyer_mail_si_demande_cremation',
        'action_id.name' => 'emailapi_send',
        'action_params' => [
          'from_name' => 'Techniciens labo anatomie',
          'from_email' => 'dons.corps@med.univ-tours.fr',
          'template_id' => '66',
          'disable_smarty' => FALSE,
          'location_type_id' => '',
          'from_email_option' => '',
          'alternative_receiver_address' => '',
          'cc' => 'dons.corps@med.univ-tours.fr',
          'bcc' => '',
          'file_on_case' => FALSE,
        ],
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_envoyer_mail_si_demande_cremation_CiviRulesRuleAction_2',
    'entity' => 'CiviRulesRuleAction',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'envoyer_mail_si_demande_cremation',
        'action_id.name' => 'changer_elimination_pour_cremation_demandee',
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_envoyer_mail_si_demande_cremation_CiviRulesRuleCondition_1',
    'entity' => 'CiviRulesRuleCondition',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'envoyer_mail_si_demande_cremation',
        'condition_id.name' => 'contact_custom_field_changed',
        'condition_params' => [
          'custom_field_id' => [92],
        ],
      ],
    ],
  ],
  [
    'name' => 'CiviRulesRule_envoyer_mail_si_demande_cremation_CiviRulesRuleCondition_2',
    'entity' => 'CiviRulesRuleCondition',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'rule_id.name' => 'envoyer_mail_si_demande_cremation',
        'condition_link' => 'AND',
        'condition_id.name' => 'demander_cremation_du_contact',
      ],
    ],
  ],
];
