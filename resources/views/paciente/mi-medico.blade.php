@extends('layouts.dashboard')

@section('title', 'Mi Médico')

@section('page-title', 'Mi Médico')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('paciente.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Mi Médico</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="row">
    <div class="col-xl-4 col-lg-5">
        <!-- Tarjeta del perfil del médico -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <div class="avatar-initial rounded-circle bg-primary">
                        {{ substr($doctor->user->nombre, 0, 1) }}{{ substr($doctor->user->apellido, 0, 1) }}
                    </div>
                </div>
                <h4 class="mb-1">Dr(a). {{ $doctor->user->nombre }} {{ $doctor->user->apellido }}</h4>
                <p class="text-muted mb-3">{{ $doctor->especialidad }}</p>
                
                <div class="d-flex justify-content-center mb-3">
                    @if($doctor->user->telefono)
                        <a href="tel:{{ $doctor->user->telefono }}" class="btn btn-outline-primary btn-icon me-2" data-bs-toggle="tooltip" title="Llamar">
                            <i class="fas fa-phone"></i>
                        </a>
                    @endif
                    <a href="mailto:{{ $doctor->user->email }}" class="btn btn-outline-primary btn-icon" data-bs-toggle="tooltip" title="Enviar correo">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
                
                <div class="divider">
                    <div class="divider-text">Información de Contacto</div>
                </div>
                
                <div class="text-start">
                    <div class="mb-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-envelope text-primary me-2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-muted">Correo Electrónico</div>
                                <div>{{ $doctor->user->email }}</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($doctor->user->telefono)
                        <div class="mb-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-muted">Teléfono</div>
                                    <div>{{ $doctor->user->telefono }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($doctor->user->direccion)
                        <div class="mb-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-muted">Dirección</div>
                                    <div>{{ $doctor->user->direccion }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Horarios de consulta -->
        @if($doctor->horario_consulta)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Horarios de Consulta</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <div class="avatar-initial rounded-circle bg-light">
                                    <i class="fas fa-clock text-primary"></i>
                                </div>
                            </div>
                        </div>
                        <div class="ms-2">
                            <h6 class="mb-0">Horario</h6>
                            <p class="text-muted mb-0">{{ $doctor->horario_consulta }}</p>
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="ms-2">
                                <p class="mb-0">Para agendar una cita, por favor contacte directamente con el médico a través de los datos de contacto proporcionados.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-xl-8 col-lg-7">
        <!-- Detalles del médico -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Perfil Profesional</h5>
            </div>
            <div class="card-body">
                @if($doctor->biografia)
                    <div class="mb-4">
                        <h6 class="mb-3">Biografía</h6>
                        <p>{{ $doctor->biografia }}</p>
                    </div>
                @endif
                
                <div class="mb-4">
                    <h6 class="mb-3">Especialidad</h6>
                    <p>{{ $doctor->especialidad }}</p>
                </div>
                
                <div class="mb-4">
                    <h6 class="mb-3">Licencia Médica</h6>
                    <div class="badge bg-light text-dark">{{ $doctor->licencia_medica }}</div>
                </div>
            </div>
        </div>
        
        <!-- Mis resultados con este médico -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Resultados Médicos</h5>
                <a href="{{ route('paciente.resultados.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list me-1"></i> Ver Todos
                </a>
            </div>
            <div class="card-body">
                @php
                    $resultados = Auth::user()->paciente->resultadosMedicos()
                        ->where('doctor_id', $doctor->id)
                        ->with('tipoResultado')
                        ->latest()
                        ->take(5)
                        ->get();
                    
                    $totalResultados = Auth::user()->paciente->resultadosMedicos()
                        ->where('doctor_id', $doctor->id)
                        ->count();
                @endphp
                
                @if($resultados->count() > 0)
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <h4 class="mb-0">{{ $totalResultados }}</h4>
                            <p class="text-muted">Resultados totales</p>
                        </div>
                        <div class="progress mb-4" style="height: 8px;">
                            @php
                                $vistos = $resultados->where('visto_por_paciente', true)->count();
                                $porcentajeVistos = $resultados->count() > 0 ? ($vistos / $resultados->count()) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentajeVistos }}%" aria-valuenow="{{ $porcentajeVistos }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Resultado</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resultados as $resultado)
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
                                            <span class="badge bg-light text-dark">
                                                {{ $resultado->tipoResultado->nombre }}
                                            </span>
                                        </td>
                                        <td>{{ $resultado->fecha_resultado->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('paciente.resultados.show', $resultado->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
                        <p class="text-muted">Actualmente no tienes resultados médicos con este doctor.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .divider {
        display: flex;
        align-items: center;
        margin: 1rem 0;
    }
    
    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .divider-text {
        padding: 0 1rem;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .btn-icon {
        width: 40px;
        height: 40px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
</style>
@endsection