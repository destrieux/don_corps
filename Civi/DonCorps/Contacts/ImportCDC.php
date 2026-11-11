<?php
// Civi/DonCorps/Contacts/ImportCDC.php
namespace Civi\DonCorps\Contacts; //Le nom complet de la classe est \Civi\DonCorps\Contacts\ImportCDC.php, 
                                 //et il correspond au fichier Civi/DonCorps/Contacts/ImportCDC.php.

class ImportCDC {        // Nom de la classe
  /**
   * Importe les CDC, leurs adresses, telephone et mail à partir de fichiers situés dans managed/
   * Invoquée à l'installation et à la mise à jour en cas de modification de ces coordonnées
   * @return bool TRUE si les contacts ont été crées ou mis à jour avec succes 
   */

    public static function ImportMain(string $toimport_file): bool { 
      // public : fonction appellable depuis n'importe ou 
      // static : on l'appelle directement sur la classe
      // string $importFile : 1er parametre = nom du ficher texte contenant les data à importer
      // bool : type de retour : la méthode renvoie true ou false
        $msg=" Ajout des centres de don du corps";
        \Civi::log()->info("don_corps : ".$msg);

        $toimport_file = \CRM_DonCorps_ExtensionUtil::path('managed/'.$toimport_file);

        if (!file_exists($toimport_file)) {
        \Civi::log()->warning("don_corps : fichier d'import introuvable ($toimport_file), CDC non importés");
        return FALSE;
        }

        $values = json_decode(file_get_contents($toimport_file), TRUE);
        if (!is_array($values)) {
        \Civi::log()->error('don_corps : fichier d\'import illisible : ' . json_last_error_msg());
        return FALSE;
        }

        //\Civi::log()->info('don_corps : ' . count($values) . ' organisations à importer');
 
        $echecs=0;

        $domain = \Civi\Api4\Domain::get(FALSE)
            ->addSelect('contact_id', 'name')
            ->addWhere('id', '=', \CRM_Core_Config::domainID())
            ->execute()
            ->first();

        $organisationId = $domain['contact_id'] ?? NULL; // id du centre par défaut À NE PAS MODIFIER
        foreach ($values as $value) {

            try {
                if (empty($value['legal_identifier'])) {
                throw new \RuntimeException('legal_identifier absent');
                }

                unset ($value['id']);
                unset ($value['hash']);
                unset ($value['email_greeting_id']);
                unset ($value['email_greeting_custom']);
                unset ($value['email_greeting_display']);

                unset ($value['postal_greeting_id']);
                unset ($value['postal_greeting_custom']);
                unset ($value['postal_greeting_display']);

                unset ($value['suffix_id']);
                unset ($value['communication_style_id']);

                unset ($value['prefix_id']);
                $value['gender_id']=NULL;
                $value['addressee_id']=1;
                $value['external_identifier']=$value['legal_identifier'];
                
                //print_r($value);
                    // on stocke le numero de siret aussi dans external_identifier pour réconcilier les les adresses, tel..

                $contacts = \Civi\Api4\Contact::get(FALSE) // on recheche contacts avec le meme siret
                    ->addSelect('id','is_deleted')
                    ->addClause('OR', ['legal_identifier', '=', $value['legal_identifier']], ['external_identifier', '=', $value['legal_identifier']])
                    ->addWhere('is_deleted', 'IN', [TRUE, FALSE]) // on regarde aussi dans la corbeille
                    ->addOrderBy('is_deleted', 'ASC')            // on présente en premier les contacts quine sont pas ds corbeille
                    ->execute();

                //print_r($contacts);

                if(!isset($contacts[0]['id'])){ // si le CDC n'existe pas : création
                        \Civi\Api4\Contact::create(FALSE)->setValues($value)->execute();
                        //$msg = "Création CDC :".$value['sort_name'];
                        //\Civi::log()->info("don_corps : ".$msg);
                        continue;
                    }
                            // si un ou plusieurs contacts existent
                foreach ($contacts as $contact){             
                    if($contact['is_deleted']==TRUE){ // si le CDC est dans la corbeille on ne fait rien
                        $msg =  "CDC dans la corbeille non modifié :".$value['sort_name'];
                        \Civi::log()->info("don_corps : ".$msg);
                        continue;
                    }

                    if($contact['id']==$organisationId){ // si le CDC est le CDC par defaut on ne fait rien
                        $msg =  "CDC par défaut non modifié :".$value['sort_name'];
                        \Civi::log()->info("don_corps : ".$msg);
                        continue;
                    }

                    // dans les autres cas : MAJ
                        $update = \Civi\Api4\Contact::update(FALSE)
                            ->setValues($value)
                            ->addWhere('id', '=', $contact['id'])
                            ->execute();
                        //$msg = "Mise à jour CDC :".$value['sort_name'];
                        //\Civi::log()->info("don_corps : ".$msg);

                    }

                } catch (\Throwable $e) {
                    $echecs++;
                    \Civi::log()->error('don_corps : échec import CDC ' . ($value['sort_name'] ?? '?') . ' : ' . $e->getMessage());
                }                                  
        }

            return $echecs === 0; // si echec est strictement 0 : retourne TRUE sinon FALSE
    }

