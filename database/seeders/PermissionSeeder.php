<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'add_user', 'description' => 'Ajouter un utilisateur'],
            ['name' => 'update_user', 'description' => 'Mettre à jour un utilisateur'],
            ['name' => 'remove_user', 'description' => 'Supprimer un utilisateur'],
            ['name' => 'view_user', 'description' => 'Voir les utilisateurs'],
            ['name' => 'update_permission', 'description' => 'Modifier une permission'],
            ['name' => 'add_permission_to_user', 'description' => 'Attribuer une permission à un utilisateur'],
            ['name' => 'remove_permission_to_user', 'description' => 'Retirer une permission d’un utilisateur'],
            ['name' => 'add_medicament', 'description' => 'Ajouter un médicament'],
            ['name' => 'update_medicament', 'description' => 'Mettre à jour un médicament'],
            ['name' => 'remove_medicament', 'description' => 'Supprimer un médicament'],
            ['name' => 'view_medicament', 'description' => 'Voir les médicaments'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }
    }
}
