# Téléchargement des extensions
L'extension don_corps nécéssite le téléchargement préalable des extensions suivantes :
    
Extension | Rôle
----------|-----
[de.systopia.civioffice](https://github.com/systopia/de.systopia.civioffice/archive/refs/heads/master.zip) | Génére des documents word et pdf depuis de modèles word
[org.civicoop.civirules](https://lab.civicrm.org/extensions/civirules/-/archive/master/civirules-master.zip?ref_type=heads) | Déclenche des actions si certaines règles sont réunies
[org.civicoop.emailapi](https://lab.civicrm.org/extensions/emailapi/-/archive/master/emailapi-master.zip?ref_type=heads) | permet d'envoyer des courriels si certaines règles sont réunies
[org.civicoop.civiruleslogger](https://github.com/CiviCooP/org.civicoop.civiruleslogger/archive/refs/heads/master.zip) | Loggue les actions déclenchées par les règles
[org.civicoop.documents](https://lab.civicrm.org/extensions/documents/-/archive/master/documents-master.zip?ref_type=heads) | Gère les documents téléchargés (mini GED)
org.civicrm.afform_admin | Génère les formulaires de recherche à partir de requetes enregistrées <br> (déja téléchargée)
[org.civicrm.contactlayout](https://lab.civicrm.org/extensions/contactlayout/-/archive/master/contactlayout-master.zip?ref_type=heads) | Gère les grilles d'affichage des contact
[de.systopia.eventmessages](https://github.com/systopia/de.systopia.eventmessages/archive/refs/heads/master.zip) | Permet l'envoi de courriels relatifs aux évenements (cérémonies...) 
[sktokens](https://lab.civicrm.org/extensions/sktokens/-/archive/main/sktokens-main.zip?ref_type=heads) | Crée des tokens depuis la base de données, utilisables dans les messages 

> Pour le moment l'extension [FrenchCodesPostaux](https://github.com/allinappli/frenchCodePost-civicrm) qui utilise l'API de la poste pour compléter les adresses est incompatible avec org.civicrm.contactlayout

## Téléchargement des extensions 
* Vous pouvez télécharger la derniere version de chacune des extensions en suivant les liens du tableau ci-dessus. <br>
Chaque extension doit être téléchargée et dézippée dans le dossier : <br>
```/var/www/html/ddctest/wp-content/plugins/civicrm/civicrm/ext/```<br>
* Vous pouvez aussi utiliser une archive contenant les extensions nécéssaires en une fois <br>
L'archive doit être téléchargée et dézippée dans le dossier : <br>
```/var/www/html/ddctest/wp-content/plugins/civicrm/civicrm/ext/```<br>

> Il n'est pas nécéssaire d'installer les extensions à ce stade mais de seumeent les télécharger ; <br> elles s'installeront en même temps que l'extension don_corps