<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up()
    {
        Schema::create('configuracion_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor');
            $table->string('tipo', 50); // 'string', 'integer', 'boolean', 'json', etc.
            $table->string('descripcion')->nullable();
            $table->boolean('editable')->default(true);
            $table->timestamps();
        });

        // Insertar configuraciones iniciales
        DB::table('configuracion_sistema')->insert([
            [
                'clave' => 'max_intentos_login',
                'valor' => '3',
                'tipo' => 'integer',
                'descripcion' => 'Número máximo de intentos de inicio de sesión antes de bloquear',
                'editable' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'clave' => 'tiempo_bloqueo_minutos',
                'valor' => '30',
                'tipo' => 'integer',
                'descripcion' => 'Tiempo de bloqueo después de exceder intentos de login (minutos)',
                'editable' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'clave' => 'tiempo_expiracion_token',
                'valor' => '60',
                'tipo' => 'integer',
                'descripcion' => 'Tiempo de expiración del token de recuperación (minutos)',
                'editable' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'clave' => 'nombre_hospital',
                'valor' => 'Hospital Sistema Laravel',
                'tipo' => 'string',
                'descripcion' => 'Nombre del hospital',
                'editable' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('configuracion_sistema');
    }
};