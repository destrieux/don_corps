<?php
// Règles de messages des cérémonies (extension Event Messages)
// À charger après : statuts des participants, modèles de messages 300/310/320 et modèle d'événement

// Récupère l'id d'une entité ou s'arrête avec un message clair
$getId = function (string $entity, array $where, string $label) {
  $row = civicrm_api4($entity, 'get', [
    'select' => ['id'],
    'where' => $where,
    'checkPermissions' => FALSE,
  ])->first();
  if (!$row) {
    throw new \CRM_Core_Exception("don_corps : $label introuvable");
  }
  return $row['id'];
};

$eventId = $getId('Event', [
  ['is_template', '=', TRUE],
  ['template_title', '=', 'Modèle de cérémonie test'],
], "modèle d'événement 'Modèle de cérémonie test'");

// Construit une règle : envoie le modèle $msgTitle quand le participant passe au statut $statusName
$rule = function (string $msgTitle, string $statusName) use ($getId, $eventId) {
  return [
    'entity' => 'EventMessageRule',
    'values' => [
      'event_id' => $eventId,
      'template_id' => $getId('MessageTemplate', [['msg_title', '=', $msgTitle]], "modèle de message '$msgTitle'"),
      'to_status' => [
        $getId('ParticipantStatusType', [['name', '=', $statusName]], "statut de participant '$statusName'"),
      ],
      'from_status' => [],
      'roles' => [],
      'languages' => [],
      'is_active' => TRUE,
    ],
  ];
};

return [
  // Désactive le message standard de confirmation d'inscription
  // (sinon un message annonçant une liste d'attente est envoyé à l'inscription)
  [
    'entity' => 'MessageTemplate',
    'values' => [
      'msg_title' => "Événements - Confirmation d'inscription et reçu (hors ligne)",
      //'msg_subject' => '',
      //'msg_text' => '',
      //'msg_html' => '',
      'is_active' => FALSE,
    ],
  ],

  // 300 - Invitation : statut On waitlist (Invité)
  $rule('300 - Cérémonie invitation (email)', 'On waitlist'),

  // 310 - Confirmation : statut Registered (Confirmé)
  $rule('310 - Cérémonie confirmation  (email)', 'Registered'),

  // 320 - Non inscription : statut Cancelled (Annulé)
  $rule('320 - Cérémonie non inscription  (email)', 'Cancelled'),
];