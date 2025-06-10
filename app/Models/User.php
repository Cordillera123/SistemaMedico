<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
        'intentos_fallidos',
        'bloqueado_hasta',
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
        'intentos_fallidos' => 'integer',
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
        return $this->role && $this->role->nombre === 'administrador';
    }

    public function isDoctor()
    {
        return $this->role && $this->role->nombre === 'doctor';
    }

    public function isPaciente()
    {
        return $this->role && $this->role->nombre === 'paciente';
    }

    // Método para incrementar los intentos fallidos de login
    public function incrementarIntentosFallidos()
    {
        $this->intentos_fallidos = ($this->intentos_fallidos ?? 0) + 1;
        $this->save();

        return $this->intentos_fallidos;
    }

    // Método para bloquear al usuario usando la configuración del sistema
    public function bloquear($minutos = null)
    {
        // Si no se especifican minutos, usar la configuración del sistema
        if ($minutos === null) {
            $minutos = ConfiguracionSistema::obtenerValor('tiempo_bloqueo_minutos', 30);
        }
        
        $this->bloqueado_hasta = Carbon::now()->addMinutes($minutos);
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
        return $this->bloqueado_hasta !== null && Carbon::now()->lt($this->bloqueado_hasta);
    }

    // Generar token de recuperación usando la configuración del sistema
    public function generarTokenRecuperacion()
    {
        // Obtener tiempo de expiración de la configuración del sistema
        $expiracionMinutos = ConfiguracionSistema::obtenerValor('tiempo_expiracion_token', 60);
        
        $token = Str::random(60);
        $this->token_recuperacion = hash('sha256', $token);
        $this->expiracion_token = Carbon::now()->addMinutes($expiracionMinutos);
        $this->save();

        return $token; // Retornar el token sin hash para enviarlo por email
    }

    // Validar token de recuperación
    public function validarTokenRecuperacion($token)
    {
        if (!$this->token_recuperacion || !$this->expiracion_token) {
            return false;
        }
        
        // Verificar que el token coincida (comparar con hash)
        if (hash('sha256', $token) !== $this->token_recuperacion) {
            return false;
        }
        
        // Verificar que no haya expirado
        if (Carbon::now()->gt($this->expiracion_token)) {
            return false;
        }
        
        return true;
    }

    // Limpiar token de recuperación
    public function limpiarTokenRecuperacion()
    {
        $this->token_recuperacion = null;
        $this->expiracion_token = null;
        $this->save();
    }

    // Verificar si el usuario ha excedido el máximo de intentos
    public function haExcedidoIntentos()
    {
        $maxIntentos = ConfiguracionSistema::obtenerValor('max_intentos_login', 3);
        return $this->intentos_fallidos >= $maxIntentos;
    }
}