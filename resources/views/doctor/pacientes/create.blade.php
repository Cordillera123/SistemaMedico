@extends('layouts.dashboard')

@section('title', 'Registrar Paciente')

@section('page-title', 'Registrar Paciente')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('doctor.pacientes.index') }}">Pacientes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registrar</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="patientTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="new-patient-tab" data-bs-toggle="tab" data-bs-target="#new-patient" type="button" role="tab" aria-controls="new-patient" aria-selected="true">
                            <i class="fas fa-user-plus me-1"></i> Nuevo Paciente
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="existing-patient-tab" data-bs-toggle="tab" data-bs-target="#existing-patient" type="button" role="tab" aria-controls="existing-patient" aria-selected="false">
                            <i class="fas fa-users me-1"></i> Seleccionar Paciente Existente
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="patientTabsContent">
                    <!-- Formulario para nuevo paciente -->
                    <div class="tab-pane fade show active" id="new-patient" role="tabpanel" aria-labelledby="new-patient-tab">
                        <form action="{{ route('doctor.pacientes.store') }}" method="POST" class="dashboard-form">
                            @csrf
                            
                            <!-- Información de la Cuenta -->
                            <div class="form-section">
                                <h6 class="form-section-title">Información de la Cuenta</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('apellido') is-invalid @enderror" id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                                        @error('apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('cedula') is-invalid @enderror" id="cedula" name="cedula" value="{{ old('cedula') }}" required>
                                        @error('cedula')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required>
                                        @error('fecha_nacimiento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="genero" class="form-label">Género <span class="text-danger">*</span></label>
                                        <select class="form-select @error('genero') is-invalid @enderror" id="genero" name="genero" required>
                                            <option value="">Seleccione...</option>
                                            <option value="Masculino" {{ old('genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                            <option value="Femenino" {{ old('genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                            <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        @error('genero')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                                        </div>
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono') }}">
                                        </div>
                                        @error('telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" id="generatePasswordBtn">
                                                <i class="fas fa-key"></i> Generar
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">La contraseña debe tener al menos 8 caracteres.</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#password_confirmation">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Información Médica -->
                            <div class="form-section">
                                <h6 class="form-section-title">Información Médica</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_sangre" class="form-label">Tipo de Sangre</label>
                                        <select class="form-select @error('tipo_sangre') is-invalid @enderror" id="tipo_sangre" name="tipo_sangre">
                                            <option value="">Seleccione...</option>
                                            <option value="A+" {{ old('tipo_sangre') == 'A+' ? 'selected' : '' }}>A+</option>
                                            <option value="A-" {{ old('tipo_sangre') == 'A-' ? 'selected' : '' }}>A-</option>
                                            <option value="B+" {{ old('tipo_sangre') == 'B+' ? 'selected' : '' }}>B+</option>
                                            <option value="B-" {{ old('tipo_sangre') == 'B-' ? 'selected' : '' }}>B-</option>
                                            <option value="AB+" {{ old('tipo_sangre') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                            <option value="AB-" {{ old('tipo_sangre') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                            <option value="O+" {{ old('tipo_sangre') == 'O+' ? 'selected' : '' }}>O+</option>
                                            <option value="O-" {{ old('tipo_sangre') == 'O-' ? 'selected' : '' }}>O-</option>
                                        </select>
                                        @error('tipo_sangre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="direccion" class="form-label">Dirección</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion') }}">
                                        </div>
                                        @error('direccion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="alergias" class="form-label">Alergias</label>
                                        <textarea class="form-control @error('alergias') is-invalid @enderror" id="alergias" name="alergias" rows="2">{{ old('alergias') }}</textarea>
                                        @error('alergias')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="antecedentes_medicos" class="form-label">Antecedentes Médicos</label>
                                        <textarea class="form-control @error('antecedentes_medicos') is-invalid @enderror" id="antecedentes_medicos" name="antecedentes_medicos" rows="3">{{ old('antecedentes_medicos') }}</textarea>
                                        @error('antecedentes_medicos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group text-end">
                                <a href="{{ route('doctor.pacientes.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Paciente
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Formulario para seleccionar paciente existente -->
                    <div class="tab-pane fade" id="existing-patient" role="tabpanel" aria-labelledby="existing-patient-tab">
                        @if(count($pacientesDisponibles) > 0)
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Seleccione un paciente existente para agregarlo a su lista de pacientes.
                            </div>
                            
                            <form action="{{ route('doctor.pacientes.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="paciente_existente" value="1">
                                
                                <div class="form-group mb-4">
                                    <label for="paciente_id" class="form-label">Seleccionar Paciente <span class="text-danger">*</span></label>
                                    <select class="form-select @error('paciente_id') is-invalid @enderror" id="paciente_id" name="paciente_id" required>
                                        <option value="">Seleccione un paciente...</option>
                                        @foreach($pacientesDisponibles as $paciente)
                                            <option value="{{ $paciente->id }}" {{ old('paciente_id') == $paciente->id ? 'selected' : '' }}>
                                                {{ $paciente->user->nombre }} {{ $paciente->user->apellido }} ({{ $paciente->user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('paciente_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="doctor_principal" name="doctor_principal" {{ old('doctor_principal') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="doctor_principal">
                                        Establecer como doctor principal de este paciente
                                    </label>
                                    <div class="form-text text-muted">
                                        Si marca esta opción, usted será designado como el médico principal del paciente. Si el paciente ya tiene un médico principal, este será reemplazado.
                                    </div>
                                </div>
                                
                                <div class="form-group text-end">
                                    <a href="{{ route('doctor.pacientes.index') }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-user-plus me-1"></i> Agregar Paciente
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info text-center py-4">
                                <i class="fas fa-info-circle fa-3x mb-3"></i>
                                <h5>No hay pacientes disponibles para asignar</h5>
                                <p class="mb-0">Todos los pacientes registrados ya están asignados a su lista. Puede crear un nuevo paciente usando la pestaña "Nuevo Paciente".</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Guardar la pestaña activa en localStorage
        const patientTabs = document.getElementById('patientTabs');
        const tabLinks = patientTabs.querySelectorAll('button[data-bs-toggle="tab"]');
        
        // Restaurar la pestaña activa
        const activeTab = localStorage.getItem('activePatientTab');
        if (activeTab) {
            const tab = new bootstrap.Tab(document.querySelector(`button[data-bs-target="${activeTab}"]`));
            tab.show();
        }
        
        // Guardar cuando cambia de pestaña
        tabLinks.forEach(tabLink => {
            tabLink.addEventListener('shown.bs.tab', function(e) {
                localStorage.setItem('activePatientTab', e.target.dataset.bsTarget);
            });
        });
        
        // Generar nombre de usuario a partir del correo
        const emailInput = document.getElementById('email');
        const usernameInput = document.getElementById('username');
        
        if (emailInput && usernameInput) {
            emailInput.addEventListener('blur', function() {
                if (emailInput.value && !usernameInput.value) {
                    // Usar la parte antes del @ como nombre de usuario sugerido
                    const emailParts = emailInput.value.split('@');
                    if (emailParts.length > 0) {
                        usernameInput.value = emailParts[0];
                    }
                }
            });
        }
        
        // Botón para generar contraseña
        const generatePasswordBtn = document.getElementById('generatePasswordBtn');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        
        if (generatePasswordBtn && passwordInput && passwordConfirmInput) {
            generatePasswordBtn.addEventListener('click', function() {
                // Generar contraseña aleatoria
                const randomPassword = generateRandomPassword(10);
                
                // Establecer la contraseña en ambos campos
                passwordInput.value = randomPassword;
                passwordConfirmInput.value = randomPassword;
                
                // Mostrar temporalmente la contraseña
                passwordInput.type = 'text';
                passwordConfirmInput.type = 'text';
                
                // Actualizar íconos del toggle
                document.querySelectorAll('.toggle-password i').forEach(icon => {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                });
                
                // Crear un elemento para mostrar la contraseña generada
                let alertElement = document.createElement('div');
                alertElement.className = 'alert alert-success mt-2';
                alertElement.innerHTML = '<i class="fas fa-info-circle me-2"></i> Contraseña generada: <strong>' + randomPassword + '</strong>';
                
                // Intentar copiar al portapapeles
                try {
                    navigator.clipboard.writeText(randomPassword).then(function() {
                        alertElement.innerHTML += ' (Copiada al portapapeles)';
                    }).catch(function(err) {
                        console.error('Error al copiar: ', err);
                    });
                } catch (e) {
                    console.error('API de portapapeles no disponible: ', e);
                }
                
                // Eliminar alerta anterior si existe
                const previousAlert = document.querySelector('.alert-password-generated');
                if (previousAlert) {
                    previousAlert.remove();
                }
                
                // Agregar clase para identificar esta alerta específica
                alertElement.classList.add('alert-password-generated');
                
                // Agregar el mensaje después del campo de contraseña
                passwordInput.closest('.row').insertAdjacentElement('afterend', alertElement);
                
                // Ocultar el mensaje después de unos segundos
                setTimeout(function() {
                    passwordInput.type = 'password';
                    passwordConfirmInput.type = 'password';
                    
                    // Restaurar íconos
                    document.querySelectorAll('.toggle-password i').forEach(icon => {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    });
                    
                    // Eliminar el mensaje gradualmente
                    fadeOut(alertElement);
                }, 5000);
            });
        }
        
        // Botones para mostrar/ocultar contraseña
        const togglePasswordButtons = document.querySelectorAll('.toggle-password');
        if (togglePasswordButtons) {
            togglePasswordButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordField = document.querySelector(targetId);
                    
                    if (passwordField) {
                        // Cambiar el tipo de campo
                        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordField.setAttribute('type', type);
                        
                        // Cambiar el ícono
                        const icon = this.querySelector('i');
                        if (type === 'password') {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    }
                });
            });
        }
        
        // Función para generar contraseña aleatoria
        function generateRandomPassword(length) {
            // Caracteres para generar contraseñas seguras
            const lowercase = 'abcdefghijklmnopqrstuvwxyz';
            const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const numbers = '0123456789';
            const specials = '!@#$%^&*()-_=+';
            
            const charset = lowercase + uppercase + numbers + specials;
            let password = '';
            
            // Asegurar al menos un carácter de cada tipo
            password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
            password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
            password += numbers.charAt(Math.floor(Math.random() * numbers.length));
            password += specials.charAt(Math.floor(Math.random() * specials.length));
            
            // Completar el resto de la contraseña
            for (let i = 4; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * charset.length);
                password += charset[randomIndex];
            }
            
            // Mezclar los caracteres para que no siempre siga el mismo patrón
            return password.split('').sort(() => 0.5 - Math.random()).join('');
        }
        
        // Función para desvanecer elementos
        function fadeOut(element) {
            let opacity = 1;
            const timer = setInterval(function() {
                if (opacity <= 0.1) {
                    clearInterval(timer);
                    element.remove();
                }
                element.style.opacity = opacity;
                opacity -= 0.1;
            }, 50);
        }
        
        // Mejorar el selector de pacientes con Select2 si está disponible
        if (typeof $.fn.select2 !== 'undefined') {
            $('#paciente_id').select2({
                placeholder: 'Buscar paciente...',
                allowClear: true,
                width: '100%'
            });
        }
    });
</script>
@endsection