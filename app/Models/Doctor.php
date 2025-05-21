<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doctores';

    protected $fillable = [
        'user_id',
        'especialidad',
        'licencia_medica',
        'biografia',
        'horario_consulta',
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con pacientes
    public function pacientes()
{
    return $this->belongsToMany(Paciente::class, 'doctor_paciente')
                ->withPivot('doctor_principal')
                ->withTimestamps();
}

    // Relación con resultados médicos
    public function resultadosMedicos()
    {
        return $this->hasMany(ResultadoMedico::class);
    }

    // Obtener el nombre completo a través de la relación con usuario
    public function getNombreCompletoAttribute()
    {
        return $this->user->nombre_completo;
    }

    // Métodos de búsqueda
    public function scopePorEspecialidad($query, $especialidad)
    {
        return $query->where('especialidad', $especialidad);
    }

    // Cantidad de pacientes asignados
    public function cantidadPacientes()
    {
        return $this->pacientes()->count();
    }

    // Cantidad de resultados médicos subidos
    public function cantidadResultados()
    {
        return $this->resultadosMedicos()->count();
    }
public function pacientesPrincipales()
{
    return $this->belongsToMany(Paciente::class, 'doctor_paciente')
                ->wherePivot('doctor_principal', true)
                ->withTimestamps();
}
}
