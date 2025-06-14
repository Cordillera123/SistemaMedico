<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionSistema;
use App\Models\Doctor;
use App\Models\LogSistema;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Faker\Provider\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdminController extends BaseController
{
   public function __construct()
{
    $this->middleware('auth');
    $this->middleware('role:administrador');
}

    /**
     * Dashboard del administrador
     */
    public function dashboard()
    {
        // Obtener estadísticas del sistema
        $totalDoctores = Doctor::count();
        $totalPacientes = \App\Models\Paciente::count();
        $totalResultados = \App\Models\ResultadoMedico::count();
        $ultimosLogs = LogSistema::with('user')->latest()->take(10)->get();
        
        // Doctores recientemente añadidos
        $ultimosDoctores = Doctor::with('user')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact(
            'totalDoctores', 
            'totalPacientes', 
            'totalResultados', 
            'ultimosLogs',
            'ultimosDoctores'
        ));
    }

    /**
     * Mostrar lista de doctores
     */
    public function doctoresIndex()
    {
        $doctores = Doctor::with('user')->paginate(10);
        return view('admin.doctores.index', compact('doctores'));
    }

    /**
     * Mostrar formulario para crear doctor
     */
    public function doctoresCreate()
    {
        return view('admin.doctores.create');
    }

    /**
     * Almacenar nuevo doctor
     */
    public function doctoresStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'especialidad' => 'required|string|max:255',
            'licencia_medica' => 'required|string|max:255|unique:doctores',
            'biografia' => 'nullable|string',
            'horario_consulta' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Buscar el rol de doctor
            $doctorRole = Role::where('nombre', 'doctor')->first();

            if (!$doctorRole) {
                throw new \Exception('No se encontró el rol de doctor');
            }

            // Crear usuario
            $user = User::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'role_id' => $doctorRole->id,
                'activo' => true,
            ]);

            // Crear doctor
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'especialidad' => $request->especialidad,
                'licencia_medica' => $request->licencia_medica,
                'biografia' => $request->biografia,
                'horario_consulta' => $request->horario_consulta,
            ]);

            // Registrar acción
            LogSistema::registrar(
                'Creación de doctor', 
                'doctores', 
                $doctor->id,
                "Doctor {$user->nombre} {$user->apellido} creado"
            );

            DB::commit();

            return redirect()->route('admin.doctores.index')
                ->with('success', 'Doctor creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al crear doctor: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Mostrar lista de usuarios con información de bloqueo
     */
   public function usuariosIndex(Request $request)
    {
        $query = User::with('role');

        // Filtros
        if ($request->filled('estado')) {
            switch ($request->estado) {
                case 'bloqueados':
                    $query->bloqueados();
                    break;
                case 'con_intentos':
                    $query->conIntentosFallidos();
                    break;
                case 'inactivos':
                    $query->where('activo', false);
                    break;
                case 'activos':
                    $query->where('activo', true)->whereNull('bloqueado_hasta');
                    break;
            }
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%")
                  ->orWhere('username', 'like', "%{$buscar}%");
            });
        }

        // AGREGADO: Soporte para resultados por página
        $perPage = $request->get('per_page', 15); // Por defecto 15
        $perPage = in_array($perPage, [15, 25, 50, 100]) ? $perPage : 15; // Validar valores permitidos
        
        $usuarios = $query->latest()->paginate($perPage)->appends($request->query());
        $roles = Role::all();
        
        $stats = [
            'total' => User::count(),
            'activos' => User::where('activo', true)->count(),
            'bloqueados' => User::bloqueados()->count(),
            'con_intentos' => User::conIntentosFallidos()->count(),
        ];
        
        return view('admin.usuarios.index', compact('usuarios', 'roles', 'stats'));
    }

    /**
     * Desbloquear usuario específico
     */
    public function desbloquearUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);
            
            if (!$usuario->estaBloqueado()) {
                return redirect()->back()
                    ->with('info', "El usuario {$usuario->nombre_completo} no está bloqueado.");
            }

            $usuario->desbloquear();
            
            LogSistema::registrar(
                'Desbloqueo manual de usuario', 
                'users', 
                $usuario->id,
                "Usuario {$usuario->nombre_completo} desbloqueado manualmente por administrador"
            );

            return redirect()->back()
                ->with('success', "Usuario {$usuario->nombre_completo} desbloqueado exitosamente.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Error al desbloquear usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Desbloquear múltiples usuarios
     */
    public function desbloquearUsuarios(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuarios' => 'required|array|min:1',
            'usuarios.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors(['usuarios' => 'Debe seleccionar al menos un usuario válido.']);
        }

        DB::beginTransaction();
        
        try {
            $usuariosDesbloqueados = [];
            $usuariosYaDesbloqueados = [];
            
            foreach ($request->usuarios as $userId) {
                $usuario = User::find($userId);
                
                if ($usuario->estaBloqueado()) {
                    $usuario->desbloquear();
                    $usuariosDesbloqueados[] = $usuario->nombre_completo;
                    
                    LogSistema::registrar(
                        'Desbloqueo masivo de usuario', 
                        'users', 
                        $usuario->id,
                        "Usuario {$usuario->nombre_completo} desbloqueado en operación masiva"
                    );
                } else {
                    $usuariosYaDesbloqueados[] = $usuario->nombre_completo;
                }
            }
            
            DB::commit();
            
            $mensaje = '';
            if (!empty($usuariosDesbloqueados)) {
                $mensaje .= 'Usuarios desbloqueados: ' . implode(', ', $usuariosDesbloqueados) . '. ';
            }
            if (!empty($usuariosYaDesbloqueados)) {
                $mensaje .= 'Ya estaban desbloqueados: ' . implode(', ', $usuariosYaDesbloqueados) . '.';
            }
            
            return redirect()->back()->with('success', $mensaje);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al desbloquear usuarios: ' . $e->getMessage()]);
        }
    }

    /**
     * Resetear intentos fallidos de un usuario
     */
    public function resetearIntentos($id)
    {
        try {
            $usuario = User::findOrFail($id);
            
            if ($usuario->intentos_fallidos == 0) {
                return redirect()->back()
                    ->with('info', "El usuario {$usuario->nombre_completo} no tiene intentos fallidos.");
            }

            $intentosAnteriores = $usuario->intentos_fallidos;
            $usuario->update(['intentos_fallidos' => 0]);
            
            LogSistema::registrar(
                'Reset de intentos fallidos', 
                'users', 
                $usuario->id,
                "Intentos fallidos del usuario {$usuario->nombre_completo} resetreados de {$intentosAnteriores} a 0"
            );

            return redirect()->back()
                ->with('success', "Intentos fallidos de {$usuario->nombre_completo} resetreados exitosamente.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Error al resetear intentos: ' . $e->getMessage()]);
        }
    }

    /**
     * Activar/Desactivar usuario
     */
    public function toggleActivoUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);
            
            $nuevoEstado = !$usuario->activo;
            $usuario->update(['activo' => $nuevoEstado]);
            
            // Si se activa, limpiar bloqueo e intentos
            if ($nuevoEstado) {
                $usuario->desbloquear();
            }
            
            LogSistema::registrar(
                $nuevoEstado ? 'Activación de usuario' : 'Desactivación de usuario', 
                'users', 
                $usuario->id,
                "Usuario {$usuario->nombre_completo} " . ($nuevoEstado ? 'activado' : 'desactivado')
            );

            $mensaje = "Usuario {$usuario->nombre_completo} " . ($nuevoEstado ? 'activado' : 'desactivado') . " exitosamente.";
            
            return redirect()->back()->with('success', $mensaje);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Error al cambiar estado del usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Ver detalles de usuario con información de seguridad
     */
     public function usuarioShow($id)
    {
        $usuario = User::with(['role'])->findOrFail($id);
        
        // Obtener información adicional según el rol
        $infoAdicional = null;
        if ($usuario->isDoctor()) {
            $infoAdicional = $usuario->doctor()->with('pacientes')->first();
        } elseif ($usuario->isPaciente()) {
            $infoAdicional = $usuario->paciente()->with('doctores')->first();
        }
        
        // Obtener logs recientes del usuario
        $logsRecientes = LogSistema::where('user_id', $usuario->id)
            ->latest()
            ->take(10)
            ->get();
        
        return view('admin.usuarios.show', compact('usuario', 'infoAdicional', 'logsRecientes'));
    }

    /**
     * Limpiar todos los usuarios bloqueados cuyo tiempo ya expiró
     */
    public function limpiarBloqueos()
    {
        try {
            $usuariosLimpiados = User::whereNotNull('bloqueado_hasta')
                ->where('bloqueado_hasta', '<=', Carbon::now())
                ->get();
            
            $cantidad = $usuariosLimpiados->count();
            
            if ($cantidad > 0) {
                foreach ($usuariosLimpiados as $usuario) {
                    $usuario->desbloquear();
                }
                
                LogSistema::registrar(
                    'Limpieza automática de bloqueos expirados', 
                    'users', 
                    null,
                    "Se limpiaron {$cantidad} bloqueos expirados"
                );
                
                return redirect()->back()
                    ->with('success', "Se limpiaron {$cantidad} bloqueos expirados.");
            } else {
                return redirect()->back()
                    ->with('info', 'No hay bloqueos expirados para limpiar.');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Error al limpiar bloqueos: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar información de un doctor
     */
    public function doctoresShow($id)
    {
        $doctor = Doctor::with(['user', 'pacientes', 'pacientes.user'])->findOrFail($id);
        return view('admin.doctores.show', compact('doctor'));
    }

    /**
     * Mostrar formulario para editar doctor
     */
    public function doctoresEdit($id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        return view('admin.doctores.edit', compact('doctor'));
    }

    /**
     * Actualizar doctor
     */
 
public function doctoresUpdate(Request $request, $id)
{
    $doctor = Doctor::findOrFail($id);
    $user = $doctor->user;

    $validator = Validator::make($request->all(), [
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'especialidad' => 'required|string|max:255',
        'licencia_medica' => 'required|string|max:255|unique:doctores,licencia_medica,' . $doctor->id,
        'biografia' => 'nullable|string',
        'horario_consulta' => 'nullable|string|max:255',
        'activo' => 'nullable|in:0,1', // Permitir 0, 1 o null
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    DB::beginTransaction();

    try {
        // Convertir el valor del checkbox a boolean
        $activo = $request->input('activo', 0) == 1;
        
        // Actualizar usuario
        $user->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'username' => $request->username,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'activo' => $activo,
        ]);

        // Actualizar doctor
        $doctor->update([
            'especialidad' => $request->especialidad,
            'licencia_medica' => $request->licencia_medica,
            'biografia' => $request->biografia,
            'horario_consulta' => $request->horario_consulta,
        ]);

        // Cambiar contraseña si se proporcionó una nueva
        if ($request->filled('password')) {
            $validator = Validator::make($request->all(), [
                'password' => 'string|min:8|confirmed',
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
            'Actualización de doctor', 
            'doctores', 
            $doctor->id,
            "Doctor {$user->nombre} {$user->apellido} actualizado"
        );

        DB::commit();

        return redirect()->route('admin.doctores.index')
            ->with('success', 'Doctor actualizado exitosamente');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['general' => 'Error al actualizar doctor: ' . $e->getMessage()])
            ->withInput();
    }
}

    /**
 * Eliminar solo el doctor (desasignar de pacientes, mantener resultados)
 */
public function doctoresDestroy($id)
{
    $doctor = Doctor::with('user')->findOrFail($id);
    $user = $doctor->user;

    DB::beginTransaction();

    try {
        // Contar cuántos resultados médicos tiene este doctor
        $totalResultados = \App\Models\ResultadoMedico::where('doctor_id', $doctor->id)->count();
        
        // Desasignar el doctor de todos sus pacientes (eliminar relaciones)
        $doctor->pacientes()->detach();
        
        // Registrar acción antes de eliminar
        LogSistema::registrar(
            'Eliminación de doctor', 
            'doctores', 
            $doctor->id,
            "Doctor {$user->nombre} {$user->apellido} eliminado (manteniendo {$totalResultados} resultados médicos)"
        );

        // Eliminar doctor y usuario (soft delete)
        $doctor->delete();
        $user->delete();

        DB::commit();

        return redirect()->route('admin.doctores.index')
            ->with('success', "Doctor {$user->nombre} {$user->apellido} eliminado exitosamente. Sus {$totalResultados} resultados médicos se mantienen en el sistema.");
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['general' => 'Error al eliminar doctor: ' . $e->getMessage()]);
    }
}

/**
 * Eliminar doctor y todos sus resultados médicos
 */
public function doctoresForceDestroy($id)
{
    $doctor = Doctor::with('user')->findOrFail($id);
    $user = $doctor->user;

    DB::beginTransaction();

    try {
        // Obtener todos los resultados médicos del doctor
        $resultados = \App\Models\ResultadoMedico::where('doctor_id', $doctor->id)->get();
        $cantidadResultados = $resultados->count();
        
        // Eliminar archivos físicos y registros de resultados
        foreach ($resultados as $resultado) {
            // Eliminar archivo PDF físico
            if ($resultado->archivo_pdf && \Illuminate\Support\Facades\Storage::disk('public')->exists($resultado->archivo_pdf)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($resultado->archivo_pdf);
            }
            
            // Eliminar registro de resultado médico
            $resultado->delete();
        }
        
        // Desasignar el doctor de todos sus pacientes
        $doctor->pacientes()->detach();
        
        // Registrar acción antes de eliminar
        LogSistema::registrar(
            'Eliminación completa de doctor y resultados', 
            'doctores', 
            $doctor->id,
            "Doctor {$user->nombre} {$user->apellido} y {$cantidadResultados} resultados médicos eliminados permanentemente"
        );

        // Eliminar doctor y usuario
        $doctor->delete();
        $user->delete();

        DB::commit();

        return redirect()->route('admin.doctores.index')
            ->with('success', "Doctor {$user->nombre} {$user->apellido} y {$cantidadResultados} resultados médicos eliminados exitosamente.");
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['general' => 'Error al eliminar doctor y resultados: ' . $e->getMessage()]);
    }
}

    /**
     * Mostrar logs del sistema
     */
    public function logs(Request $request)
    {
        $query = LogSistema::with('user');

        // Filtros
        if ($request->filled('user_id')) {
            $query->porUsuario($request->user_id);
        }

        if ($request->filled('accion')) {
            $query->porAccion($request->accion);
        }

        if ($request->filled('tabla')) {
            $query->porTabla($request->tabla);
        }

        if ($request->filled('fecha')) {
            $query->porFecha($request->fecha);
        }

        $logs = $query->latest()->paginate(20);
        
        // Obtener opciones para filtros
        $usuarios = User::with('role')->select('id', 'nombre', 'apellido', 'role_id')->get();
        $acciones = LogSistema::select('accion')->distinct()->get()->pluck('accion');
        $tablas = LogSistema::select('tabla_afectada')->distinct()->get()->pluck('tabla_afectada');
        
        return view('admin.logs.index', compact('logs', 'usuarios', 'acciones', 'tablas'));
    }

    /**
     * Mostrar configuración del sistema
     */
    public function configuracion()
    {
        $configuraciones = \App\Models\ConfiguracionSistema::where('editable', true)->get();
        return view('admin.configuracion.index', compact('configuraciones'));
    }

    /**
     * Actualizar configuración del sistema
     */

      /**
     * Actualizar configuración del sistema
     */
    public function configuracionUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'config_nombre_hospital' => 'nullable|string|max:255',
            'config_max_intentos_login' => 'nullable|integer|min:1|max:10',
            'config_tiempo_bloqueo_minutos' => 'nullable|integer|min:5|max:1440',
            'config_tiempo_expiracion_token' => 'nullable|integer|min:5|max:1440',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            foreach ($request->except('_token', '_method') as $clave => $valor) {
                if (strpos($clave, 'config_') === 0) {
                    $clave = substr($clave, 7); // Quitar el prefijo 'config_'
                    ConfiguracionSistema::establecerValor($clave, $valor);
                }
            }

            LogSistema::registrar('Actualización de configuración del sistema');
            
            DB::commit();

            return redirect()->back()->with('success', 'Configuración actualizada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al actualizar configuración: ' . $e->getMessage()]);
        }
    }

    /**
     * Restablecer configuración por defecto
     */
    public function configuracionReset(Request $request)
    {
        DB::beginTransaction();

        try {
            ConfiguracionSistema::resetearPorDefecto();
            
            LogSistema::registrar('Restablecimiento de configuración a valores por defecto');
            
            DB::commit();

            return redirect()->back()->with('success', 'Configuración restablecida a valores por defecto');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al restablecer configuración: ' . $e->getMessage()]);
        }
    }

    /**
     * Limpiar caché del sistema
     */
    public function limpiarCache(Request $request)
    {
        try {
            Cache::flush();
            
            LogSistema::registrar('Limpieza de caché del sistema');
            
            return redirect()->back()->with('success', 'Caché del sistema limpiada exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Error al limpiar caché: ' . $e->getMessage()]);
        }
    }
    /**
 * Mostrar formulario para cambiar contraseña del administrador
 */
