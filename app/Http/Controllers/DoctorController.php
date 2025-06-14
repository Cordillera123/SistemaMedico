<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\LogSistema;
use App\Models\Paciente;
use App\Models\ResultadoMedico;
use App\Models\Role;
use App\Models\TipoResultado;
use App\Models\User;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class DoctorController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:doctor');
    }

    /**
     * Dashboard del doctor
     */
    public function dashboard()
    {
        $doctor = Auth::user()->doctor;
        
        // Estadísticas para el dashboard
        $totalPacientes = $doctor->pacientes()->count();
        $totalResultados = $doctor->resultadosMedicos()->count();
        $resultadosRecientes = $doctor->resultadosMedicos()
            ->with(['paciente.user', 'tipoResultado'])
            ->latest()
            ->take(5)
            ->get();
        
        // Últimos pacientes añadidos
        $ultimosPacientes = $doctor->pacientes()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();
        
        return view('doctor.dashboard', compact(
            'doctor', 
            'totalPacientes', 
            'totalResultados', 
            'resultadosRecientes',
            'ultimosPacientes'
        ));
    }

    /**
     * Mostrar lista de pacientes del doctor
     */
   /**
 * Mostrar lista de pacientes del doctor
 */
public function pacientesIndex(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $doctor = $user->doctor;
    
    // Iniciar la consulta con la relación de usuarios y la información de pivot
    $query = $doctor->pacientes()
        ->with('user')
        ->withPivot('doctor_principal');
    
    // Aplicar filtro de cédula si existe
    if ($request->has('cedula') && !empty($request->cedula)) {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('cedula', 'LIKE', '%' . $request->cedula . '%');
        });
    }
    
    // Aplicar otros filtros si existen
    if ($request->has('genero') && !empty($request->genero)) {
        $query->where('genero', $request->genero);
    }
    
    if ($request->has('tipo_sangre') && !empty($request->tipo_sangre)) {
        $query->where('tipo_sangre', $request->tipo_sangre);
    }
    
    // Obtener los pacientes paginados
    $pacientes = $query->latest()->paginate(10);
    
    // Si es una solicitud AJAX, devolver datos en formato JSON
    if ($request->ajax()) {
        return response()->json([
            'pacientes' => $pacientes
        ]);
    }
    
    // Obtener listas para los filtros de la interfaz
    $generos = ['Masculino', 'Femenino', 'Otro'];
    $tiposSangre = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    
    // Devolver la vista con los datos
    return view('doctor.pacientes.index', compact('pacientes', 'generos', 'tiposSangre'));
}
    public function resultadosBuscarPorCedula()
{
    return view('doctor.resultados.buscar-cedula');
}
    /**
 * Buscar paciente disponible por cédula (que no esté asignado al doctor)
 */
