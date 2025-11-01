<?php
eval(`cv php:boot`);


  // création des templates de cérmonies ; les statuts et les roles des participants doivent etre crées en amont
  echo "  -Création des Templates de cérémonie".PHP_EOL;
        
  $to_create =  [        //Corriger_civililite : Déclaration de l'Action
      'entity' => 'Event',
      'values' => 
      [ 'title' => 'Cérémonie test',
          'summary' => "Cérémonie d'hommage aux donneurs et à leurs familles",
          'description' => NULL,
          'participant_listing_id' => NULL,
          'is_public' => FALSE,
          'start_date' => NULL,
          'end_date' => NULL,
          'is_online_registration' => FALSE,
          'registration_link_text' => NULL,
          'registration_start_date' => NULL,
          'registration_end_date' => NULL,
          'max_participants' => NULL,
          'event_full_text' => 'Cet événement est actuellement complet.',
          'is_monetary' => FALSE,
          'financial_type_id' => NULL,
          'payment_processor' => NULL,
          'is_map' => FALSE,
          'is_active' => TRUE,
          'fee_label' => NULL,
          'is_show_location' => FALSE,
          'loc_block_id' => '',
          'intro_text' => NULL,
          'footer_text' => NULL,
          'confirm_title' => NULL,
          'confirm_text' => NULL,
          'confirm_footer_text' => NULL,
          'is_email_confirm' => FALSE,
          'confirm_email_text' => NULL,
          'confirm_from_name' => NULL,
          'confirm_from_email' => NULL,
          'cc_confirm' => NULL,
          'bcc_confirm' => NULL,
          'default_fee_id' => NULL,
          'default_discount_fee_id' => NULL,
          'thankyou_title' => NULL,
          'thankyou_text' => NULL,
          'thankyou_footer_text' => NULL,
          'is_pay_later' => FALSE,
          'pay_later_text' => NULL,
          'pay_later_receipt' => NULL,
          'is_partial_payment' => FALSE,
          'initial_amount_label' => NULL,
          'initial_amount_help_text' => NULL,
          'min_initial_amount' => NULL,
          'is_multiple_registrations' => FALSE,
          'max_additional_participants' => 0,
          'allow_same_participant_emails' => FALSE,
          'has_waitlist' => FALSE,
          'requires_approval' => FALSE,
          'expiration_time' => NULL,
          'allow_selfcancelxfer' => FALSE,
          'selfcancelxfer_time' => 0,
          'waitlist_text' => NULL,
          'approval_req_text' => NULL,
          'is_template' => TRUE,
          'template_title' => 'Modèle de cérémonie test',
          'created_id' => NULL,
          'currency' => NULL,
          'is_share' => FALSE,
          'is_confirm_enabled' => TRUE,
          'parent_event_id' => NULL,
          'slot_label_id' => NULL,
          'dedupe_rule_group_id' => NULL,
          'is_billing_required' => FALSE,
          'is_show_calendar_links' => TRUE,
          'event_type_id:name' => 'Cérémonie Hommage',
          'default_role_id:name' => 'Attendee',
      ],
  ];
  $event_id=create_entity($to_create);


