@extends('layouts.dashboard')

@section('title', 'Mi Perfil')

@section('page-title', 'Mi Perfil')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Mi Perfil</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="row">
    <div class="col-lg-4">
        <!-- Tarjeta de perfil -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <div class="avatar-initial rounded-circle bg-primary">
                        {{ substr(Auth::user()->nombre, 0, 1) }}{{ substr(Auth::user()->apellido, 0, 1) }}
                    </div>
                </div>
                <h4 class="card-title mb-0">Dr. {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</h4>
                <p class="text-muted">{{ $doctor->especialidad }}</p>
                
                <div class="d-flex justify-content-center mt-3">
                    @if(Auth::user()->telefono)
                        <a href="tel:{{ Auth::user()->telefono }}" class="btn btn-outline-primary rounded-circle me-2">
                            <i class="fas fa-phone"></i>
                        </a>
                    @endif
                    <a href="mailto:{{ Auth::user()->email }}" class="btn btn-outline-primary rounded-circle">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Información de la cuenta -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de la Cuenta</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <span class="text-muted me-2"><i class="fas fa-user"></i></span>
                        <span class="fw-medium">Nombre de Usuario:</span>
                        <span class="float-end">{{ Auth::user()->username }}</span>
                    </li>
                    <li class="mb-3">
                        <span class="text-muted me-2"><i class="fas fa-envelope"></i></span>
                        <span class="fw-medium">Correo Electrónico:</span>
                        <span class="float-end">{{ Auth::user()->email }}</span>
                    </li>
                    @if(Auth::user()->telefono)
                        <li class="mb-3">
                            <span class="text-muted me-2"><i class="fas fa-phone"></i></span>
                            <span class="fw-medium">Teléfono:</span>
                            <span class="float-end">{{ Auth::user()->telefono }}</span>
                        </li>
                    @endif
                    @if(Auth::user()->direccion)
                        <li>
                            <span class="text-muted me-2"><i class="fas fa-map-marker-alt"></i></span>
                            <span class="fw-medium">Dirección:</span>
                            <span class="float-end">{{ Auth::user()->direccion }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Estadísticas</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="py-3 rounded bg-light mb-2">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                        <h4 class="mb-0">{{ $doctor->pacientes()->count() }}</h4>
                        <small class="text-muted">Pacientes</small>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="py-3 rounded bg-light mb-2">
                            <i class="fas fa-file-medical fa-2x text-success"></i>
                        </div>
                        <h4 class="mb-0">{{ $doctor->resultadosMedicos()->count() }}</h4>
                        <small class="text-muted">Resultados</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <!-- Formulario de actualización de perfil -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actualizar Perfil</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('doctor.perfil.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle mt-1"></i>
                            </div>
                            <div class="ms-2">
                                <p class="mb-0">Actualice su información personal y profesional. Si desea cambiar su contraseña, complete los campos correspondientes.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información Personal -->
                    <div class="form-section">
                        <h6 class="form-section-title">Información Personal</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', Auth::user()->nombre) }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('apellido') is-invalid @enderror" id="apellido" name="apellido" value="{{ old('apellido', Auth::user()->apellido) }}" required>
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', Auth::user()->telefono) }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', Auth::user()->direccion) }}">
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información Profesional -->
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
                                <label for="horario_consulta" class="form-label">Horario de Consulta</label>
                                <input type="text" class="form-control @error('horario_consulta') is-invalid @enderror" id="horario_consulta" name="horario_consulta" value="{{ old('horario_consulta', $doctor->horario_consulta) }}" placeholder="Ej: Lunes a Viernes de 8:00 a 16:00">
                                @error('horario_consulta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="biografia" class="form-label">Biografía</label>
                                <textarea class="form-control @error('biografia') is-invalid @enderror" id="biografia" name="biografia" rows="4" placeholder="Describa brevemente su experiencia profesional y especialidades...">{{ old('biografia', $doctor->biografia) }}</textarea>
                                @error('biografia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cambio de Contraseña -->
                    <div class="form-section">
                        <h6 class="form-section-title">Cambiar Contraseña</h6>
                        <p class="text-muted mb-3">Deje estos campos en blanco si no desea cambiar su contraseña.</p>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="password_actual" class="form-label">Contraseña Actual</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password_actual') is-invalid @enderror" id="password_actual" name="password_actual">
                                    <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password_actual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="password" class="form-label">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                    <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                    <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-xl {
        width: 96px;
        height: 96px;
        font-size: 2rem;
    }
    
    .avatar-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }
    
    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .form-section:last-child {
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle para mostrar/ocultar contraseña
        const passwordToggles = document.querySelectorAll('.password-toggle');
        passwordToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const input = this.previousElementSibling;
                
                if (input) {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    // Cambiar el icono
                    this.innerHTML = type === 'password' 
                        ? '<i class="fas fa-eye"></i>' 
                        : '<i class="fas fa-eye-slash"></i>';
                }
            });
        });
    });
</script>
@endsection