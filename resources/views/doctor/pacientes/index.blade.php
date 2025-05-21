@extends('layouts.dashboard')

@section('title', 'Mis Pacientes')

@section('page-title', 'Mis Pacientes')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pacientes</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Lista de Pacientes</h5>
        <a href="{{ route('doctor.pacientes.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> Agregar Paciente
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchPatient" placeholder="Buscar paciente...">
                </div>
            </div>
        </div>
        
        @if(count($pacientes) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="patientsTable">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Información de Contacto</th>
                            <th>Edad / Género</th>
                            <th>Resultados</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pacientes as $paciente)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="avatar-initial rounded-circle bg-primary">
                                                {{ substr($paciente->user->nombre, 0, 1) }}{{ substr($paciente->user->apellido, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $paciente->user->nombre }} {{ $paciente->user->apellido }}</h6>
                                            @if($paciente->pivot->doctor_principal)
                                                <span class="badge bg-primary">Doctor Principal</span>
                                            @endif
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
                                <td>
                                    <div>{{ $paciente->edad }} años</div>
                                    <small class="text-muted">{{ $paciente->genero }}</small>
                                </td>
                                <td>
                                    @php
                                        $totalResultados = \App\Models\ResultadoMedico::where('paciente_id', $paciente->id)
                                            ->where('doctor_id', Auth::user()->doctor->id)
                                            ->count();
                                        
                                        $resultadosNoVistos = \App\Models\ResultadoMedico::where('paciente_id', $paciente->id)
                                            ->where('doctor_id', Auth::user()->doctor->id)
                                            ->where('visto_por_paciente', false)
                                            ->count();
                                    @endphp
                                    
                                    <div>
                                        <span class="badge bg-info">{{ $totalResultados }} resultados</span>
                                    </div>
                                    @if($resultadosNoVistos > 0)
                                        <div>
                                            <span class="badge bg-warning text-dark">{{ $resultadosNoVistos }} pendientes</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $paciente->user->activo ? 'bg-success' : 'bg-danger' }}">
                                        {{ $paciente->user->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('doctor.pacientes.show', $paciente->id) }}">
                                                    <i class="fas fa-eye me-2 text-info"></i> Ver Detalles
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('doctor.pacientes.edit', $paciente->id) }}">
                                                    <i class="fas fa-edit me-2 text-primary"></i> Editar Información
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('doctor.resultados.create', ['paciente_id' => $paciente->id]) }}">
                                                    <i class="fas fa-file-medical me-2 text-success"></i> Subir Resultado
                                                </a>
                                            </li>
                                            @if(!$paciente->pivot->doctor_principal)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('doctor.pacientes.set-principal', $paciente->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-user-md me-2 text-warning"></i> Establecer como Doctor Principal
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $pacientes->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <img src="{{ asset('img/empty-patients.svg') }}" alt="No hay pacientes" class="img-fluid mb-3" style="max-height: 150px;">
                <h5>No tiene pacientes asignados</h5>
                <p class="text-muted">Agregue pacientes a su lista para comenzar a gestionarlos.</p>
                <a href="{{ route('doctor.pacientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Agregar Paciente
                </a>
            </div>
        @endif
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
    
    .btn-icon {
        padding: 0.25rem 0.5rem;
        line-height: 1;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Búsqueda de pacientes
        const searchInput = document.getElementById('searchPatient');
        const tableRows = document.querySelectorAll('#patientsTable tbody tr');
        
        if (searchInput && tableRows.length > 0) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                
                tableRows.forEach(row => {
                    const patientText = row.textContent.toLowerCase();
                    row.style.display = patientText.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    });
</script>
@endsection