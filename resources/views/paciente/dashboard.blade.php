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
                    <a href="{{ route('paciente.resultados.index') }}" class="btn btn-outline-danger btn-sm">
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
    <div class="col-xl-4 col-md-6 mb-4">
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
    <div class="col-xl-4 col-md-6 mb-4">
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
    
    <!-- Mi Médico -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">MI MÉDICO</h6>
                        <h2 class="stat-card-value">Dr(a). {{ substr($paciente->doctor->user->apellido, 0, 12) }}</h2>
                    </div>
                </div>
                <a href="{{ route('paciente.mi-medico') }}" class="btn btn-sm btn-outline-success w-100">
                    Ver información <i class="fas fa-arrow-right ms-1"></i>
                </a>
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
        
        <!-- Información del Médico -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Mi Médico</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <div class="avatar-initial rounded-circle bg-primary">
                            {{ substr($paciente->doctor->user->nombre, 0, 1) }}{{ substr($paciente->doctor->user->apellido, 0, 1) }}
                        </div>
                    </div>
                    <h5 class="mb-1">Dr(a). {{ $paciente->doctor->user->nombre }} {{ $paciente->doctor->user->apellido }}</h5>
                    <p class="text-muted mb-0">{{ $paciente->doctor->especialidad }}</p>
                </div>
                <hr>
                <div class="mb-3">
                    @if($paciente->doctor->horario_consulta)
                        <div class="mb-2">
                            <i class="fas fa-clock text-muted me-2"></i>
                            <span>{{ $paciente->doctor->horario_consulta }}</span>
                        </div>
                    @endif
                    @if($paciente->doctor->user->telefono)
                        <div class="mb-2">
                            <i class="fas fa-phone text-muted me-2"></i>
                            <span>{{ $paciente->doctor->user->telefono }}</span>
                        </div>
                    @endif
                    <div>
                        <i class="fas fa-envelope text-muted me-2"></i>
                        <span>{{ $paciente->doctor->user->email }}</span>
                    </div>
                </div>
                <div class="text-center">
                    <a href="{{ route('paciente.mi-medico') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-info-circle me-1"></i> Ver más información
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection