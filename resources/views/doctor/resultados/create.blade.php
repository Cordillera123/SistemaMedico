@extends('layouts.dashboard')

@section('title', 'Subir Resultado Médico')

@section('page-title', 'Subir Resultado Médico')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('doctor.resultados.index') }}">Resultados</a></li>
        <li class="breadcrumb-item active" aria-current="page">Subir</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Información del Resultado Médico</h5>
    </div>
    <div class="card-body">
        <!-- Sección de búsqueda de paciente por cédula -->
        <div class="form-section mb-4">
            <h6 class="form-section-title">Buscar Paciente por Cédula</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control" placeholder="Ingrese cédula del paciente..." id="searchCedula">
                        <button class="btn btn-outline-primary" type="button" id="btnSearchCedula">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <small class="form-text text-muted">Busque al paciente por su número de cédula para subir resultados.</small>
                </div>
            </div>
        </div>

        <form action="{{ route('doctor.resultados.store') }}" method="POST" enctype="multipart/form-data" class="dashboard-form">
            @csrf
            
            <!-- Paciente y Tipo de Resultado -->
            <div class="form-section">
                <h6 class="form-section-title">Información General</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="paciente_id" class="form-label">Paciente <span class="text-danger">*</span></label>
                        <select class="form-select @error('paciente_id') is-invalid @enderror" id="paciente_id" name="paciente_id" required>
                            <option value="">Seleccione un paciente</option>
                            @foreach($pacientes as $paciente)
                                <option value="{{ $paciente->id }}" 
                                    data-cedula="{{ $paciente->cedula ?? 'N/A' }}"
                                    {{ old('paciente_id', request('paciente_id')) == $paciente->id ? 'selected' : '' }}>
                                    {{ $paciente->user->nombre }} {{ $paciente->user->apellido }} 
                                    @if($paciente->cedula)
                                        - {{ $paciente->cedula }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('paciente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if(count($pacientes) == 0)
                            <div class="alert alert-warning mt-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No tiene pacientes asignados. <a href="{{ route('doctor.pacientes.create') }}" class="alert-link">Registre un paciente</a> antes de subir resultados.
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tipo_resultado_id" class="form-label">Tipo de Resultado <span class="text-danger">*</span></label>
                        <select class="form-select @error('tipo_resultado_id') is-invalid @enderror" id="tipo_resultado_id" name="tipo_resultado_id" required>
                            <option value="">Seleccione un tipo</option>
                            @foreach($tiposResultados as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_resultado_id') == $tipo->id ? 'selected' : '' }}>
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
            
            <!-- Información del Resultado -->
            <div class="form-section">
                <h6 class="form-section-title">Detalles del Resultado</h6>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo') }}" placeholder="Ej: Análisis de Sangre - Mayo 2023" required>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fecha_resultado" class="form-label">Fecha del Resultado <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_resultado') is-invalid @enderror" id="fecha_resultado" name="fecha_resultado" value="{{ old('fecha_resultado', date('Y-m-d')) }}" required>
                        @error('fecha_resultado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" placeholder="Descripción o notas adicionales sobre el resultado">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confidencial" name="confidencial" {{ old('confidencial') ? 'checked' : '' }}>
                            <label class="form-check-label" for="confidencial">
                                Marcar como confidencial
                            </label>
                            <small class="form-text text-muted d-block">Los resultados confidenciales solo son visibles para el paciente y los doctores asignados.</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Archivo PDF -->
            <div class="form-section">
                <h6 class="form-section-title">Archivo PDF</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="archivo_pdf" class="form-label">Archivo PDF <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="file" class="form-control @error('archivo_pdf') is-invalid @enderror" id="archivo_pdf" name="archivo_pdf" accept=".pdf" required>
                            <label class="input-group-text" for="archivo_pdf">Seleccionar</label>
                        </div>
                        @error('archivo_pdf')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">El archivo debe estar en formato PDF y no debe exceder los 10 MB.</div>
                    </div>
                </div>
            </div>
            
            <div class="form-group text-end">
                <a href="{{ route('doctor.resultados.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary" id="btnGuardar">
                    <i class="fas fa-save me-1"></i> Guardar Resultado
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
        const fileInput = document.getElementById('archivo_pdf');
        const maxFileSize = 10 * 1024 * 1024; // 10 MB en bytes
        const btnGuardar = document.getElementById('btnGuardar');
        
        form.addEventListener('submit', function(e) {
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size;
                
                if (fileSize > maxFileSize) {
                    e.preventDefault();
                    alert('El archivo excede el tamaño máximo permitido de 10 MB.');
                    return;
                }
                
                // Deshabilitar el botón para evitar múltiples envíos
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';
            }
        });
        
        // Búsqueda por cédula
        const btnSearchCedula = document.getElementById('btnSearchCedula');
        const searchCedula = document.getElementById('searchCedula');
        const pacienteSelect = document.getElementById('paciente_id');
        
        btnSearchCedula.addEventListener('click', function() {
            const cedula = searchCedula.value.trim().toLowerCase();
            
            if (!cedula) {
                alert('Por favor ingrese un número de cédula para buscar.');
                return;
            }
            
            // Buscar paciente por cédula
            let encontrado = false;
            
            for (let i = 0; i < pacienteSelect.options.length; i++) {
                const option = pacienteSelect.options[i];
                const cedulaPaciente = option.getAttribute('data-cedula');
                
                if (cedulaPaciente && cedulaPaciente.toLowerCase() === cedula) {
                    // Seleccionar este paciente
                    pacienteSelect.value = option.value;
                    encontrado = true;
                    
                    // Efecto visual para destacar que se encontró
                    pacienteSelect.classList.add('is-valid');
                    setTimeout(() => {
                        pacienteSelect.classList.remove('is-valid');
                    }, 2000);
                    
                    break;
                }
            }
            
            if (!encontrado) {
                alert('No se encontró ningún paciente con la cédula indicada.');
                searchCedula.classList.add('is-invalid');
                setTimeout(() => {
                    searchCedula.classList.remove('is-invalid');
                }, 2000);
            }
        });
        
        // También permitir buscar presionando Enter
        searchCedula.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnSearchCedula.click();
            }
        });
        
        // Vista previa del nombre del archivo seleccionado
        fileInput.addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Ningún archivo seleccionado';
            const fileSize = this.files[0]?.size || 0;
            const fileSizeMB = (fileSize / (1024 * 1024)).toFixed(2);
            
            const fileLabel = this.nextElementSibling;
            fileLabel.textContent = fileName;
            
            // Mostrar tamaño del archivo
            const fileText = document.querySelector('.form-text');
            if (fileSize > 0) {
                fileText.innerHTML = `Archivo seleccionado: <strong>${fileName}</strong> (${fileSizeMB} MB)`;
                
                // Añadir advertencia si el archivo es grande pero menor al límite
                if (fileSize > maxFileSize * 0.7 && fileSize <= maxFileSize) {
                    fileText.innerHTML += ' <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> El archivo es grande, pero aún está dentro del límite permitido.</span>';
                }
            }
        });
    });
</script>
@endsection