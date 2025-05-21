<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'username',
        'password',
        'telefono',
        'direccion',
        'role_id',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'token_recuperacion',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'bloqueado_hasta' => 'datetime',
        'expiracion_token' => 'datetime',
        'activo' => 'boolean',
    ];

    // Relación con rol
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relación con doctor (si es que el usuario es un doctor)
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    // Relación con paciente (si es que el usuario es un paciente)
    public function paciente()
    {
        return $this->hasOne(Paciente::class);
    }

    // Relación con notificaciones
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }

    // Relación con logs
    public function logs()
    {
        return $this->hasMany(LogSistema::class);
    }

    // Método para obtener el nombre completo
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // Métodos helpers para verificar el rol
    public function isAdmin()
    {
        return $this->role->nombre === 'administrador';
    }

    public function isDoctor()
    {
        return $this->role->nombre === 'doctor';
    }

    public function isPaciente()
    {
        return $this->role->nombre === 'paciente';
    }

    // Método para incrementar los intentos fallidos de login
    public function incrementarIntentosFallidos()
    {
        $this->intentos_fallidos += 1;
        $this->save();

        return $this->intentos_fallidos;
    }

    // Método para bloquear al usuario
    public function bloquear($minutos)
    {
        $this->bloqueado_hasta = now()->addMinutos($minutos);
        $this->save();
    }

    // Método para desbloquear al usuario
    public function desbloquear()
    {
        $this->intentos_fallidos = 0;
        $this->bloqueado_hasta = null;
        $this->save();
    }

    // Verificar si el usuario está bloqueado
    public function estaBloqueado()
    {
        return $this->bloqueado_hasta !== null && now()->lt($this->bloqueado_hasta);
    }

    // Generar token de recuperación
    public function generarTokenRecuperacion($expiracionMinutos = 60)
    {
        $this->token_recuperacion = \Illuminate\Support\Str::random(60);
        $this->expiracion_token = now()->addMinutes($expiracionMinutos);
        $this->save();

        return $this->token_recuperacion;
    }

    // Validar token de recuperación
    public function validarTokenRecuperacion($token)
    {
        return $this->token_recuperacion === $token && 
               $this->expiracion_token !== null && 
               now()->lt($this->expiracion_token);
    }

    // Limpiar token de recuperación
    public function limpiarTokenRecuperacion()
    {
        $this->token_recuperacion = null;
        $this->expiracion_token = null;
        $this->save();
    }
}