<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    use HasFactory;

    protected $table = 'configuracion_sistema';

    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'descripcion',
        'editable',
    ];

    protected $casts = [
        'editable' => 'boolean',
    ];

    // Método para obtener un valor de configuración por su clave
    public static function obtenerValor($clave, $valorPorDefecto = null)
    {
        $config = self::where('clave', $clave)->first();
        
        if (!$config) {
            return $valorPorDefecto;
        }
        
        // Convertir el valor según su tipo
        switch ($config->tipo) {
            case 'integer':
                return (int) $config->valor;
            case 'float':
                return (float) $config->valor;
            case 'boolean':
                return filter_var($config->valor, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($config->valor, true);
            default:
                return $config->valor;
        }
    }

    // Método para establecer un valor de configuración
    public static function establecerValor($clave, $valor)
    {
        $config = self::where('clave', $clave)->first();
        
        if (!$config) {
            return false;
        }
        
        // No permitir modificar configuraciones no editables
        if (!$config->editable) {
            return false;
        }
        
        // Convertir el valor según su tipo antes de guardarlo
        if ($config->tipo === 'json' && is_array($valor)) {
            $valor = json_encode($valor);
        } elseif ($config->tipo === 'boolean') {
            $valor = $valor ? '1' : '0';
        }
        
        $config->valor = $valor;
        return $config->save();
    }

    // Método para crear una nueva configuración
    public static function crearConfiguracion($clave, $valor, $tipo = 'string', $descripcion = null, $editable = true)
    {
        // Validar que la clave no exista
        if (self::where('clave', $clave)->exists()) {
            return false;
        }
        
        // Crear la configuración
        return self::create([
            'clave' => $clave,
            'valor' => $valor,
            'tipo' => $tipo,
            'descripcion' => $descripcion,
            'editable' => $editable,
        ]);
    }
}