public function mostrarCambiarPassword()
{
    return view('admin.configuracion.cambiar-password');
}

/**
 * Actualizar contraseña del administrador
 */
public function cambiarPassword(Request $request)
{
    DB::beginTransaction();
    
    try {
        $currentUser = Auth::user();
        
        // Verificar si ambos campos están completos
        if ($request->filled('password_actual') && $request->filled('password')) {
            // Verificar contraseña actual
            if (!Hash::check($request->password_actual, $currentUser->password)) {
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
            
            // Verificar que la nueva contraseña sea diferente a la actual
            if (Hash::check($request->password, $currentUser->password)) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['password' => 'La nueva contraseña debe ser diferente a la actual'])
                    ->withInput();
            }
            
            // Actualizar contraseña - explicit type casting to avoid IDE errors
            /** @var \App\Models\User $currentUser */
            $currentUser->update([
                'password' => Hash::make($request->password),
            ]);
            
            // Alternativa si el error persiste:
            // User::where('id', $currentUser->id)->update([
            //     'password' => Hash::make($request->password),
            // ]);
            
            // Registrar la acción en el log del sistema
            LogSistema::registrar(
                'Cambio de contraseña', 
                'users', 
                $currentUser->id,
                "Administrador {$currentUser->nombre} {$currentUser->apellido} cambió su contraseña"
            );
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Contraseña actualizada exitosamente');
        } else {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Debe proporcionar la contraseña actual y la nueva'])
                ->withInput();
        }
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['general' => 'Error al cambiar la contraseña: ' . $e->getMessage()])
            ->withInput();
    }
}

