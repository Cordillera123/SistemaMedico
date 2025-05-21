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
        <!-- Información del Paciente -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Información del Paciente</h5>
                <div>
                    @if($esDoctorPrincipal)
                        <span class="badge bg-primary">Doctor Principal</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl mx-auto mb-3">
                        <div class="avatar-initial rounded-circle bg-primary">
                            {{ substr($paciente->user->nombre, 0, 1) }}{{ substr($paciente->user->apellido, 0, 1) }}
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $paciente->user->nombre }} {{ $paciente->user->apellido }}</h5>
                    <p class="text-muted">
                        {{ $paciente->genero }} · {{ $paciente->edad }} años
                    </p>
                </div>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Fecha de Nacimiento</span>
                        <span class="fw-medium">{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Correo Electrónico</span>
                        <span class="fw-medium">{{ $paciente->user->email }}</span>
                    </li>
                    @if($paciente->user->telefono)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Teléfono</span>
                            <span class="fw-medium">{{ $paciente->user->telefono }}</span>
                        </li>
                    @endif
                    @if($paciente->user->direccion)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Dirección</span>
                            <span class="fw-medium">{{ $paciente->user->direccion }}</span>
                        </li>
                    @endif
                    @if($paciente->tipo_sangre)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Tipo de Sangre</span>
                            <span class="fw-medium">{{ $paciente->tipo_sangre }}</span>
                        </li>
                    @endif
                </ul>
                
                <div class="mt-3">
                    <a href="{{ route('doctor.pacientes.edit', $paciente->id) }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-edit me-1"></i> Editar Información Médica
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Información Médica -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información Médica</h5>
            </div>
            <div class="card-body">
                @if($paciente->alergias)
                    <div class="mb-3">
                        <h6>Alergias</h6>
                        <p>{{ $paciente->alergias }}</p>
                    </div>
                @endif
                
                @if($paciente->antecedentes_medicos)
                    <div class="mb-3">
                        <h6>Antecedentes Médicos</h6>
                        <p>{{ $paciente->antecedentes_medicos }}</p>
                    </div>
                @endif
                
                @if(!$paciente->alergias && !$paciente->antecedentes_medicos)
                    <p class="text-muted">No hay información médica registrada.</p>
                @endif
            </div>
        </div>
        
        <!-- Otros Doctores del Paciente -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Otros Médicos Asignados</h5>
            </div>
            <div class="card-body">
                @if(count($otrosDoctores) > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($otrosDoctores as $otroDoctor)
                            <li class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <div class="avatar-initial rounded-circle bg-primary">
                                            {{ substr($otroDoctor->user->nombre, 0, 1) }}{{ substr($otroDoctor->user->apellido, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Dr. {{ $otroDoctor->user->nombre }} {{ $otroDoctor->user->apellido }}</h6>
                                        <small class="text-muted">{{ $otroDoctor->especialidad }}</small>
                                    </div>
                                    @if($paciente->esDoctorPrincipal($otroDoctor))
                                        <span class="badge bg-primary ms-auto">Principal</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Este paciente no tiene otros médicos asignados.</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <!-- Acciones Rápidas -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('doctor.resultados.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fas fa-file-medical fa-2x mb-2"></i>
                            <span>Subir Resultado Médico</span>
                        </a>
                    </div>
                    
                    <div class="col-md-4">
                        <a href="{{ route('doctor.pacientes.edit', $paciente->id) }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="fas fa-edit fa-2x mb-2"></i>
                            <span>Editar Información</span>
                        </a>
                    </div>
                    
                    @if(!$esDoctorPrincipal)
                        <div class="col-md-4">
                            <form action="{{ route('doctor.pacientes.set-principal', $paciente->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-user-md fa-2x mb-2"></i>
                                    <span>Establecer como Doctor Principal</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Resultados Médicos -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Resultados Médicos</h5>
                <a href="{{ route('doctor.resultados.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Nuevo Resultado
                </a>
            </div>
            <div class="card-body p-0">
                @if(count($resultados) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
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
                                                <span class="badge bg-secondary">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('doctor.resultados.show', $resultado->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('doctor.resultados.edit', $resultado->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('resultados.descargar', $resultado->id) }}" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-3">
                        {{ $resultados->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <img src="{{ asset('img/empty-results.svg') }}" alt="No hay resultados" class="img-fluid mb-3" style="max-height: 150px;">
                        <h5>No hay resultados médicos</h5>
                        <p class="text-muted">Aún no ha registrado ningún resultado médico para este paciente.</p>
                        <a href="{{ route('doctor.resultados.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Subir Nuevo Resultado
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
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #e5e7eb;
        color: #fff;
        font-weight: 600;
        overflow: hidden;
    }
    
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
    }
    
    .avatar-xl {
        width: 80px;
        height: 80px;
        font-size: 1.75rem;
    }
    
    .avatar-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-primary {
        background-color: #2563eb !important;
    }
</style>
@endsection