public function buscarPacienteDisponible(Request $request)
{
    $cedula = $request->cedula;
    
    if (empty($cedula)) {
        return response()->json([
            'success' => false,
            'message' => 'Debe proporcionar una cédula para buscar'
        ]);
    }
    
    $doctor = Auth::user()->doctor;
    
    // Buscar paciente por cédula en AMBAS tablas (users Y pacientes)
    $paciente = Paciente::where(function($query) use ($cedula) {
        // Buscar en la tabla pacientes
        $query->where('cedula', $cedula)
              // O buscar en la tabla users
              ->orWhereHas('user', function($subQuery) use ($cedula) {
                  $subQuery->where('cedula', $cedula);
              });
    })->with('user')->first();
    
    if (!$paciente) {
        return response()->json([
            'success' => false,
            'message' => 'No se encontró ningún paciente con esa cédula en el sistema'
        ]);
    }
    
    // Verificar si el paciente ya está asignado a este doctor
    $yaAsignado = $doctor->pacientes()
        ->where('pacientes.id', $paciente->id)
        ->exists();
    
    if ($yaAsignado) {
        return response()->json([
            'success' => false,
            'message' => 'Este paciente ya está asignado a su lista de pacientes'
        ]);
    }
    
    // Obtener la cédula desde donde esté disponible
    $cedulaPaciente = $paciente->cedula ?? $paciente->user->cedula ?? 'No disponible';
    
    return response()->json([
        'success' => true,
        'paciente' => [
            'id' => $paciente->id,
            'nombre' => $paciente->user->nombre,
            'apellido' => $paciente->user->apellido,
            'nombre_completo' => $paciente->user->nombre_completo,
            'cedula' => $cedulaPaciente, // CORREGIDO: obtener desde donde esté disponible
            'email' => $paciente->user->email,
            'edad' => $paciente->edad,
            'genero' => $paciente->genero
        ]
    ]);
}
    // Buscar paciente por cédula (para API)
    public function buscarPacientePorCedula(Request $request)
    {
        $cedula = $request->cedula;
        
        if (empty($cedula)) {
            return response()->json([
                'success' => false,
                'message' => 'Debe proporcionar una cédula para buscar'
            ]);
        }
        
        $doctor = Auth::user()->doctor;
        $paciente = $doctor->pacientes()
            ->with('user')
            ->where('cedula', $cedula)
            ->first();
        
        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ningún paciente con esa cédula'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'paciente' => [
                'id' => $paciente->id,
                'nombre' => $paciente->user->nombre,
                'apellido' => $paciente->user->apellido,
                'nombre_completo' => $paciente->user->nombre_completo,
                'cedula' => $paciente->cedula,
                'edad' => $paciente->edad,
                'genero' => $paciente->genero
            ]
        ]);
    }
    /**
     * Mostrar formulario para crear paciente
     */
    public function pacientesCreate()
{
    // Obtener todos los pacientes que no están asignados a este doctor
    $doctor = Auth::user()->doctor;
    
    // Obtener IDs de pacientes ya asignados
    $pacientesAsignados = $doctor->pacientes()->pluck('pacientes.id')->toArray();
    
    // Buscar todos los pacientes que no están asignados
    $pacientesDisponibles = Paciente::whereNotIn('id', $pacientesAsignados)
        ->with('user') // Cargar datos del usuario
        ->get();
    
    return view('doctor.pacientes.create', compact('pacientesDisponibles'));
}

    /**
     * Almacenar nuevo paciente
     */


// Modificación del método pacientesStore para manejar el checkbox de doctor principal para nuevos pacientes

public function pacientesStore(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $doctor = $user->doctor;
    
    // Determinar si estamos creando un nuevo paciente o eligiendo uno existente
    $isExistingPatient = $request->has('paciente_existente');
    
    if ($isExistingPatient) {
        // Código para paciente existente - sin cambios
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:pacientes,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Obtener el paciente seleccionado
            $paciente = Paciente::findOrFail($request->paciente_id);
            
            // Verificar si ya existe la relación
            $yaExisteRelacion = $doctor->pacientes()->where('paciente_id', $paciente->id)->exists();
            
            if ($yaExisteRelacion) {
                return redirect()->back()
                    ->withErrors(['paciente_id' => 'Este paciente ya está asignado a usted.'])
                    ->withInput();
            }
            
            // El checkbox estará presente en la solicitud solo si está marcado
            $esDoctorPrincipal = $request->has('doctor_principal');
            
            // Establecer la relación
            $doctor->pacientes()->attach($paciente->id, [
                'doctor_principal' => $esDoctorPrincipal
            ]);
            
            // Si se establece como doctor principal, actualizar al paciente
            if ($esDoctorPrincipal) {
                $paciente->establecerDoctorPrincipal($doctor);
            }
            
            // Registrar acción
            LogSistema::registrar(
                'Asignación de paciente existente', 
                'pacientes', 
                $paciente->id,
                "Paciente {$paciente->user->nombre} {$paciente->user->apellido} asignado al doctor {$doctor->user->nombre} {$doctor->user->apellido}"
            );
            
            DB::commit();
            
            return redirect()->route('doctor.pacientes.index')
                ->with('success', 'Paciente asignado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al asignar paciente: ' . $e->getMessage()])
                ->withInput();
        }
    } else {
        // VALIDACIONES PARA NUEVO PACIENTE (SOLO TABLA PACIENTES PARA CÉDULA)
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'cedula' => [
                'required',
                'string',
                'max:20',
                'unique:pacientes,cedula', // SOLO verificar en tabla pacientes
                'regex:/^[0-9]{10}$/' // Opcional: validar formato de cédula ecuatoriana
            ],
            'fecha_nacimiento' => 'required|date|before:today',
            'genero' => 'required|string|in:Masculino,Femenino,Otro',
            'tipo_sangre' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'alergias' => 'nullable|string|max:1000',
            'antecedentes_medicos' => 'nullable|string|max:2000',
        ], [
            // Mensajes personalizados
            'cedula.unique' => 'Ya existe un paciente registrado con esta cédula.',
            'cedula.regex' => 'La cédula debe tener exactamente 10 dígitos.',
            'email.unique' => 'Ya existe un usuario registrado con este correo electrónico.',
            'username.unique' => 'Ya existe un usuario registrado con este nombre de usuario.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'genero.in' => 'El género seleccionado no es válido.',
            'tipo_sangre.in' => 'El tipo de sangre seleccionado no es válido.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Buscar el rol de paciente
            $pacienteRole = Role::where('nombre', 'paciente')->first();

            if (!$pacienteRole) {
                throw new \Exception('No se encontró el rol de paciente');
            }

            // VALIDACIÓN ADICIONAL: Doble verificación antes de crear
            $emailExiste = User::where('email', $request->email)->exists();
            $usernameExiste = User::where('username', $request->username)->exists();
            $cedulaExiste = Paciente::where('cedula', $request->cedula)->exists();
            
            if ($emailExiste) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['email' => 'El correo electrónico ya está registrado en el sistema.'])
                    ->withInput();
            }
            
            if ($usernameExiste) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['username' => 'El nombre de usuario ya está registrado en el sistema.'])
                    ->withInput();
            }
            
            if ($cedulaExiste) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['cedula' => 'La cédula ya está registrada en el sistema.'])
                    ->withInput();
            }

            // Crear usuario SIN cédula (ya que la manejas solo en pacientes)
            $newUser = User::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'role_id' => $pacienteRole->id,
                'activo' => true,
            ]);

            // Crear paciente CON cédula
            $paciente = Paciente::create([
                'user_id' => $newUser->id,
                'cedula' => $request->cedula,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'tipo_sangre' => $request->tipo_sangre,
                'alergias' => $request->alergias,
                'antecedentes_medicos' => $request->antecedentes_medicos,
            ]);

            // Verificar si el checkbox está marcado
            $esDoctorPrincipal = $request->has('doctor_principal');
            
            // Crear la relación doctor-paciente con el valor del checkbox
            $doctor->pacientes()->attach($paciente->id, [
                'doctor_principal' => $esDoctorPrincipal
            ]);

            // Registrar acción
            LogSistema::registrar(
                'Creación de paciente', 
                'pacientes', 
                $paciente->id,
                "Paciente {$newUser->nombre} {$newUser->apellido} (Cédula: {$request->cedula}) creado por el doctor {$doctor->user->nombre} {$doctor->user->apellido}"
            );

            DB::commit();

            return redirect()->route('doctor.pacientes.index')
                ->with('success', "Paciente {$newUser->nombre} {$newUser->apellido} creado exitosamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al crear paciente: ' . $e->getMessage()])
                ->withInput();
        }
    }
}

    /**
     * Mostrar información de un paciente
     */
  public function pacientesShow($id)
{
    $doctor = Auth::user()->doctor;
    
    // CAMBIO: Verificar que el paciente esté asignado a este doctor usando la nueva relación
    $paciente = $doctor->pacientes()
        ->where('pacientes.id', $id)
        ->with('user')
        ->firstOrFail();

    $resultados = ResultadoMedico::where('paciente_id', $paciente->id)
        ->where('doctor_id', $doctor->id)
        ->with('tipoResultado')
        ->latest()
        ->paginate(10);
    
    // Obtener si es el doctor principal
    $esDoctorPrincipal = $paciente->esDoctorPrincipal($doctor);
    
    // Obtener los otros doctores de este paciente
    $otrosDoctores = $paciente->doctores()
        ->where('doctores.id', '!=', $doctor->id)
        ->with('user')
        ->get();
    
    return view('doctor.pacientes.show', compact('paciente', 'resultados', 'esDoctorPrincipal', 'otrosDoctores'));
}

public function setPrincipal($id)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $doctor = $user->doctor;
    
    // Verificar si el paciente está asignado a este doctor
    $paciente = $doctor->pacientes()
        ->where('pacientes.id', $id)
        ->firstOrFail();
    
    DB::beginTransaction();
    
    try {
        // Establecer este doctor como principal
        $paciente->establecerDoctorPrincipal($doctor);
        
        // Registrar acción
        LogSistema::registrar(
            'Cambio de doctor principal', 
            'pacientes', 
            $paciente->id,
            "El doctor {$doctor->user->nombre} {$doctor->user->apellido} se estableció como doctor principal del paciente {$paciente->user->nombre} {$paciente->user->apellido}"
        );
        
        DB::commit();
        
        return redirect()->route('doctor.pacientes.show', $paciente->id)
            ->with('success', 'Ahora usted es el doctor principal de este paciente.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['general' => 'Error al establecer como doctor principal: ' . $e->getMessage()]);
    }
}

    /**
     * Mostrar formulario para editar paciente
     */
    public function pacientesEdit($id)
{
    $doctor = Auth::user()->doctor;
    
    // CAMBIO: Verificar que el paciente esté asignado a este doctor usando la nueva relación
    $paciente = $doctor->pacientes()
        ->where('pacientes.id', $id)
        ->with('user')
        ->firstOrFail();
    
    return view('doctor.pacientes.edit', compact('paciente'));
}

    /**
     * Actualizar paciente
     */
  public function pacientesUpdate(Request $request, $id)
{
    $doctor = Auth::user()->doctor;
    
    // CAMBIO: Verificar que el paciente esté asignado a este doctor
    $paciente = $doctor->pacientes()
        ->where('pacientes.id', $id)
        ->firstOrFail();
    
    $user = $paciente->user;

    // Validar solo los campos editables
    $validator = Validator::make($request->all(), [
        'tipo_sangre' => 'nullable|string|max:10',
        'direccion' => 'nullable|string|max:255',
        'alergias' => 'nullable|string',
        'antecedentes_medicos' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    DB::beginTransaction();

    try {
        // Actualizar solo la dirección en el modelo User
        $user->direccion = $request->direccion;
        $user->save();

        // Actualizar paciente con la información médica
        $paciente->tipo_sangre = $request->tipo_sangre;
        $paciente->alergias = $request->alergias;
        $paciente->antecedentes_medicos = $request->antecedentes_medicos;
        $paciente->save();

        // Registrar acción
        LogSistema::registrar(
            'Actualización de información médica de paciente', 
            'pacientes', 
            $paciente->id,
            "Información médica del paciente {$user->nombre} {$user->apellido} actualizada"
        );

        DB::commit();

        return redirect()->route('doctor.pacientes.show', $paciente->id)
            ->with('success', 'Información médica del paciente actualizada exitosamente');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['general' => 'Error al actualizar paciente: ' . $e->getMessage()])
            ->withInput();
    }
}

/**
 * Eliminar paciente de la lista del doctor (desasignar)
 */

public function pacientesDestroy($id)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $doctor = $user->doctor;
    
    // Verificar que el paciente esté asignado a este doctor
    $paciente = $doctor->pacientes()
        ->where('pacientes.id', $id)
        ->with('user')
        ->firstOrFail();
    
    DB::beginTransaction();
    
    try {
        // ELIMINADO: Ya no verificamos si hay resultados, simplemente desasignamos
        // La verificación de resultados se hace solo en el frontend para informar al usuario
        
        // Desasignar el paciente del doctor (eliminar la relación)
        $doctor->pacientes()->detach($paciente->id);
        
        // Registrar acción
        LogSistema::registrar(
            'Eliminación de paciente de lista', 
            'pacientes', 
            $paciente->id,
            "Paciente {$paciente->user->nombre} {$paciente->user->apellido} eliminado de la lista del doctor {$doctor->user->nombre} {$doctor->user->apellido} (manteniendo resultados médicos)"
        );
        
        DB::commit();
        
        return redirect()->route('doctor.pacientes.index')
            ->with('success', "Paciente {$paciente->user->nombre} {$paciente->user->apellido} eliminado de su lista exitosamente. Los resultados médicos se mantienen en el sistema.");
            
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['general' => 'Error al eliminar paciente: ' . $e->getMessage()]);
    }
}

/**
 * Forzar eliminación de paciente y sus resultados SOLO DE ESTE DOCTOR
 */
