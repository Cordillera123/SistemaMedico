
@extends('layouts.dashboard')

@section('title', 'Gestión de Doctores')

@section('page-title', 'Gestión de Doctores')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Doctores</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Lista de Doctores</h5>
        <a href="{{ route('admin.doctores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Agregar Doctor
        </a>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control table-search" placeholder="Buscar doctor..." data-table="doctors-table" id="searchInput">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="especialidadFilter">
                    <option value="">Todas las especialidades</option>
                    @php
                        $especialidades = $doctores->pluck('especialidad')->unique()->sort();
                    @endphp
                    @foreach($especialidades as $especialidad)
                        <option value="{{ $especialidad }}">{{ $especialidad }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
        </div>

        <!-- Tabla de doctores -->
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="doctors-table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Especialidad</th>
                        <th>Contacto</th>
                        <th>Pacientes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctores as $doctor)
                        <tr data-especialidad="{{ $doctor->especialidad }}" data-status="{{ $doctor->user->activo ? '1' : '0' }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-2">
                                        <div class="avatar-initial rounded-circle bg-primary">
                                            {{ substr($doctor->user->nombre, 0, 1) }}{{ substr($doctor->user->apellido, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $doctor->user->nombre }} {{ $doctor->user->apellido }}</h6>
                                        <small class="text-muted">
                                            {{ $doctor->licencia_medica }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $doctor->especialidad }}</td>
                            <td>
                                <div>
                                    <i class="fas fa-envelope text-muted me-1"></i> {{ $doctor->user->email }}
                                </div>
                                @if($doctor->user->telefono)
                                    <div>
                                        <i class="fas fa-phone text-muted me-1"></i> {{ $doctor->user->telefono }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="text-center">
                                    <span class="badge bg-info rounded-pill">{{ $doctor->pacientes()->count() }}</span>
                                </div>
                            </td>
                            <td>
                                @if($doctor->user->activo)
                                    <span class="badge status-badge status-active">Activo</span>
                                @else
                                    <span class="badge status-badge status-inactive">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.doctores.show', $doctor->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.doctores.edit', $doctor->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.doctores.destroy', $doctor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro que desea eliminar este doctor?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $doctores->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtro por especialidad
        const especialidadFilter = document.getElementById('especialidadFilter');
        especialidadFilter.addEventListener('change', filterDoctors);
        
        // Filtro por estado
        const statusFilter = document.getElementById('statusFilter');
        statusFilter.addEventListener('change', filterDoctors);
        
        function filterDoctors() {
            const especialidad = especialidadFilter.value;
            const status = statusFilter.value;
            const rows = document.querySelectorAll('#doctors-table tbody tr');
            
            rows.forEach(row => {
                const rowEspecialidad = row.getAttribute('data-especialidad');
                const rowStatus = row.getAttribute('data-status');
                
                let showRow = true;
                
                if (especialidad && rowEspecialidad !== especialidad) {
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