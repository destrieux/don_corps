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

   \Civi::log()->info('don_corps : ' . count($values) . ' organisations à importer');
 
   //var_dump($values);
    // ... traitement de $values ...

    $echecs=0;
    $domain = \Civi\Api4\Domain::get(FALSE)
        ->addSelect('contact_id', 'name')
        ->addWhere('id', '=', \CRM_Core_Config::domainID())
        ->execute()
        ->first();

    $organisationId = $domain['contact_id'] ?? NULL;
    echo $organisationId;  // id du centre par défaut

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
                // on stocke le numero de siret aussi dans external_identifier pour réconcilier les les adresses, tel..


            $contact = \Civi\Api4\Contact::get(FALSE) // on recheche contacts avec le meme siret
                ->addSelect('id','is_deleted')
                ->addWhere('legal_identifier', '=', $value['legal_identifier'])
                ->addWhere('is_deleted', 'IN', ['1', '0']) // on regarde aussi dans la corbeille
                ->addWhere('id','!=',$organisationId)       // on exclue le CDC par defaut pour ne pas écraser ses données
                ->execute()
                ->first();

                       \Civi::log()->info("don_corps : ".$contact['id']);
             

                if(!$contact['id']==$organisationId){ // si le CDC est le CDC par defaut on ne fait rien
                    $msg =  "CDC par défaut non modifié :".$value['sort_name'];
                    \Civi::log()->info("don_corps : ".$msg);
                    continue;

                }
                
                if(!$contact){ // si le CDC n'existe pas : création
                    \Civi\Api4\Contact::create(FALSE)->setValues($value)->execute();
                    $msg = "Création CDC :".$value['sort_name'];
                    \Civi::log()->info("don_corps : ".$msg);
                    continue;

                }
                 // si le contact existe on l'update. ; si dans la corbeille on restaure/update
                    $update = \Civi\Api4\Contact::update(FALSE)
                        ->setValues($value)
                        ->addWhere('id', '=', $contact['id'])
                        ->addValue('is_deleted', FALSE)
                        ->execute();
                    $msg = "Mise à jour / Restauration CDC :".$value['sort_name'];
                    \Civi::log()->info("don_corps : ".$msg);
                
            } catch (\Throwable $e) {
                $echecs++;
                \Civi::log()->error('don_corps : échec import CDC ' . ($value['sort_name'] ?? '?') . ' : ' . $e->getMessage());
            }                                  
        }

    return $echecs === 0; // si echec est strictement 0 : retourne TRUE sinon FALSE
  }

}

