{{-- resources/views/paciente/sin-medico.blade.php --}}
@extends('layouts.app')

@section('title', 'Mi Información Médica')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Header -->
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-user-md"></i> Mi Información Médica
                </h1>
            </div>
        </div>
    </div>

    <!-- Mensaje informativo -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Información:</strong> Actualmente no tienes ningún médico asignado, pero puedes ver tus resultados médicos históricos.
            </div>
        </div>
    </div>

    <!-- Estadísticas de resultados históricos -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Resultados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalResultados }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-medical fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resultados Revisados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $resultadosVistos }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Resultados Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $resultadosNoVistos }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Médicos Históricos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $resultadosRecientes->pluck('doctor.user.nombre_completo')->unique()->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-md fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados médicos recientes -->
    @if($resultadosRecientes->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-medical"></i> Resultados Médicos Recientes
                    </h6>
                    <a href="{{ route('paciente.resultados.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Médico</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resultadosRecientes as $resultado)
                                <tr>
                                    <td>{{ $resultado->fecha_resultado->format('d/m/Y') }}</td>
                                    <td>{{ $resultado->titulo }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $resultado->tipoResultado->nombre ?? 'Sin tipo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <i class="fas fa-user-md"></i>
                                            {{ $resultado->doctor->user->nombre_completo ?? 'Médico no disponible' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($resultado->visto_por_paciente)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Revisado
                                            </span>
                                        @else
                                            <span class="badge badge-warning">
                                                <i class="fas fa-exclamation"></i> Nuevo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('paciente.resultados.show', $resultado->id) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Ver resultado">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('paciente.resultados.descargar', $resultado->id) }}" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Descargar PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Mensaje cuando no hay resultados -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No tienes resultados médicos</h5>
                    <p class="text-muted">
                        Cuando un médico te asigne resultados médicos, aparecerán aquí.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Información adicional -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Información Importante
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-light">
                        <ul class="mb-0">
                            <li>Aunque no tengas médicos asignados actualmente, puedes acceder a todos tus resultados médicos históricos.</li>
                            <li>Los resultados de médicos que ya no están asignados a ti permanecen disponibles en tu historial.</li>
                            <li>Si necesitas que un médico específico tenga acceso a tu historial, contacta con el administrador del sistema.</li>
                            <li>Puedes actualizar tu perfil personal en cualquier momento desde la sección "Mi Perfil".</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection