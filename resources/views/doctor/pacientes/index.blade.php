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
                                            <!-- NUEVA SECCIÓN PARA ELIMINAR -->
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger" onclick="confirmarEliminacion({{ $paciente->id }}, '{{ $paciente->user->nombre }} {{ $paciente->user->apellido }}', {{ $totalResultados }})">
                                                    <i class="fas fa-trash me-2"></i> Eliminar de mi Lista
                                                </button>
                                            </li>
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
               
                <h5>No tiene pacientes asignados</h5>
                <p class="text-muted">Agregue pacientes a su lista para comenzar a gestionarlos.</p>
                <a href="{{ route('doctor.pacientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Agregar Paciente
                </a>
            </div>
        @endif
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN PARA ELIMINAR PACIENTE -->
<div class="modal fade" id="eliminarPacienteModal" tabindex="-1" aria-labelledby="eliminarPacienteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eliminarPacienteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <!-- El contenido se llenará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer" id="modalFooter">
                <!-- Los botones se llenarán dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- FORMULARIOS OCULTOS PARA ELIMINACIÓN -->
<form id="formEliminarSinResultados" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="formEliminarConResultados" method="POST" style="display: none;">
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
        /* CORREGIDO: Usar el color verde/teal del diseño */
        background-color: #17a2b8 !important;
    }
    
    /* SOBRESCRIBIR el color azul por defecto de Bootstrap */
    .avatar-initial.bg-primary {
        background-color: #17a2b8 !important;
    }
    
    .btn-icon {
        padding: 0.25rem 0.5rem;
        line-height: 1;
    }
    
    /* Estilos para la tabla */
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
    
    .dropdown-item.text-danger:hover {
        background-color: #f8d7da;
        color: #721c24 !important;
    }
    
    /* Responsive styles */
    @media (max-width: 768px) {
        .dropdown-menu {
            min-width: 200px;
        }
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

    /**
     * Función para confirmar la eliminación de un paciente
     */
   /**
 * Función mejorada para confirmar la eliminación de un paciente
 */
function confirmarEliminacion(pacienteId, nombrePaciente, totalResultados) {
    const modal = new bootstrap.Modal(document.getElementById('eliminarPacienteModal'));
    const modalContent = document.getElementById('modalContent');
    const modalFooter = document.getElementById('modalFooter');
    
    if (totalResultados > 0) {
        // Si hay resultados, mostrar opciones
        modalContent.innerHTML = `
            <div class="alert alert-warning d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                <div>
                    <strong>¡Atención!</strong> Este paciente tiene resultados médicos que usted subió.
                </div>
            </div>
            <p class="mb-3">El paciente <strong>${nombrePaciente}</strong> tiene <strong>${totalResultados} resultado(s) médico(s)</strong> que usted subió al sistema.</p>
            <p class="mb-3"><strong>¿Qué desea hacer?</strong></p>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-warning mb-3">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-user-minus text-warning fs-2 mb-2"></i>
                            <h6 class="card-title">Solo Quitar de mi Lista</h6>
                            <p class="card-text small">
                                • El paciente se quitará de su lista<br>
                                • <strong>Sus resultados médicos se mantendrán</strong><br>
                                • Otros doctores seguirán teniendo acceso<br>
                                • Podrá reasignarlo en el futuro
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-danger mb-3">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-trash text-danger fs-2 mb-2"></i>
                            <h6 class="card-title">Eliminar Paciente y MIS Resultados</h6>
                            <p class="card-text small">
                                • Se eliminará de su lista<br>
                                • <strong>Se eliminarán SOLO sus ${totalResultados} resultado(s)</strong><br>
                                • Resultados de otros doctores se mantienen<br>
                                • Acción <strong>PERMANENTE</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <small><strong>Importante:</strong> Esta acción NO elimina al paciente del sistema completamente. Solo afecta su relación personal con el paciente y los resultados que USTED subió.</small>
            </div>
        `;
        
        modalFooter.innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="button" class="btn btn-warning" onclick="eliminarSoloLista(${pacienteId})">
                <i class="fas fa-user-minus me-1"></i> Solo Quitar de mi Lista
            </button>
            <button type="button" class="btn btn-danger" onclick="eliminarConResultados(${pacienteId})">
                <i class="fas fa-trash me-1"></i> Eliminar Paciente y MIS Resultados
            </button>
        `;
    } else {
        // Si no hay resultados, eliminación simple
        modalContent.innerHTML = `
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fs-4"></i>
                <div>
                    <strong>Eliminación Simple:</strong> Este paciente no tiene resultados médicos que usted haya subido.
                </div>
            </div>
            <div class="text-center mb-4">
                <i class="fas fa-user-times text-muted" style="font-size: 4rem;"></i>
            </div>
            <p class="text-center mb-3">¿Está seguro de que desea quitar a <strong>${nombrePaciente}</strong> de su lista de pacientes?</p>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <small><strong>Tranquilo:</strong> El paciente solo será removido de su lista personal. Podrá reasignarlo en el futuro si es necesario. Esta acción no afecta a otros doctores.</small>
            </div>
        `;
        
        modalFooter.innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="button" class="btn btn-primary" onclick="eliminarSoloLista(${pacienteId})">
                <i class="fas fa-user-minus me-1"></i> Quitar de mi Lista
            </button>
        `;
    }
    
    modal.show();
}
    
    /**
     * Eliminar solo la relación doctor-paciente
     */
    function eliminarSoloLista(pacienteId) {
        const form = document.getElementById('formEliminarSinResultados');
        form.action = `/doctor/pacientes/${pacienteId}`;
        
        // Mostrar loading en el botón
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Eliminando...';
        button.disabled = true;
        
        form.submit();
    }
    
    /**
     * Eliminar paciente y todos sus resultados
     */
    function eliminarConResultados(pacienteId) {
        const form = document.getElementById('formEliminarConResultados');
        form.action = `/doctor/pacientes/${pacienteId}/force-delete`;
        
        // Mostrar loading en el botón
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Eliminando...';
        button.disabled = true;
        
        form.submit();
    }
</script>
@endsection