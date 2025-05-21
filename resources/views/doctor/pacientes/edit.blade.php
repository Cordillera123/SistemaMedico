@extends('layouts.dashboard')

@section('title', 'Editar Paciente')

@section('page-title', 'Editar Paciente')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('doctor.pacientes.index') }}">Pacientes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar Paciente</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Información del Paciente</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('doctor.pacientes.update', $paciente->id) }}" method="POST" class="dashboard-form">
            @csrf
            @method('PUT')
            
            <!-- Información de la Cuenta (Solo Lectura) -->
            <div class="form-section">
                <h6 class="form-section-title">Información Personal <small class="text-muted">(Solo lectura)</small></h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" value="{{ $paciente->user->nombre }}" readonly disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" value="{{ $paciente->user->apellido }}" readonly disabled>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cedula" class="form-label">Cédula</label>
                        <input type="text" class="form-control" id="cedula" value="{{ $paciente->user->cedula ?? 'No registrada' }}" readonly disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="text" class="form-control" id="fecha_nacimiento" value="{{ $paciente->fecha_nacimiento->format('d/m/Y') }}" readonly disabled>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" value="{{ $paciente->user->email }}" readonly disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="genero" class="form-label">Género</label>
                        <input type="text" class="form-control" id="genero" value="{{ $paciente->genero }}" readonly disabled>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">Nombre de Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" value="{{ $paciente->user->username }}" readonly disabled>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" id="telefono" value="{{ $paciente->user->telefono ?? 'No registrado' }}" readonly disabled>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Información Médica (Editable) -->
            <div class="form-section">
                <h6 class="form-section-title">Información Médica <small class="text-success">(Editable)</small></h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tipo_sangre" class="form-label">Tipo de Sangre</label>
                        <select class="form-select @error('tipo_sangre') is-invalid @enderror" id="tipo_sangre" name="tipo_sangre">
                            <option value="">Seleccione...</option>
                            <option value="A+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('tipo_sangre', $paciente->tipo_sangre) == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                        @error('tipo_sangre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $paciente->user->direccion) }}">
                        </div>
                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="alergias" class="form-label">Alergias</label>
                        <textarea class="form-control @error('alergias') is-invalid @enderror" id="alergias" name="alergias" rows="2">{{ old('alergias', $paciente->alergias) }}</textarea>
                        @error('alergias')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="antecedentes_medicos" class="form-label">Antecedentes Médicos</label>
                        <textarea class="form-control @error('antecedentes_medicos') is-invalid @enderror" id="antecedentes_medicos" name="antecedentes_medicos" rows="4">{{ old('antecedentes_medicos', $paciente->antecedentes_medicos) }}</textarea>
                        @error('antecedentes_medicos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle mt-1"></i>
                    </div>
                    <div class="ms-2">
                        <p class="mb-0">Solo puede editar la información médica del paciente. La información personal y credenciales de acceso solo pueden ser modificadas por el administrador del sistema.</p>
                    </div>
                </div>
            </div>
            
            <div class="form-group text-end">
                <a href="{{ route('doctor.pacientes.show', $paciente->id) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Volver a Detalles
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('styles')
<style>
    /* Estilos para los campos de solo lectura */
    input[readonly], input[disabled] {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
    
    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .form-section:last-of-type {
        border-bottom: none;
    }
    
    .form-section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1.25rem;
    }
</style>
@endsection