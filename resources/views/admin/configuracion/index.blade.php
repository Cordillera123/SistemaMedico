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
                        <input type="text" class="form-control" id="config_nombre_hospital" name="config_nombre_hospital" 
                            value="{{ old('config_nombre_hospital', \App\Models\ConfiguracionSistema::obtenerValor('nombre_hospital', 'Hospital Sistema Laravel')) }}">
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
                        <input type="number" class="form-control" id="config_max_intentos_login" name="config_max_intentos_login" 
                            value="{{ old('config_max_intentos_login', \App\Models\ConfiguracionSistema::obtenerValor('max_intentos_login', 3)) }}" min="1" max="10">
                        <div class="form-text">Número de intentos fallidos antes de bloquear la cuenta.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="config_tiempo_bloqueo_minutos" class="form-label">Tiempo de Bloqueo (minutos)</label>
                        <input type="number" class="form-control" id="config_tiempo_bloqueo_minutos" name="config_tiempo_bloqueo_minutos" 
                            value="{{ old('config_tiempo_bloqueo_minutos', \App\Models\ConfiguracionSistema::obtenerValor('tiempo_bloqueo_minutos', 30)) }}" min="5" max="1440">
                        <div class="form-text">Minutos que permanecerá bloqueada una cuenta.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="config_tiempo_expiracion_token" class="form-label">Expiración del Token (minutos)</label>
                        <input type="number" class="form-control" id="config_tiempo_expiracion_token" name="config_tiempo_expiracion_token" 
                            value="{{ old('config_tiempo_expiracion_token', \App\Models\ConfiguracionSistema::obtenerValor('tiempo_expiracion_token', 60)) }}" min="5" max="1440">
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
                        <button type="button" class="btn btn-warning">
                            <i class="fas fa-broom me-1"></i> Limpiar Caché
                        </button>
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
                <button type="button" class="btn btn-info">Generar Respaldo</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para purgar logs -->
<div class="modal fade" id="purgeLogsModal" tabindex="-1" aria-labelledby="purgeLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purgeLogsModalLabel">Purgar Logs Antiguos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar los logs de más de 30 días de antigüedad?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
                <div class="form-group">
                    <label for="purge-days" class="form-label">Eliminar logs de más de:</label>
                    <select class="form-select" id="purge-days">
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
                <button type="button" class="btn btn-danger">Purgar Logs</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para restablecer configuración -->
<div class="modal fade" id="resetConfigModal" tabindex="-1" aria-labelledby="resetConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog">
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
                <button type="button" class="btn btn-danger">Restablecer</button>
            </div>
        </div>
    </div>
</div>
@endsection