@extends('layouts.dashboard')

@section('title', 'Dashboard Doctor')

@section('page-title', 'Dashboard Doctor')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<!-- Estadísticas Generales -->
<div class="row">
    <!-- Total Pacientes -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">MIS PACIENTES</h6>
                        <h2 class="stat-card-value">{{ $totalPacientes }}</h2>
                    </div>
                </div>
                <a href="{{ route('doctor.pacientes.index') }}" class="btn btn-sm btn-outline-primary w-100">
                    Ver pacientes <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Total Resultados -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">RESULTADOS SUBIDOS</h6>
                        <h2 class="stat-card-value">{{ $totalResultados }}</h2>
                    </div>
                </div>
                <a href="{{ route('doctor.resultados.index') }}" class="btn btn-sm btn-outline-success w-100">
                    Ver resultados <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Información del Doctor -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card stat-card-info">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">MI ESPECIALIDAD</h6>
                        <h2 class="stat-card-value">{{ Str::limit($doctor->especialidad, 15) }}</h2>
                    </div>
                </div>
                <a href="{{ route('doctor.perfil') }}" class="btn btn-sm btn-outline-info w-100">
                    Ver mi perfil <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Últimos Pacientes -->
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Pacientes Recientes</h5>
                <a href="{{ route('doctor.pacientes.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Agregar Paciente
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Contacto</th>
                                <th>Resultados</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosPacientes as $paciente)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm">
                                                    <div class="avatar-initial rounded-circle bg-primary">
                                                        {{ substr($paciente->user->nombre, 0, 1) }}{{ substr($paciente->user->apellido, 0, 1) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ms-2">
                                                <span class="fw-semibold">{{ $paciente->user->nombre }} {{ $paciente->user->apellido }}</span>
                                                <br>
                                                <small class="text-muted">{{ $paciente->genero }} - {{ $paciente->edad }} años</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="fas fa-envelope text-muted me-1"></i> {{ $paciente->user->email }}
                                        </div>
                                        @if($paciente->user->telefono)
                                            <div>
                                                <i class="fas fa-phone text-muted me-1"></i> {{ $paciente->user->telefono }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill">
                                            {{ $paciente->resultadosMedicos()->count() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('doctor.pacientes.show', $paciente->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('doctor.resultados.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Subir resultado">
                                                <i class="fas fa-file-upload"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay pacientes registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(count($ultimosPacientes) > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('doctor.pacientes.index') }}" class="btn btn-link">Ver todos los pacientes</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Últimos Resultados -->
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Resultados Recientes</h5>
                <a href="{{ route('doctor.resultados.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-upload me-1"></i> Subir Resultado
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($resultadosRecientes as $resultado)
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm">
                                        <div class="avatar-initial rounded-circle bg-success">
                                            <i class="fas fa-file-medical"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ $resultado->titulo }}</h6>
                                        <small class="text-muted">{{ $resultado->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        <span class="fw-semibold">Paciente:</span> {{ $resultado->paciente->user->nombre }} {{ $resultado->paciente->user->apellido }}
                                        <span class="badge bg-light text-dark">{{ $resultado->tipoResultado->nombre }}</span>
                                    </p>
                                    <div class="mt-2">
                                        <a href="{{ route('doctor.resultados.show', $resultado->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                        <a href="{{ route('resultados.descargar', $resultado->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> Descargar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-4">
                            <p class="mb-0 text-muted">No hay resultados subidos</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @if(count($resultadosRecientes) > 0)
                <div class="card-footer text-center">
                    <a href="{{ route('doctor.resultados.index') }}" class="btn btn-link">Ver todos los resultados</a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection