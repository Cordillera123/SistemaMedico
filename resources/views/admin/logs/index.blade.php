@extends('layouts.dashboard')

@section('title', 'Logs del Sistema')

@section('page-title', 'Logs del Sistema')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Logs del Sistema</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Registros de Actividad</h5>
        <div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filtersModal">
                <i class="fas fa-filter me-1"></i> Filtros Avanzados
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros rápidos -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar en logs...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="accionFilter">
                    <option value="">Todas las acciones</option>
                    @foreach($acciones as $accion)
                        <option value="{{ $accion }}" {{ request('accion') == $accion ? 'selected' : '' }}>{{ $accion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="tablaFilter">
                    <option value="">Todas las tablas</option>
                    @foreach($tablas as $tabla)
                        @if($tabla)
                            <option value="{{ $tabla }}" {{ request('tabla') == $tabla ? 'selected' : '' }}>{{ $tabla }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" id="fechaFilter" value="{{ request('fecha') }}">
            </div>
        </div>

        <!-- Resumen de actividad -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white mb-0">Total Logs</h6>
                                <div class="h2 mb-0 font-weight-bold">{{ $logs->total() }}</div>
                            </div>
                            <div>
                                <i class="fas fa-clipboard-list fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white mb-0">Usuarios Activos</h6>
                                <div class="h2 mb-0 font-weight-bold">
                                    {{ \App\Models\LogSistema::distinct('user_id')->count('user_id') }}
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white mb-0">Hoy</h6>
                                <div class="h2 mb-0 font-weight-bold">
                                    {{ \App\Models\LogSistema::whereDate('created_at', today())->count() }}
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-calendar-day fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white mb-0">Última Semana</h6>
                                <div class="h2 mb-0 font-weight-bold">
                                    {{ \App\Models\LogSistema::where('created_at', '>=', now()->subWeek())->count() }}
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-calendar-week fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de logs -->
        <div class="table-responsive">
            <table class="table table-hover" id="logsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Tabla</th>
                        <th>Detalles</th>
                        <th>IP / Agente</th>
                        <th>Fecha/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                               @if($log->user)
    <div class="d-flex align-items-center">
        <div class="avatar avatar-sm me-2">
            <div class="avatar-initial rounded-circle bg-primary">
                {{ substr($log->user->nombre, 0, 1) }}{{ substr($log->user->apellido, 0, 1) }}
            </div>
        </div>
        <div>
            <span class="d-block">{{ $log->user->nombre }} {{ $log->user->apellido }}</span>
            <small class="text-muted">{{ $log->user->role->nombre ?? 'Sin rol' }}</small>
        </div>
    </div>
@else
    <div class="d-flex align-items-center">
        <div class="avatar avatar-sm me-2">
            <div class="avatar-initial rounded-circle bg-secondary">
                <i class="fas fa-cog fa-sm"></i>
            </div>
        </div>
        <div>
            <span class="d-block text-muted">Sistema</span>
            <small class="text-muted">Acción automática</small>
        </div>
    </div>
@endif
                            </td>
                            <td>
                                <span class="badge 
                                    @if(str_contains($log->accion, 'Login'))
                                        bg-success
                                    @elseif(str_contains($log->accion, 'Creación'))
                                        bg-primary
                                    @elseif(str_contains($log->accion, 'Actualización'))
                                        bg-info
                                    @elseif(str_contains($log->accion, 'Eliminación'))
                                        bg-danger
                                    @else
                                        bg-secondary
                                    @endif
                                ">
                                    {{ $log->accion }}
                                </span>
                            </td>
                            <td>
                                @if($log->tabla_afectada)
                                    <span class="badge bg-light text-dark">{{ $log->tabla_afectada }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($log->detalles)
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $log->detalles }}">
                                        {{ $log->detalles }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="small">{{ $log->ip_address }}</span>
                                    <span class="d-inline-block text-truncate small text-muted" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="small">{{ $log->created_at->format('d/m/Y') }}</span>
                                    <span class="small text-muted">{{ $log->created_at->format('H:i:s') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación Mejorada -->
        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <div class="d-flex align-items-center">
                <small class="text-muted me-3">
                    Mostrando {{ $logs->firstItem() }} a {{ $logs->lastItem() }} 
                    de {{ $logs->total() }} registros
                </small>
                
                {{-- Selector de resultados por página --}}
                <div class="d-flex align-items-center">
                    <small class="text-muted me-2">Mostrar:</small>
                    <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                    </select>
                </div>
            </div>
            
            {{-- Paginación personalizada --}}
            <div class="pagination-wrapper">
                @if ($logs->hasPages())
                    <nav aria-label="Navegación de logs">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Botón Anterior --}}
                            @if ($logs->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->previousPageUrl() }}" rel="prev">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Números de página --}}
                            @php
                                $start = max($logs->currentPage() - 2, 1);
                                $end = min($start + 4, $logs->lastPage());
                                $start = max($end - 4, 1);
                            @endphp

                            @if($start > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->url(1) }}">1</a>
                                </li>
                                @if($start > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            @for ($i = $start; $i <= $end; $i++)
                                @if ($i == $logs->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $i }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $logs->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endif
                            @endfor

                            @if($end < $logs->lastPage())
                                @if($end < $logs->lastPage() - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->url($logs->lastPage()) }}">{{ $logs->lastPage() }}</a>
                                </li>
                            @endif

                            {{-- Botón Siguiente --}}
                            @if ($logs->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $logs->nextPageUrl() }}" rel="next">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de filtros avanzados -->
