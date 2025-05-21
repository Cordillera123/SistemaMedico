@extends('layouts.dashboard')

@section('title', 'Resultados Médicos')

@section('page-title', 'Resultados Médicos')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Resultados Médicos</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Lista de Resultados</h5>
        <a href="{{ route('doctor.resultados.create') }}" class="btn btn-primary">
            <i class="fas fa-file-upload me-1"></i> Subir Nuevo Resultado
        </a>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control table-search" placeholder="Buscar resultados..." data-table="results-table" id="searchInput">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="pacienteFilter">
                    <option value="">Todos los pacientes</option>
                    @php
                        $doctor = Auth::user()->doctor;
                        $pacientesIds = $resultados->pluck('paciente_id')->unique();
                        $pacientes = App\Models\Paciente::whereIn('id', $pacientesIds)
                            ->with('user')
                            ->get()
                            ->sortBy('user.nombre');
                    @endphp
                    @foreach($pacientes as $paciente)
                        <option value="{{ $paciente->id }}">{{ $paciente->user->nombre }} {{ $paciente->user->apellido }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="tipoFilter">
                    <option value="">Todos los tipos</option>
                    @php
                        $tipos = $resultados->pluck('tipoResultado.nombre')->unique()->sort();
                    @endphp
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo }}">{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">Todos</option>
                    <option value="visto">Vistos</option>
                    <option value="no-visto">No vistos</option>
                </select>
            </div>
        </div>

        <!-- Tabla de resultados -->
        @if($resultados->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="results-table">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resultados as $resultado)
                            <tr data-paciente="{{ $resultado->paciente_id }}" 
                                data-tipo="{{ $resultado->tipoResultado->nombre }}"
                                data-status="{{ $resultado->visto_por_paciente ? 'visto' : 'no-visto' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-initial rounded-circle {{ $resultado->paciente->genero == 'Masculino' ? 'bg-primary' : 'bg-info' }}">
                                                {{ substr($resultado->paciente->user->nombre, 0, 1) }}{{ substr($resultado->paciente->user->apellido, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            {{ $resultado->paciente->user->nombre }} {{ $resultado->paciente->user->apellido }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $resultado->titulo }}</div>
                                    @if($resultado->descripcion)
                                        <small class="text-muted">{{ Str::limit($resultado->descripcion, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $resultado->tipoResultado->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>{{ $resultado->fecha_resultado->format('d/m/Y') }}</span>
                                        <small class="text-muted">{{ $resultado->created_at->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($resultado->visto_por_paciente)
                                        <span class="badge status-badge status-active">
                                            <i class="fas fa-check-circle me-1"></i> Visto
                                            <small class="d-block">{{ $resultado->fecha_visualizacion ? $resultado->fecha_visualizacion->format('d/m/Y H:i') : '' }}</small>
                                        </span>
                                    @else
                                        <span class="badge status-badge status-pending">
                                            <i class="fas fa-exclamation-circle me-1"></i> No visto
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('doctor.resultados.show', $resultado->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('doctor.resultados.edit', $resultado->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('resultados.descargar', $resultado->id) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $resultado->id }}" data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de confirmación de eliminación -->
                                    <div class="modal fade" id="deleteModal{{ $resultado->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $resultado->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $resultado->id }}">Confirmar Eliminación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Está seguro que desea eliminar el resultado <strong>{{ $resultado->titulo }}</strong>?</p>
                                                    <p class="text-danger mb-0">Esta acción no se puede deshacer.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('doctor.resultados.destroy', $resultado->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $resultados->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-file-medical fa-4x text-muted"></i>
                </div>
                <h4>No hay resultados médicos</h4>
                <p class="text-muted">No has subido ningún resultado médico aún.</p>
                <a href="{{ route('doctor.resultados.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-file-upload me-1"></i> Subir Primer Resultado
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtro por paciente
        const pacienteFilter = document.getElementById('pacienteFilter');
        pacienteFilter.addEventListener('change', filterResults);
        
        // Filtro por tipo
        const tipoFilter = document.getElementById('tipoFilter');
        tipoFilter.addEventListener('change', filterResults);
        
        // Filtro por estado
        const statusFilter = document.getElementById('statusFilter');
        statusFilter.addEventListener('change', filterResults);
        
        function filterResults() {
            const paciente = pacienteFilter.value;
            const tipo = tipoFilter.value;
            const status = statusFilter.value;
            const rows = document.querySelectorAll('#results-table tbody tr');
            
            rows.forEach(row => {
                const rowPaciente = row.getAttribute('data-paciente');
                const rowTipo = row.getAttribute('data-tipo');
                const rowStatus = row.getAttribute('data-status');
                
                let showRow = true;
                
                if (paciente && rowPaciente !== paciente) {
                    showRow = false;
                }
                
                if (tipo && rowTipo !== tipo) {
                    showRow = false;
                }
                
                if (status && rowStatus !== status) {
                    showRow = false;
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        }
    });
</script>
@endsection