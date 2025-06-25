<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Crear la nueva tabla para archivos
        Schema::create('resultado_medico_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resultado_medico_id')->constrained('resultados_medicos')->onDelete('cascade');
            $table->string('nombre_original'); // Nombre del archivo original
            $table->string('nombre_archivo'); // Nombre único del archivo en storage
            $table->string('ruta_archivo'); // Ruta completa del archivo
            $table->bigInteger('tamaño'); // Tamaño en bytes
            $table->string('mime_type')->default('application/pdf');
            $table->integer('orden')->default(1); // Orden de visualización
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['resultado_medico_id', 'orden']);
        });

        // 2. Migrar datos existentes (si los hay)
        // IMPORTANTE: Ejecutar esto solo si ya tienes datos
        if (Schema::hasColumn('resultados_medicos', 'archivo_pdf')) {
            DB::statement('
                INSERT INTO resultado_medico_archivos (
                    resultado_medico_id, 
                    nombre_original, 
                    nombre_archivo, 
                    ruta_archivo, 
                    tamaño, 
                    orden,
                    created_at,
                    updated_at
                )
                SELECT 
                    id,
                    SUBSTRING_INDEX(archivo_pdf, "/", -1) as nombre_original,
                    SUBSTRING_INDEX(archivo_pdf, "/", -1) as nombre_archivo,
                    archivo_pdf,
                    0 as tamaño,
                    1 as orden,
                    created_at,
                    updated_at
                FROM resultados_medicos 
                WHERE archivo_pdf IS NOT NULL AND archivo_pdf != ""
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('resultado_medico_archivos');
    }
};