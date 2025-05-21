@extends('layouts.dashboard')

@section('title', 'Mis Resultados Médicos')

@section('page-title', 'Mis Resultados Médicos')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('paciente.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Resultados Médicos</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Lista de Resultados</h5>
        <div>
            @if(request('nuevos'))
                <a href="{{ route('paciente.resultados.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-list me-1"></i> Mostrar todos
                </a>
            @else
                <a href="{{ route('paciente.resultados.index', ['nuevos' => 1]) }}" class="btn btn-outline-danger btn-sm me-2">
                    <i class="fas fa-bell me-1"></i> Solo nuevos
                </a>
            @endif
            <a href="{{ route('paciente.dashboard') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control table-search" placeholder="Buscar resultados..." data-table="results-table" id="searchInput">
                </div>
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
                <select class="form-select" id="yearFilter">
                    <option value="">Todos los años</option>
                    @php
                        $years = $resultados->pluck('fecha_resultado')->map(function($date) {
                            return $date->format('Y');
                        })->unique()->sort()->reverse();
                    @endphp
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">Todos</option>
                    <option value="new">No vistos</option>
                    <option value="seen">Vistos</option>
                </select>
            </div>
        </div>

        <!-- Tabla de resultados -->
        @if($resultados->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="results-table">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resultados as $resultado)
                            <tr data-tipo="{{ $resultado->tipoResultado->nombre }}" 
                                data-year="{{ $resultado->fecha_resultado->format('Y') }}"
                                data-status="{{ $resultado->visto_por_paciente ? 'seen' : 'new' }}">
                                <td>
                                    @if($resultado->visto_por_paciente)
                                        <span class="badge status-badge status-active">
                                            <i class="fas fa-check-circle me-1"></i> Visto
                                        </span>
                                    @else
                                        <span class="badge status-badge status-pending">
                                            <i class="fas fa-exclamation-circle me-1"></i> Nuevo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar avatar-sm">
                                                <div class="avatar-initial rounded-circle bg-primary">
                                                    <i class="fas fa-file-medical"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <h6 class="mb-0">{{ $resultado->titulo }}</h6>
                                            <small class="text-muted">
                                                @if($resultado->descripcion)
                                                    {{ Str::limit($resultado->descripcion, 50) }}
                                                @else
                                                    <em>Sin descripción</em>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $resultado->tipoResultado->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-medium">{{ $resultado->fecha_resultado->format('d/m/Y') }}</span>
                                    </div>
                                    <small class="text-muted">
                                        Subido {{ $resultado->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('paciente.resultados.show', $resultado->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('paciente.resultados.descargar', $resultado->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Descargar PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
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
                <img src="{{ asset('images/empty-results.svg') }}" alt="No hay resultados" class="mb-3" style="max-width: 200px; opacity: 0.5;">
                <h4>No hay resultados médicos disponibles</h4>
                <p class="text-muted">Actualmente no tienes ningún resultado médico para visualizar.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtro por tipo
        const tipoFilter = document.getElementById('tipoFilter');
        tipoFilter.addEventListener('change', filterResults);
        
        // Filtro por año
        const yearFilter = document.getElementById('yearFilter');
        yearFilter.addEventListener('change', filterResults);
        
        // Filtro por estado
        const statusFilter = document.getElementById('statusFilter');
        statusFilter.addEventListener('change', filterResults);
        
        function filterResults() {
            const tipo = tipoFilter.value;
            const year = yearFilter.value;
            const status = statusFilter.value;
            const rows = document.querySelectorAll('#results-table tbody tr');
            
            rows.forEach(row => {
                const rowTipo = row.getAttribute('data-tipo');
                const rowYear = row.getAttribute('data-year');
                const rowStatus = row.getAttribute('data-status');
                
                let showRow = true;
                
                if (tipo && rowTipo !== tipo) {
                    showRow = false;
                }
                
                if (year && rowYear !== year) {
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