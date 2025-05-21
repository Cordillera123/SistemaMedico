<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Eliminar la columna doctor_id de la tabla pacientes
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropColumn('doctor_id');
        });

        // Crear tabla pivote para la relación muchos a muchos
        Schema::create('doctor_paciente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctores')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->boolean('doctor_principal')->default(false); // Para indicar si es el doctor principal
            $table->timestamps();
            
            // Para evitar duplicados
            $table->unique(['doctor_id', 'paciente_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctor_paciente');
        
        Schema::table('pacientes', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->constrained('doctores');
        });
    }
};