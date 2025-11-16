# Installer unoconv, les codes barres, cv et cli
## Unoconv
Unoconv est un convertisseur qui génère des pdf à partir de documents word. <br>
Il a besoin de libre office qui s'installe en même temps que lui.  <br>
```apt-get update``` <br>
```apt-get install unoconv``` <br>

Pour tester : ```unoconv``` retourne ; <br>
```unoconv: you have to provide a filename or url as argument```<br>
```Try unoconv -h for more information.``` <br>

## Police LibreBarcode39-Regular.ttf 
Elle sera utile pour imprimer des codes barres. <br>
Elle est téléchargeable depuis [Google Fonts](https://fonts.google.com/specimen/Libre+Barcode+39).

```cp LibreBarcode39-Regular.ttf /usr/local/share/fonts/``` <br>
```chmod 644 /usr/local/share/fonts/LibreBarcode39-Regular.ttf``` <br>
```fc-list | grep LibreBarcode39``` <br>

La dernière commande vérifie que la police est bien disponible ; elle doit retourner son chemin.

## cv
cv est une interface en ligne de commandes pour interragir avec CiviCRM. <br>
Son installation est indispensable à utilisation des API et à l'installation en ligne de commandes.

Vérifiez que cv n'est pas installé en tapant ```cv``` dans un terminal :

* Si cv n'est pas installé, récupérez [cv.phar](https://download.civicrm.org/cv/cv.phar) <br>
Installez le dans un répertoire inclu dans $PATH, par exemple :<br>
```sudo mv cv.phar /usr/local/bin/cv```<br>
```sudo chmod ug+x /usr/local/bin/cv``` <br>

* Si cv est installé, une aide va s'afficher

## Installer wp-cli
wp-cli est le langage de communication de CiviCRM avec Wordpress. Il est utilisé pour lancer des tâches programmées.

```sudo su``` <br>
```cd ```<br>
```curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar ``` <br>
```php wp-cli.phar --info (vérifie que tout est ok) ``` <br>
```mv wp-cli.phar /usr/local/bin/wp ``` <br>
```chmod ugo+x /usr/local/bin/wp``` <br>