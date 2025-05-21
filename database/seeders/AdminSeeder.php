<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database with an admin user.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email' => 'admin@everlastingart.com',
            'password' => Hash::make('admin'), 
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->command->info('Usuario administrador creado con éxito:');
        $this->command->info('Email: admin@everlastingart.com');
        $this->command->info('Contraseña: password');
        $this->command->info('¡Recuerda cambiar la contraseña en producción!');
    }
}