/**
 * Actualizar email del administrador
 */
public function cambiarEmail(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email_actual' => 'required|email',
        'nuevo_email' => 'required|email|unique:users,email,' . Auth::id(),
        'password_confirmacion' => 'required|string',
    ], [
        'email_actual.required' => 'El email actual es obligatorio',
        'email_actual.email' => 'El email actual debe ser válido',
        'nuevo_email.required' => 'El nuevo email es obligatorio',
        'nuevo_email.email' => 'El nuevo email debe ser válido',
        'nuevo_email.unique' => 'Este email ya está en uso por otro usuario',
        'password_confirmacion.required' => 'La contraseña de confirmación es obligatoria',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    DB::beginTransaction();
    
    try {
        $currentUser = Auth::user();
        
        // Verificar que el email actual coincida
        if ($request->email_actual !== $currentUser->email) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['email_actual' => 'El email actual no coincide con el registrado'])
                ->withInput();
        }
        
        // Verificar que el nuevo email sea diferente al actual
        if ($request->nuevo_email === $currentUser->email) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['nuevo_email' => 'El nuevo email debe ser diferente al actual'])
                ->withInput();
        }
        
        // Verificar la contraseña de confirmación
        if (!Hash::check($request->password_confirmacion, $currentUser->password)) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['password_confirmacion' => 'La contraseña de confirmación es incorrecta'])
                ->withInput();
        }
        
        // Actualizar el email
        /** @var \App\Models\User $currentUser */
        $currentUser->update([
            'email' => $request->nuevo_email,
            'email_verified_at' => null, // Requerir nueva verificación si es necesario
        ]);
        
        // Registrar la acción en el log del sistema
        LogSistema::registrar(
            'Cambio de email', 
            'users', 
            $currentUser->id,
            "Administrador {$currentUser->nombre} {$currentUser->apellido} cambió su email de {$request->email_actual} a {$request->nuevo_email}"
        );
        
        DB::commit();
        
        return redirect()->back()
            ->with('success', 'Email actualizado exitosamente');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['general' => 'Error al cambiar el email: ' . $e->getMessage()])
            ->withInput();
    }
}

    /**
     * Purgar logs antiguos
     */
