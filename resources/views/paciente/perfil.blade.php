@extends('layouts.dashboard')

@section('title', 'Mi Perfil')

@section('page-title', 'Mi Perfil')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('paciente.dashboard') }}">Dashboard</a></li>
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
                    <div class="avatar-initial rounded-circle {{ $paciente->genero == 'Masculino' ? 'bg-primary' : 'bg-info' }}">
                        {{ substr(Auth::user()->nombre, 0, 1) }}{{ substr(Auth::user()->apellido, 0, 1) }}
                    </div>
                </div>
                <h4 class="card-title mb-0">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</h4>
                <p class="text-muted">{{ $paciente->edad }} años - {{ $paciente->genero }}</p>
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
        
        <!-- Información médica -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información Médica</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Fecha de Nacimiento:</span>
                        <span>{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Género:</span>
                        <span>{{ $paciente->genero }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Tipo de Sangre:</span>
                        <span>{{ $paciente->tipo_sangre ?: 'No especificado' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Doctor Asignado:</span>
                        <span>Dr(a). {{ $paciente->doctor->user->nombre }} {{ $paciente->doctor->user->apellido }}</span>
                    </li>
                </ul>
                
                @if($paciente->alergias)
                    <div class="mt-3">
                        <h6 class="fw-medium">Alergias:</h6>
                        <p class="mb-0">{{ $paciente->alergias }}</p>
                    </div>
                @endif
                
                <div class="mt-3 text-center">
                    <a href="{{ route('paciente.mi-medico') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-user-md me-1"></i> Ver Información de mi Médico
                    </a>
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
                <form action="{{ route('paciente.perfil.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle mt-1"></i>
                            </div>
                            <div class="ms-2">
                                <p class="mb-0">Actualice su información personal. Si desea cambiar su contraseña, complete los campos correspondientes.</p>
                                <p class="mb-0 mt-2">Para actualizar su información médica, contacte a su médico asignado.</p>
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