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
                        @php
                            $totalResultados = \App\Models\ResultadoMedico::where('doctor_id', $doctor->id)->count();
                        @endphp
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
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
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
                                    <!-- BOTÓN DE ELIMINACIÓN MEJORADO -->
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar" onclick="confirmarEliminacionDoctor({{ $doctor->id }}, '{{ $doctor->user->nombre }} {{ $doctor->user->apellido }}', {{ $doctor->pacientes()->count() }}, {{ $totalResultados }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- MODAL DE CONFIRMACIÓN PARA ELIMINAR DOCTOR -->
<div class="modal fade" id="eliminarDoctorModal" tabindex="-1" aria-labelledby="eliminarDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eliminarDoctorModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirmar Eliminación de Doctor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalContentDoctor">
                    <!-- El contenido se llenará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer" id="modalFooterDoctor">
                <!-- Los botones se llenarán dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- FORMULARIOS OCULTOS PARA ELIMINACIÓN -->
<form id="formEliminarDoctorSolo" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="formEliminarDoctorConResultados" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('styles')
<style>
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #fff;
        font-weight: 600;
        overflow: hidden;
    }
    
    .avatar-md {
        width: 40px;
        height: 40px;
        font-size: 0.875rem;
    }
    
    .avatar-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #17a2b8;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.25rem;
        justify-content: center;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
    
    .table-responsive {
        border-radius: 0.375rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.2rem;
    }
    
    /* Estilos adicionales para el modal */
    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }
    
    /* Responsive table styles */
    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Búsqueda en tiempo real
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#doctors-table tbody tr');
            
            tableRows.forEach(row => {
                const doctorText = row.textContent.toLowerCase();
                row.style.display = doctorText.includes(searchTerm) ? '' : 'none';
            });
        });
        
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
        
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    /**
     * Función para confirmar la eliminación de un doctor
     */
    function confirmarEliminacionDoctor(doctorId, nombreDoctor, totalPacientes, totalResultados) {
        const modal = new bootstrap.Modal(document.getElementById('eliminarDoctorModal'));
        const modalContent = document.getElementById('modalContentDoctor');
        const modalFooter = document.getElementById('modalFooterDoctor');
        
        if (totalResultados > 0) {
            // Si hay resultados, mostrar opciones
            modalContent.innerHTML = `
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                    <div>
                        <strong>¡Atención!</strong> Este doctor tiene resultados médicos en el sistema.
                    </div>
                </div>
                <p class="mb-3">El doctor <strong>${nombreDoctor}</strong> tiene:</p>
                <ul class="mb-3">
                    <li><strong>${totalPacientes} paciente(s)</strong> asignado(s)</li>
                    <li><strong>${totalResultados} resultado(s) médico(s)</strong> subido(s) al sistema</li>
                </ul>
                <p class="mb-3"><strong>¿Qué desea hacer?</strong></p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-warning mb-3">
                            <div class="card-body text-center p-3">
                                <i class="fas fa-user-minus text-warning fs-2 mb-2"></i>
                                <h6 class="card-title">Solo Eliminar Doctor</h6>
                                <p class="card-text small">
                                    • El doctor será eliminado del sistema<br>
                                    • Los pacientes serán desasignados<br>
                                    • <strong>Los ${totalResultados} resultados médicos se mantendrán</strong><br>
                                    • Los pacientes podrán ser asignados a otros doctores
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-danger mb-3">
                            <div class="card-body text-center p-3">
                                <i class="fas fa-trash text-danger fs-2 mb-2"></i>
                                <h6 class="card-title">Eliminar Doctor y Resultados</h6>
                                <p class="card-text small">
                                    • El doctor será eliminado del sistema<br>
                                    • Los pacientes serán desasignados<br>
                                    • <strong>Los ${totalResultados} resultados médicos serán eliminados PERMANENTEMENTE</strong><br>
                                    • Se eliminarán también los archivos PDF
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <small><strong>Importante:</strong> Los pacientes NO serán eliminados del sistema, solo serán desasignados de este doctor y podrán ser asignados a otros doctores en el futuro.</small>
                </div>
            `;
            
            modalFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" onclick="eliminarDoctorSolo(${doctorId})">
                    <i class="fas fa-user-minus me-1"></i> Solo Eliminar Doctor
                </button>
                <button type="button" class="btn btn-danger" onclick="eliminarDoctorConResultados(${doctorId})">
                    <i class="fas fa-trash me-1"></i> Eliminar Doctor y Resultados
                </button>
            `;
        } else {
            // Si no hay resultados, eliminación simple
            modalContent.innerHTML = `
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fs-4"></i>
                    <div>
                        <strong>Eliminación Simple:</strong> Este doctor no tiene resultados médicos en el sistema.
                    </div>
                </div>
                <div class="text-center mb-4">
                    <i class="fas fa-user-md text-muted" style="font-size: 4rem;"></i>
                </div>
                <p class="text-center mb-3">¿Está seguro de que desea eliminar al doctor <strong>${nombreDoctor}</strong>?</p>
                <div class="mb-3">
                    <strong>Información del doctor:</strong>
                    <ul class="mt-2">
                        <li>${totalPacientes} paciente(s) asignado(s) (serán desasignados)</li>
                        <li>0 resultados médicos</li>
                    </ul>
                </div>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <small><strong>Tranquilo:</strong> Los pacientes solo serán desasignados de este doctor pero permanecerán en el sistema y podrán ser asignados a otros doctores.</small>
                </div>
            `;
            
            modalFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="eliminarDoctorSolo(${doctorId})">
                    <i class="fas fa-trash me-1"></i> Eliminar Doctor
                </button>
            `;
        }
        
        modal.show();
    }
    
    /**
     * Eliminar solo el doctor (mantener resultados)
     */
    function eliminarDoctorSolo(doctorId) {
        const form = document.getElementById('formEliminarDoctorSolo');
        form.action = `/admin/doctores/${doctorId}`;
        
        // Mostrar loading en el botón
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Eliminando...';
        button.disabled = true;
        
        form.submit();
    }
    
    /**
     * Eliminar doctor y todos sus resultados
     */
    function eliminarDoctorConResultados(doctorId) {
        const form = document.getElementById('formEliminarDoctorConResultados');
        form.action = `/admin/doctores/${doctorId}/force-delete`;
        
        // Mostrar loading en el botón
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Eliminando...';
        button.disabled = true;
        
        form.submit();
    }
</script>
@endsection