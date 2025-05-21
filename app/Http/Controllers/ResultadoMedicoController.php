<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\LogSistema;
use App\Models\Paciente;
use App\Models\ResultadoMedico;
use App\Models\TipoResultado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

class ResultadoMedicoController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar lista de resultados médicos
     * Se limitará según el rol del usuario
     */
    public function index()
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Administrador ve todos los resultados
            $resultados = ResultadoMedico::with(['paciente.user', 'doctor.user', 'tipoResultado'])
                ->latest()
                ->paginate(10);
                
            return view('admin.resultados.index', compact('resultados'));
            
        } elseif ($user->isDoctor()) {
            // Doctor ve solo sus resultados
            $doctor = $user->doctor;
            $resultados = ResultadoMedico::where('doctor_id', $doctor->id)
                ->with(['paciente.user', 'tipoResultado'])
                ->latest()
                ->paginate(10);
                
            return view('doctor.resultados.index', compact('resultados'));
            
        } elseif ($user->isPaciente()) {
            // Paciente ve solo sus resultados
            $paciente = $user->paciente;
            $resultados = ResultadoMedico::where('paciente_id', $paciente->id)
                ->with(['doctor.user', 'tipoResultado'])
                ->latest()
                ->paginate(10);
                
            return view('paciente.resultados.index', compact('resultados'));
        }
        
        return redirect()->route('home')->with('error', 'No tiene permisos para ver resultados médicos');
    }

    /**
     * Mostrar formulario para crear un nuevo resultado médico
     * Solo disponible para doctores
     */
    public function create()
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isDoctor()) {
            return redirect()->route('home')->with('error', 'No tiene permisos para crear resultados médicos');
        }
        
        $doctor = $user->doctor;
        $pacientes = $doctor->pacientes()->with('user')->get();
        $tiposResultados = TipoResultado::all();
        
        return view('resultados.create', compact('pacientes', 'tiposResultados'));
    }

    /**
     * Almacenar un nuevo resultado médico
     * Solo disponible para doctores
     */
    public function store(Request $request)
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isDoctor()) {
            return redirect()->route('home')->with('error', 'No tiene permisos para crear resultados médicos');
        }
        
        $doctor = $user->doctor;
        
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:pacientes,id',
            'tipo_resultado_id' => 'required|exists:tipos_resultados,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'archivo_pdf' => 'required|file|mimes:pdf|max:10240', // Máximo 10MB
            'fecha_resultado' => 'required|date',
            'confidencial' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Verificar que el paciente pertenece a este doctor
        $paciente = Paciente::where('id', $request->paciente_id)
            ->where('doctor_id', $doctor->id)
            ->firstOrFail();

        DB::beginTransaction();

        try {
            // Almacenar archivo PDF
            $path = $request->file('archivo_pdf')->store('resultados_medicos');

            // Crear resultado médico
            $resultado = ResultadoMedico::create([
                'paciente_id' => $paciente->id,
                'doctor_id' => $doctor->id,
                'tipo_resultado_id' => $request->tipo_resultado_id,
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'archivo_pdf' => $path,
                'fecha_resultado' => $request->fecha_resultado,
                'confidencial' => $request->has('confidencial'),
                'visto_por_paciente' => false,
            ]);

            // Crear notificación para el paciente
            ResultadoMedico::notificarNuevoResultado($resultado);

            // Registrar acción
            LogSistema::registrar(
                'Subida de resultado médico', 
                'resultados_medicos', 
                $resultado->id,
                "Resultado médico '{$resultado->titulo}' subido para el paciente {$paciente->user->nombre} {$paciente->user->apellido}"
            );

            DB::commit();

            return redirect()->route('resultados.index')
                ->with('success', 'Resultado médico subido exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al subir resultado médico: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Mostrar un resultado médico específico
     */
    public function show($id)
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        $resultado = ResultadoMedico::with(['paciente.user', 'doctor.user', 'tipoResultado'])->findOrFail($id);
        
        // Verificar permisos
        if ($user->isAdmin()) {
            // El administrador puede ver cualquier resultado
        } elseif ($user->isDoctor()) {
            // El doctor solo puede ver resultados que ha subido
            if ($resultado->doctor_id != $user->doctor->id) {
                return redirect()->route('home')->with('error', 'No tiene permisos para ver este resultado médico');
            }
        } elseif ($user->isPaciente()) {
            // El paciente solo puede ver sus propios resultados
            if ($resultado->paciente_id != $user->paciente->id) {
                return redirect()->route('home')->with('error', 'No tiene permisos para ver este resultado médico');
            }
            
            // Marcar como visto si no lo estaba
            if (!$resultado->visto_por_paciente) {
                $resultado->marcarComoVisto();
                
                // Registrar acción
                LogSistema::registrar(
                    'Visualización de resultado médico', 
                    'resultados_medicos', 
                    $resultado->id,
                    "Resultado médico '{$resultado->titulo}' visualizado por primera vez por el paciente"
                );
            }
        } else {
            return redirect()->route('home')->with('error', 'No tiene permisos para ver resultados médicos');
        }
        
        return view('resultados.show', compact('resultado'));
    }

    /**
     * Mostrar formulario para editar un resultado médico
     * Solo disponible para doctores que han creado el resultado
     */
    public function edit($id)
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isDoctor()) {
            return redirect()->route('home')->with('error', 'No tiene permisos para editar resultados médicos');
        }
        
        $doctor = $user->doctor;
        $resultado = ResultadoMedico::where('doctor_id', $doctor->id)->findOrFail($id);
        $tiposResultados = TipoResultado::all();
        
        return view('resultados.edit', compact('resultado', 'tiposResultados'));
    }

    /**
     * Actualizar un resultado médico
     * Solo disponible para doctores que han creado el resultado
     */
    public function update(Request $request, $id)
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isDoctor()) {
            return redirect()->route('home')->with('error', 'No tiene permisos para actualizar resultados médicos');
        }
        
        $doctor = $user->doctor;
        $resultado = ResultadoMedico::where('doctor_id', $doctor->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo_resultado_id' => 'required|exists:tipos_resultados,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:10240', // Máximo 10MB
            'fecha_resultado' => 'required|date',
            'confidencial' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Actualizar archivo PDF si se proporcionó uno nuevo
            if ($request->hasFile('archivo_pdf')) {
                // Eliminar archivo anterior
                Storage::delete($resultado->archivo_pdf);
                
                // Almacenar nuevo archivo
                
$path = $request->file('archivo_pdf')->store('resultados_medicos', 'public');
                $resultado->archivo_pdf = $path;
                
                // Si se cambia el archivo, marcar como no visto por el paciente
                $resultado->visto_por_paciente = false;
                $resultado->fecha_visualizacion = null;
            }

            // Actualizar resultado médico
            $resultado->update([
                'tipo_resultado_id' => $request->tipo_resultado_id,
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'fecha_resultado' => $request->fecha_resultado,
                'confidencial' => $request->has('confidencial'),
            ]);

            // Si se modificó sustancialmente, notificar al paciente nuevamente
            if ($request->hasFile('archivo_pdf') || $resultado->wasChanged('titulo') || $resultado->wasChanged('tipo_resultado_id')) {
                ResultadoMedico::notificarNuevoResultado($resultado);
            }

            // Registrar acción
            LogSistema::registrar(
                'Actualización de resultado médico', 
                'resultados_medicos', 
                $resultado->id,
                "Resultado médico '{$resultado->titulo}' actualizado"
            );

            DB::commit();

            return redirect()->route('resultados.show', $resultado->id)
                ->with('success', 'Resultado médico actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al actualizar resultado médico: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Eliminar un resultado médico
     * Solo disponible para doctores que han creado el resultado o administradores
     */
    public function destroy($id)
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        $resultado = ResultadoMedico::findOrFail($id);
        
        // Verificar permisos
        if ($user->isAdmin()) {
            // El administrador puede eliminar cualquier resultado
        } elseif ($user->isDoctor()) {
            // El doctor solo puede eliminar resultados que ha subido
            if ($resultado->doctor_id != $user->doctor->id) {
                return redirect()->route('home')->with('error', 'No tiene permisos para eliminar este resultado médico');
            }
        } else {
            return redirect()->route('home')->with('error', 'No tiene permisos para eliminar resultados médicos');
        }

        DB::beginTransaction();

        try {
            // Registrar acción antes de eliminar
            LogSistema::registrar(
                'Eliminación de resultado médico', 
                'resultados_medicos', 
                $resultado->id,
                "Resultado médico '{$resultado->titulo}' eliminado por " . ($user->isAdmin() ? 'administrador' : 'doctor')
            );

            // Eliminar resultado médico (soft delete)
            $resultado->delete();

            DB::commit();

            return redirect()->route('resultados.index')
                ->with('success', 'Resultado médico eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al eliminar resultado médico: ' . $e->getMessage()]);
        }
    }

    /**
     * Descargar archivo PDF de un resultado médico
     */
    public function descargar($id)
    {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        $resultado = ResultadoMedico::findOrFail($id);
        
        // Verificar permisos
        if ($user->isAdmin()) {
            // El administrador puede descargar cualquier resultado
        } elseif ($user->isDoctor()) {
            // El doctor solo puede descargar resultados que ha subido
            if ($resultado->doctor_id != $user->doctor->id) {
                return redirect()->route('home')->with('error', 'No tiene permisos para descargar este resultado médico');
            }
        } elseif ($user->isPaciente()) {
            // El paciente solo puede descargar sus propios resultados
            if ($resultado->paciente_id != $user->paciente->id) {
                return redirect()->route('home')->with('error', 'No tiene permisos para descargar este resultado médico');
            }
            
            // Marcar como visto si no lo estaba
            if (!$resultado->visto_por_paciente) {
                $resultado->marcarComoVisto();
            }
        } else {
            return redirect()->route('home')->with('error', 'No tiene permisos para descargar resultados médicos');
        }
        
        // Registrar acción
        LogSistema::registrar(
            'Descarga de archivo de resultado médico', 
            'resultados_medicos', 
            $resultado->id,
            "Archivo de resultado médico '{$resultado->titulo}' descargado por " . 
            ($user->isAdmin() ? 'administrador' : ($user->isDoctor() ? 'doctor' : 'paciente'))
        );
        
       return response()->download(
        storage_path('app/public/' . str_replace('public/', '', $resultado->archivo_pdf)),
        $resultado->titulo . '.pdf'
        );
    }
}