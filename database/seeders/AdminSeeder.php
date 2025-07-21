<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Obtener el rol de administrador
        $adminRole = Role::where('nombre', 'administrador')->first();
        
        if (!$adminRole) {
            $this->command->error('El rol de administrador no existe. Ejecuta primero el RoleSeeder.');
            return;
        }

        // Lista de administradores a crear
        $admins = [
            [
                'nombre' => 'Administrador',
                'apellido' => 'Principal',
                'email' => 'admin@sistemamedico.com',
                'username' => 'admin',
                'password' => 'admin123',
                'telefono' => '+1234567890',
                'direccion' => 'Dirección Administrativa Principal'
            ],
            [
                'nombre' => 'Super',
                'apellido' => 'Admin',
                'email' => 'superadmin@sistemamedico.com',
                'username' => 'superadmin',
                'password' => 'superadmin123',
                'telefono' => '+1234567891',
                'direccion' => 'Dirección Super Administrador'
            ],
            [
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'email' => 'sistema@sistemamedico.com',
                'username' => 'adminsistema',
                'password' => 'sistema123',
                'telefono' => '+1234567892',
                'direccion' => 'Dirección Administrador Sistema'
            ]
        ];

        foreach ($admins as $adminData) {
            // Verificar si ya existe un usuario con ese email o username
            $existingUser = User::where('email', $adminData['email'])
                               ->orWhere('username', $adminData['username'])
                               ->first();

            if (!$existingUser) {
                $admin = User::create([
                    'nombre' => $adminData['nombre'],
                    'apellido' => $adminData['apellido'],
                    'email' => $adminData['email'],
                    'username' => $adminData['username'],
                    'password' => Hash::make($adminData['password']),
                    'telefono' => $adminData['telefono'],
                    'direccion' => $adminData['direccion'],
                    'role_id' => $adminRole->id,
                    'activo' => true,
                    'intentos_fallidos' => 0,
                    'email_verified_at' => now()
                ]);

                $this->command->info("Administrador creado: {$admin->username} ({$admin->email})");
            } else {
                $this->command->warn("El usuario {$adminData['username']} ya existe, omitiendo...");
            }
        }

        $this->command->info('Seeder de administradores completado.');
    }
}
