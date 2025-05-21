<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\LogSistema;
use App\Models\Role;
use App\Models\User;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

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
            'activo' => 'boolean',
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
                'username' => $request->username,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'activo' => $request->has('activo'),
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
     * Eliminar doctor
     */
    public function doctoresDestroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        $user = $doctor->user;

        DB::beginTransaction();

        try {
            // Registrar acción antes de eliminar
            LogSistema::registrar(
                'Eliminación de doctor', 
                'doctores', 
                $doctor->id,
                "Doctor {$user->nombre} {$user->apellido} eliminado"
            );

            // Eliminar doctor (soft delete)
            $doctor->delete();
            $user->delete();

            DB::commit();

            return redirect()->route('admin.doctores.index')
                ->with('success', 'Doctor eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al eliminar doctor: ' . $e->getMessage()]);
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
        $usuarios = User::select('id', 'nombre', 'apellido')->get();
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
    public function configuracionUpdate(Request $request)
    {
        foreach ($request->except('_token', '_method') as $clave => $valor) {
            if (strpos($clave, 'config_') === 0) {
                $clave = substr($clave, 7); // Quitar el prefijo 'config_'
                \App\Models\ConfiguracionSistema::establecerValor($clave, $valor);
            }
        }

        LogSistema::registrar('Actualización de configuración del sistema');
        
        return redirect()->back()->with('success', 'Configuración actualizada exitosamente');
    }
}