<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    // Relación con usuarios
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Método para verificar si un rol es administrador
    public function isAdmin()
    {
        return $this->nombre === 'administrador';
    }

    // Método para verificar si un rol es doctor
    public function isDoctor()
    {
        return $this->nombre === 'doctor';
    }

    // Método para verificar si un rol es paciente
    public function isPaciente()
    {
        return $this->nombre === 'paciente';
    }
}