/**
     * Purgar logs antiguos
     */
    public function purgarLogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dias' => 'required|integer|min:15|max:365', // Cambiado de min:30 a min:15
        ], [
            'dias.min' => 'El período mínimo para purgar logs es de 15 días.',
            'dias.max' => 'El período máximo para purgar logs es de 365 días.',
            'dias.required' => 'Debe seleccionar un período válido.',
            'dias.integer' => 'El período debe ser un número entero.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            $fecha = now()->subDays($request->dias);
            $cantidad = LogSistema::where('created_at', '<', $fecha)->count();
            
            if ($cantidad == 0) {
                return redirect()->back()->with('info', "No se encontraron logs anteriores a {$request->dias} días para eliminar.");
            }
            
            // Eliminar los logs antiguos
            LogSistema::where('created_at', '<', $fecha)->delete();
            
            // Registrar la acción de purga
            LogSistema::registrar(
                "Purga de logs antiguos", 
                'logs_sistema', 
                null,
                "Se eliminaron {$cantidad} registros de logs con más de {$request->dias} días. Fecha límite: {$fecha->format('d/m/Y H:i:s')}"
            );
            
            DB::commit();

            return redirect()->back()->with('success', "✅ Purga completada exitosamente: Se eliminaron {$cantidad} registros de logs anteriores a {$request->dias} días.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al purgar logs: ' . $e->getMessage()]);
        }
    }
    
    
}