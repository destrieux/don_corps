<?php
return [
  [
    'entity' => 'MessageTemplate',
    'values' => [
      'msg_title' => 'Demander crémation au secrétariat',
      'msg_subject' => 'Merci de demander crémation pour : {contact.display_name}',
      'msg_html' => '<p>Bonjour</p>
<p>Merci de prévoir le transfert et de nous communiquer la date de crémation de :</p>
<p>{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label} {contact.first_name} {Tokens_for_contact_Champs_de_fu.CONCAT_WS_last_name_nick_name}</p>
<p>né(e) le {contact.birth_date} à {Tokens_for_contact_Champs_de_fu.Compl_m_nt_tat_civil.Ville_de_naissance}</p>
<p>décédé(e) le {contact.deceased_date} à {Tokens_for_contact_Champs_de_fu.Prise_en_charge_au_d_c_s.Ville_de_d_c_s}</p>
<p>Nous restons à votre disposition pour tout renseignement complémentaire.</p>
<p>Les techniciens du laboratoire de {domain.city}</p>',
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'MessageTemplate',
    'values' => [
      'msg_title' => '001 - Préinscription: envoi informations MAIL',
      'msg_subject' => "Envoi d'informations d'inscription",
      'msg_html' => "<p>{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</p>\r\n\r\n<p>Je vous remercie de votre volonté de donner votre corps à des fins d’enseignement et de recherche et vous engage à consulter notre site web : <strong><a href=\"https://dons-corps.univ-tours.fr\">{domain.description}</a>.</strong></p>\r\n\r\n<p>Vous y trouverez de nombreuses informations et des réponses aux principales questions que vous pourriez vous poser.</p>\r\n\r\n<p>Vous pourrez télécharger les documents nécessaires à votre inscription :<br />\r\n<strong>1) le guide d’information officiel : <a href=\"https://dons-corps.univ-tours.fr/medias/fichier/guide-information-juil2024-avectours_1733124203133-pdf?ID_FICHE=402072&amp;INLINE=FALSE\">Téléchargez le guide</a></strong></p>\r\n\r\n<p><strong>2)&nbsp; le formulaire de promesse de don à nous retourner par courrier si vous poursuivez votre démarche : <a href=\"https://dons-corps.univ-tours.fr/medias/fichier/declaration-consentement-don-corps-avecrgpd-2023-09-22_1759243997537-pdf?ID_FICHE=402072&amp;INLINE=FALSE\">Téléchargez le dossier d'inscription</a></strong></p>\r\n\r\n<p>N’hésitez pas à nous contacter si vous avez besoin d’information complémentaire</p>\r\n\r\n<p>Je vous remercie à nouveau de votre intérêt pour le don du corps et vous prie d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de ma parfaite considération.</p>\r\n\r\n<p>{domain.supplemental_address_3}<br />\r\nCentre d'accueil des corps de {domain.city}</p>",
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'MessageTemplate',
    'values' => [
      'msg_title' => '002 - Préinscription: envoi informations POSTALE',
      'msg_subject' => "Envoi d'informations d'inscription",
      'msg_html' => "<p>{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</p>\r\n\r\n<p>Je vous remercie de votre volonté de donner votre corps à des fins d’enseignement et de recherche.</p>\r\n\r\n<p>Nous allons vous adresser très prochainement votre dossier d'inscription par voie postale.</p>\r\n\r\n<p>Vous pouvez consulter notre site web : <strong><a href=\"https://dons-corps.univ-tours.fr\">{domain.description}</a>&nbsp;</strong>où vous<strong>&nbsp;</strong>trouverez de nombreuses informations et des réponses aux questions que vous pourriez vous poser.</p>\r\n\r\n<p>Je vous remercie à nouveau de votre intérêt pour le don du corps et vous prie d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de ma parfaite considération.</p>\r\n\r\n<p>{domain.supplemental_address_3}<br />\r\nCentre d'accueil des corps de {domain.city}</p>",
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'MessageTemplate',
    'values' => [
      'msg_title' => '010 - Inscription : Mail confirmation inscription',
      'msg_subject' => "Confirmation inscription Centre d'accueil des corps",
      'msg_html' => "<p>{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</p>\r\n\r\n<p>Nous vous remercions de votre demande d'inscription à notre centre d'accueil des corps, dont nous accusons réception.<br />\r\nVotre numéro d'inscription est le {Tokens_for_contact_Champs_de_fu.Promesse_de_don.N_de_don}.<br />\r\nVotre carte définitive et les documents relatifs à votre inscription vous parviendront par courrier dans les semaines qui viennent.<br />\r\nNous vous prions de bien vouloir excuser ce délai, dû à un grand nombre de demandes.</p>\r\n\r\n<p><strong>Votre inscription est néanmoins effective dès aujourd'hui et nous vous engageons à conserver une copie de ce message avec vos papiers d'identité</strong>.</p>\r\n\r\n<p>Nous restons à votre disposition pour tout renseignement complémentaire et vous prions d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de notre parfaite considération.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>{domain.supplemental_address_3}<br />\r\nCentre d'accueil des corps de {domain.city}</p>",
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'MessageTemplate',
    'values' => [
      'msg_title' => '300 - Cérémonie invitation (email)',
      'msg_subject' => "Cérémonie d'hommage {event.start_date} à {event.start_date|crmDate:\"Time\"}",
      'msg_html' => "<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous avons l'honneur de vous inviter à la cérémonie organisée par le Centre d'accueil des corps de {domain.city} en l'honneur des donneurs et de leurs proches.<br />\r\nElle aura lieu le<strong> {event.start_date} à {event.start_date|crmDate:\"Time\"} au&nbsp;</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><strong>{event.location}</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Cette manifestation, d'une durée d'environ deux heures, sera l'occasion de nous recueillir en mémoire des personnes qui, comme votre proche, ont donné récemment leur corps à des fins d'enseignement médical et de recherche.<br />\r\nMerci de nous indiquer par retour de mail si vous souhaitez y participer et de nous communiquer le nombre de personnes présentes.</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous restons à votre disposition pour tout renseignement complémentaire et vous prions d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de notre parfaite considération.</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"2\">{domain.supplemental_address_3}<br />\r\n{domain.supplemental_address_2}<br />\r\ndu centre d'accueil des corps de {domain.city}</font></p>",
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'MessageTemplate',
    'values' => [
      'msg_title' => '310 - Cérémonie confirmation  (email)',
      'msg_subject' => "Cérémonie d'hommage {event.start_date} à {event.start_date|crmDate:\"Time\"}",
      'msg_html' => "<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous vous confirmons votre inscription à la cérémonie d'hommage aux donneurs et à leurs proches qui débutera le<strong> {event.start_date} à {event.start_date|crmDate:\"Time\"} au&nbsp;</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><strong>{event.location}</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Merci de vous présenter au funérarium 15 minutes avant le début de la cérémonie et de respecter le nombre de personnes que vous nous avez communiqué.</font></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous restons à votre disposition pour tout renseignement complémentaire et vous prions d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de notre parfaite considération.</font></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"2\">{domain.supplemental_address_3}<br />\r\n{domain.supplemental_address_2}<br />\r\ndu Centre de Don du Corps de {domain.city}</font></font></p>",
      'is_active' => TRUE,
    ],
  ],
  [
    'entity' => 'MessageTemplate',
    'values' => [
      'msg_title' => '320 - Cérémonie non inscription  (email)',
      'msg_subject' => "Cérémonie d'hommage {event.start_date} à {event.start_date|crmDate:\"Time\"}",
      'msg_html' => "<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">{Tokens_for_contact_Champs_de_fu.postal_greeting_id:label},</font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous avons bien noté que vous ne participerez pas à la cérémonie d'hommage aux donneurs et à leurs proches du<strong> {event.start_date}.</strong></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"3\">Nous vous prions d'agréer, {Tokens_for_contact_Champs_de_fu.postal_greeting_id:label}, l'expression de notre parfaite considération.</font></font></p>\r\n\r\n<p><font face=\"Arial, Verdana, sans-serif\" size=\"3\"><font face=\"Arial, Verdana, sans-serif\" size=\"2\">{domain.supplemental_address_3}<br />\r\n{domain.supplemental_address_2}<br />\r\ndu Centre de Don du Corps de {domain.city}</font></font></p>",
      'is_active' => TRUE,
    ],
  ],
];