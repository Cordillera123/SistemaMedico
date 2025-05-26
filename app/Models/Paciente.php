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
        'cedula',
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

    // Relación muchos-a-muchos con doctores
    public function doctores()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_paciente')
                    ->withPivot('doctor_principal')
                    ->withTimestamps();
    }

    // Obtener el doctor principal
    public function doctorPrincipal()
    {
        return $this->doctores()
                    ->wherePivot('doctor_principal', true)
                    ->first();
    }

    // Verificar si un doctor es el principal
    public function esDoctorPrincipal(Doctor $doctor)
    {
        return $this->doctores()
                    ->wherePivot('doctor_id', $doctor->id)
                    ->wherePivot('doctor_principal', true)
                    ->exists();
    }

    // Establecer un doctor como principal
    public function establecerDoctorPrincipal(Doctor $doctor)
    {
        // Quitar la marca de principal a todos los doctores
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

    // Resto de métodos auxiliares...
    public function resultadosNoVistos()
    {
        return $this->resultadosMedicos()
                    ->where('visto_por_paciente', false)
                    ->get();
    }

    public function cantidadResultados()
    {
        return $this->resultadosMedicos()->count();
    }

    public function cantidadResultadosNoVistos()
    {
        return $this->resultadosMedicos()
                    ->where('visto_por_paciente', false)
                    ->count();
    }

    public function scopePorCedula($query, $cedula)
    {
        return $query->where('cedula', 'LIKE', "%{$cedula}%");
    }
}