@extends('layouts.dashboard')

@section('title', 'Dashboard Paciente')

@section('page-title', 'Mi Dashboard')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<!-- Bienvenida y Resumen -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1">Bienvenido(a), {{ Auth::user()->nombre }}!</h4>
                <p class="text-muted mb-0">Aquí encontrarás toda la información relacionada con tu historial médico y resultados.</p>
            </div>
            <div class="text-center text-sm-end mt-3 mt-sm-0">
                <div class="badge bg-primary p-2 fs-6 mb-2 d-block">
                    <i class="fas fa-clipboard-check me-1"></i> {{ $totalResultados }} Resultados Médicos
                </div>
                @if($resultadosNoVistos > 0)
                    <a href="{{ route('paciente.resultados.index', ['nuevos' => 1]) }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-bell me-1"></i> {{ $resultadosNoVistos }} resultado(s) nuevo(s)
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas y Acciones Rápidas -->
<div class="row">
    <!-- Resultados Médicos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">MIS RESULTADOS</h6>
                        <h2 class="stat-card-value">{{ $totalResultados }}</h2>
                    </div>
                </div>
                <a href="{{ route('paciente.resultados.index') }}" class="btn btn-sm btn-outline-primary w-100">
                    Ver mis resultados <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Resultados No Vistos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-danger">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">RESULTADOS NUEVOS</h6>
                        <h2 class="stat-card-value">{{ $resultadosNoVistos }}</h2>
                    </div>
                </div>
                <a href="{{ route('paciente.resultados.index', ['nuevos' => 1]) }}" class="btn btn-sm btn-outline-danger w-100">
                    Ver resultados nuevos <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Mis Médicos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">MIS MÉDICOS</h6>
                        <h2 class="stat-card-value">{{ $totalDoctores }}</h2>
                    </div>
                </div>
                @if($totalDoctores > 1)
                    <a href="{{ route('paciente.mi-medico') }}" class="btn btn-sm btn-outline-success w-100">
                        Ver todos mis médicos <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                @elseif($doctorPrincipal)
                    <a href="{{ route('paciente.mi-medico') }}" class="btn btn-sm btn-outline-success w-100">
                        Ver mi médico <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                @else
                    <div class="btn btn-sm btn-outline-secondary w-100 disabled">
                        Sin médicos asignados
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Médico Principal -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-info">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">MÉDICO PRINCIPAL</h6>
                        @if($doctorPrincipal)
                            <h2 class="stat-card-value" style="font-size: 1.2rem;">
                                Dr(a). {{ substr($doctorPrincipal->user->apellido, 0, 10) }}
                            </h2>
                        @else
                            <h2 class="stat-card-value" style="font-size: 1rem;">No asignado</h2>
                        @endif
                    </div>
                </div>
                @if($doctorPrincipal)
                    <a href="{{ route('paciente.mi-medico') }}" class="btn btn-sm btn-outline-info w-100">
                        Ver información <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                @else
                    <div class="btn btn-sm btn-outline-secondary w-100 disabled">
                        Sin médico principal
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Últimos Resultados -->
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Resultados Recientes</h5>
                <a href="{{ route('paciente.resultados.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list me-1"></i> Ver Todos
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($resultadosRecientes as $resultado)
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm">
                                        <div class="avatar-initial rounded-circle {{ $resultado->visto_por_paciente ? 'bg-success' : 'bg-danger' }}">
                                            <i class="fas fa-{{ $resultado->visto_por_paciente ? 'check-circle' : 'exclamation-circle' }}"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ $resultado->titulo }}</h6>
                                        <small class="text-muted">{{ $resultado->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        <span class="badge bg-light text-dark">{{ $resultado->tipoResultado->nombre }}</span>
                                        <span class="text-muted">• Dr(a). {{ $resultado->doctor->user->apellido }}</span>
                                        @if(!$resultado->visto_por_paciente)
                                            <span class="badge bg-danger ms-1">Nuevo</span>
                                        @endif
                                    </p>
                                    <div class="mt-2">
                                        <a href="{{ route('paciente.resultados.show', $resultado->id) }}" class="btn btn-sm btn-outline-info me-1">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                        <a href="{{ route('paciente.resultados.descargar', $resultado->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> Descargar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-4">
                            <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted">No tienes resultados médicos</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Notificaciones y Médico -->
    <div class="col-xl-4 col-lg-5">
        <!-- Notificaciones -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Notificaciones</h5>
                <a href="{{ route('paciente.notificaciones') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-bell me-1"></i> Ver Todas
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($notificaciones as $notificacion)
                        <div class="list-group-item">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="notification-icon">
                                        @if($notificacion->tipo == 'resultado_nuevo')
                                            <i class="fas fa-file-medical text-primary"></i>
                                        @else
                                            <i class="fas fa-bell text-secondary"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $notificacion->titulo }}</h6>
                                    <p class="text-muted mb-1 small">{{ $notificacion->mensaje }}</p>
                                    <small class="text-muted">{{ $notificacion->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-4">
                            <p class="mb-0 text-muted">No tienes notificaciones</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Información de Médicos -->
        @if($totalDoctores > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        @if($doctorPrincipal)
                            Mi Médico Principal
                        @else
                            Mis Médicos
                        @endif
                    </h5>
                    @if($totalDoctores > 1)
                        <span class="badge bg-secondary">{{ $totalDoctores }} médicos</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($doctorPrincipal)
                        <!-- Mostrar médico principal -->
                        <div class="text-center mb-3">
                            <div class="avatar avatar-lg mx-auto mb-3">
                                <div class="avatar-initial rounded-circle bg-primary">
                                    {{ substr($doctorPrincipal->user->nombre, 0, 1) }}{{ substr($doctorPrincipal->user->apellido, 0, 1) }}
                                </div>
                            </div>
                            <h5 class="mb-1">
                                Dr(a). {{ $doctorPrincipal->user->nombre }} {{ $doctorPrincipal->user->apellido }}
                                <span class="badge bg-success ms-1">Principal</span>
                            </h5>
                            <p class="text-muted mb-0">{{ $doctorPrincipal->especialidad }}</p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            @if($doctorPrincipal->horario_consulta)
                                <div class="mb-2">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <span>{{ $doctorPrincipal->horario_consulta }}</span>
                                </div>
                            @endif
                            @if($doctorPrincipal->user->telefono)
                                <div class="mb-2">
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    <span>{{ $doctorPrincipal->user->telefono }}</span>
                                </div>
                            @endif
                            <div>
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <span>{{ $doctorPrincipal->user->email }}</span>
                            </div>
                        </div>
                    @else
                        <!-- Mostrar todos los médicos si no hay principal -->
                        @foreach($doctores->take(2) as $doctor)
                            <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md">
                                        <div class="avatar-initial rounded-circle bg-primary">
                                            {{ substr($doctor->user->nombre, 0, 1) }}{{ substr($doctor->user->apellido, 0, 1) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Dr(a). {{ $doctor->user->nombre }} {{ $doctor->user->apellido }}</h6>
                                    <p class="text-muted mb-0 small">{{ $doctor->especialidad }}</p>
                                    @if($doctor->user->telefono)
                                        <p class="text-muted mb-0 small">
                                            <i class="fas fa-phone me-1"></i>{{ $doctor->user->telefono }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        @if($totalDoctores > 2)
                            <div class="text-center text-muted small">
                                ... y {{ $totalDoctores - 2 }} médico(s) más
                            </div>
                        @endif
                    @endif
                    
                    <div class="text-center mt-3">
                        @if($totalDoctores == 1)
                            <a href="{{ route('paciente.mi-medico') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-info-circle me-1"></i> Ver información completa
                            </a>
                        @else
                            <a href="{{ route('paciente.mi-medico') }}" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-info-circle me-1"></i> Ver información
                            </a>
                            
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información Médica</h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-user-md fa-4x text-muted mb-3"></i>
                    <h5>Sin médicos asignados</h5>
                    <p class="text-muted">Actualmente no tienes médicos asignados. Contacta al administrador para que te asignen un médico.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    .stat-card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.15s ease-in-out;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    .stat-card-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .stat-card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
    }
    
    .stat-card-primary .stat-card-icon {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }
    
    .stat-card-danger .stat-card-icon {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    .stat-card-success .stat-card-icon {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }
    
    .stat-card-info .stat-card-icon {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }
    
    .stat-card-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .stat-card-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    
    .notification-icon {
        width: 24px;
        text-align: center;
    }
</style>
@endsection