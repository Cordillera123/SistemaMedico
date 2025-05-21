<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ResultadoMedico extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'resultados_medicos';

    protected $fillable = [
        'paciente_id',
        'doctor_id',
        'tipo_resultado_id',
        'titulo',
        'descripcion',
        'archivo_pdf',
        'fecha_resultado',
        'confidencial',
        'visto_por_paciente',
        'fecha_visualizacion',
    ];

    protected $casts = [
        'fecha_resultado' => 'date',
        'fecha_visualizacion' => 'datetime',
        'confidencial' => 'boolean',
        'visto_por_paciente' => 'boolean',
    ];

    // Relación con paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    // Relación con doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Relación con tipo de resultado
    public function tipoResultado()
    {
        return $this->belongsTo(TipoResultado::class);
    }

    // Método para marcar como visto por el paciente
    public function marcarComoVisto()
    {
        $this->visto_por_paciente = true;
        $this->fecha_visualizacion = now();
        $this->save();

        return true;
    }

    // URL completa del archivo PDF
    public function getUrlPdfAttribute()
    {
        return Storage::url($this->archivo_pdf);
    }

    // Nombre del archivo
    public function getNombreArchivoAttribute()
    {
        return basename($this->archivo_pdf);
    }

    // Método para eliminar el archivo físico al eliminar el registro
    public static function boot()
    {
        parent::boot();

        // Antes de eliminar un registro, eliminar también el archivo físico
        static::deleting(function($resultado) {
            if ($resultado->isForceDeleting()) {
                Storage::delete($resultado->archivo_pdf);
            }
        });
    }

    // Generar notificación al crear un nuevo resultado
    public static function notificarNuevoResultado($resultado)
    {
        $paciente = $resultado->paciente;
        
        // Crear notificación para el paciente
        Notificacion::create([
            'user_id' => $paciente->user_id,
            'titulo' => 'Nuevo resultado médico disponible',
            'mensaje' => "Tu doctor ha subido un nuevo resultado médico: {$resultado->titulo}",
            'tipo' => 'resultado_nuevo',
            'notificable_type' => ResultadoMedico::class,
            'notificable_id' => $resultado->id
        ]);
    }
}