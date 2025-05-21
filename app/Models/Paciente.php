<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'cedula', // Añadimos el campo de cédula
        'fecha_nacimiento',
        'genero',
        'tipo_sangre',
        'alergias',
        'antecedentes_medicos',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con doctor
    public function doctores()
{
    return $this->belongsToMany(Doctor::class, 'doctor_paciente')
                ->withPivot('doctor_principal')
                ->withTimestamps();
}

public function doctorPrincipal()
{
    return $this->belongsToMany(Doctor::class, 'doctor_paciente')
                ->wherePivot('doctor_principal', true)
                ->first();
}

// Método auxiliar para verificar si un doctor es el principal
public function esDoctorPrincipal(Doctor $doctor)
{
    return $this->doctores()
                ->wherePivot('doctor_id', $doctor->id)
                ->wherePivot('doctor_principal', true)
                ->exists();
}

// Método para establecer un doctor como principal
public function establecerDoctorPrincipal(Doctor $doctor)
{
    // Primero, quitar la marca de principal a todos los doctores
    $this->doctores()->updateExistingPivot(
        $this->doctores()->pluck('doctores.id')->toArray(),
        ['doctor_principal' => false]
    );
    
    // Establecer el nuevo doctor principal
    $this->doctores()->updateExistingPivot($doctor->id, ['doctor_principal' => true]);
    
    return true;
}

    // Relación con resultados médicos
    public function resultadosMedicos()
    {
        return $this->hasMany(ResultadoMedico::class);
    }

    // Obtener edad basada en la fecha de nacimiento
    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento->age;
    }

    // Obtener nombre completo del paciente a través de la relación con usuario
    public function getNombreCompletoAttribute()
    {
        return $this->user->nombre_completo;
    }

    // Obtener resultados médicos no vistos
    public function resultadosNoVistos()
    {
        return $this->resultadosMedicos()
                    ->where('visto_por_paciente', false)
                    ->get();
    }

    // Cantidad de resultados médicos
    public function cantidadResultados()
    {
        return $this->resultadosMedicos()->count();
    }

    // Cantidad de resultados médicos no vistos
    public function cantidadResultadosNoVistos()
    {
        return $this->resultadosMedicos()
                    ->where('visto_por_paciente', false)
                    ->count();
    }

    // Obtener resultados médicos por tipo
    public function resultadosPorTipo($tipoId)
    {
        return $this->resultadosMedicos()
                    ->where('tipo_resultado_id', $tipoId)
                    ->get();
    }

    // Verificar si tiene alergias
    public function tieneAlergias()
    {
        return !empty($this->alergias);
    }

    // Scope para buscar por cédula
    public function scopePorCedula($query, $cedula)
    {
        return $query->where('cedula', 'LIKE', "%{$cedula}%");
    }
}