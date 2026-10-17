<?php
namespace Civi\DonCorps;

class UpdateSearchGroups {
    //////////// update_search_group() /////////
    // Cette fonction modifie le nom des groupes utilisés dans les requetes (par exemple Archives_64 en Archives_52)
    // Elle doit être invoquée en post installation
    //
    // syntaxe : update_search_archive($file, $group_title);
    //
    //      $file : nom du ficher mgd de la requete
    //      $group_title : titre du groupe (ex. Archives...) 
    //
    // elle est appelée par : function don_corps_civicrm_postinstall()
    //////////////


    public static function updateAll(): void {

        self::updateSearchGroup( // appelle une méthode de la meme classe
            'SavedSearch_Op_rations_fun_raires_de_plus_de_5_ans.mgd.php',
            'Archives',
            'Archives_'
        );

        self::updateSearchGroup(
            'SavedSearch_Donneurs_annul_s.mgd.php',
            'Archives',
            'Archives_'
        );

        self::updateSearchGroup(
            'SavedSearch_Archives_dans_protocole_in_ni_ex_vivo.mgd.php',
            'Archives sans protocole in ni ex vivo',
            'Archives_sans_protocole_in_ni_ex__'
        );

        self::updateSearchGroup(
            'SavedSearch_Archives_dans_protocole_in_ni_ex_vivo.mgd.php',
            'Archives',
            'Archives_'
        );

        self::updateSearchGroup(
            'SavedSearch_Donneurs_sans_PAQPF.mgd.php',
            'Archives',
            'Archives_'
        );

        self::updateSearchGroup(
            'SavedSearch_Donneurs_sans_PAQPF.mgd.php',
            'donneur avec PAQPF',
            'donneur_avec_P_'
        );

        self::updateSearchGroup(
            'SavedSearch_Donneurs_vivants_ano_ville_CP.mgd.php',
            'annulations (incluant anonymes)',
            'Annulation_'
        );
    }


    public static function updateSearchGroup(
        string $file,
        string $group_title,
        string $group_name_prefix
    ): void {

        $file = \Civi::paths()->getPath(
            '[civicrm.root]/ext/don_corps/managed/'
        ) . $file;

        //echo "Title : " . $group_title . PHP_EOL;
        //echo "Prefix : " . $group_name_prefix . PHP_EOL;

        $groups = civicrm_api4('Group', 'get', [
            'select' => [
                'name',
            ],
            'where' => [
                ['title', '=', $group_title],
                ['name', 'CONTAINS', $group_name_prefix],
            ],
            'checkPermissions' => FALSE,
        ]);


        if (!isset($groups[0])) {
            echo "Groupe introuvable avec title : "
                . $group_title
                . " et nom : "
                . $group_name_prefix
                . PHP_EOL;
            return;
        } else {
            $group_name_new = "['" . $groups[0]['name'] . "']";
            //echo $group_name_new.PHP_EOL;
        }


        if (!file_exists($file)) {
            echo "Fichier introuvable : " . $file . PHP_EOL;
            return;
        }

        $content = file_get_contents($file);

        if ($content === false) {
            echo "Impossible de lire le fichier : "
                . $file
                . PHP_EOL;
            return;
        }

        preg_match_all(
            "/" . preg_quote($group_name_prefix, '/') . "[0-9]+/",
            $content,
            $matches
        );

        if (empty($matches[0])) {
            echo "Nom de groupe original introuvable dans "
                . $file
                . PHP_EOL;
            return;
        }

        $group_name_orig = "['" . $matches[0][0] . "']";

        //echo "Orig : " . $group_name_orig . PHP_EOL;
        //echo "New  : " . $group_name_new . PHP_EOL;

        if ($group_name_orig === $group_name_new) {
            echo "  - Aucun remplacement nécessaire dans "
                . $file
                . PHP_EOL;
            return;
        }

        $backup = $file . '.bak';

        if (!copy($file, $backup)) {
            echo "  Impossible de créer la sauvegarde : "
                . $backup
                . PHP_EOL;
            return;
        }

        $content = str_replace(
            $group_name_orig,
            $group_name_new,
            $content
        );

        if (file_put_contents($file, $content) === false) {
            echo "  Impossible d'écrire le fichier : "
                . $file
                . PHP_EOL;
            return;
        }

        echo "  - Remplacement de "
            . $group_name_orig
            . " par "
            . $group_name_new
            . " effectué dans "
            . $file
            . PHP_EOL;

        echo "    Sauvegarde : " . $backup . PHP_EOL;
        echo PHP_EOL;
    }
}