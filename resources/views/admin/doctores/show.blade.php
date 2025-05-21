@extends('layouts.dashboard')

@section('title', 'Detalle de Doctor')

@section('page-title', 'Detalle de Doctor')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.doctores.index') }}">Doctores</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detalle</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="row">
    <div class="col-xl-4 col-lg-5">
        <!-- Tarjeta de perfil -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl mx-auto mb-3">
                        <div class="avatar-initial rounded-circle bg-primary">
                            {{ substr($doctor->user->nombre, 0, 1) }}{{ substr($doctor->user->apellido, 0, 1) }}
                        </div>
                    </div>
                    <h4 class="mb-0">{{ $doctor->user->nombre }} {{ $doctor->user->apellido }}</h4>
                    <p class="text-muted">{{ $doctor->especialidad }}</p>
                    
                    <div class="mt-3">
                        @if($doctor->user->activo)
                            <span class="badge status-badge status-active">
                                <i class="fas fa-check-circle me-1"></i> Activo
                            </span>
                        @else
                            <span class="badge status-badge status-inactive">
                                <i class="fas fa-times-circle me-1"></i> Inactivo
                            </span>
                        @endif
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Información de Contacto</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                </div>
                                <div class="flex-grow-1">{{ $doctor->user->email }}</div>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user text-primary me-2"></i>
                                </div>
                                <div class="flex-grow-1">{{ $doctor->user->username }}</div>
                            </div>
                        </li>
                        @if($doctor->user->telefono)
                            <li class="mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                    </div>
                                    <div class="flex-grow-1">{{ $doctor->user->telefono }}</div>
                                </div>
                            </li>
                        @endif
                        @if($doctor->user->direccion)
                            <li>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    </div>
                                    <div class="flex-grow-1">{{ $doctor->user->direccion }}</div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
                
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Información Profesional</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-md text-primary me-2"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-medium">Especialidad:</span> {{ $doctor->especialidad }}
                                </div>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-id-card text-primary me-2"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-medium">Licencia Médica:</span> {{ $doctor->licencia_medica }}
                                </div>
                            </div>
                        </li>
                        @if($doctor->horario_consulta)
                            <li>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-clock text-primary me-2"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-medium">Horario de Consulta:</span><br>
                                        {{ $doctor->horario_consulta }}
                                    </div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.doctores.edit', $doctor->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Editar Doctor
                    </a>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i> Eliminar Doctor
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Información de la cuenta -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de la Cuenta</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Estado</span>
                        @if($doctor->user->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Fecha de Registro</span>
                        <span>{{ $doctor->created_at->format('d/m/Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Última Actualización</span>
                        <span>{{ $doctor->updated_at->format('d/m/Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Email Verificado</span>
                        @if($doctor->user->email_verified_at)
                            <span class="badge bg-success">Verificado</span>
                        @else
                            <span class="badge bg-warning">Pendiente</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-xl-8 col-lg-7">
        <!-- Biografía y detalles -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Biografía</h5>
            </div>
            <div class="card-body">
                @if($doctor->biografia)
                    <p>{{ $doctor->biografia }}</p>
                @else
                    <p class="text-muted">No hay información biográfica disponible.</p>
                @endif
            </div>
        </div>
        
        <!-- Pacientes asignados -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Pacientes Asignados</h5>
                <span class="badge bg-primary">{{ $doctor->pacientes->count() }} pacientes</span>
            </div>
            <div class="card-body">
                @if($doctor->pacientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Edad/Género</th>
                                    <th>Contacto</th>
                                    <th>Resultados</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doctor->pacientes as $paciente)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <div class="avatar-initial rounded-circle {{ $paciente->genero == 'Masculino' ? 'bg-primary' : 'bg-info' }}">
                                                        {{ substr($paciente->user->nombre, 0, 1) }}{{ substr($paciente->user->apellido, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $paciente->user->nombre }} {{ $paciente->user->apellido }}</h6>
                                                    @if(!$paciente->user->activo)
                                                        <small class="text-danger">(Inactivo)</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $paciente->edad }} años / {{ $paciente->genero }}
                                        </td>
                                        <td>
                                            <small class="d-block">{{ $paciente->user->email }}</small>
                                            @if($paciente->user->telefono)
                                                <small class="d-block">{{ $paciente->user->telefono }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $paciente->resultadosMedicos->count() }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3">
                        <div class="mb-2">
                            <i class="fas fa-users fa-3x text-muted"></i>
                        </div>
                        <p class="mb-0">Este doctor no tiene pacientes asignados.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Actividad reciente -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actividad Reciente</h5>
            </div>
            <div class="card-body">
                @php
                    $logs = \App\Models\LogSistema::where('user_id', $doctor->user->id)
                        ->orWhere(function($query) use ($doctor) {
                            $query->where('tabla_afectada', 'doctores')
                                ->where('registro_id', $doctor->id);
                        })
                        ->latest()
                        ->take(10)
                        ->get();
                @endphp

                @if($logs->count() > 0)
                    <div class="timeline">
                        @foreach($logs as $log)
                            <div class="timeline-item">
                                <div class="timeline-item-marker">
                                    <div class="timeline-item-marker-indicator 
                                        @if(str_contains($log->accion, 'Login'))
                                            bg-success
                                        @elseif(str_contains($log->accion, 'Creación'))
                                            bg-primary
                                        @elseif(str_contains($log->accion, 'Actualización'))
                                            bg-info
                                        @elseif(str_contains($log->accion, 'Eliminación'))
                                            bg-danger
                                        @else
                                            bg-secondary
                                        @endif">
                                        <i class="fas fa-{{ 
                                            str_contains($log->accion, 'Login') ? 'sign-in-alt' : 
                                            (str_contains($log->accion, 'Creación') ? 'plus' : 
                                            (str_contains($log->accion, 'Actualización') ? 'edit' : 
                                            (str_contains($log->accion, 'Eliminación') ? 'trash' : 
                                            'history'))) 
                                        }}"></i>
                                    </div>
                                </div>
                                <div class="timeline-item-content">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">{{ $log->accion }}</span>
                                        <span class="text-muted small">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($log->detalles)
                                        <p class="text-muted mb-0 small">{{ $log->detalles }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <div class="mb-2">
                            <i class="fas fa-history fa-3x text-muted"></i>
                        </div>
                        <p class="mb-0">No hay registros de actividad.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar al doctor <strong>{{ $doctor->user->nombre }} {{ $doctor->user->apellido }}</strong>?</p>
                <p class="text-danger mb-0">Esta acción no se puede deshacer y puede afectar a los pacientes asignados a este doctor.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('admin.doctores.destroy', $doctor->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 1.5rem;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0.75rem;
        height: 100%;
        width: 2px;
        background-color: #e5e7eb;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .timeline-item-marker {
        position: absolute;
        top: 0;
        left: -1.5rem;
        width: 1.5rem;
        height: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .timeline-item-marker-indicator {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background-color: #1f2937;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .timeline-item-content {
        padding-bottom: 1rem;
        padding-left: 0.5rem;
    }
</style>
@endsection