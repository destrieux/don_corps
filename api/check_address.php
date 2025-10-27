<?php
eval(`cv php:boot`);
$exp_dir = '/Users/destri_c/Desktop/import/';       // racine du répertoire d'import export

function check_stuff(){
    $exp_dir = func_get_arg(0);                      // répertoire import export
    $name = func_get_arg(1);                        // nom de du fichier à vérifier

    $tochek_file = $exp_dir.$name.".txt";
    $json = file_get_contents($tochek_file);
    $existings = json_decode($json, true);           // variable contenant les données originales qui ont normalement été importées

    $chk_file = $exp_dir."check_".$name.".txt";
    $json = file_get_contents($chk_file);
    $check = json_decode($json, true);
    echo $name." : ".count($check)." lignes ont été importées sur ".count($existings);

    if (count($check)==count($existings)) {// le bon nombre de lignes ont été importées
        echo " ---> OK".PHP_EOL;
    } else {                               // discordance entre lignes importées et à importer
                                           // on vérifie les lignes qui n'ont pas été importées
        foreach ($existings as $existing){
            $key = array_search($existing['contact_id'],$check);
            //var_dump($key);
            if ($key == FALSE){
            echo PHP_EOL."ligne non importée pour old contact id : ".$existing['contact_id'].PHP_EOL;
            }else{
                echo ".";  
            }
        } 

    }

}


//check_stuff($exp_dir,'15_adresses');
//check_stuff($exp_dir,'20_telephone');
check_stuff($exp_dir,'25_Email');



   echo "on continue".PHP_EOL;



  