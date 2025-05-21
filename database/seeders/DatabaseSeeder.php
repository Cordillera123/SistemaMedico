<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Deshabilitar chequeo de claves foráneas temporalmente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Llamar a los seeders
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TipoResultadoSeeder::class,
        ]);
        
        // Reactivar chequeo de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}