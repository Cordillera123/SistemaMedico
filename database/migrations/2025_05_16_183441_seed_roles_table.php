<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('roles')->insert([
            ['nombre' => 'administrador', 'descripcion' => 'Administrador del sistema', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'doctor', 'descripcion' => 'Médico del hospital', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'paciente', 'descripcion' => 'Paciente del hospital', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        DB::table('roles')->whereIn('nombre', ['administrador', 'doctor', 'paciente'])->delete();
    }
};