@extends('layouts.dashboard')

@section('title', 'Configuración del Sistema')

@section('page-title', 'Configuración del Sistema')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Configuración</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')

{{-- Mensajes de éxito o error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Error:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Configuración General</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.configuracion.update') }}" method="POST" class="dashboard-form">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-light">
                                <i class="fas fa-hospital text-primary"></i>
                            </div>
                        </div>
                        <div class="ms-2">
                            <h5 class="mb-0">Información del Hospital</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="alert alert-info mb-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle me-2"></i>
                            </div>
                            <div>
                                <p class="mb-0">Configure la información básica del hospital y los parámetros del sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Información básica -->
            <div class="form-section">
                <h6 class="form-section-title">Información Básica</h6>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="config_nombre_hospital" class="form-label">Nombre del Hospital</label>
                        <input type="text" class="form-control @error('config_nombre_hospital') is-invalid @enderror" 
                            id="config_nombre_hospital" name="config_nombre_hospital" 
                            value="{{ old('config_nombre_hospital', \App\Models\ConfiguracionSistema::obtenerValor('nombre_hospital', 'Hospital Sistema Laravel')) }}">
                        @error('config_nombre_hospital')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Este nombre aparecerá en los correos y notificaciones.</div>
                    </div>
                </div>
            </div>
            
            <!-- Configuración de Login -->
            <div class="form-section">
                <h6 class="form-section-title">Configuración de Seguridad</h6>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="config_max_intentos_login" class="form-label">Intentos Máximos de Login</label>
                        <input type="number" class="form-control @error('config_max_intentos_login') is-invalid @enderror" 
                            id="config_max_intentos_login" name="config_max_intentos_login" 
                            value="{{ old('config_max_intentos_login', \App\Models\ConfiguracionSistema::obtenerValor('max_intentos_login', 3)) }}" 
                            min="1" max="10">
                        @error('config_max_intentos_login')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Número de intentos fallidos antes de bloquear la cuenta.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="config_tiempo_bloqueo_minutos" class="form-label">Tiempo de Bloqueo (minutos)</label>
                        <input type="number" class="form-control @error('config_tiempo_bloqueo_minutos') is-invalid @enderror" 
                            id="config_tiempo_bloqueo_minutos" name="config_tiempo_bloqueo_minutos" 
                            value="{{ old('config_tiempo_bloqueo_minutos', \App\Models\ConfiguracionSistema::obtenerValor('tiempo_bloqueo_minutos', 30)) }}" 
                            min="5" max="1440">
                        @error('config_tiempo_bloqueo_minutos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Minutos que permanecerá bloqueada una cuenta.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="config_tiempo_expiracion_token" class="form-label">Expiración del Token (minutos)</label>
                        <input type="number" class="form-control @error('config_tiempo_expiracion_token') is-invalid @enderror" 
                            id="config_tiempo_expiracion_token" name="config_tiempo_expiracion_token" 
                            value="{{ old('config_tiempo_expiracion_token', \App\Models\ConfiguracionSistema::obtenerValor('tiempo_expiracion_token', 60)) }}" 
                            min="5" max="1440">
                        @error('config_tiempo_expiracion_token')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Validez del token de recuperación de contraseña.</div>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="text-end mt-4">
                <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#resetConfigModal">
                    <i class="fas fa-undo me-1"></i> Restablecer por Defecto
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Nueva Sección: Cambio de Email del Administrador --}}
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-envelope me-2"></i>
            Cambiar Email de Acceso
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.cambiar-email.update') }}" method="POST" id="changeEmailForm">
            @csrf
            @method('PUT')
            
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-light">
                                <i class="fas fa-at text-info"></i>
                            </div>
                        </div>
                        <div class="ms-2">
                            <h6 class="mb-0">Correo Electrónico</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="alert alert-info mb-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle me-2"></i>
                            </div>
                            <div>
                                <p class="mb-0">Email actual: <strong>{{ auth()->user()->email }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h6 class="form-section-title">Actualizar Email</h6>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="email_actual" class="form-label required">Email Actual</label>
                        <input type="email" 
                               class="form-control @error('email_actual') is-invalid @enderror" 
                               id="email_actual" 
                               name="email_actual" 
                               value="{{ old('email_actual') }}"
                               placeholder="Confirme su email actual">
                        @error('email_actual')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="nuevo_email" class="form-label required">Nuevo Email</label>
                        <input type="email" 
                               class="form-control @error('nuevo_email') is-invalid @enderror" 
                               id="nuevo_email" 
                               name="nuevo_email" 
                               value="{{ old('nuevo_email') }}"
                               placeholder="Ingrese el nuevo email">
                        @error('nuevo_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Este será su nuevo email de acceso</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="password_confirmacion" class="form-label required">Contraseña de Confirmación</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password_confirmacion') is-invalid @enderror" 
                                   id="password_confirmacion" 
                                   name="password_confirmacion" 
                                   placeholder="Confirme con su contraseña">
                            <button class="btn btn-outline-secondary password-toggle" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmacion')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                </div>
                                <div>
                                    <strong>Importante:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Después del cambio, deberá usar el nuevo email para iniciar sesión</li>
                                        <li>Asegúrese de que el nuevo email sea válido y accesible</li>
                                        <li>Se registrará esta acción en el log del sistema</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-end mt-4">
                <button type="button" class="btn btn-secondary me-2" onclick="clearEmailForm()">
                    <i class="fas fa-times me-1"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-envelope me-1"></i> Cambiar Email
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Sección: Cambio de Contraseña del Administrador --}}
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-key me-2"></i>
            Cambiar Contraseña
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.cambiar-password.update') }}" method="POST" id="changePasswordForm">
            @csrf
            @method('PUT')
            
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-light">
                                <i class="fas fa-shield-alt text-warning"></i>
                            </div>
                        </div>
                        <div class="ms-2">
                            <h6 class="mb-0">Seguridad de la Cuenta</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="alert alert-warning mb-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                            </div>
                            <div>
                                <p class="mb-0">Mantenga su cuenta segura cambiando regularmente su contraseña.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h6 class="form-section-title">Actualizar Contraseña</h6>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="password_actual" class="form-label required">Contraseña Actual</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password_actual') is-invalid @enderror" 
                                   id="password_actual" 
                                   name="password_actual" 
                                   placeholder="Ingrese su contraseña actual">
                            <button class="btn btn-outline-secondary password-toggle" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_actual')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="password" class="form-label required">Nueva Contraseña</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Mínimo 8 caracteres"
                                   minlength="8">
                            <button class="btn btn-outline-secondary password-toggle" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Mínimo 8 caracteres</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="password_confirmation" class="form-label required">Confirmar Nueva Contraseña</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Repita la nueva contraseña"
                                   minlength="8">
                            <button class="btn btn-outline-secondary password-toggle" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-lightbulb me-2"></i>
                                </div>
                                <div>
                                    <strong>Recomendaciones de seguridad:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Use al menos 8 caracteres</li>
                                        <li>Combine letras mayúsculas y minúsculas</li>
                                        <li>Incluya números y símbolos</li>
                                        <li>Evite usar información personal</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-end mt-4">
                <button type="button" class="btn btn-secondary me-2" onclick="clearPasswordForm()">
                    <i class="fas fa-times me-1"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-key me-1"></i> Cambiar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <!-- Mantenimiento y respaldo -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Mantenimiento del Sistema</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-md">
                            <div class="avatar-initial rounded-circle bg-info">
                                <i class="fas fa-database"></i>
                            </div>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Respaldo de Base de Datos</h6>
                        <p class="text-muted mb-0">Crea un respaldo completo de la base de datos</p>
                    </div>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#backupModal">
                            <i class="fas fa-download me-1"></i> Generar Respaldo
                        </button>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-md">
                            <div class="avatar-initial rounded-circle bg-warning">
                                <i class="fas fa-broom"></i>
                            </div>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Limpiar Caché</h6>
                        <p class="text-muted mb-0">Elimina la caché del sistema</p>
                    </div>
                    <div class="ms-auto">
                        <form action="{{ route('admin.cache.limpiar') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning" onclick="return confirm('¿Está seguro de limpiar la caché del sistema?')">
                                <i class="fas fa-broom me-1"></i> Limpiar Caché
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-md">
                            <div class="avatar-initial rounded-circle bg-danger">
                                <i class="fas fa-trash"></i>
                            </div>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Limpiar Registros Antiguos</h6>
                        <p class="text-muted mb-0">Elimina logs del sistema con más de 30 días</p>
                    </div>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#purgeLogsModal">
                            <i class="fas fa-trash me-1"></i> Purgar Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <!-- Información del sistema -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Sistema</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span>Versión de Laravel</span>
                        <span class="badge bg-primary">{{ app()->version() }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span>Versión de PHP</span>
                        <span class="badge bg-success">{{ phpversion() }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span>Servidor Web</span>
                        <span class="badge bg-info">{{ isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Desconocido' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span>Versión del Sistema</span>
                        <span class="badge bg-dark">1.0.0</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span>Doctores Registrados</span>
                        <span class="badge bg-primary">{{ \App\Models\Doctor::count() }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span>Pacientes Registrados</span>
                        <span class="badge bg-success">{{ \App\Models\Paciente::count() }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal para generar respaldo -->
<div class="modal fade" id="backupModal" tabindex="-1" aria-labelledby="backupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="backupModalLabel">Generar Respaldo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea generar un respaldo de la base de datos?</p>
                <p>Este proceso puede tomar varios minutos dependiendo del tamaño de la base de datos.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info" onclick="alert('Funcionalidad de respaldo aún no implementada')">Generar Respaldo</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para purgar logs -->
<div class="modal fade" id="purgeLogsModal" tabindex="-1" aria-labelledby="purgeLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.logs.purgar') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purgeLogsModalLabel">Purgar Logs Antiguos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar los logs antiguos?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                    <div class="form-group">
                        <label for="dias" class="form-label">Eliminar logs de más de:</label>
                        <select class="form-select" id="dias" name="dias">
                            <option value="30">30 días</option>
                            <option value="60">60 días</option>
                            <option value="90">90 días</option>
                            <option value="180">6 meses</option>
                            <option value="365">1 año</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Purgar Logs</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal para restablecer configuración -->
<div class="modal fade" id="resetConfigModal" tabindex="-1" aria-labelledby="resetConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.configuracion.reset') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetConfigModalLabel">Restablecer Configuración</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea restablecer todas las configuraciones a sus valores por defecto?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Restablecer</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Auto ocultar alertas después de 5 segundos
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            if (!alert.classList.contains('alert-info')) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        });
    }, 5000);

    // Funciones para cambio de contraseña
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle para mostrar/ocultar contraseña
        const passwordToggles = document.querySelectorAll('.password-toggle');
        passwordToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const input = this.previousElementSibling;
                
                if (input) {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    // Cambiar el icono
                    this.innerHTML = type === 'password' 
                        ? '<i class="fas fa-eye"></i>' 
                        : '<i class="fas fa-eye-slash"></i>';
                }
            });
        });

        // Validación en tiempo real para confirmación de contraseña
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        
        function validatePasswordMatch() {
            if (password.value && passwordConfirmation.value) {
                if (password.value !== passwordConfirmation.value) {
                    passwordConfirmation.setCustomValidity('Las contraseñas no coinciden');
                    passwordConfirmation.classList.add('is-invalid');
                } else {
                    passwordConfirmation.setCustomValidity('');
                    passwordConfirmation.classList.remove('is-invalid');
                }
            }
        }
        
        if (password && passwordConfirmation) {
            password.addEventListener('input', validatePasswordMatch);
            passwordConfirmation.addEventListener('input', validatePasswordMatch);
        }
    });

    // Limpiar formulario de contraseña
    function clearPasswordForm() {
        document.getElementById('password_actual').value = '';
        document.getElementById('password').value = '';
        document.getElementById('password_confirmation').value = '';
        
        // Limpiar clases de validación
        document.querySelectorAll('#changePasswordForm .is-invalid').forEach(function(element) {
            element.classList.remove('is-invalid');
        });
    }

    // Limpiar formulario de email
    function clearEmailForm() {
        document.getElementById('email_actual').value = '';
        document.getElementById('nuevo_email').value = '';
        document.getElementById('password_confirmacion').value = '';
        
        // Limpiar clases de validación
        document.querySelectorAll('#changeEmailForm .is-invalid').forEach(function(element) {
            element.classList.remove('is-invalid');
        });
    }
</script>
@endpush

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-section {
    margin-bottom: 2rem;
}

.form-section-title {
    font-weight: 600;
    color: #495057;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #dee2e6;
}

.avatar {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.avatar-sm {
    width: 2rem;
    height: 2rem;
}

.avatar-md {
    width: 3rem;
    height: 3rem;
}

.avatar-initial {
    color: white;
    font-size: 1rem;
}

.input-group .btn-outline-secondary {
    border-left: 0;
}

#changePasswordForm .input-group input:focus + .btn-outline-secondary {
    border-color: #80bdff;
}
</style>
@endpush