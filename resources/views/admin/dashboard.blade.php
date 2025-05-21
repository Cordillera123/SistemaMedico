@extends('layouts.dashboard')

@section('title', 'Dashboard Administrativo')

@section('page-title', 'Dashboard Administrativo')

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
    <!-- Total Doctores -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">TOTAL DOCTORES</h6>
                        <h2 class="stat-card-value">{{ $totalDoctores }}</h2>
                    </div>
                </div>
                <a href="{{ route('admin.doctores.index') }}" class="btn btn-sm btn-outline-primary w-100">
                    Ver doctores <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Total Pacientes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">TOTAL PACIENTES</h6>
                        <h2 class="stat-card-value">{{ $totalPacientes }}</h2>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Total Resultados -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-info">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">RESULTADOS MÉDICOS</h6>
                        <h2 class="stat-card-value">{{ $totalResultados }}</h2>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Actividad Reciente -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-warning">
            <div class="card-body">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h6 class="stat-card-title">REGISTROS DEL SISTEMA</h6>
                        <h2 class="stat-card-value">{{ count($ultimosLogs) }}</h2>
                    </div>
                </div>
                <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-outline-warning w-100">
                    Ver logs <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Últimos Doctores -->
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Doctores Recientes</h5>
                <a href="{{ route('admin.doctores.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Agregar Doctor
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Especialidad</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosDoctores as $doctor)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm">
                                                    <div class="avatar-initial rounded-circle bg-primary">
                                                        {{ substr($doctor->user->nombre, 0, 1) }}{{ substr($doctor->user->apellido, 0, 1) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ms-2">
                                                <span class="fw-semibold">{{ $doctor->user->nombre }} {{ $doctor->user->apellido }}</span>
                                                <br>
                                                <small class="text-muted">{{ $doctor->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $doctor->especialidad }}</td>
                                    <td>{{ $doctor->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.doctores.show', $doctor->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.doctores.edit', $doctor->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay doctores registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(count($ultimosDoctores) > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.doctores.index') }}" class="btn btn-link">Ver todos los doctores</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Actividad Reciente -->
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Actividad Reciente</h5>
                <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-list me-1"></i> Ver Todos
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($ultimosLogs as $log)
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm">
                                        @if(str_contains($log->accion, 'Login'))
                                            <div class="avatar-initial rounded-circle bg-success">
                                                <i class="fas fa-sign-in-alt"></i>
                                            </div>
                                        @elseif(str_contains($log->accion, 'Creación'))
                                            <div class="avatar-initial rounded-circle bg-primary">
                                                <i class="fas fa-plus"></i>
                                            </div>
                                        @elseif(str_contains($log->accion, 'Actualización'))
                                            <div class="avatar-initial rounded-circle bg-info">
                                                <i class="fas fa-edit"></i>
                                            </div>
                                        @elseif(str_contains($log->accion, 'Eliminación'))
                                            <div class="avatar-initial rounded-circle bg-danger">
                                                <i class="fas fa-trash"></i>
                                            </div>
                                        @else
                                            <div class="avatar-initial rounded-circle bg-secondary">
                                                <i class="fas fa-history"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ $log->accion }}</h6>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        @if($log->user)
                                            <span class="fw-semibold">{{ $log->user->nombre }} {{ $log->user->apellido }}</span> -
                                        @endif
                                        @if($log->tabla_afectada)
                                            <span class="badge bg-light text-dark">{{ $log->tabla_afectada }}</span>
                                        @endif
                                        @if($log->detalles)
                                            {{ Str::limit($log->detalles, 80) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-4">
                            <p class="mb-0 text-muted">No hay registros de actividad</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection