<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ResultadoMedicoController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Página de inicio
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de recuperación de contraseña
Route::get('/password/reset', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Ruta de inicio por defecto - redirige según el rol
    Route::get('/home', function () {
         /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isDoctor()) {
            return redirect()->route('doctor.dashboard');
        } elseif ($user->isPaciente()) {
            return redirect()->route('paciente.dashboard');
        } else {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Rol de usuario no válido');
        }
    })->name('home');

    // Rutas para administradores
     Route::prefix('admin')->middleware(['role:administrador'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Gestión de doctores
        Route::get('/doctores', [AdminController::class, 'doctoresIndex'])->name('admin.doctores.index');
        Route::get('/doctores/create', [AdminController::class, 'doctoresCreate'])->name('admin.doctores.create');
        Route::post('/doctores', [AdminController::class, 'doctoresStore'])->name('admin.doctores.store');
        Route::get('/doctores/{id}', [AdminController::class, 'doctoresShow'])->name('admin.doctores.show');
        Route::get('/doctores/{id}/edit', [AdminController::class, 'doctoresEdit'])->name('admin.doctores.edit');
        Route::put('/doctores/{id}', [AdminController::class, 'doctoresUpdate'])->name('admin.doctores.update');
        
        // RUTAS DE ELIMINACIÓN DE DOCTORES - CORREGIDAS
        Route::delete('/doctores/{id}', [AdminController::class, 'doctoresDestroy'])->name('admin.doctores.destroy');
        Route::delete('/doctores/{id}/force-delete', [AdminController::class, 'doctoresForceDestroy'])->name('admin.doctores.force-destroy');
        
        // Logs del sistema
        Route::get('/logs', [AdminController::class, 'logs'])->name('admin.logs');
        
        // Configuración del sistema
        Route::get('/configuracion', [AdminController::class, 'configuracion'])->name('admin.configuracion');
        Route::post('/configuracion', [AdminController::class, 'configuracionUpdate'])->name('admin.configuracion.update');
        Route::post('/configuracion/reset', [AdminController::class, 'configuracionReset'])->name('admin.configuracion.reset');
        
        // Ruta para cambiar email del administrador
        Route::put('/cambiar-email', [AdminController::class, 'cambiarEmail'])
            ->name('admin.cambiar-email.update');

        // Ruta para cambiar contraseña del administrador
        Route::put('/cambiar-password', [AdminController::class, 'cambiarPassword'])
            ->name('admin.cambiar-password.update');
            
        // Mantenimiento
        Route::post('/cache/limpiar', [AdminController::class, 'limpiarCache'])->name('admin.cache.limpiar');
        Route::post('/logs/purgar', [AdminController::class, 'purgarLogs'])->name('admin.logs.purgar');
    });

     Route::prefix('doctor')->middleware(['role:doctor'])->name('doctor.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de pacientes - TODAS LAS RUTAS DE PACIENTES JUNTAS
        Route::get('/pacientes', [DoctorController::class, 'pacientesIndex'])->name('pacientes.index');
        Route::get('/pacientes/create', [DoctorController::class, 'pacientesCreate'])->name('pacientes.create');
        Route::post('/pacientes', [DoctorController::class, 'pacientesStore'])->name('pacientes.store');
        // NUEVAS RUTAS PARA ELIMINAR PACIENTES
    Route::delete('/pacientes/{id}', [DoctorController::class, 'pacientesDestroy'])->name('pacientes.destroy');
    Route::delete('/pacientes/{id}/force-delete', [DoctorController::class, 'pacientesForceDestroy'])->name('pacientes.force-destroy');
    
    Route::put('/pacientes/{id}/set-principal', [DoctorController::class, 'setPrincipal'])->name('pacientes.set-principal');
        
        // RUTAS ESPECÍFICAS DE BÚSQUEDA DE PACIENTES (ANTES DE LAS RUTAS CON PARÁMETROS)
        Route::get('/pacientes/buscar-cedula', [DoctorController::class, 'buscarPacientePorCedula'])
            ->name('pacientes.buscar-cedula');
        Route::get('/pacientes/buscar-disponible', [DoctorController::class, 'buscarPacienteDisponible'])
            ->name('pacientes.buscar-disponible');
        
        // RUTAS CON PARÁMETROS DE PACIENTES (AL FINAL)
        Route::get('/pacientes/{id}', [DoctorController::class, 'pacientesShow'])->name('pacientes.show');
        Route::get('/pacientes/{id}/edit', [DoctorController::class, 'pacientesEdit'])->name('pacientes.edit');
        Route::put('/pacientes/{id}', [DoctorController::class, 'pacientesUpdate'])->name('pacientes.update');
        Route::put('/pacientes/{id}/set-principal', [DoctorController::class, 'setPrincipal'])
            ->name('pacientes.set-principal');
        
        // Gestión de resultados médicos - ORDEN CRÍTICO
        Route::get('/resultados', [DoctorController::class, 'resultadosIndex'])->name('resultados.index');
        
        // ¡IMPORTANTE! Estas rutas específicas DEBEN ir ANTES que las rutas con parámetros
        Route::get('/resultados/buscar-cedula', [DoctorController::class, 'resultadosBuscarPorCedula'])
            ->name('resultados.buscar-cedula');
        Route::get('/resultados/por-cedula', [DoctorController::class, 'resultadosPorCedulaPaciente'])
            ->name('resultados.por-cedula');
        Route::get('/resultados/create', [DoctorController::class, 'resultadosCreate'])
            ->name('resultados.create');
        
        // Rutas con parámetros van AL FINAL
        Route::post('/resultados', [DoctorController::class, 'resultadosStore'])->name('resultados.store');
        Route::get('/resultados/{id}/edit', [DoctorController::class, 'resultadosEdit'])->name('resultados.edit');
        Route::put('/resultados/{id}', [DoctorController::class, 'resultadosUpdate'])->name('resultados.update');
        Route::delete('/resultados/{id}', [DoctorController::class, 'resultadosDestroy'])->name('resultados.destroy');
        
        // Esta ruta DEBE ir al final porque captura cualquier cosa después de /resultados/
        Route::get('/resultados/{id}', [DoctorController::class, 'resultadosShow'])->name('resultados.show');
        
        // Perfil del doctor
        Route::get('/perfil', [DoctorController::class, 'perfil'])->name('perfil');
        Route::put('/perfil', [DoctorController::class, 'perfilUpdate'])->name('perfil.update');
    });

    // Rutas para pacientes
    Route::prefix('paciente')->middleware(['role:paciente'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [PacienteController::class, 'dashboard'])->name('paciente.dashboard');
        
        // Resultados médicos
        Route::get('/resultados', [PacienteController::class, 'resultadosIndex'])->name('paciente.resultados.index');
         Route::get('/resultados/{id}/descargar', [PacienteController::class, 'resultadosDescargar'])->name('paciente.resultados.descargar');
        Route::get('/resultados/{id}', [PacienteController::class, 'resultadosShow'])->name('paciente.resultados.show');
       
        
        // RUTAS FALTANTES AGREGADAS
        // Información de médicos
        Route::get('/mi-medico', [PacienteController::class, 'miMedico'])->name('paciente.mi-medico');
        Route::get('/mis-medicos', [PacienteController::class, 'misMedicos'])->name('paciente.mis-medicos');
        
        // Notificaciones
        Route::get('/notificaciones', [PacienteController::class, 'notificaciones'])->name('paciente.notificaciones');
        
        // Perfil del paciente
        Route::get('/perfil', [PacienteController::class, 'perfil'])->name('paciente.perfil');
        Route::put('/perfil', [PacienteController::class, 'perfilUpdate'])->name('paciente.perfil.update');
    });

    // Rutas generales para resultados médicos (controlador centralizado)
    Route::prefix('resultados')->group(function () {
        Route::get('/', [ResultadoMedicoController::class, 'index'])->name('resultados.index');
        Route::get('/create', [ResultadoMedicoController::class, 'create'])->name('resultados.create');
        Route::post('/', [ResultadoMedicoController::class, 'store'])->name('resultados.store');
        Route::get('/{id}', [ResultadoMedicoController::class, 'show'])->name('resultados.show');
        Route::get('/{id}/edit', [ResultadoMedicoController::class, 'edit'])->name('resultados.edit');
        Route::put('/{id}', [ResultadoMedicoController::class, 'update'])->name('resultados.update');
        Route::delete('/{id}', [ResultadoMedicoController::class, 'destroy'])->name('resultados.destroy');
        Route::get('/{id}/descargar', [ResultadoMedicoController::class, 'descargar'])->name('resultados.descargar');
    });
});

// Fallback route - para URLs que no existen
Route::fallback(function () {
    return redirect()->route('home')->with('error', 'Página no encontrada');
});
// AGREGAR AL FINAL DE web.php (TEMPORAL PARA DEBUG)
Route::get('/debug-doctor-routes', function() {
    $doctorRoutes = collect(Route::getRoutes())->filter(function($route) {
        return str_contains($route->uri(), 'doctor/resultados');
    })->map(function($route) {
        return [
            'name' => $route->getName(),
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'action' => $route->getActionName()
        ];
    })->values();
    
    return response()->json([
        'doctor_resultados_routes' => $doctorRoutes,
        'expected_route' => 'doctor/resultados/buscar-cedula',
        'route_exists' => Route::has('doctor.resultados.buscar-cedula'),
        'route_url' => Route::has('doctor.resultados.buscar-cedula') ? route('doctor.resultados.buscar-cedula') : 'NOT_FOUND'
    ]);
});
Route::get('/test-buscar-cedula-no-auth', function() {
    try {
        return view('doctor.resultados.buscar-cedula');
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al cargar la vista',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});