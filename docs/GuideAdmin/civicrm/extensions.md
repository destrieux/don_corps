# Installation des extensions de CiviCRM
L'extension don_corps nécéssite l'installation préalable des extensions suivantes :
    
Extension | Rôle
----------|-----
de.systopia.civioffice | Génére des documents word et pdf depuis de modèles word
org.civicoop.civirules | Déclenche des actions si certaines règles sont réunies
org.civicoop.emailapi | permet d'envoyer des courriels si certaines règles sont réunies
org.civicoop.civiruleslogger | Loggue les actions déclenchées par les règles
org.civicoop.documents | Gère les documents téléchargés (mini GED)
org.civicrm.afform_admin | Génère les formulaires de recherche à partir de requetes enregistrées
org.civicrm.contactlayout | Gère les grilles d'affichage des contact
de.systopia.eventmessages | Permet l'envoi de courriels relatifs aux évenements (cérémonies...) 
sktokens | Crée des tokens depuis la base de données, utilisables dans les messages 

> Pour le moment l'extension [FrenchCodesPostaux](https://github.com/allinappli/frenchCodePost-civicrm) qui utilise l'API de la poste pour compléter les adresses est incompatible avec org.civicrm.contactlayout

Installation depuis un paquet préparé pour le don du corps
les extensions sont comprises dans l'archives ; passez directement au paramétrage de CiviOffice

Installation depuis des paquets séparés
Dans CiviCRM > administration > paramètres système > gérer extensions
Verifier que les extensions suivantes sont installées :
Contact Layout Editor(personalisation des écrans de saisie) à chercher dans onglet ajouter
Contribution cancel actions (defaut)
Documents (gestion documents)
Fuzion Tokens (gère les jetons pour insertions automatiques dans les courriers)
CiviOffice (utilise des modèles word pour la correspondance)
civi_ddc (outils pour la gstion du don de corps - en développement)
Installer manuellement French Codes postaux (complétion auto des adresses via API La Poste)
Récupérer extension depuis Download the extension package https://github.com/allinappli/frenchCodePost-civicrm (inutilise si vous avez utilisé l'archive d'installation incuant les extensions; passez alors à l'étape suivante)
Utiliser une version >=1.1 les antérieures ne marchent pas
cp frenchcodepostaux-X.X.zip /var/www/html/ddctest/wp-content/uploads/civicrm/ext/
cd /var/www/html/ddctest/wp-content/uploads/civicrm/ext/
unzip frenchcodepostaux-X.X.zip
chown -R www-data:www-data frenchcodepostaux-X.X

Rechargez la page des extensions et cliquez sur installer sur FrenchCodesPostaux qui est apparue
installer manuellement les paquets suivants :
Related tokens (https://civicrm.org/extensions/related-tokens): pour récupérer les informations des personnes référentes ;
More Greetings Options (https://civicrm.org/extensions/more-greetings-personalised-strings-all-contacts): pour créer des civilités complexes par exemple extraire la civilité depuis le genre
Dompdf-fonts (https://github.com/eileenmcnaughton/dompdf-fonts): pour imprimer correctement les docs html en pdf
Civirules (https://civicrm.org/fr/extensions/civirules): pour parametrer les purges
CiviCRM Configuration Loader (https://docs.civicrm.org/configitems/en/latest/): pour exporter/importer configurations