<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
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
        'token_recuperacion',
        'expiracion_token',
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

    // RELACIONES
    
    /**
     * Relación con rol
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relación con doctor (si es que el usuario es un doctor)
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * Relación con paciente (si es que el usuario es un paciente)
     */
    public function paciente()
    {
        return $this->hasOne(Paciente::class);
    }

    /**
     * Relación con notificaciones
     */
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }

    /**
     * Relación con logs
     */
    public function logs()
    {
        return $this->hasMany(LogSistema::class);
    }

    // ACCESSORS Y MUTATORS

    /**
     * Obtener el nombre completo del usuario
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // MÉTODOS DE VERIFICACIÓN DE ROL

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        return $this->role && $this->role->nombre === 'administrador';
    }

    /**
     * Verificar si el usuario es doctor
     */
    public function isDoctor()
    {
        return $this->role && $this->role->nombre === 'doctor';
    }

    /**
     * Verificar si el usuario es paciente
     */
    public function isPaciente()
    {
        return $this->role && $this->role->nombre === 'paciente';
    }

    // MÉTODOS DE GESTIÓN DE INTENTOS FALLIDOS Y BLOQUEO

    /**
     * Incrementar los intentos fallidos de login
     */
    public function incrementarIntentosFallidos()
    {
        $this->intentos_fallidos = ($this->intentos_fallidos ?? 0) + 1;
        $this->save();

        return $this->intentos_fallidos;
    }

    /**
     * Bloquear al usuario usando la configuración del sistema
     */
    public function bloquear($minutos = null)
    {
        // Si no se especifican minutos, usar la configuración del sistema
        if ($minutos === null) {
            $minutos = ConfiguracionSistema::obtenerValor('tiempo_bloqueo_minutos', 30);
        }
        
        $this->bloqueado_hasta = Carbon::now()->addMinutes($minutos);
        $this->save();
    }

    /**
     * Desbloquear al usuario y resetear intentos fallidos
     */
    public function desbloquear()
    {
        $this->intentos_fallidos = 0;
        $this->bloqueado_hasta = null;
        $this->save();
    }

    /**
     * Verificar si el usuario está bloqueado
     */
    public function estaBloqueado()
    {
        if (!$this->bloqueado_hasta) {
            return false;
        }

        // Si el tiempo de bloqueo ya pasó, desbloquear automáticamente
        if (Carbon::now()->greaterThan($this->bloqueado_hasta)) {
            $this->desbloquear();
            return false;
        }

        return true;
    }

    /**
     * Verificar si el usuario ha excedido el máximo de intentos
     */
    public function haExcedidoIntentos()
    {
        $maxIntentos = ConfiguracionSistema::obtenerValor('max_intentos_login', 3);
        return $this->intentos_fallidos >= $maxIntentos;
    }

    /**
     * Obtener tiempo restante de bloqueo en minutos
     */
    public function tiempoRestanteBloqueo()
    {
        if (!$this->estaBloqueado()) {
            return 0;
        }

        return Carbon::now()->diffInMinutes($this->bloqueado_hasta, false);
    }

    /**
     * Obtener información completa del bloqueo
     */
    public function getInfoBloqueo()
    {
        if (!$this->estaBloqueado()) {
            return null;
        }

        $minutosRestantes = $this->tiempoRestanteBloqueo();
        
        return [
            'bloqueado' => true,
            'hasta' => $this->bloqueado_hasta,
            'minutos_restantes' => $minutosRestantes,
            'tiempo_legible' => $this->formatearTiempoRestante($minutosRestantes)
        ];
    }

    /**
     * Formatear tiempo restante de forma legible
     */
    private function formatearTiempoRestante($minutos)
    {
        if ($minutos < 1) {
            return 'Menos de 1 minuto';
        }

        if ($minutos < 60) {
            return $minutos . ' minuto' . ($minutos > 1 ? 's' : '');
        }

        $horas = floor($minutos / 60);
        $minutosRestantes = $minutos % 60;

        $texto = $horas . ' hora' . ($horas > 1 ? 's' : '');
        
        if ($minutosRestantes > 0) {
            $texto .= ' y ' . $minutosRestantes . ' minuto' . ($minutosRestantes > 1 ? 's' : '');
        }

        return $texto;
    }

    // MÉTODOS DE RECUPERACIÓN DE CONTRASEÑA

    /**
     * Generar token de recuperación usando la configuración del sistema
     */
    public function generarTokenRecuperacion()
    {
        // Obtener tiempo de expiración de la configuración del sistema
        $expiracionMinutos = ConfiguracionSistema::obtenerValor('tiempo_expiracion_token', 60);
        
        $token = Str::random(60);
        $this->token_recuperacion = Hash::make($token);
        $this->expiracion_token = Carbon::now()->addMinutes($expiracionMinutos);
        $this->save();

        return $token; // Retornar el token sin hash para enviarlo por email
    }

    /**
     * Validar token de recuperación
     */
    public function validarTokenRecuperacion($token)
    {
        if (!$this->token_recuperacion || !$this->expiracion_token) {
            return false;
        }
        
        // Verificar que no haya expirado
        if (Carbon::now()->greaterThan($this->expiracion_token)) {
            $this->limpiarTokenRecuperacion();
            return false;
        }
        
        // Verificar que el token coincida usando Hash::check
        return Hash::check($token, $this->token_recuperacion);
    }

    /**
     * Limpiar token de recuperación
     */
    public function limpiarTokenRecuperacion()
    {
        $this->token_recuperacion = null;
        $this->expiracion_token = null;
        $this->save();
    }

    // SCOPES PARA QUERIES

    /**
     * Scope para obtener usuarios bloqueados
     */
    public function scopeBloqueados($query)
    {
        return $query->whereNotNull('bloqueado_hasta')
                    ->where('bloqueado_hasta', '>', Carbon::now());
    }

    /**
     * Scope para obtener usuarios con intentos fallidos
     */
    public function scopeConIntentosFallidos($query)
    {
        return $query->where('intentos_fallidos', '>', 0);
    }

    /**
     * Scope para obtener usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para obtener usuarios inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    /**
     * Scope para buscar por nombre, apellido, email o username
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('apellido', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%")
              ->orWhere('username', 'like', "%{$termino}%");
        });
    }

    /**
     * Scope para filtrar por rol
     */
    public function scopePorRol($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    // MÉTODOS ESTÁTICOS DE UTILIDAD

    /**
     * Limpiar bloqueos expirados de forma masiva
     */
    public static function limpiarBloqueosExpirados()
    {
        return static::whereNotNull('bloqueado_hasta')
            ->where('bloqueado_hasta', '<=', Carbon::now())
            ->update([
                'bloqueado_hasta' => null,
                'intentos_fallidos' => 0
            ]);
    }

    /**
     * Obtener estadísticas de usuarios
     */
    public static function obtenerEstadisticas()
    {
        return [
            'total' => static::count(),
            'activos' => static::activos()->count(),
            'inactivos' => static::inactivos()->count(),
            'bloqueados' => static::bloqueados()->count(),
            'con_intentos' => static::conIntentosFallidos()->count(),
        ];
    }
}