    public static function ImportTel(string $toimport_file): bool {
      // public : fonction appellable depuis n'importe ou 
      // static : on l'appelle directement sur la classe
      // string $importFile : 1er parametre = nom du ficher texte contenant les data à importer
      // bool : type de retour : la méthode renvoie true ou false

        $msg=" Ajout des téléphones des centres de don du corps";
        \Civi::log()->info("don_corps : ".$msg);
        $toimport_file = \CRM_DonCorps_ExtensionUtil::path('managed/'.$toimport_file);

        if (!file_exists($toimport_file)) {
        \Civi::log()->warning("don_corps : fichier d'import introuvable ($toimport_file), téléphones non importés");
        return FALSE;
        }

        $values = json_decode(file_get_contents($toimport_file), TRUE);
        if (!is_array($values)) {
        \Civi::log()->error('don_corps : fichier d\'import illisible : ' . json_last_error_msg());
        return FALSE;
        }

        //\Civi::log()->info('don_corps : ' . count($values) . ' téléphones à importer');
 
        $echecs=0;

        //print_r($values);

        foreach ($values as $value) {
            try {
          // on verifie que le CDC rattaché au téléphone existe bien
          // i.e., avec le meme numéro de SIRET (legal_identifier)
            //print_r($value);

            $contact = \Civi\Api4\Contact::get(FALSE)
                ->addSelect('id')
                ->addClause('OR', ['external_identifier', '=', $value['contact_id.legal_identifier']])
                ->addWhere('is_deleted', '=', FALSE)
                ->execute()
                ->first();

            if(!$contact){
                $msg = "Pas de CDC avec external identifier ".$value['contact_id.legal_identifier']." - Tel non importé";
                \Civi::log()->info("don_corps : ".$msg);
                continue ;
            }

            $phone = \Civi\Api4\Phone::get(FALSE) // on recherche si un téléphone identique existe pour ce contact
                ->addSelect('id')
                ->addWhere('contact_id', '=', $contact['id'])
                ->addWhere('phone', '=', $value['phone'])
                ->execute()
                ->first();  
                
            if ($phone) {   // si un téléphone identique existe pour ce contact : on ne fait rien
                //$msg= "Ce téléphone : ".$value['phone']." existe déja pour CDC id ".$contact['id']." -> Non modifié";
                //\Civi::log()->info("don_corps : ".$msg);
                continue ;
            }

            // Sinon : on le crée en le rattachant au contact retrouvé
            $value['contact_id'] = $contact['id'];
            unset($value['contact_id.legal_identifier']);
            //print_r($value);

            $results = \Civi\Api4\Phone::create(FALSE) ->setValues($value) ->execute();

            //$msg= "Création téléphone : ".$value['phone']." pour CDC id ".$value['contact_id'];
            //\Civi::log()->info("don_corps : ".$msg);

            } catch (\Throwable $e) {
                $echecs++;
                \Civi::log()->error('don_corps : échec import CDC ' . ($value['sort_name'] ?? '?') . ' : ' . $e->getMessage());
            } 
        }
        return $echecs === 0; // si echec est strictement 0 : retourne TRUE sinon FALSE  
    }

    public static function ImportEmail(string $toimport_file): bool {
      // public : fonction appellable depuis n'importe ou 
      // static : on l'appelle directement sur la classe
      // string $importFile : 1er parametre = nom du ficher texte contenant les data à importer
      // bool : type de retour : la méthode renvoie true ou false
        $msg=" Ajout des courriels des centres de don du corps";
        \Civi::log()->info("don_corps : ".$msg);

        $toimport_file = \CRM_DonCorps_ExtensionUtil::path('managed/'.$toimport_file);

        if (!file_exists($toimport_file)) {
        \Civi::log()->warning("don_corps : fichier d'import introuvable ($toimport_file), emails non importés");
        return FALSE;
        }

        $values = json_decode(file_get_contents($toimport_file), TRUE);
        if (!is_array($values)) {
        \Civi::log()->error('don_corps : fichier d\'import illisible : ' . json_last_error_msg());
        return FALSE;
        }

        //\Civi::log()->info('don_corps : ' . count($values) . ' emails à importer');
        $echecs=0;

        foreach ($values as $value) {
            try {
            // on verifie que le CDC rattaché au mail existe bien
            // i.e., avec le meme numéro de SIRET (legal_identifier)
            $contact = \Civi\Api4\Contact::get(FALSE)
                ->addSelect('id')
                ->addClause('OR', ['external_identifier', '=', $value['contact_id.legal_identifier']])
                ->addWhere('is_deleted', '=', FALSE)
                ->execute()
                ->first();

            if(!$contact){
                $msg = "Pas de CDC avec external identifier ".$value['contact_id.legal_identifier']." - Email non importé";
                \Civi::log()->info("don_corps : ".$msg);
                continue ;
            }

            $email = \Civi\Api4\Email::get(FALSE) // on recherche si un email identique existe pour ce contact
                ->addSelect('id')
                ->addWhere('contact_id', '=', $contact['id'])
                ->addWhere('email', '=', $value['email'])
                ->execute()
                ->first();  
                
            if ($email) {   // si un email identique existe pour ce contact : on ne fait rien
                //$msg= "Ce courriel : ".$value['email']." existe déja pour CDC id ".$contact['id']." -> Non modifié";
                //\Civi::log()->info("don_corps : ".$msg);
                continue ;
            }

            // Sinon : on le crée en le rattachant au contact retrouvé
            $value['contact_id'] = $contact['id'];
            unset($value['contact_id.legal_identifier']);

            $results = \Civi\Api4\Email::create(FALSE) ->setValues($value) ->execute();

            //$msg= "Création courriel : ".$value['email']." pour CDC id ".$value['contact_id'];
            //\Civi::log()->info("don_corps : ".$msg);
            
            } catch (\Throwable $e) {
                $echecs++;
                \Civi::log()->error('don_corps : échec import CDC ' . ($value['sort_name'] ?? '?') . ' : ' . $e->getMessage());
            } 
        }
        return $echecs === 0; // si echec est strictement 0 : retourne TRUE sinon FALSE  
    }

