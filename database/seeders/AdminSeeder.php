<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Récupérer le rôle Admin
        $adminRole = Role::where('name', 'Admin')->first();

        // Créer ou mettre à jour l'administrateur
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // Identifiant unique de l'administrateur
            [
                'id' => Str::uuid(),
                'name' => 'Administrateur',
                'password' => bcrypt('password'), // Mot de passe par défaut (à changer plus tard)
                'role_id' => $adminRole->id, // Associer le rôle Admin
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}