<div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filtersModalLabel">Filtros Avanzados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.logs.index') }}" method="GET">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">Usuario</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Todos los usuarios</option>
                               @foreach($usuarios as $usuario)
    <option value="{{ $usuario->id }}" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>
        {{ $usuario->nombre }} {{ $usuario->apellido }} ({{ $usuario->role->nombre ?? 'Sin rol' }})
    </option>
@endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="accion" class="form-label">Acción</label>
                            <select class="form-select" id="accion" name="accion">
                                <option value="">Todas las acciones</option>
                                @foreach($acciones as $accion)
                                    <option value="{{ $accion }}" {{ request('accion') == $accion ? 'selected' : '' }}>{{ $accion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tabla" class="form-label">Tabla Afectada</label>
                            <select class="form-select" id="tabla" name="tabla">
                                <option value="">Todas las tablas</option>
                                @foreach($tablas as $tabla)
                                    @if($tabla)
                                        <option value="{{ $tabla }}" {{ request('tabla') == $tabla ? 'selected' : '' }}>{{ $tabla }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="{{ request('fecha') }}">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="ip_address" class="form-label">Dirección IP</label>
                            <input type="text" class="form-control" id="ip_address" name="ip_address" value="{{ request('ip_address') }}" placeholder="Ej: 192.168.1.1">
                        </div>
                        <div class="col-md-6">
                            <label for="registro_id" class="form-label">ID del Registro</label>
                            <input type="number" class="form-control" id="registro_id" name="registro_id" value="{{ request('registro_id') }}" placeholder="Ej: 123">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary">Limpiar Filtros</a>
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('styles')
<style>
/* Avatars para logs */
.avatar {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.avatar-sm {
    width: 2rem;
    height: 2rem;
}

.avatar-initial {
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
}

/* Corrección de paginación - flechas más pequeñas y elegantes */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    margin: 0 0.125rem;
    border: 1px solid #dee2e6;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.15s ease-in-out;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
    transform: translateY(-1px);
}

.pagination .page-item.active .page-link {
    background-color: #20c997;
    border-color: #20c997;
    color: white;
    font-weight: 600;
}

.pagination .page-item.disabled .page-link {
    color: #adb5bd;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

/* Flechas de navegación más pequeñas y elegantes */
.pagination .page-link[rel="prev"],
.pagination .page-link[rel="next"] {
    padding: 0.5rem 0.875rem;
    font-size: 1rem;
    font-weight: 500;
}

/* Ocultar el texto original de las flechas y mostrar solo íconos */
.pagination .page-link[rel="prev"] span,
.pagination .page-link[rel="next"] span {
    display: none;
}

.pagination .page-link[rel="prev"]:after {
    content: "‹";
    font-size: 1.2rem;
    font-weight: bold;
}

.pagination .page-link[rel="next"]:after {
    content: "›";
    font-size: 1.2rem;
    font-weight: bold;
}

/* Estilo para dispositivos móviles */
@media (max-width: 768px) {
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .pagination .page-link[rel="prev"]:after,
    .pagination .page-link[rel="next"]:after {
        font-size: 1rem;
    }
}

/* Mejoras adicionales para la tabla de logs */
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75em;
    font-weight: 500;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Filtros rápidos
        const searchInput = document.getElementById('searchInput');
        const accionFilter = document.getElementById('accionFilter');
        const tablaFilter = document.getElementById('tablaFilter');
        const fechaFilter = document.getElementById('fechaFilter');
        
        searchInput.addEventListener('keyup', filterTable);
        accionFilter.addEventListener('change', applyFilters);
        tablaFilter.addEventListener('change', applyFilters);
        fechaFilter.addEventListener('change', applyFilters);
        
        function filterTable() {
            const searchText = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#logsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        }
        
        function applyFilters() {
            const accion = accionFilter.value;
            const tabla = tablaFilter.value;
            const fecha = fechaFilter.value;
            
            // Construir URL con filtros
            let url = new URL(window.location.href);
            url.searchParams.delete('page'); // Resetear la paginación
            
            if (accion) url.searchParams.set('accion', accion);
            else url.searchParams.delete('accion');
            
            if (tabla) url.searchParams.set('tabla', tabla);
            else url.searchParams.delete('tabla');
            
            if (fecha) url.searchParams.set('fecha', fecha);
            else url.searchParams.delete('fecha');
            
            window.location.href = url.toString();
        }
    });
     function changePerPage(value) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page'); // Reset a página 1
            window.location.href = url.toString();
        }
</script>
@endsection