    public static function ImportAdresse(string $toimport_file): bool {
      // public : fonction appellable depuis n'importe ou 
      // static : on l'appelle directement sur la classe
      // string $importFile : 1er parametre = nom du ficher texte contenant les data à importer
      // bool : type de retour : la méthode renvoie true ou false
        $msg=" Ajout des adresses des centres de don du corps";
        \Civi::log()->info("don_corps : ".$msg);

        $toimport_file = \CRM_DonCorps_ExtensionUtil::path('managed/'.$toimport_file);

        if (!file_exists($toimport_file)) {
        \Civi::log()->warning("don_corps : fichier d'import introuvable ($toimport_file), adresses non importés");
        return FALSE;
        }

        $values = json_decode(file_get_contents($toimport_file), TRUE);
        if (!is_array($values)) {
        \Civi::log()->error('don_corps : fichier d\'import illisible : ' . json_last_error_msg());
        return FALSE;
        }

        //\Civi::log()->info('don_corps : ' . count($values) . ' adresses à importer');
        $echecs=0;
        foreach ($values as $value) {
            try {
                // on verifie que le CDC rattaché à l'addresse existe bien
                // i.e., avec le meme numéro de SIRET (legal_identifier)
                $contact = \Civi\Api4\Contact::get(FALSE)
                    ->addSelect('id')
                    ->addClause('OR', ['external_identifier', '=', $value['contact_id.legal_identifier']])
                    ->addWhere('is_deleted', '=', FALSE)
                    ->execute()
                    ->first();

                if(!$contact){
                    $msg = "Pas de CDC avec external identifier ".$value['contact_id.legal_identifier']." - Adresse non importée";
                    \Civi::log()->info("don_corps : ".$msg);
                    continue ;
                }

                $adresse = \Civi\Api4\Address::get(FALSE) // on recherche si une adresse  identique existe pour ce contact
                    ->addSelect('id')
                    ->addWhere('contact_id', '=', $contact['id'])
                    ->addClause('OR', ['street_address', '=', $value['street_address']], ['street_address', 'IS NULL'])
                    ->addClause('OR', ['postal_code', '=', $value['postal_code']], ['postal_code', 'IS NULL'])
                    ->addClause('OR', ['city', '=', $value['city']], ['city', 'IS NULL'])
                    ->execute()
                    ->first();  

                $value['contact_id'] = $contact['id'];
                unset($value['contact_id.legal_identifier']);

                if ($adresse) {   // si une adress identique existe pour ce contact : on la met à jour
                    //$msg= "Cette adresse : ".$value['street_address']." existe déja pour CDC id ".$contact['id']." -> Mise à jour";
                    //\Civi::log()->info("don_corps : ".$msg);
                    $results = \Civi\Api4\Address::update(FALSE) 
                        ->addWhere('id', '=', $adresse['id']) 
                        ->setValues($value) 
                        ->execute();
                    continue ;
                }
                //$msg= "Création de l'adresse : ".$value['street_address']." pour CDC id ".$contact['id'];
                //\Civi::log()->info("don_corps : ".$msg);
                $results = \Civi\Api4\Address::create(FALSE) ->setValues($value) ->execute();
                continue ;
            } catch (\Throwable $e) {
                $echecs++;
                \Civi::log()->error('don_corps : échec import CDC ' . ($value['sort_name'] ?? '?') . ' : ' . $e->getMessage());
            } 
        }
        return $echecs === 0; // si echec est strictement 0 : retourne TRUE sinon FALSE  
    }
}

