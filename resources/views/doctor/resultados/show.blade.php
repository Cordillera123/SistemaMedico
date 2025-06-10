@extends('layouts.dashboard')

@section('title', 'Detalle de Resultado Médico')

@section('page-title', 'Detalle de Resultado Médico')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('doctor.resultados.index') }}">Resultados Médicos</a></li>
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
                            <i class="fas fa-check-circle me-1"></i> Visto por el paciente
                            <small class="d-block">{{ $resultado->fecha_visualizacion ? $resultado->fecha_visualizacion->format('d/m/Y H:i') : '' }}</small>
                        </span>
                    @else
                        <span class="badge status-badge status-pending">
                            <i class="fas fa-exclamation-circle me-1"></i> No visto
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
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <small class="text-muted d-block">Paciente</small>
                                        <span class="fw-medium">{{ $resultado->paciente->user->nombre }} {{ $resultado->paciente->user->apellido }}</span>
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
                        <a href="{{ route('resultados.descargar', $resultado->id) }}" class="btn btn-primary me-2">
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
        <!-- Información del paciente -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Paciente</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <div class="avatar-initial rounded-circle {{ $resultado->paciente->genero == 'Masculino' ? 'bg-primary' : 'bg-info' }}">
                            {{ substr($resultado->paciente->user->nombre, 0, 1) }}{{ substr($resultado->paciente->user->apellido, 0, 1) }}
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $resultado->paciente->user->nombre }} {{ $resultado->paciente->user->apellido }}</h5>
                    <p class="text-muted mb-0">{{ $resultado->paciente->edad }} años - {{ $resultado->paciente->genero }}</p>
                </div>
                <hr>
                <div class="mb-3">
                    @if($resultado->paciente->user->telefono)
                        <div class="mb-2">
                            <i class="fas fa-phone text-muted me-2"></i>
                            <span>{{ $resultado->paciente->user->telefono }}</span>
                        </div>
                    @endif
                    <div class="mb-2">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        <span>{{ $resultado->paciente->user->email }}</span>
                    </div>
                    @if($resultado->paciente->tipo_sangre)
                        <div class="mb-2">
                            <i class="fas fa-tint text-muted me-2"></i>
                            <span>Tipo de sangre: {{ $resultado->paciente->tipo_sangre }}</span>
                        </div>
                    @endif
                </div>
                <div class="text-center">
                    <a href="{{ route('doctor.pacientes.show', $resultado->paciente->id) }}" class="btn btn-outline-primary btn-sm">
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
                    <a href="{{ route('doctor.resultados.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver a la lista
                    </a>
                    <a href="{{ route('doctor.resultados.edit', $resultado->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Editar Resultado
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i> Eliminar Resultado
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar PDF -->
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
                    <small class="text-muted">Verificando archivo...</small>
                </div>
                <div class="ratio ratio-16x9" id="pdf-container" style="display: none;">
                    <iframe id="pdf-iframe" src="" allowfullscreen style="border: none;"></iframe>
                </div>
                <div id="pdf-error" class="alert alert-danger" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> No se pudo cargar el PDF para previsualización.
                    <br><small>El archivo se encuentra en: <code id="pdf-path"></code></small>
                    <br><a href="{{ route('resultados.descargar', $resultado->id) }}" class="alert-link">Descargue el archivo para verlo</a>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="{{ route('resultados.descargar', $resultado->id) }}" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i> Descargar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
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
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pdfModal = document.getElementById('pdfViewerModal');
    
    if (pdfModal) {
        pdfModal.addEventListener('show.bs.modal', function() {
            cargarPdf();
        });
        
        pdfModal.addEventListener('hidden.bs.modal', function() {
            limpiarModal();
        });
    }
});

function cargarPdf() {
    const iframe = document.getElementById('pdf-iframe');
    const loading = document.getElementById('pdf-loading');
    const container = document.getElementById('pdf-container');
    const error = document.getElementById('pdf-error');
    const pathElement = document.getElementById('pdf-path');
    
    // Resetear estados
    loading.style.display = 'block';
    container.style.display = 'none';
    error.style.display = 'none';
    
    // Construir URL del PDF basándose en la estructura que ya funciona
    const pdfFileName = '{{ $resultado->archivo_pdf }}';
    const pdfUrl = `${window.location.origin}/storage/${pdfFileName}`;
    
    // Mostrar la ruta para debug
    if (pathElement) {
        pathElement.textContent = pdfUrl;
    }
    
    console.log('Cargando PDF desde:', pdfUrl);
    
    // Verificar que el archivo existe
    fetch(pdfUrl, { method: 'HEAD' })
        .then(response => {
            if (response.ok) {
                console.log('Archivo encontrado, cargando en iframe...');
                
                // Configurar eventos del iframe
                iframe.onload = function() {
                    console.log('PDF cargado exitosamente');
                    loading.style.display = 'none';
                    container.style.display = 'block';
                };
                
                iframe.onerror = function() {
                    console.error('Error en iframe al cargar PDF');
                    mostrarError();
                };
                
                // Cargar PDF en iframe
                iframe.src = pdfUrl;
                
                // Timeout de seguridad más largo
                setTimeout(() => {
                    if (loading.style.display !== 'none') {
                        console.warn('Timeout: El PDF tardó demasiado en cargar');
                        mostrarError();
                    }
                }, 15000);
                
            } else {
                console.error('Archivo no encontrado. Status:', response.status);
                mostrarError();
            }
        })
        .catch(error => {
            console.error('Error al verificar archivo:', error);
            mostrarError();
        });
}

function mostrarError() {
    document.getElementById('pdf-loading').style.display = 'none';
    document.getElementById('pdf-error').style.display = 'block';
}

function limpiarModal() {
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