@extends('layouts.dashboard')

@section('title', 'Detalles del Paciente')

@section('page-title', 'Detalles del Paciente')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('doctor.pacientes.index') }}">Pacientes</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $paciente->user->nombre }} {{ $paciente->user->apellido }}</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="row">
    <div class="col-lg-4">
        <!-- Tarjeta de información del paciente -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl mx-auto mb-3">
                        <div class="avatar-initial rounded-circle bg-primary">
                            {{ substr($paciente->user->nombre, 0, 1) }}{{ substr($paciente->user->apellido, 0, 1) }}
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $paciente->user->nombre }} {{ $paciente->user->apellido }}</h4>
                    
                    @if($esDoctorPrincipal)
                        <span class="badge bg-success">Eres su médico principal</span>
                    @else
                        <span class="badge bg-secondary">Médico secundario</span>
                    @endif
                </div>
                
                <div class="info-divider"></div>
                
                <div class="patient-info">
                    <h5 class="section-title">Información Personal</h5>
                    
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-id-card me-2"></i> Cédula:</span>
                        <span class="info-value">{{ $paciente->cedula ?? 'No registrada' }}</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-calendar me-2"></i> Fecha de Nacimiento:</span>
                        <span class="info-value">{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-venus-mars me-2"></i> Género:</span>
                        <span class="info-value">{{ $paciente->genero }}</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-tint me-2"></i> Tipo de Sangre:</span>
                        <span class="info-value">{{ $paciente->tipo_sangre ?? 'No especificado' }}</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-envelope me-2"></i> Email:</span>
                        <span class="info-value">{{ $paciente->user->email }}</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-phone me-2"></i> Teléfono:</span>
                        <span class="info-value">{{ $paciente->user->telefono ?? 'No registrado' }}</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-map-marker-alt me-2"></i> Dirección:</span>
                        <span class="info-value">{{ $paciente->user->direccion ?? 'No registrada' }}</span>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('doctor.pacientes.edit', $paciente->id) }}" class="btn btn-primary btn-block w-100">
                        <i class="fas fa-edit me-1"></i> Editar Información Médica
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Tarjeta de información médica -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información Médica</h5>
            </div>
            <div class="card-body">
                @if($paciente->alergias)
                    <div class="mb-3">
                        <h6><i class="fas fa-allergies me-2 text-warning"></i> Alergias</h6>
                        <p class="text-muted">{{ $paciente->alergias }}</p>
                    </div>
                @endif
                
                @if($paciente->antecedentes_medicos)
                    <div>
                        <h6><i class="fas fa-file-medical-alt me-2 text-info"></i> Antecedentes Médicos</h6>
                        <p class="text-muted">{{ $paciente->antecedentes_medicos }}</p>
                    </div>
                @endif
                
                @if(!$paciente->alergias && !$paciente->antecedentes_medicos)
                    <p class="text-center text-muted">No hay información médica registrada.</p>
                @endif
            </div>
        </div>
        
        <!-- Otros médicos del paciente -->
        @if(count($otrosDoctores) > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Otros Médicos</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($otrosDoctores as $otroDoctor)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-0">Dr. {{ $otroDoctor->user->nombre }} {{ $otroDoctor->user->apellido }}</h6>
                                    <small class="text-muted">{{ $otroDoctor->especialidad }}</small>
                                </div>
                                @if($otroDoctor->pivot->doctor_principal)
                                    <span class="badge bg-primary rounded-pill">Principal</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
    
    <div class="col-lg-8">
        <!-- Resultados médicos -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Resultados Médicos</h5>
                <a href="{{ route('doctor.resultados.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Subir Resultado
                </a>
            </div>
            <div class="card-body">
                @if(count($resultados) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resultados as $resultado)
                                    <tr>
                                        <td>{{ $resultado->titulo }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $resultado->tipoResultado->nombre }}</span>
                                        </td>
                                        <td>{{ $resultado->fecha_resultado->format('d/m/Y') }}</td>
                                        <td>
                                            @if($resultado->visto_por_paciente)
                                                <span class="badge bg-success">Visto</span>
                                            @else
                                                <span class="badge bg-warning">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('resultados.show', $resultado->id) }}" class="btn btn-sm btn-info" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('resultados.descargar', $resultado->id) }}" class="btn btn-sm btn-secondary" title="Descargar">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="{{ route('resultados.edit', $resultado->id) }}" class="btn btn-sm btn-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $resultados->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-file-medical text-muted fa-3x"></i>
                        </div>
                        <h5>No hay resultados médicos</h5>
                        <p class="text-muted">Aún no has subido resultados médicos para este paciente.</p>
                        <a href="{{ route('doctor.resultados.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Subir Resultado
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-xl {
        width: 96px;
        height: 96px;
        font-size: 2rem;
    }
    
    .avatar-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }
    
    .info-divider {
        height: 1px;
        background-color: #e5e7eb;
        margin: 1.5rem 0;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #4b5563;
    }
    
    .patient-info {
        margin-bottom: 1.5rem;
    }
    
    .info-item {
        margin-bottom: 0.75rem;
        display: flex;
        flex-wrap: wrap;
    }
    
    .info-label {
        width: 100%;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        color: #111827;
    }
    
    @media (min-width: 576px) {
        .info-label {
            width: 40%;
            margin-bottom: 0;
        }
        
        .info-value {
            width: 60%;
        }
    }
</style>
@endsection