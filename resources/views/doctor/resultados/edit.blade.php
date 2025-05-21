@extends('layouts.dashboard')

@section('title', 'Editar Resultado Médico')

@section('page-title', 'Editar Resultado Médico')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('doctor.resultados.index') }}">Resultados</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Editar Información del Resultado</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('doctor.resultados.update', $resultado->id) }}" method="POST" enctype="multipart/form-data" class="dashboard-form">
            @csrf
            @method('PUT')
            
            <!-- Información del Resultado -->
            <div class="form-section">
                <h6 class="form-section-title">Información General</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="paciente_id" class="form-label">Paciente</label>
                        <input type="text" class="form-control" value="{{ $resultado->paciente->user->nombre }} {{ $resultado->paciente->user->apellido }}" readonly disabled>
                        <div class="form-text">El paciente no se puede cambiar. Si necesita asignar este resultado a otro paciente, elimine este y cree uno nuevo.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tipo_resultado_id" class="form-label">Tipo de Resultado <span class="text-danger">*</span></label>
                        <select class="form-select @error('tipo_resultado_id') is-invalid @enderror" id="tipo_resultado_id" name="tipo_resultado_id" required>
                            <option value="">Seleccione un tipo</option>
                            @foreach($tiposResultados as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_resultado_id', $resultado->tipo_resultado_id) == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_resultado_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Detalles del Resultado -->
            <div class="form-section">
                <h6 class="form-section-title">Detalles del Resultado</h6>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo', $resultado->titulo) }}" required>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fecha_resultado" class="form-label">Fecha del Resultado <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_resultado') is-invalid @enderror" id="fecha_resultado" name="fecha_resultado" value="{{ old('fecha_resultado', $resultado->fecha_resultado->format('Y-m-d')) }}" required>
                        @error('fecha_resultado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" placeholder="Descripción o notas adicionales sobre el resultado">{{ old('descripcion', $resultado->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Archivo PDF -->
            <div class="form-section">
                <h6 class="form-section-title">Archivo PDF</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="archivo_pdf" class="form-label">Archivo PDF Actual</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="{{ $resultado->nombre_archivo }}" readonly>
                            <a href="{{ route('resultados.descargar', $resultado->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                        <div class="form-text mb-3">Archivo PDF actual asociado a este resultado.</div>
                        
                        <label for="archivo_pdf_nuevo" class="form-label">Reemplazar Archivo PDF</label>
                        <div class="input-group">
                            <input type="file" class="form-control @error('archivo_pdf') is-invalid @enderror" id="archivo_pdf_nuevo" name="archivo_pdf" accept=".pdf">
                            <label class="input-group-text" for="archivo_pdf_nuevo">Seleccionar</label>
                        </div>
                        @error('archivo_pdf')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Opcional. Deje en blanco para mantener el archivo actual. Si sube un nuevo archivo, el anterior será reemplazado.</div>
                        <div class="form-text">El archivo debe estar en formato PDF y no debe exceder los 10 MB.</div>
                    </div>
                </div>
            </div>
            
            <!-- Opciones Adicionales -->
            <div class="form-section">
                <h6 class="form-section-title">Opciones Adicionales</h6>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confidencial" name="confidencial" {{ old('confidencial', $resultado->confidencial) ? 'checked' : '' }}>
                    <label class="form-check-label" for="confidencial">
                        Marcar como confidencial
                    </label>
                    <div class="form-text">Los resultados confidenciales solo pueden ser vistos por el médico y el paciente.</div>
                </div>
                
                @if($resultado->visto_por_paciente)
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="ms-2">
                                <p class="mb-0">Este resultado ya ha sido visto por el paciente el {{ $resultado->fecha_visualizacion->format('d/m/Y H:i') }}. Si realiza cambios importantes, como subir un nuevo archivo, el resultado se marcará como no visto para que el paciente pueda revisarlo nuevamente.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="form-group text-end">
                <a href="{{ route('doctor.resultados.show', $resultado->id) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Actualizar Resultado
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validar el tamaño del archivo antes de enviar el formulario
        const form = document.querySelector('form');
        const fileInput = document.getElementById('archivo_pdf_nuevo');
        const maxFileSize = 10 * 1024 * 1024; // 10 MB en bytes
        
        form.addEventListener('submit', function(e) {
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size;
                
                if (fileSize > maxFileSize) {
                    e.preventDefault();
                    alert('El archivo excede el tamaño máximo permitido de 10 MB.');
                }
            }
        });
    });
</script>
@endsection