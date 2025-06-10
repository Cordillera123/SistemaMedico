@extends('layouts.dashboard')

@section('title', 'Detalle de Resultado Médico')

@section('page-title', 'Detalle de Resultado Médico')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('paciente.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('paciente.resultados.index') }}">Resultados Médicos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detalle</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="row">
    <div class="col-lg-8">
        <!-- Información del resultado -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Información del Resultado</h5>
                <div>
                    @if($resultado->visto_por_paciente)
                        <span class="badge status-badge status-active">
                            <i class="fas fa-check-circle me-1"></i> Visto
                        </span>
                    @else
                        <span class="badge status-badge status-pending">
                            <i class="fas fa-exclamation-circle me-1"></i> Nuevo
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h4 class="mb-3">{{ $resultado->titulo }}</h4>
                    
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-light">
                                            <i class="fas fa-calendar-alt text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <small class="text-muted d-block">Fecha del Resultado</small>
                                        <span class="fw-medium">{{ $resultado->fecha_resultado->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-light">
                                            <i class="fas fa-tag text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <small class="text-muted d-block">Tipo de Resultado</small>
                                        <span class="fw-medium">{{ $resultado->tipoResultado->nombre }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-light">
                                            <i class="fas fa-user-md text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <small class="text-muted d-block">Doctor</small>
                                        <span class="fw-medium">Dr(a). {{ $resultado->doctor->user->nombre }} {{ $resultado->doctor->user->apellido }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm bg-light">
                                            <i class="fas fa-clock text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <small class="text-muted d-block">Subido</small>
                                        <span class="fw-medium">{{ $resultado->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($resultado->descripcion)
                        <div class="mb-4">
                            <h6 class="fw-bold">Descripción</h6>
                            <p class="mb-0">{{ $resultado->descripcion }}</p>
                        </div>
                    @endif
                </div>

                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-file-pdf fa-4x text-danger"></i>
                    </div>
                    <h5 class="mb-3">Documento PDF</h5>
                    <p class="text-muted mb-4">El resultado se encuentra disponible en formato PDF, puede visualizarlo o descargarlo para su revisión.</p>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('paciente.resultados.descargar', $resultado->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-download me-1"></i> Descargar PDF
                        </a>
                        <!-- Si deseas añadir visualización directa del PDF -->
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#pdfViewerModal">
                            <i class="fas fa-eye me-1"></i> Ver PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Información adicional -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Doctor</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <div class="avatar-initial rounded-circle bg-primary">
                            {{ substr($resultado->doctor->user->nombre, 0, 1) }}{{ substr($resultado->doctor->user->apellido, 0, 1) }}
                        </div>
                    </div>
                    <h5 class="mb-1">Dr(a). {{ $resultado->doctor->user->nombre }} {{ $resultado->doctor->user->apellido }}</h5>
                    <p class="text-muted mb-0">{{ $resultado->doctor->especialidad }}</p>
                </div>
                <hr>
                <div class="mb-3">
                    @if($resultado->doctor->horario_consulta)
                        <div class="mb-2">
                            <i class="fas fa-clock text-muted me-2"></i>
                            <span>{{ $resultado->doctor->horario_consulta }}</span>
                        </div>
                    @endif
                    @if($resultado->doctor->user->telefono)
                        <div class="mb-2">
                            <i class="fas fa-phone text-muted me-2"></i>
                            <span>{{ $resultado->doctor->user->telefono }}</span>
                        </div>
                    @endif
                    <div>
                        <i class="fas fa-envelope text-muted me-2"></i>
                        <span>{{ $resultado->doctor->user->email }}</span>
                    </div>
                </div>
                <div class="text-center">
                    <a href="{{ route('paciente.mi-medico') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-info-circle me-1"></i> Ver perfil completo
                    </a>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Acciones</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('paciente.resultados.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver a la lista
                    </a>
                    <a href="{{ route('paciente.resultados.descargar', $resultado->id) }}" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Descargar PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar PDF -->
<div class="modal fade" id="pdfViewerModal" tabindex="-1" aria-labelledby="pdfViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfViewerModalLabel">{{ $resultado->titulo }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pdf-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando PDF...</p>
                </div>
                <div class="ratio ratio-16x9" id="pdf-container" style="display: none;">
                    <iframe id="pdf-iframe" src="" allowfullscreen style="border: none;"></iframe>
                </div>
                <div id="pdf-error" class="alert alert-danger" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> No se pudo cargar el PDF para previsualización.
                    <br><a href="{{ route('paciente.resultados.descargar', $resultado->id) }}" class="alert-link">Descargue el archivo para verlo</a>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="{{ route('paciente.resultados.descargar', $resultado->id) }}" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i> Descargar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pdfModal = document.getElementById('pdfViewerModal');
    
    if (pdfModal) {
        pdfModal.addEventListener('show.bs.modal', function() {
            cargarPdfPaciente();
        });
        
        pdfModal.addEventListener('hidden.bs.modal', function() {
            limpiarModalPaciente();
        });
    }
});

function cargarPdfPaciente() {
    const iframe = document.getElementById('pdf-iframe');
    const loading = document.getElementById('pdf-loading');
    const container = document.getElementById('pdf-container');
    const error = document.getElementById('pdf-error');
    
    // Resetear estados
    loading.style.display = 'block';
    container.style.display = 'none';
    error.style.display = 'none';
    
    // Construir URL del PDF
    const pdfFileName = '{{ $resultado->archivo_pdf }}';
    const pdfUrl = `${window.location.origin}/storage/${pdfFileName}`;
    
    console.log('Paciente - Cargando PDF desde:', pdfUrl);
    
    // Verificar que el archivo existe
    fetch(pdfUrl, { method: 'HEAD' })
        .then(response => {
            if (response.ok) {
                console.log('Paciente - Archivo encontrado, cargando en iframe...');
                
                // Configurar eventos del iframe
                iframe.onload = function() {
                    console.log('Paciente - PDF cargado exitosamente');
                    loading.style.display = 'none';
                    container.style.display = 'block';
                };
                
                iframe.onerror = function() {
                    console.error('Paciente - Error en iframe al cargar PDF');
                    mostrarErrorPaciente();
                };
                
                // Cargar PDF en iframe
                iframe.src = pdfUrl;
                
                // Timeout de seguridad
                setTimeout(() => {
                    if (loading.style.display !== 'none') {
                        console.warn('Paciente - Timeout: El PDF tardó demasiado en cargar');
                        mostrarErrorPaciente();
                    }
                }, 15000);
                
            } else {
                console.error('Paciente - Archivo no encontrado. Status:', response.status);
                mostrarErrorPaciente();
            }
        })
        .catch(error => {
            console.error('Paciente - Error al verificar archivo:', error);
            mostrarErrorPaciente();
        });
}

function mostrarErrorPaciente() {
    document.getElementById('pdf-loading').style.display = 'none';
    document.getElementById('pdf-error').style.display = 'block';
}

function limpiarModalPaciente() {
    const iframe = document.getElementById('pdf-iframe');
    if (iframe) {
        iframe.src = '';
    }
    
    // Resetear estados
    document.getElementById('pdf-loading').style.display = 'block';
    document.getElementById('pdf-container').style.display = 'none';
    document.getElementById('pdf-error').style.display = 'none';
}
</script>