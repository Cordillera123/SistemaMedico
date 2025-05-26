@extends('layouts.dashboard')

@section('title', 'Mis Médicos')

@section('page-title', 'Mis Médicos')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('paciente.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Mis Médicos</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="row">
    <!-- Información de médicos -->
    <div class="col-xl-4 col-lg-5">
        @if($doctorPrincipal)
            <!-- Médico Principal -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star text-warning me-2"></i>Mi Médico Principal
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="avatar avatar-xl mx-auto mb-3">
                        <div class="avatar-initial rounded-circle bg-primary">
                            {{ substr($doctorPrincipal->user->nombre, 0, 1) }}{{ substr($doctorPrincipal->user->apellido, 0, 1) }}
                        </div>
                    </div>
                    <h4 class="mb-1">Dr(a). {{ $doctorPrincipal->user->nombre }} {{ $doctorPrincipal->user->apellido }}</h4>
                    <p class="text-muted mb-3">{{ $doctorPrincipal->especialidad }}</p>
                    
                    <div class="d-flex justify-content-center mb-3">
                        @if($doctorPrincipal->user->telefono)
                            <a href="tel:{{ $doctorPrincipal->user->telefono }}" class="btn btn-outline-primary btn-icon me-2" data-bs-toggle="tooltip" title="Llamar">
                                <i class="fas fa-phone"></i>
                            </a>
                        @endif
                        <a href="mailto:{{ $doctorPrincipal->user->email }}" class="btn btn-outline-primary btn-icon" data-bs-toggle="tooltip" title="Enviar correo">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                    
                    <!-- Estadísticas del médico principal -->
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">{{ $estadisticasPorDoctor[$doctorPrincipal->id]['total_resultados'] ?? 0 }}</h4>
                                <small class="text-muted">Resultados</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-danger mb-0">{{ $estadisticasPorDoctor[$doctorPrincipal->id]['resultados_nuevos'] ?? 0 }}</h4>
                            <small class="text-muted">Nuevos</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Otros médicos o todos si no hay principal -->
        @if($doctores->count() > 1 || !$doctorPrincipal)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        @if($doctorPrincipal)
                            Otros Médicos
                        @else
                            Mis Médicos
                        @endif
                    </h5>
                </div>
                <div class="card-body p-0">
                    @foreach($doctores as $doctor)
                        @if(!$doctorPrincipal || $doctor->id !== $doctorPrincipal->id)
                            <div class="d-flex align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md">
                                        <div class="avatar-initial rounded-circle bg-secondary">
                                            {{ substr($doctor->user->nombre, 0, 1) }}{{ substr($doctor->user->apellido, 0, 1) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Dr(a). {{ $doctor->user->nombre }} {{ $doctor->user->apellido }}</h6>
                                    <p class="text-muted mb-1 small">{{ $doctor->especialidad }}</p>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark me-2">
                                            {{ $estadisticasPorDoctor[$doctor->id]['total_resultados'] ?? 0 }} resultados
                                        </span>
                                        @if(($estadisticasPorDoctor[$doctor->id]['resultados_nuevos'] ?? 0) > 0)
                                            <span class="badge bg-danger">
                                                {{ $estadisticasPorDoctor[$doctor->id]['resultados_nuevos'] }} nuevos
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($doctor->user->telefono)
                                        <a href="tel:{{ $doctor->user->telefono }}" class="btn btn-sm btn-outline-primary btn-icon me-1" data-bs-toggle="tooltip" title="Llamar">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                    @endif
                                    <a href="mailto:{{ $doctor->user->email }}" class="btn btn-sm btn-outline-primary btn-icon" data-bs-toggle="tooltip" title="Enviar correo">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Información de contacto detallada -->
        @if($doctorReferencia)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información de Contacto</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <i class="fas fa-envelope text-primary me-2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-muted">Correo Electrónico</div>
                                <div>{{ $doctorReferencia->user->email }}</div>
                            </div>
                        </div>
                        
                        @if($doctorReferencia->user->telefono)
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-muted">Teléfono</div>
                                    <div>{{ $doctorReferencia->user->telefono }}</div>
                                </div>
                            </div>
                        @endif
                        
                        @if($doctorReferencia->user->direccion)
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-muted">Dirección</div>
                                    <div>{{ $doctorReferencia->user->direccion }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    @if($doctorReferencia->horario_consulta)
                        <div class="alert alert-info mb-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="ms-2">
                                    <strong>Horario de Consulta:</strong><br>
                                    {{ $doctorReferencia->horario_consulta }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="col-xl-8 col-lg-7">
        <!-- Estadísticas generales -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card stat-card-primary">
                    <div class="card-body text-center">
                        <h3 class="text-primary mb-1">{{ $totalResultados }}</h3>
                        <p class="text-muted mb-0">Total de Resultados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card stat-card-success">
                    <div class="card-body text-center">
                        <h3 class="text-success mb-1">{{ $resultadosVistos }}</h3>
                        <p class="text-muted mb-0">Resultados Vistos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card stat-card-danger">
                    <div class="card-body text-center">
                        <h3 class="text-danger mb-1">{{ $resultadosNoVistos }}</h3>
                        <p class="text-muted mb-0">Resultados Nuevos</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Resultados médicos recientes -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Resultados Médicos Recientes</h5>
                <a href="{{ route('paciente.resultados.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list me-1"></i> Ver Todos
                </a>
            </div>
            <div class="card-body">
                @if($resultadosRecientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Resultado</th>
                                    <th>Médico</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resultadosRecientes as $resultado)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar avatar-sm">
                                                        <div class="avatar-initial rounded-circle {{ $resultado->visto_por_paciente ? 'bg-success' : 'bg-danger' }}">
                                                            <i class="fas fa-{{ $resultado->visto_por_paciente ? 'check-circle' : 'exclamation-circle' }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ms-2">
                                                    <h6 class="mb-0">{{ $resultado->titulo }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <div class="avatar-initial rounded-circle bg-primary">
                                                        {{ substr($resultado->doctor->user->nombre, 0, 1) }}{{ substr($resultado->doctor->user->apellido, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="small">Dr(a). {{ $resultado->doctor->user->apellido }}</div>
                                                    @if($doctorPrincipal && $resultado->doctor->id === $doctorPrincipal->id)
                                                        <span class="badge bg-warning text-dark small">Principal</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $resultado->tipoResultado->nombre }}
                                            </span>
                                        </td>
                                        <td>{{ $resultado->fecha_resultado->format('d/m/Y') }}</td>
                                        <td>
                                            @if($resultado->visto_por_paciente)
                                                <span class="badge bg-success">Visto</span>
                                            @else
                                                <span class="badge bg-danger">Nuevo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('paciente.resultados.show', $resultado->id) }}" class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('paciente.resultados.descargar', $resultado->id) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-file-medical fa-4x text-muted"></i>
                        </div>
                        <h5>No hay resultados médicos</h5>
                        <p class="text-muted">Actualmente no tienes resultados médicos disponibles.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    .stat-card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endsection