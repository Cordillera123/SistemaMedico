<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoResultado extends Model
{
    use HasFactory;

    protected $table = 'tipos_resultados';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Relación con resultados médicos
    public function resultadosMedicos()
    {
        return $this->hasMany(ResultadoMedico::class);
    }

    // Cantidad de resultados de este tipo
    public function cantidadResultados()
    {
        return $this->resultadosMedicos()->count();
    }
}