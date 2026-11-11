<?php
// Civi/DonCorps/WPUsers/WPPriviledges.php
namespace Civi\DonCorps\WPUsers; 
use CRM_DonCorps_ExtensionUtil as E;

class WPPriviledges {        // Nom de la classe
    public static function ExportCaps(string $export_caps_file): bool {   
        $export_caps_file = \CRM_DonCorps_ExtensionUtil::path('managed/'.$export_caps_file);
        $msg="Export des privilèges civicrm selon le rôle wordpress dans : ".$export_caps_file;
        \Civi::log()->info("don_corps : ".$msg); 
        
        if (!function_exists('get_role')) {
            \Civi::log()->warning("don_corps : WordPress n'est pas chargé, permissions non appliquées");
            return FALSE;
        }

        $users = [   // rôles à exporter
                'editor',
                'author',
                'contributor',
                'subscriber',
                'anonymous_user'
        ];

        $echecs = 0;
        try{
            # Récupère toutes les permissions de civirm et conversion au format WP (minuscules sans espaces)
            $permissions = \Civi\Api4\Permission::get(FALSE)->addSelect('name')->execute()->column('name');
            $caps = array_map(fn($p) => \CRM_Utils_String::munge(strtolower($p)), $permissions);

            $perms=[]; // tableau contenant les permissions par rôle
            
            foreach($users as $user){
                # Récupère les permissions WP pour un rôle donné
                $role = get_role($user);
                # Croise les 2 pour ne garder que les permissions accordées à l'utilisateur dans civicrm
                
                $civi = array_filter(array_keys($role->capabilities), fn($c) => in_array($c, $caps, TRUE));
                sort($civi);
                $perms[] = ['role' => $user, 'caps' => $civi];
            } 
            
            file_put_contents($export_caps_file, json_encode($perms, JSON_PRETTY_PRINT));

        } catch (\Throwable $e) {
                $echecs++;
                \Civi::log()->error('don_corps : échec export privilèges ' . ($value['sort_name'] ?? '?') . ' : ' . $e->getMessage());
        } 
        return TRUE;
     }

    public static function ModifyCaps(string $toimport_file): bool {   
        $msg="Modification des privilèges civicrm selon le rôle wordpress";
        \Civi::log()->info("don_corps : ".$msg); 
        
        $toimport_file = \CRM_DonCorps_ExtensionUtil::path('managed/'.$toimport_file);

        if (!file_exists($toimport_file)) {
        \Civi::log()->warning("don_corps : fichier introuvable ($toimport_file), permissions civicrm non modifiées");
        return FALSE;
        }

        $values = json_decode(file_get_contents($toimport_file), TRUE);
        if (!is_array($values)) {
        \Civi::log()->error('don_corps : fichier illisible, permissions civicrm non modifiées : ' . json_last_error_msg());
        return FALSE;
        }

        // Toutes permissions de civicrm
        $permissions = \Civi\Api4\Permission::get(FALSE)->addSelect('name')->execute()->column('name');


        //print_r($values);


        $echecs=0;     
        try {
            foreach($values as $value){

                ## On ne modifie pas les permissions de l'admin
                if ($value['role']=='administrator'){
                    \Civi::log()->warning('don_corps : rôle : ' . $value['role']." - permissions non modifiées pour ce rôle");
                    continue;
                }

                $rolename = get_role($value['role']);

                if (!$rolename){
                    \Civi::log()->warning('don_corps : rôle : ' . $value['role']." n'existe pas - permissions non modifiées pour ce rôle");
                    continue;
                }
                ## On supprime toutes les permissions civicrm du rôle
                foreach ($permissions as $permission) {
                    $perm = \CRM_Utils_String::munge(strtolower($permission));
                    //echo $perm.PHP_EOL;

                    if ($rolename->has_cap($perm)) {
                        $rolename->remove_cap($perm);
                        //\Civi::log()->warning("don_corps : Rôle : ".$value['role']." -> Privilège retiré : ".$perm);
                    }
                }

                ## On ajoute les permissions civicrm du rôle
                foreach ($value['caps'] as $cap){
                    $rolename->add_cap($cap);
                }

            }

        } catch (\Throwable $e) {
                $echecs++;
                \Civi::log()->error('don_corps : échec modification privilèges ' . ($value['sort_name'] ?? '?') . ' : ' . $e->getMessage());
        } 
        return TRUE;
     }
}