<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoResultado;

class TipoResultadoSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            ['nombre' => 'Análisis de Sangre', 'descripcion' => 'Resultados de hemograma completo'],
            ['nombre' => 'Radiografía', 'descripcion' => 'Imágenes radiográficas'],
            ['nombre' => 'Electrocardiograma', 'descripcion' => 'Resultados de ECG'],
            ['nombre' => 'Tomografía', 'descripcion' => 'Imágenes de tomografía computarizada'],
            ['nombre' => 'Resonancia Magnética', 'descripcion' => 'Imágenes de resonancia magnética'],
            ['nombre' => 'Análisis de Orina', 'descripcion' => 'Resultados de examen de orina']
        ];
        
        foreach ($tipos as $tipo) {
            TipoResultado::create($tipo);
        }
    }
}