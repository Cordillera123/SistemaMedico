<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'titulo',
        'mensaje',
        'tipo',
        'leida',
        'notificable_type',
        'notificable_id',
    ];

    protected $casts = [
        'leida' => 'boolean',
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación polimórfica con el modelo relacionado (puede ser ResultadoMedico u otro)
    public function notificable()
    {
        return $this->morphTo();
    }

    // Marcar como leída
    public function marcarComoLeida()
    {
        $this->leida = true;
        $this->save();

        return true;
    }

    // Scope para notificaciones no leídas
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    // Scope por tipo
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}