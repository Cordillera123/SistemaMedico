@extends('layouts.dashboard')

@section('title', 'Editar Doctor')

@section('page-title', 'Editar Doctor')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.doctores.index') }}">Doctores</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Editar Información del Doctor</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.doctores.update', $doctor->id) }}" method="POST" class="dashboard-form">
            @csrf
            @method('PUT')
            
            <!-- Datos de Usuario -->
            <div class="form-section">
                <h6 class="form-section-title">Información de la Cuenta</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $doctor->user->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('apellido') is-invalid @enderror" id="apellido" name="apellido" value="{{ old('apellido', $doctor->user->apellido) }}" required>
                        @error('apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $doctor->user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $doctor->user->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Nueva Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Dejar en blanco para mantener la actual">
                            <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">La contraseña debe tener al menos 8 caracteres.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Dejar en blanco para mantener la actual">
                            <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <!-- Campo oculto para asegurar que siempre se envíe un valor -->
                            <input type="hidden" name="activo" value="0">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" {{ old('activo', $doctor->user->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">
                                Cuenta activa
                            </label>
                            <div class="form-text">Desmarque esta opción para desactivar temporalmente la cuenta del doctor.</div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            
            <!-- Datos de Contacto -->
            <div class="form-section">
                <h6 class="form-section-title">Datos de Contacto</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $doctor->user->telefono) }}">
                        </div>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $doctor->user->direccion) }}">
                        </div>
                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Datos Profesionales -->
            <div class="form-section">
                <h6 class="form-section-title">Información Profesional</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="especialidad" class="form-label">Especialidad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('especialidad') is-invalid @enderror" id="especialidad" name="especialidad" value="{{ old('especialidad', $doctor->especialidad) }}" required>
                        @error('especialidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="licencia_medica" class="form-label">Licencia Médica <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('licencia_medica') is-invalid @enderror" id="licencia_medica" name="licencia_medica" value="{{ old('licencia_medica', $doctor->licencia_medica) }}" required>
                        @error('licencia_medica')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="biografia" class="form-label">Biografía</label>
                        <textarea class="form-control @error('biografia') is-invalid @enderror" id="biografia" name="biografia" rows="4">{{ old('biografia', $doctor->biografia) }}</textarea>
                        @error('biografia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="horario_consulta" class="form-label">Horario de Consulta</label>
                        <input type="text" class="form-control @error('horario_consulta') is-invalid @enderror" id="horario_consulta" name="horario_consulta" value="{{ old('horario_consulta', $doctor->horario_consulta) }}" placeholder="Ej: Lunes a Viernes 8:00 - 16:00">
                        @error('horario_consulta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="form-group text-end">
                <a href="{{ route('admin.doctores.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Actualizar Doctor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection