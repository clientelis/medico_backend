<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // ID des rôles
        $adminRole = Role::where('name', 'Admin')->first();
        $memberRole = Role::where('name', 'Membre')->first();

        // Permissions Admin (toutes les permissions)
        $permissionsAdmin = [
            'add_user', 'update_user', 'remove_user', 'view_user',
            'update_permission', 'add_permission_to_user', 'remove_permission_to_user',
            'add_medicament', 'update_medicament', 'remove_medicament', 'view_medicament'
        ];

        // Permissions Membre (uniquement voir les médicaments)
        $permissionsMember = ['view_medicament'];

        // Assigner les permissions à l'Admin
        foreach ($permissionsAdmin as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();

            RolePermission::updateOrCreate(
                ['role_id' => $adminRole->id, 'permission_id' => $permission->id],
                ['role_id' => $adminRole->id, 'permission_id' => $permission->id]
            );
        }

        // Assigner les permissions au Membre
        foreach ($permissionsMember as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();

            RolePermission::updateOrCreate(
                ['role_id' => $memberRole->id, 'permission_id' => $permission->id],
                ['role_id' => $memberRole->id, 'permission_id' => $permission->id]
            );
        }
    }
}