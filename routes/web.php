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
        Route::delete('/doctores/{id}', [AdminController::class, 'doctoresDestroy'])->name('admin.doctores.destroy');
        
        // Logs del sistema
        Route::get('/logs', [AdminController::class, 'logs'])->name('admin.logs');
        
        // Configuración del sistema
        Route::get('/configuracion', [AdminController::class, 'configuracion'])->name('admin.configuracion');
        Route::post('/configuracion', [AdminController::class, 'configuracionUpdate'])->name('admin.configuracion.update');
    });

    // Rutas para doctores
    Route::prefix('doctor')->middleware(['role:doctor'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
        
        // Gestión de pacientes
        Route::get('/pacientes', [DoctorController::class, 'pacientesIndex'])->name('doctor.pacientes.index');
        Route::get('/pacientes/create', [DoctorController::class, 'pacientesCreate'])->name('doctor.pacientes.create');
        Route::post('/pacientes', [DoctorController::class, 'pacientesStore'])->name('doctor.pacientes.store');
        Route::get('/pacientes/{id}', [DoctorController::class, 'pacientesShow'])->name('doctor.pacientes.show');
        Route::get('/pacientes/{id}/edit', [DoctorController::class, 'pacientesEdit'])->name('doctor.pacientes.edit');
        Route::put('/pacientes/{id}', [DoctorController::class, 'pacientesUpdate'])->name('doctor.pacientes.update');
        Route::put('/doctor/pacientes/{id}/set-principal', [App\Http\Controllers\DoctorController::class, 'setPrincipal'])
    ->name('doctor.pacientes.set-principal');
        // Gestión de resultados médicos
        Route::get('/resultados', [DoctorController::class, 'resultadosIndex'])->name('doctor.resultados.index');
        Route::get('/resultados/create', [DoctorController::class, 'resultadosCreate'])->name('doctor.resultados.create');
        Route::post('/resultados', [DoctorController::class, 'resultadosStore'])->name('doctor.resultados.store');
        Route::get('/resultados/{id}', [DoctorController::class, 'resultadosShow'])->name('doctor.resultados.show');
        Route::get('/resultados/{id}/edit', [DoctorController::class, 'resultadosEdit'])->name('doctor.resultados.edit');
        Route::put('/resultados/{id}', [DoctorController::class, 'resultadosUpdate'])->name('doctor.resultados.update');
        Route::delete('/resultados/{id}', [DoctorController::class, 'resultadosDestroy'])->name('doctor.resultados.destroy');
        
        // Búsqueda por cédula - API
        Route::get('/pacientes/buscar-por-cedula', [DoctorController::class, 'buscarPacientePorCedula'])
            ->name('doctor.pacientes.buscar-por-cedula');
        
        // Búsqueda de resultados por cédula - API
        Route::get('/resultados/por-cedula', [DoctorController::class, 'resultadosPorCedulaPaciente'])
            ->name('doctor.resultados.por-cedula');
        
        // Vista para búsqueda por cédula - ACTUALIZADA
        Route::get('/resultados/buscar-por-cedula', [DoctorController::class, 'resultadosBuscarPorCedula'])
            ->name('doctor.resultados.buscar-cedula');
        
        // Perfil del doctor
        Route::get('/perfil', [DoctorController::class, 'perfil'])->name('doctor.perfil');
        Route::put('/perfil', [DoctorController::class, 'perfilUpdate'])->name('doctor.perfil.update');
    });

    // Rutas para pacientes
    Route::prefix('paciente')->middleware(['role:paciente'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [PacienteController::class, 'dashboard'])->name('paciente.dashboard');
        
        // Resultados médicos
        Route::get('/resultados', [PacienteController::class, 'resultadosIndex'])->name('paciente.resultados.index');
        Route::get('/resultados/{id}', [PacienteController::class, 'resultadosShow'])->name('paciente.resultados.show');
        Route::get('/resultados/{id}/descargar', [PacienteController::class, 'resultadosDescargar'])->name('paciente.resultados.descargar');
        
        // Información de médico - URL corregida
        Route::get('/mi-medico', [PacienteController::class, 'miMedico'])->name('paciente.mi-medico');
        
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