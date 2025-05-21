<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LogSistema extends Model
{
    use HasFactory;

    protected $table = 'logs_sistema';

    protected $fillable = [
        'user_id',
        'accion',
        'tabla_afectada',
        'registro_id',
        'detalles',
        'ip_address',
        'user_agent',
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Método estático para registrar una acción
  public static function registrar($accion, $tablaAfectada = null, $registroId = null, $detalles = null)
{
    // Use Auth facade explicitly instead of auth() helper
    $userId = null;
    if (\Illuminate\Support\Facades\Auth::check()) {
        $userId = \Illuminate\Support\Facades\Auth::user()->id;
    }
    
    return self::create([
        'user_id' => $userId,
        'accion' => $accion,
        'tabla_afectada' => $tablaAfectada,
        'registro_id' => $registroId,
        'detalles' => $detalles,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);
}

    // Scope para filtrar por usuario
    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope para filtrar por acción
    public function scopePorAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    // Scope para filtrar por tabla afectada
    public function scopePorTabla($query, $tabla)
    {
        return $query->where('tabla_afectada', $tabla);
    }

    // Scope para filtrar por fecha
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('created_at', $fecha);
    }
}