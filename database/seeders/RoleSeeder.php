<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Asegúrate de que la tabla esté vacía antes de insertar
        DB::table('roles')->truncate();
        
        // Insertar roles
        Role::create([
            'nombre' => 'administrador',
            'descripcion' => 'Administrador del sistema'
        ]);
        
        Role::create([
            'nombre' => 'doctor',
            'descripcion' => 'Médico del hospital'
        ]);
        
        Role::create([
            'nombre' => 'paciente',
            'descripcion' => 'Paciente del hospital'
        ]);
    }
}