public function pacientesForceDestroy($id)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $doctor = $user->doctor;
    
    // Verificar que el paciente esté asignado a este doctor
    $paciente = $doctor->pacientes()
        ->where('pacientes.id', $id)
        ->with('user')
        ->firstOrFail();
    
    DB::beginTransaction();
    
    try {
        // IMPORTANTE: Solo eliminar resultados de ESTE doctor, no todos los resultados del paciente
        $resultados = ResultadoMedico::where('paciente_id', $paciente->id)
            ->where('doctor_id', $doctor->id) // SOLO los resultados de este doctor
            ->get();
        
        $cantidadResultados = $resultados->count();
        
        foreach ($resultados as $resultado) {
            // Eliminar archivo físico
            if ($resultado->archivo_pdf && Storage::disk('public')->exists($resultado->archivo_pdf)) {
                Storage::disk('public')->delete($resultado->archivo_pdf);
            }
            
            // Eliminar registro (soft delete si está configurado)
            $resultado->delete();
        }
        
        // Desasignar el paciente del doctor
        $doctor->pacientes()->detach($paciente->id);
        
        // Registrar acción
        LogSistema::registrar(
            'Eliminación completa de paciente y resultados del doctor', 
            'pacientes', 
            $paciente->id,
            "Paciente {$paciente->user->nombre} {$paciente->user->apellido} y {$cantidadResultados} resultado(s) médico(s) eliminados de la lista del doctor {$doctor->user->nombre} {$doctor->user->apellido}"
        );
        
        DB::commit();
        
        return redirect()->route('doctor.pacientes.index')
            ->with('success', "Paciente {$paciente->user->nombre} {$paciente->user->apellido} y {$cantidadResultados} resultado(s) médico(s) eliminados exitosamente de su lista.");
            
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['general' => 'Error al eliminar paciente y resultados: ' . $e->getMessage()]);
    }
}
    /**
     * Resultados médicos
     */
    public function resultadosIndex()
    {
        $doctor = Auth::user()->doctor;
        $resultados = ResultadoMedico::where('doctor_id', $doctor->id)
            ->with(['paciente.user', 'tipoResultado'])
            ->latest()
            ->paginate(10);
        
        return view('doctor.resultados.index', compact('resultados'));
    }


    /**
     * Mostrar formulario para subir un resultado médico
     */
   public function resultadosCreate()
    {
        $doctor = Auth::user()->doctor;
        
        // CAMBIO: Obtener pacientes usando la relación muchos-a-muchos
        $pacientes = $doctor->pacientes()->with('user')->get();
        $tiposResultados = TipoResultado::all();
        
        return view('doctor.resultados.create', compact('pacientes', 'tiposResultados'));
    }

    /**
     * Almacenar nuevo resultado médico
     */
    public function resultadosStore(Request $request)
    {
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

        $doctor = Auth::user()->doctor;
        
        // CAMBIO CLAVE: Verificar que el paciente esté asignado a este doctor
        $paciente = Paciente::findOrFail($request->paciente_id);
        
        $estaAsignado = $doctor->pacientes()
            ->where('pacientes.id', $paciente->id)
            ->exists();
            
        if (!$estaAsignado) {
            return redirect()->back()
                ->withErrors(['paciente_id' => 'Este paciente no está asignado a usted.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Almacenar archivo PDF correctamente
            $path = $request->file('archivo_pdf')->store('resultados_medicos', 'public');

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
            if (method_exists(ResultadoMedico::class, 'notificarNuevoResultado')) {
                ResultadoMedico::notificarNuevoResultado($resultado);
            }

            // Registrar acción
            LogSistema::registrar(
                'Subida de resultado médico', 
                'resultados_medicos', 
                $resultado->id,
                "Resultado médico '{$resultado->titulo}' subido para el paciente {$paciente->user->nombre} {$paciente->user->apellido}"
            );

            DB::commit();

            return redirect()->route('doctor.resultados.index')
                ->with('success', 'Resultado médico subido exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al subir resultado médico: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Mostrar información de un resultado médico
     */
    public function resultadosShow($id)
    {
        $doctor = Auth::user()->doctor;
        $resultado = ResultadoMedico::where('doctor_id', $doctor->id)
            ->with(['paciente.user', 'tipoResultado'])
            ->findOrFail($id);
        
        return view('doctor.resultados.show', compact('resultado'));
    }

    /**
     * Mostrar formulario para editar un resultado médico
     */
    public function resultadosEdit($id)
    {
        $doctor = Auth::user()->doctor;
        $resultado = ResultadoMedico::where('doctor_id', $doctor->id)
            ->findOrFail($id);
        
        $tiposResultados = TipoResultado::all();
        
        return view('doctor.resultados.edit', compact('resultado', 'tiposResultados'));
    }

    /**
     * Actualizar resultado médico
     */
    public function resultadosUpdate(Request $request, $id)
    {
        $doctor = Auth::user()->doctor;
        $resultado = ResultadoMedico::where('doctor_id', $doctor->id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo_resultado_id' => 'required|exists:tipos_resultados,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:10240',
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
                Storage::disk('public')->delete($resultado->archivo_pdf);
                
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
                if (method_exists(ResultadoMedico::class, 'notificarNuevoResultado')) {
                    ResultadoMedico::notificarNuevoResultado($resultado);
                }
            }

            // Registrar acción
            LogSistema::registrar(
                'Actualización de resultado médico', 
                'resultados_medicos', 
                $resultado->id,
                "Resultado médico '{$resultado->titulo}' actualizado"
            );

            DB::commit();

            return redirect()->route('doctor.resultados.show', $resultado->id)
                ->with('success', 'Resultado médico actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al actualizar resultado médico: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Eliminar resultado médico
     */
    public function resultadosDestroy($id)
    {
        $doctor = Auth::user()->doctor;
        $resultado = ResultadoMedico::where('doctor_id', $doctor->id)
            ->findOrFail($id);

        DB::beginTransaction();

        try {
            // Registrar acción antes de eliminar
            LogSistema::registrar(
                'Eliminación de resultado médico', 
                'resultados_medicos', 
                $resultado->id,
                "Resultado médico '{$resultado->titulo}' eliminado"
            );

            // Eliminar resultado médico (soft delete)
            $resultado->delete();

            DB::commit();

            return redirect()->route('doctor.resultados.index')
                ->with('success', 'Resultado médico eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['general' => 'Error al eliminar resultado médico: ' . $e->getMessage()]);
        }
    }

    /**
     * Perfil del doctor
     */
    public function perfil()
    {
        $doctor = Auth::user()->doctor;
        return view('doctor.perfil', compact('doctor'));
    }

    /**
     * Actualizar perfil del doctor
     */
    public function perfilUpdate(Request $request)
{
    // Obtenemos el usuario y doctor de manera explícita para evitar problemas
    $userId = Auth::id();
    $user = User::findOrFail($userId);
    $doctor = Doctor::where('user_id', $userId)->firstOrFail();

    // Validar los datos de entrada
    $validator = Validator::make($request->all(), [
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'especialidad' => 'required|string|max:255',
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
        // Actualizar usuario manualmente
        $user->nombre = $request->nombre;
        $user->apellido = $request->apellido;
        $user->email = $request->email;
        $user->telefono = $request->telefono;
        $user->direccion = $request->direccion;
        $user->save();

        // Actualizar doctor manualmente
        $doctor->especialidad = $request->especialidad;
        $doctor->biografia = $request->biografia;
        $doctor->horario_consulta = $request->horario_consulta;
        $doctor->save();

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

            // Actualizar contraseña
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Registrar acción
        LogSistema::registrar(
            'Actualización de perfil de doctor', 
            'doctores', 
            $doctor->id,
            "Perfil del doctor {$user->nombre} {$user->apellido} actualizado"
        );

        DB::commit();

        return redirect()->route('doctor.perfil')
            ->with('success', 'Perfil actualizado exitosamente');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['general' => 'Error al actualizar perfil: ' . $e->getMessage()])
            ->withInput();
    }
}
        public function resultadosPorCedulaPaciente(Request $request)
    {
        $cedula = $request->cedula;
        
        if (empty($cedula)) {
            return response()->json([
                'success' => false,
                'message' => 'Debe proporcionar una cédula para buscar'
            ]);
        }
        
        $doctor = Auth::user()->doctor;
        
        // Primero buscamos el paciente por cédula
        $paciente = $doctor->pacientes()
            ->where('cedula', $cedula)
            ->first();
        
        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ningún paciente con esa cédula'
            ]);
        }
        
        // Obtenemos los resultados médicos del paciente
        $resultados = ResultadoMedico::where('paciente_id', $paciente->id)
            ->where('doctor_id', $doctor->id)
            ->with(['tipoResultado', 'paciente.user'])
            ->latest()
            ->get();
        
        return response()->json([
            'success' => true,
            'paciente' => [
                'id' => $paciente->id,
                'nombre_completo' => $paciente->user->nombre_completo,
                'cedula' => $paciente->cedula
            ],
            'resultados' => $resultados->map(function ($resultado) {
                return [
                    'id' => $resultado->id,
                    'titulo' => $resultado->titulo,
                    'tipo' => $resultado->tipoResultado->nombre,
                    'fecha' => $resultado->fecha_resultado->format('d/m/Y'),
                    'visto' => $resultado->visto_por_paciente,
                    'url_ver' => route('doctor.resultados.show', $resultado->id),
                    'url_descargar' => route('resultados.descargar', $resultado->id)
                ];
            })
        ]);
    }
    

}