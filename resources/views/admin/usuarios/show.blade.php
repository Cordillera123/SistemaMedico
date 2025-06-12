@extends('layouts.dashboard')

@section('title', 'Detalles de Usuario')

@section('page-title', 'Detalles de Usuario')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $usuario->nombre_completo }}</li>
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

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
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

<div class="row">
    {{-- Información del Usuario --}}
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>
                    Información Personal
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <div class="avatar-initial rounded-circle bg-{{ $usuario->isAdmin() ? 'danger' : ($usuario->isDoctor() ? 'primary' : 'success') }}">
                        <i class="fas fa-{{ $usuario->isAdmin() ? 'user-shield' : ($usuario->isDoctor() ? 'user-md' : 'user') }} fa-2x"></i>
                    </div>
                </div>
                
                <h4 class="mb-1">{{ $usuario->nombre_completo }}</h4>
                <span class="badge bg-{{ $usuario->isAdmin() ? 'danger' : ($usuario->isDoctor() ? 'primary' : 'success') }} mb-3">
                    {{ ucfirst($usuario->role->nombre) }}
                </span>
                
                {{-- Estado del usuario --}}
                <div class="mb-3">
                    @if($usuario->estaBloqueado())
                        @php $infoBloqueo = $usuario->getInfoBloqueo() @endphp
                        <div class="alert alert-danger">
                            <i class="fas fa-lock me-2"></i>
                            <strong>Usuario Bloqueado</strong><br>
                            <small>Tiempo restante: {{ $infoBloqueo['tiempo_legible'] }}</small>
                        </div>
                    @elseif(!$usuario->activo)
                        <div class="alert alert-warning">
                            <i class="fas fa-pause me-2"></i>
                            <strong>Usuario Inactivo</strong>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check me-2"></i>
                            <strong>Usuario Activo</strong>
                        </div>
                    @endif
                </div>
                
                {{-- Acciones rápidas --}}
                <div class="d-grid gap-2">
                    @if($usuario->estaBloqueado())
                        <form action="{{ route('admin.usuarios.desbloquear', $usuario->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100"
                                    onclick="return confirm('¿Desbloquear a {{ $usuario->nombre_completo }}?')">
                                <i class="fas fa-unlock me-1"></i> Desbloquear Usuario
                            </button>
                        </form>
                    @endif
                    
                    @if($usuario->intentos_fallidos > 0)
                        <form action="{{ route('admin.usuarios.resetear-intentos', $usuario->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100"
                                    onclick="return confirm('¿Resetear intentos fallidos?')">
                                <i class="fas fa-redo me-1"></i> Resetear Intentos
                            </button>
                        </form>
                    @endif
                    
                    @if($usuario->id !== auth()->id())
                        <form action="{{ route('admin.usuarios.toggle-activo', $usuario->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="btn btn-{{ $usuario->activo ? 'outline-danger' : 'outline-success' }} btn-sm w-100"
                                    onclick="return confirm('¿{{ $usuario->activo ? 'Desactivar' : 'Activar' }} usuario?')">
                                <i class="fas fa-{{ $usuario->activo ? 'pause' : 'play' }} me-1"></i>
                                {{ $usuario->activo ? 'Desactivar' : 'Activar' }} Usuario
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Información de Seguridad --}}
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    Información de Seguridad
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Intentos Fallidos:</strong></div>
                    <div class="col-6">
                        @if($usuario->intentos_fallidos > 0)
                            <span class="badge bg-warning">{{ $usuario->intentos_fallidos }}</span>
                        @else
                            <span class="text-success">0</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Bloqueado hasta:</strong></div>
                    <div class="col-6">
                        @if($usuario->bloqueado_hasta)
                            <small class="text-danger">
                                {{ $usuario->bloqueado_hasta->format('d/m/Y H:i') }}
                            </small>
                        @else
                            <span class="text-success">No bloqueado</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6"><strong>Token Recuperación:</strong></div>
                    <div class="col-6">
                        @if($usuario->token_recuperacion)
                            <span class="badge bg-info">Activo</span>
                            @if($usuario->expiracion_token)
                                <br><small class="text-muted">
                                    Expira: {{ $usuario->expiracion_token->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        @else
                            <span class="text-muted">No activo</span>
                        @endif
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-6"><strong>Último Acceso:</strong></div>
                    <div class="col-6">
                        <small class="text-muted">
                            {{ $usuario->updated_at ? $usuario->updated_at->format('d/m/Y H:i') : 'Nunca' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Información Detallada --}}
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Información Detallada
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nombre:</strong></td>
                                <td>{{ $usuario->nombre }}</td>
                            </tr>
                            <tr>
                                <td><strong>Apellido:</strong></td>
                                <td>{{ $usuario->apellido }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $usuario->email }}</td>
                            </tr>
                            <tr>
                                 <td><strong>Username:</strong></td>
                                <td>{{ '@' . $usuario->username }}</td>
                            </tr>
                            <tr>
                                <td><strong>Teléfono:</strong></td>
                                <td>{{ $usuario->telefono ?: 'No especificado' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Dirección:</strong></td>
                                <td>{{ $usuario->direccion ?: 'No especificado' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha de Registro:</strong></td>
                                <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Última Actualización:</strong></td>
                                <td>{{ $usuario->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Información Específica del Rol --}}
        @if($infoAdicional)
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-{{ $usuario->isDoctor() ? 'user-md' : 'user' }} me-2"></i>
                        Información de {{ ucfirst($usuario->role->nombre) }}
                    </h6>
                </div>
                <div class="card-body">
                    @if($usuario->isDoctor())
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Especialidad:</strong></td>
                                        <td>{{ $infoAdicional->especialidad }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Licencia Médica:</strong></td>
                                        <td>{{ $infoAdicional->licencia_medica }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Horario de Consulta:</strong></td>
                                        <td>{{ $infoAdicional->horario_consulta ?: 'No especificado' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pacientes Asignados:</strong></td>
                                        <td>{{ $infoAdicional->pacientes()->count() }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @if($infoAdicional->biografia)
                            <div class="mt-3">
                                <strong>Biografía:</strong>
                                <p class="mt-2">{{ $infoAdicional->biografia }}</p>
                            </div>
                        @endif
                    @elseif($usuario->isPaciente())
                        {{-- Información específica de paciente si es necesario --}}
                        <div class="row">
                            <div class="col-12">
                                <p>Información específica del paciente...</p>
                                {{-- Agregar campos específicos de paciente aquí --}}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- Logs Recientes --}}
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Actividad Reciente (Últimos 10 registros)
                </h6>
            </div>
            <div class="card-body">
                @if($logsRecientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                    <th>Tabla</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logsRecientes as $log)
                                    <tr>
                                        <td>
                                            <small>{{ $log->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                str_contains(strtolower($log->accion), 'error') ? 'danger' : 
                                                (str_contains(strtolower($log->accion), 'login') ? 'success' : 'info') 
                                            }}">
                                                {{ $log->accion }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $log->tabla_afectada ?: '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($log->detalles, 50) ?: '-' }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.logs.index', ['user_id' => $usuario->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i> Ver Todos los Logs
                        </a>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-history fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No hay registros de actividad reciente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Botón de regreso --}}
<div class="row">
    <div class="col-12">
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver a la Lista de Usuarios
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto ocultar alertas después de 5 segundos
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            if (!alert.classList.contains('alert-info')) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        });
    }, 5000);
});
</script>
@endpush

@push('styles')
<style>
.avatar {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.avatar-xl {
    width: 5rem;
    height: 5rem;
}

.avatar-initial {
    color: white;
    font-size: 0.875rem;
}

.avatar-xl .avatar-initial {
    font-size: 2rem;
}

.table-borderless td {
    border: none;
    padding: 0.25rem 0.5rem;
}

.table-borderless td:first-child {
    padding-left: 0;
    width: 40%;
}

.card .table-sm {
    margin-bottom: 0;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush