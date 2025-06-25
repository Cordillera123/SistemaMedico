<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ResultadoMedicoArchivo extends Model
{
    use HasFactory;

    protected $table = 'resultado_medico_archivos';

    protected $fillable = [
        'resultado_medico_id',
        'nombre_original',
        'nombre_archivo',
        'ruta_archivo',
        'tamaño',
        'mime_type',
        'orden',
    ];

    protected $casts = [
        'tamaño' => 'integer',
        'orden' => 'integer',
    ];

    // === RELACIONES ===
    
    /**
     * Relación con el resultado médico
     */
    public function resultadoMedico()
    {
        return $this->belongsTo(ResultadoMedico::class);
    }

    // === ACCESSORS ===
    
    /**
     * Obtener la URL pública del archivo
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->ruta_archivo);
    }

    /**
     * Obtener el tamaño formateado
     */
    public function getTamañoFormateadoAttribute()
    {
        return $this->formatFileSize($this->tamaño);
    }

    /**
     * Verificar si el archivo existe físicamente
     */
    public function getExisteAttribute()
    {
        return Storage::disk('public')->exists($this->ruta_archivo);
    }

    /**
     * Obtener la extensión del archivo
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->nombre_original, PATHINFO_EXTENSION);
    }

    // === MÉTODOS DE UTILIDAD ===
    
    /**
     * Formatear el tamaño del archivo
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Generar una URL de descarga segura
     */
    public function generarUrlDescarga()
    {
        return route('resultados.descargar-archivo', [
            'resultado' => $this->resultado_medico_id,
            'archivo' => $this->id
        ]);
    }

    /**
     * Obtener información completa del archivo
     */
    public function getInfoCompleta()
    {
        return [
            'id' => $this->id,
            'nombre_original' => $this->nombre_original,
            'nombre_archivo' => $this->nombre_archivo,
            'tamaño' => $this->tamaño,
            'tamaño_formateado' => $this->tamaño_formateado,
            'mime_type' => $this->mime_type,
            'extension' => $this->extension,
            'orden' => $this->orden,
            'url' => $this->url,
            'url_descarga' => $this->generarUrlDescarga(),
            'existe' => $this->existe,
            'fecha_subida' => $this->created_at->format('d/m/Y H:i'),
        ];
    }

    // === SCOPES ===
    
    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden');
    }

    /**
     * Scope para archivos válidos (que existen físicamente)
     */
    public function scopeValidos($query)
    {
        return $query->whereRaw('LENGTH(ruta_archivo) > 0');
    }

    // === BOOT METHOD ===
    
    public static function boot()
    {
        parent::boot();

        // Al eliminar un archivo, eliminar el archivo físico
        static::deleting(function($archivo) {
            if (Storage::disk('public')->exists($archivo->ruta_archivo)) {
                Storage::disk('public')->delete($archivo->ruta_archivo);
            }
        });

        // Al crear un archivo, asegurar orden único
        static::creating(function($archivo) {
            if (!$archivo->orden) {
                $maxOrden = static::where('resultado_medico_id', $archivo->resultado_medico_id)
                    ->max('orden');
                $archivo->orden = ($maxOrden ?? 0) + 1;
            }
        });
    }

    // === VALIDACIONES PERSONALIZADAS ===
    
    /**
     * Validar que el archivo sea PDF
     */
    public function esPdf()
    {
        return $this->mime_type === 'application/pdf' || 
               strtolower($this->extension) === 'pdf';
    }

    /**
     * Validar tamaño del archivo
     */
    public function esTamañoValido($maxSize = 10485760) // 10MB por defecto
    {
        return $this->tamaño <= $maxSize;
    }

    /**
     * Validar integridad del archivo
     */
    public function validarIntegridad()
    {
        if (!$this->existe) {
            return false;
        }

        $tamañoReal = Storage::disk('public')->size($this->ruta_archivo);
        return $tamañoReal === $this->tamaño;
    }
}