# Installation de l'extension don_corps
* Téléchargez l'extension [don_corps](https://github.com/destrieux/don_corps/archive/refs/heads/main.zip) <br>
Déplacez là et decompactez là dans : : <br>
```/var/www/html/ddctest/wp-content/plugins/civicrm/civicrm/ext/```<br>
* Raffraichissez CiviCRM<br>
```cd /var/www/html/ddctest/wp-content/plugins/civicrm/civicrm/ext/don_corps```<br>
```cv flush``` <br>
* Lancez l'installation<br>
```cv ext:enable don_corps``` <br>

> Ne pas installer en mode graphique car l'installation est longue (installation de don_corps et des autres extensions nécéssaire) et échouerait en raison d'un time-out.