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
                                <div class="form-check mb-3 mt-3">
    <input class="form-check-input" type="checkbox" id="doctor_principal" name="doctor_principal" value="1" {{ old('doctor_principal', true) ? 'checked' : '' }}>
    <label class="form-check-label" for="doctor_principal">
        Establecer como médico principal
    </label>
    <small class="form-text text-muted d-block">
        Si se marca esta opción, serás establecido como el médico principal del paciente.
        Si hay otros médicos asignados, esto podría cambiar su médico principal actual.
    </small>
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
                   <!-- Formulario mejorado para buscar y agregar paciente existente -->
                    <div class="tab-pane fade" id="existing-patient" role="tabpanel" aria-labelledby="existing-patient-tab">
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            Busque un paciente por su número de cédula para agregarlo a su lista de pacientes.
                        </div>
                        
                        <!-- Buscador por cédula -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Buscar Paciente por Cédula</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            <input type="text" class="form-control" placeholder="Ingrese número de cédula..." id="searchCedulaExistente">
                                            <button class="btn btn-primary" type="button" id="btnSearchPacienteExistente">
                                                <i class="fas fa-search me-1"></i> Buscar
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Ingrese el número de cédula del paciente que desea agregar.</small>
                                    </div>
                                </div>
                                
                                <!-- Loader -->
                                <div id="searchLoaderExistente" class="text-center py-3" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Buscando...</span>
                                    </div>
                                    <p class="mt-2">Buscando paciente...</p>
                                </div>
                                
                                <!-- Mensaje cuando no se encuentra -->
                                <div id="notFoundMessageExistente" class="alert alert-warning" style="display: none;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span id="notFoundTextExistente">No se encontró ningún paciente con la cédula proporcionada.</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información del paciente encontrado -->
                        <div id="pacienteEncontradoContainer" style="display: none;">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="card-title mb-0"><i class="fas fa-user-check me-2"></i>Paciente Encontrado</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-lg me-3">
                                                    <div class="avatar-initial rounded-circle bg-primary">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h5 class="mb-1" id="pacienteEncontradoNombre"></h5>
                                                    <p class="mb-1">
                                                        <span class="badge bg-light text-dark" id="pacienteEncontradoCedula"></span>
                                                    </p>
                                                    <p class="mb-0 text-muted" id="pacienteEncontradoEmail"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-md-end">
                                                <p class="mb-1"><strong>Género:</strong> <span id="pacienteEncontradoGenero"></span></p>
                                                <p class="mb-0"><strong>Edad:</strong> <span id="pacienteEncontradoEdad"></span> años</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Formulario para agregar paciente -->
                            <form id="formAgregarPacienteExistente" action="{{ route('doctor.pacientes.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="paciente_existente" value="1">
                                <input type="hidden" name="paciente_id" id="pacienteEncontradoId">
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Configuración de Asignación</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="doctor_principal_existente" name="doctor_principal" value="1">
                                            <label class="form-check-label" for="doctor_principal_existente">
                                                <strong>Establecer como médico principal</strong>
                                            </label>
                                            <small class="form-text text-muted d-block">
                                                Si se marca esta opción, serás establecido como el médico principal del paciente.
                                                Si el paciente ya tiene un médico principal, este será reemplazado.
                                            </small>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Información:</strong> Este paciente será agregado a su lista de pacientes y podrá gestionar sus resultados médicos.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group text-end mt-4">
                                    <button type="button" class="btn btn-secondary me-2" id="btnCancelarAgregar">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-user-plus me-1"></i> Agregar a Mis Pacientes
                                    </button>
                                </div>
                            </form>
                        </div>
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
        // ===== FUNCIONALIDAD EXISTENTE =====
        
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
        
        // ===== NUEVA FUNCIONALIDAD: BÚSQUEDA DE PACIENTES EXISTENTES =====
        
        // Variables para la búsqueda de pacientes existentes
        const searchCedulaExistente = document.getElementById('searchCedulaExistente');
        const btnSearchPacienteExistente = document.getElementById('btnSearchPacienteExistente');
        const searchLoaderExistente = document.getElementById('searchLoaderExistente');
        const notFoundMessageExistente = document.getElementById('notFoundMessageExistente');
        const notFoundTextExistente = document.getElementById('notFoundTextExistente');
        const pacienteEncontradoContainer = document.getElementById('pacienteEncontradoContainer');
        const btnCancelarAgregar = document.getElementById('btnCancelarAgregar');
        
        // Elementos para mostrar información del paciente
        const pacienteEncontradoNombre = document.getElementById('pacienteEncontradoNombre');
        const pacienteEncontradoCedula = document.getElementById('pacienteEncontradoCedula');
        const pacienteEncontradoEmail = document.getElementById('pacienteEncontradoEmail');
        const pacienteEncontradoGenero = document.getElementById('pacienteEncontradoGenero');
        const pacienteEncontradoEdad = document.getElementById('pacienteEncontradoEdad');
        const pacienteEncontradoId = document.getElementById('pacienteEncontradoId');
        
        // Función para buscar paciente existente por cédula
        function buscarPacienteExistente() {
            const cedula = searchCedulaExistente.value.trim();
            
            if (!cedula) {
                alert('Por favor ingrese un número de cédula para buscar.');
                return;
            }
            
            // Mostrar loader y ocultar otros elementos
            searchLoaderExistente.style.display = 'block';
            notFoundMessageExistente.style.display = 'none';
            pacienteEncontradoContainer.style.display = 'none';
            
            // Realizar la petición AJAX para buscar pacientes disponibles
            fetch(`/doctor/pacientes/buscar-disponible?cedula=${cedula}`)
                .then(response => response.json())
                .then(data => {
                    // Ocultar loader
                    searchLoaderExistente.style.display = 'none';
                    
                    if (data.success) {
                        // Mostrar información del paciente encontrado
                        pacienteEncontradoNombre.textContent = data.paciente.nombre_completo;
                        pacienteEncontradoCedula.textContent = 'Cédula: ' + data.paciente.cedula;
                        pacienteEncontradoEmail.textContent = data.paciente.email;
                        pacienteEncontradoGenero.textContent = data.paciente.genero;
                        pacienteEncontradoEdad.textContent = data.paciente.edad;
                        pacienteEncontradoId.value = data.paciente.id;
                        
                        pacienteEncontradoContainer.style.display = 'block';
                    } else {
                        // No se encontró el paciente o ya está asignado
                        notFoundTextExistente.textContent = data.message;
                        notFoundMessageExistente.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchLoaderExistente.style.display = 'none';
                    notFoundTextExistente.textContent = 'Ocurrió un error al buscar. Por favor intente nuevamente.';
                    notFoundMessageExistente.style.display = 'block';
                });
        }
        
        // Event listeners para búsqueda de pacientes existentes
        if (btnSearchPacienteExistente) {
            btnSearchPacienteExistente.addEventListener('click', buscarPacienteExistente);
        }
        
        if (searchCedulaExistente) {
            searchCedulaExistente.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    buscarPacienteExistente();
                }
            });
        }
        
        if (btnCancelarAgregar) {
            btnCancelarAgregar.addEventListener('click', function() {
                pacienteEncontradoContainer.style.display = 'none';
                searchCedulaExistente.value = '';
                notFoundMessageExistente.style.display = 'none';
            });
        }
        
        // ===== FUNCIONES AUXILIARES =====
        
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
        
        // ===== MEJORAS ADICIONALES =====
        
        // Mejorar el selector de pacientes con Select2 si está disponible
        if (typeof $.fn.select2 !== 'undefined') {
            $('#paciente_id').select2({
                placeholder: 'Buscar paciente...',
                allowClear: true,
                width: '100%'
            });
        }
        
        // Limpiar campos de búsqueda al cambiar de pestaña
        tabLinks.forEach(tabLink => {
            tabLink.addEventListener('shown.bs.tab', function(e) {
                // Si se cambia a la pestaña de paciente existente, limpiar formulario
                if (e.target.dataset.bsTarget === '#existing-patient') {
                    if (searchCedulaExistente) {
                        searchCedulaExistente.value = '';
                    }
                    if (pacienteEncontradoContainer) {
                        pacienteEncontradoContainer.style.display = 'none';
                    }
                    if (notFoundMessageExistente) {
                        notFoundMessageExistente.style.display = 'none';
                    }
                }
            });
        });
        
        // Validación adicional para el formulario de agregar paciente existente
        const formAgregarPacienteExistente = document.getElementById('formAgregarPacienteExistente');
        if (formAgregarPacienteExistente) {
            formAgregarPacienteExistente.addEventListener('submit', function(e) {
                const pacienteId = document.getElementById('pacienteEncontradoId').value;
                
                if (!pacienteId) {
                    e.preventDefault();
                    alert('Error: No se ha seleccionado ningún paciente. Por favor, busque y seleccione un paciente primero.');
                    return false;
                }
                
                // Mostrar confirmación antes de enviar
                if (!confirm('¿Está seguro que desea agregar este paciente a su lista?')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
        
        // Auto-focus en el campo de búsqueda cuando se activa la pestaña
        const existingPatientTab = document.getElementById('existing-patient-tab');
        if (existingPatientTab) {
            existingPatientTab.addEventListener('shown.bs.tab', function() {
                if (searchCedulaExistente) {
                    setTimeout(() => {
                        searchCedulaExistente.focus();
                    }, 100);
                }
            });
        }
    });
</script>
@endsection