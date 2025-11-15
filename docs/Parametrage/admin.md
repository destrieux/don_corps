# Paramètres à régler par l'Administrateur
## Organisation par défaut
Il s'agit de votre centre d'accueil des corps. Les informations entrées ici seront reprises dans les courriers.<br>
Allez à **Administrer > Communication > Adresse de l'organisation et infos de contact**

* **Nom de l'organisation** : nom complet de votre centre (ex. *Centre d'accueil des corps de XXX*),
* **Description** : site web de votre centre (ex. *https://dons-corps.univ-XXX.fr/*),
* **Rue, Complement d'adresse 1, Ville, Code Postal, Pays** : adresse de votre centre,
* **Complément d'adresse 2** : directeur du centre de don (ex. *Pr YYY, Directeur*),
* **Complément d'adresse 3** : gestionnaire du centre de don (ex. Mme ZZZ, Gestionaire),
* **Courriel** : courriel du centre de don,
* **Téléphone** : vous pouvez, si besoin, inscrire plusieurs numéros (ex. *02 22 22 22 22 ou 02 44 44 44 44*).

> Attention à bien respecter le contenu des champs **Description, Complement d'adresse 2 et 3**, sinon les courriers seront incompréhensibles.

## Paramètres de messagerie

### Adresses d'expédition
Vous pouvez définir ici plusieurs adresses d'expéditions (secrétarit, techniciens...).<br>
Il est conseillé à l'administrateur de créer des adresses fonctionnelles de messagerie et de les inscrire ici.

Allez à **Administrer > Communication > Site From email adresses** <br>
**Ajouter une adresse de courriel**

* **Nom affiché** : celui qui s'affichera lors du choix de l'expéditeur et dans les courriels,
* **Courriel** : adresse courriel,
* **Description** : texte libre,
* **Par défaut** : à cocher pour l'adresse que vous utiliserez par défaut.

### SMTP/Sendmail
Vous devez définir ici comment les mails seront envoyés.<br>
**Administrer > Paramètres Système > Courriers sortants (SMTP/Sendmail)**<br>
Modifiez les champs suivants si besoin : 

* **Simple mail limit** : c'est le nombre maximal de courriels à expédier en une fois. Ce nombre ne doit pas être trop élevé afin que votre site ne soit pas considéré comme envoyant du spam. Au dessus de ce nombre, les messages sont envoyés par paquets.
* **Configuration Mailer/SMTP** : à définir par l'administrateur

Il est possible d'envoyer un mais de test pour vérifier le paramétrage : <br> **Enregistrer et envoyer un courriel de test**