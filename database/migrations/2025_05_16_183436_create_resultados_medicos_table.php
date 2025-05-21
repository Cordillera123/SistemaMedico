<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resultados_medicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->foreignId('doctor_id')->constrained('doctores');
            $table->foreignId('tipo_resultado_id')->constrained('tipos_resultados');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('archivo_pdf');
            $table->date('fecha_resultado');
            $table->boolean('confidencial')->default(false);
            $table->boolean('visto_por_paciente')->default(false);
            $table->timestamp('fecha_visualizacion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resultados_medicos');
    }
};