// installation des regles de message pour les cérémonies
  // message '300 Cérémonie invitation (email)' pour statut 'On waitlist' (Invité)
  $messageTemplates = civicrm_api4('MessageTemplate', 'get', [                /// récupère l'id du MessageTemplate
    'select' => [
      'id',
    ],
    'where' => [
      ['msg_title', '=', '300 Cérémonie invitation (email)'],
    ],
    'limit' => 1,
    'checkPermissions' => FALSE,
  ]);

  $participantStatusTypes = civicrm_api4('ParticipantStatusType', 'get', [    /// récupère l'id de statut pour 'On waitlist' (invité)
    'where' => [
      ['is_active', '=', TRUE],
      ['name', '=', 'On waitlist'],
    ],
    'limit' => 1,
    'checkPermissions' => FALSE,
  ]);

  if(isset($messageTemplates[0]) && isset($participantStatusTypes[0]) &&isset($event_id)){

    $to_create =  [        //Corriger_civililite : Déclaration de l'Action
      'entity' => 'EventMessageRule',
      'values' => [
          'event_id' => $event_id,
          'is_active' => 1,
          'template_id' => $messageTemplates[0]['id'],
          'from_status' => [],
          'to_status' => [
            $participantStatusTypes[0]['id'],
          ],
          'languages' => [],
          'roles' => [],
          'attachments' => NULL,
      ],
    ];

    //print_r($to_create);

  } else {
    echo "Message template, statut, ou évenement manquent".PHP_EOL;
  }
  create_entity($to_create);


  // message '310 Cérémonie confirmation  (email)' pour statut Registered (Confirmé)
   $messageTemplates = civicrm_api4('MessageTemplate', 'get', [                /// récupère l'id du MessageTemplate
    'select' => [
      'id',
    ],
    'where' => [
      ['msg_title', '=', '310 Cérémonie confirmation  (email)'],
    ],
    'limit' => 1,
    'checkPermissions' => FALSE,
  ]);

  $participantStatusTypes = civicrm_api4('ParticipantStatusType', 'get', [    /// récupère l'id de statut pour 'Registered' (Confirmé)
    'where' => [
      ['is_active', '=', TRUE],
      ['name', '=', 'Registered'],
    ],
    'limit' => 1,
    'checkPermissions' => FALSE,
  ]);

  if(isset($messageTemplates[0]) && isset($participantStatusTypes[0]) &&isset($event_id)){

    $to_create =  [        //Corriger_civililite : Déclaration de l'Action
      'entity' => 'EventMessageRule',
      'values' => [
          'event_id' => $event_id,
          'is_active' => 1,
          'template_id' => $messageTemplates[0]['id'],
          'from_status' => [],
          'to_status' => [
            $participantStatusTypes[0]['id'],
          ],
          'languages' => [],
          'roles' => [],
          'attachments' => NULL,
      ],
    ];

    //print_r($to_create);

  } else {
    echo "Message template, statut, ou évenement manquent".PHP_EOL;
  }
  create_entity($to_create);


// message '320 Cérémonie non inscription  (email)' pour statut Cancelled (Annulé)
$messageTemplates = civicrm_api4('MessageTemplate', 'get', [                /// récupère l'id du MessageTemplate
  'select' => [
    'id',
  ],
  'where' => [
    ['msg_title', '=', '320 Cérémonie non inscription  (email)'],
  ],
  'limit' => 1,
  'checkPermissions' => FALSE,
]);

$participantStatusTypes = civicrm_api4('ParticipantStatusType', 'get', [    /// récupère l'id de statut pour 'Registered' (Confirmé)
  'where' => [
    ['is_active', '=', TRUE],
    ['name', '=', 'Cancelled'],
  ],
  'limit' => 1,
  'checkPermissions' => FALSE,
]);

if(isset($messageTemplates[0]) && isset($participantStatusTypes[0]) &&isset($event_id)){

  $to_create =  [        //Corriger_civililite : Déclaration de l'Action
    'entity' => 'EventMessageRule',
    'values' => [
        'event_id' => $event_id,
        'is_active' => 1,
        'template_id' => $messageTemplates[0]['id'],
        'from_status' => [],
        'to_status' => [
          $participantStatusTypes[0]['id'],
        ],
        'languages' => [],
        'roles' => [],
        'attachments' => NULL,
    ],
  ];

  //print_r($to_create);

} else {
  echo "Message template, statut, ou évenement manquent".PHP_EOL;
}
create_entity($to_create);


// Inactivation du message template de confirmation d'inscription ; si non supprimé un message est envoyé à l'inscription prevenant d'une liste d attente
$to_create =  [       
  'entity' => 'MessageTemplate',
  'values' => [
    'msg_title' => "Événements - Confirmation d'inscription et reçu (hors ligne)",
    'is_active' => FALSE,
    'msg_subject' => '',
    'msg_text' => '',
    'msg_html' => '',
  ],
];
create_entity($to_create);

