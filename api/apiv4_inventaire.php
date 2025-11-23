<?php
// Perform bootstrap : gets civicrm parameters
// Needs CV to be installed ; see https://docs.civicrm.org/installation/en/latest/general/cli-cv/
eval(`cv php:boot`);



// Cree la table inventaire quicontinet les données du fichier csv



#var_dump($argv[1]);

function get_data_help ()
{
echo " "."\n";
echo "Usage : api4_inventaire inventaire.csv [-l -i Loc_Date_Operateur]"."\n";;
echo "	inventaire.csv : fichier inventaire à utiliser"."\n";;
echo "	-l : liste les inventaires présents dans ce fichier"."\n";;
echo "	-i : traite l'inventaire Loc_Date_Operateur"."\n";;
echo " "."\n";
exit;
}


function get_data ()
{
$file = func_get_arg(0);
unset($inventaire);

$row = 1;

if (($handle = fopen($file, "r")) !== FALSE) {

    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $num = count($data);
        for ($c=0; $c < $num; $c++) {
			if($row>1){
				$inventaire[$row-2][$c]=$data[$c];
			}


        }


        $oper = civicrm_api4('Contact', 'get', [   	// recupere le nom de famille de l'opérateur
  			'select' => [
    			'last_name',
  				],
  			'where' => [
    			['id', '=', $data[0]],
  				],
  			'checkPermissions' => FALSE,
		]);

		$lieu = civicrm_api4('Contact', 'get', [	// recupere le lieu
  			'select' => [
    			'sort_name',
  				],
  			'where' => [
    			['id', '=', $data[1]],
  				],
  			'checkPermissions' => FALSE,
		]);


    	$inventaire[$row-2][$c]=$lieu[0]['sort_name']."_".$data['3']."_".$oper[0]['last_name'];  // identifiant de l'inventaire
        $row++;
    }
    fclose($handle);
}

return $inventaire;
}

$numargs = count($argv); // nombre d'arguments passés incluant la commande


if($numargs<3){								// absence d'argument ou seulement un argument
	get_data_help ();
}

if(!file_exists($argv[1])){  				// fichier inventaire inexistant
	echo "Le fichier inventaire : ".$argv[1]." n'existe pas"."\n";
	get_data_help ();
}else
{
$file=$argv[1];
}


