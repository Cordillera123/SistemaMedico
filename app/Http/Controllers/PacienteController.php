<?php

// =======================
// PACIENTECONTROLLER CORREGIDO
// =======================

namespace App\Http\Controllers;

use App\Models\LogSistema;
use App\Models\Notificacion;
use App\Models\Paciente;
use App\Models\ResultadoMedico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

class PacienteController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:paciente');
    }

    /**
     * Dashboard del paciente - CORREGIDO
     */
    public function dashboard()
    {
        $user = Auth::user();
        $paciente = $user->paciente;
        
        // Verificar que el paciente existe
        if (!$paciente) {
            return redirect()->route('login')->with('error', 'No se encontró información del paciente.');
        }

        // Estadísticas para el dashboard
        $totalResultados = $paciente->resultadosMedicos()->count();
        $resultadosNoVistos = $paciente->resultadosNoVistos()->count();

        // Resultados recientes
        $resultadosRecientes = $paciente->resultadosMedicos()
            ->with(['doctor.user', 'tipoResultado'])
            ->latest()
            ->take(5)
            ->get();

        // CAMBIO: Obtener el doctor principal del paciente
        $doctorPrincipal = $paciente->doctorPrincipal();
        
        // Obtener todos los doctores del paciente
        $doctores = $paciente->doctores()->with('user')->get();
        $totalDoctores = $doctores->count();

        // Notificaciones no leídas
        $notificaciones = Notificacion::where('user_id', $user->id)
            ->where('leida', false)
            ->latest()
            ->take(5)
            ->get();

        return view('paciente.dashboard', compact(
            'paciente',
            'totalResultados',
            'resultadosNoVistos',
            'resultadosRecientes',
            'notificaciones',
            'doctorPrincipal',
            'doctores',
            'totalDoctores'
        ));
    }

    /**
     * Mostrar lista de resultados médicos del paciente
     */
    public function resultadosIndex(Request $request)
    {
        $paciente = Auth::user()->paciente;
        
        $query = $paciente->resultadosMedicos()
            ->with(['doctor.user', 'tipoResultado']);
        
        // Filtrar solo resultados nuevos si se solicita
        if ($request->has('nuevos') && $request->nuevos == 1) {
            $query->where('visto_por_paciente', false);
        }
        
        $resultados = $query->latest()->paginate(10);

        return view('paciente.resultados.index', compact('resultados'));
    }

    /**
     * Mostrar un resultado médico específico
     */
    public function resultadosShow($id)
    {
        $paciente = Auth::user()->paciente;
        $resultado = ResultadoMedico::where('paciente_id', $paciente->id)
            ->with(['doctor.user', 'tipoResultado'])
            ->findOrFail($id);

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

        return view('paciente.resultados.show', compact('resultado'));
    }

    /**
     * Descargar archivo PDF de un resultado médico
     */
    public function resultadosDescargar($id)
    {
        $paciente = Auth::user()->paciente;
        $resultado = ResultadoMedico::where('paciente_id', $paciente->id)
            ->findOrFail($id);

        // Marcar como visto si no lo estaba
        if (!$resultado->visto_por_paciente) {
            $resultado->marcarComoVisto();
        }

        // Registrar acción
        LogSistema::registrar(
            'Descarga de archivo de resultado médico',
            'resultados_medicos',
            $resultado->id,
            "Archivo de resultado médico '{$resultado->titulo}' descargado por el paciente"
        );

        return response()->download(
            storage_path('app/public/' . str_replace('public/', '', $resultado->archivo_pdf)),
            $resultado->titulo . '.pdf'
        );
    }

    /**
     * Mostrar información de los médicos del paciente - CORREGIDO
     */
    public function miMedico()
    {
        $paciente = Auth::user()->paciente;
        
        // Obtener todos los doctores
        $doctores = $paciente->doctores()->with('user')->get();
        
        // Si no tiene ningún doctor asignado
        if ($doctores->count() == 0) {
            return view('paciente.sin-medico');
        }
        
        // Obtener el doctor principal
        $doctorPrincipal = $paciente->doctorPrincipal();
        
        // Si no tiene doctor principal pero tiene doctores, usar el primero como referencia
        $doctorReferencia = $doctorPrincipal ?: $doctores->first();

        // Estadísticas generales (todos los médicos)
        $totalResultados = $paciente->resultadosMedicos()->count();
        $resultadosVistos = $paciente->resultadosMedicos()
            ->where('visto_por_paciente', true)
            ->count();
        $resultadosNoVistos = $totalResultados - $resultadosVistos;
        
        // Resultados recientes de todos los médicos
        $resultadosRecientes = $paciente->resultadosMedicos()
            ->with(['tipoResultado', 'doctor.user'])
            ->latest()
            ->take(5)
            ->get();

        // Estadísticas por cada doctor
        $estadisticasPorDoctor = [];
        foreach ($doctores as $doctor) {
            $estadisticasPorDoctor[$doctor->id] = [
                'total_resultados' => $paciente->resultadosMedicos()
                    ->where('doctor_id', $doctor->id)
                    ->count(),
                'resultados_nuevos' => $paciente->resultadosMedicos()
                    ->where('doctor_id', $doctor->id)
                    ->where('visto_por_paciente', false)
                    ->count(),
            ];
        }

        return view('paciente.mi-medico', compact(
            'doctorPrincipal',
            'doctorReferencia',
            'doctores',
            'totalResultados',
            'resultadosVistos',
            'resultadosNoVistos',
            'resultadosRecientes',
            'estadisticasPorDoctor'
        ));
    }
    
    /**
     * Mostrar todos los médicos del paciente
     */
    public function misMedicos()
    {
        $paciente = Auth::user()->paciente;
        $doctores = $paciente->doctores()->with('user')->get();
        $doctorPrincipal = $paciente->doctorPrincipal();
        
        // Estadísticas por cada doctor
        $estadisticasPorDoctor = [];
        foreach ($doctores as $doctor) {
            $estadisticasPorDoctor[$doctor->id] = [
                'total_resultados' => $paciente->resultadosMedicos()
                    ->where('doctor_id', $doctor->id)
                    ->count(),
                'resultados_nuevos' => $paciente->resultadosMedicos()
                    ->where('doctor_id', $doctor->id)
                    ->where('visto_por_paciente', false)
                    ->count(),
            ];
        }
        
        return view('paciente.mis-medicos', compact(
            'doctores', 
            'doctorPrincipal', 
            'estadisticasPorDoctor'
        ));
    }

    /**
     * Mostrar notificaciones del paciente
     */
    public function notificaciones()
    {
        $user = Auth::user();
        $notificaciones = Notificacion::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // Marcar todas como leídas
        foreach ($notificaciones as $notificacion) {
            if (!$notificacion->leida) {
                $notificacion->marcarComoLeida();
            }
        }

        return view('paciente.notificaciones', compact('notificaciones'));
    }

    /**
     * Perfil del paciente
     */
    public function perfil()
    {
        $paciente = Auth::user()->paciente;
        return view('paciente.perfil', compact('paciente'));
    }

    /**
     * Actualizar perfil del paciente
     */
    public function perfilUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @var \App\Models\Paciente $paciente */
        $paciente = $user->paciente;

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Actualizar usuario
            $user->update([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
            ]);

            // Cambiar contraseña si se proporcionó una nueva
            if ($request->filled('password_actual') && $request->filled('password')) {
                // Verificar contraseña actual
                if (!Hash::check($request->password_actual, $user->password)) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withErrors(['password_actual' => 'La contraseña actual es incorrecta'])
                        ->withInput();
                }

                $validator = Validator::make($request->all(), [
                    'password' => 'required|string|min:8|confirmed',
                ]);

                if ($validator->fails()) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            // Registrar acción
            LogSistema::registrar(
                'Actualización de perfil de paciente',
                'pacientes',
                $paciente->id,
                "Perfil del paciente {$user->nombre} {$user->apellido} actualizado"
            );

            DB::commit();

            return redirect()->route('paciente.perfil')
                ->with('success', 'Perfil actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al actualizar perfil: ' . $e->getMessage()])
                ->withInput();
        }
    }
}