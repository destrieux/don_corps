<?php

eval(`cv php:boot`);

  echo "Modification des profils personnalisés".PHP_EOL;
  
      install_UFGroup ('Centre_d_accueil_des_corps','Centres de don du corpsDDC','New CDC'); 
      install_UFGroup ('CESP_29');
      install_UFGroup ('D_mographie_animal'); 
      install_UFGroup ('Dates_naissance_et_d_c_s_17'); 
      install_UFGroup ('Demandeur_information_22','ContactsDDC','New Demandeur_d_informationDDC'); 
      install_UFGroup ('Employeur'); 
      install_UFGroup ('Inscription_anat_compar_e', 'ContactsDDC','Nouvelle pièce anat comparée'); 
      install_UFGroup ('Inscription_donateur','ContactsDDC','New DonateurDDC'); 
      install_UFGroup ('inscription_pompes', 'Pompes funebresDDC','New Pompes'); 
      install_UFGroup ('Inscription_proche_donateur_14','ContactsDDC','Ajouter proche donateurDDC');
      install_UFGroup ('Lieu_de_stockage', 'Pièces anatomiquesDDC','New Emprunteur'); 
      install_UFGroup ('Mairie', 'MairiesDDC','New Mairies'); 
      install_UFGroup ('Op_rations_fun_raires_r_alis_es_30');
      install_UFGroup ('Personnel_de_centre_de_don_de_corps', 'Centres de don du corpsDDC','New Personnel'); 
      install_UFGroup ('Profil_sans_nom_20'); // Adresse incorrecte OK
      install_UFGroup ('Restitution_28'); 
      install_UFGroup ('Type_de_contact_23'); 