for ($i = 2; $i < $numargs; $i++) {
	$arg = $argv[$i];
#	echo $i." ".$arg."\n";

	switch ($arg) {
        case '-l':
        	echo "------"."\n";
            echo "Inventaires contenus dans ce fichier : "."\n";

            $inventaire=get_data($file);								// table inventaire contient les données du fichier inventaire.csv + une col concatenant lieu_date_ope
            $inventaire_name = array_column($inventaire, '5');			// ne garde que la colonne de concatenation
			$inventaire_name_sing = array_unique($inventaire_name);		// ne garde que les valeurs uniques apres concaténation
#			print_r($inventaire_name_sing);
#			print_r($inventaire);

			$indices=array_keys($inventaire_name_sing);

			foreach($indices as $indice){								// affiche la liste des inventaires et leur indice
				if(($indice!='0'))
				{
				echo "(".$indice.") : ".$inventaire_name_sing[$indice]."\n";
				}
				}

         break;

         case '-i':
         	$inventaire=get_data($file);								// table inventaire contient les données du fichier inventaire.csv + une col concatenant lieu_date_ope
         	$inv_to_process = $argv[$i+1];
         	$inventaire_name = array_column($inventaire, '5');			// ne garde que la colonne de concatenation
			$inventaire_name_sing = array_unique($inventaire_name);		// ne garde que les valeurs uniques apres concaténation
			$indices=array_keys($inventaire_name_sing);					// liste les seuls indices

#			print_r($indices);
#			print_r($inventaire_name_sing);

			if (($inv_to_process!=0) and ($inventaire_name_sing[$inv_to_process]!= NULL))
				{
				$inv_to_process = $inv_to_process -1;
				$name = $inventaire[$inv_to_process][5];
				$operateur = $inventaire[$inv_to_process][0];
				$lieu =  $inventaire[$inv_to_process][1];
				$date = $inventaire[$inv_to_process][3];


				$oper_n = civicrm_api4('Contact', 'get', [   	// recupere le nom de famille de l'opérateur
  					'select' => [
    				'last_name',
  						],
  					'where' => [
    				['id', '=', $operateur],
    				['contact_sub_type', '=', 'Personnel'],
  					],
  				'checkPermissions' => FALSE,
				]);
				$oper_name=$oper_n[0]['last_name'];

				$lieu_n = civicrm_api4('Contact', 'get', [	// recupere le lieu
  					'select' => [
    				'sort_name',
  					],
  				'where' => [
    				['id', '=', $lieu],
    				['contact_sub_type', '=', 'Emprunteur'],
  					],
  				'checkPermissions' => FALSE,
				]);
				$lieu_name=$lieu_n[0]['sort_name'];

				if($oper_name==NULL){				// verifie que l'opérateur existe
					echo "------"."\n";
					echo "Operateur incorrect : ".$operateur."\n";
					get_data_help ();
				}

				if($lieu_name==NULL){				// verifie que le lieu existe
					echo "------"."\n";
					echo "Lieu incorrect : ".$operateur."\n";
					get_data_help ();
				}

#				$name='inventaire_test';

				$optionValues = civicrm_api4('OptionValue', 'get', [
  					'where' => [
   					['option_group_id:name', '=', 'Utilisation_du_corps_Inventaires'],
    				['label', '=', $name],
  						],
 					 'checkPermissions' => FALSE,
				]);

				$inventaire_value=$optionValues[0]['value'];							// valeur de l'inventaire à supprimer dans les pieces

#echo $name."\n";
#print_r($optionValues[0]);

				if($optionValues[0]['label']!=NULL){									// si l'inventaire existe,
					echo "supprime inventaire : ".$name."\n";
					$results2 = civicrm_api4('OptionValue', 'delete', [					// supprime l'inventaire
 					 'where' => [
  				  	  ['label', '=', $name],
 								 ],
 					 'checkPermissions' => FALSE,
						]);

				$utilisationDuCorpses2 = civicrm_api4('Custom_Utilisation_du_corps', 'get', [	// recupere les anciennes valeurs,
					'select' => [
				  	'id',
				  	'N_de_pi_ce_ou_de_corps',
					'Inventaires',
						],
 					'where' => [
						['Inventaires', '!=', NULL],
						],
						  'checkPermissions' => FALSE,
						]);


				foreach ($utilisationDuCorpses2 as $piece2){
#					echo $piece2['id']."\n";
#					var_dump($piece2['Inventaires']);
					$j=0;
					foreach($piece2['Inventaires'] as $inventaires_indiv){
#						echo $j." ".$inventaires_indiv."\n";

						if ($inventaires_indiv==$inventaire_value){
#							echo "supprime ".$j." ".$inventaires_indiv."\n";
							unset ($piece2['Inventaires'][$j]);
						}
						$j++;
					}


#					echo $piece2['id']."\n";
#					var_dump ($piece2['Inventaires']);

					$suppr = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
						 	 'values' => [
						 	   'Inventaires' => $piece2['Inventaires'],
							  ],
							  'where' => [
							    ['id', '=', $piece2['id']],
								  ],
 							  'checkPermissions' => FALSE,
							 	]);

				}



				}

					echo "------"."\n";
					echo "Crée/met à jour l'inventaire : ".$name."\n";
					echo "opérateur : ".$oper_name." (".$operateur.")"."\n";
					echo "lieu : ".$lieu_name." (".$lieu.")"."\n";
					echo "date : ".$date."\n";


				$results = civicrm_api4('OptionValue', 'create', [						//  crée l'inventaire
					'values' => [
				    'option_group_id.name' => 'Utilisation_du_corps_Inventaires',
                    'label' => $name,
					  ],
					'checkPermissions' => FALSE,
					  ]);
				$inventaire_value=$results[0][value];
				echo "inventaire : ".$inventaire_value."\n";
				echo "------"."\n";





				foreach($inventaire as $invent){														//  attribue la valeur inventaire aux pieces
					if ($invent[5] == $name){
#						echo $invent[0]." ".$invent[1]." ".$invent[2]." ".$invent[3]." ".$invent[5]."\n";

						$utilisationDuCorpses = civicrm_api4('Custom_Utilisation_du_corps', 'get', [	// recupere les anciennes valeurs,
						  'select' => [
				  			'id',
				  			'N_de_pi_ce_ou_de_corps',
							'Inventaires',
						  ],
						  'where' => [
							['N_de_pi_ce_ou_de_corps', '=', $invent[2]],
						  ],
						  'checkPermissions' => FALSE,
						]);


						if($utilisationDuCorpses[0]['id']==NULL){									// si la pièce n'existe pas : le signale
							echo $invent[2].": n'existe pas dans la base de données !!!!!!"."\n";
						}else																		// si la piece existe modifie inventaire
						{
							$piece_id=$utilisationDuCorpses[0]['id'];								// id de la piece
							$inventaires_list=$utilisationDuCorpses[0]['Inventaires'];				// liste initiale des inventaires pour cette piece
#							var_dump($inventaires_list);
							array_push($inventaires_list,$inventaire_value);						// nouvelle liste des inventaires pour cette piece
#							var_dump($inventaires_list);


							echo $invent[2].": ajoute l'inventaire ".$name."\n";
							$results = civicrm_api4('Custom_Utilisation_du_corps', 'update', [
						 	 'values' => [
						 	   'Inventaires' => $inventaires_list,
							  ],
							  'where' => [
							    ['id', '=', $piece_id],
								  ],
							  'checkPermissions' => FALSE,
							 	]);




						}




					}
				}




				} // fin 'if (($inv_to_process!=0) and ($inventaire_name_sing[$inv_to_process]!= NULL))'



			else{
				echo "------"."\n";
				echo "numéro d'inventaire invalide : ".$inv_to_process."\n";
				get_data_help ();
				}

         break;

} // fin switch
} // fin boucle for
