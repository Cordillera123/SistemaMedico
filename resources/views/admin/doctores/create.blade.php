@extends('layouts.dashboard')

@section('title', 'Crear Nuevo Doctor')

@section('page-title', 'Crear Nuevo Doctor')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.doctores.index') }}">Doctores</a></li>
        <li class="breadcrumb-item active" aria-current="page">Crear</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Información del Doctor</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.doctores.store') }}" method="POST" class="dashboard-form">
            @csrf
            
            <!-- Datos de Usuario -->
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
                    <div class="col-md-12 mb-3">
                        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                            <button type="button" class="btn btn-outline-primary" id="generate-username" title="Generar nombre de usuario automáticamente">
                                <i class="fas fa-magic"></i>
                            </button>
                        </div>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                       
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1" title="Mostrar/Ocultar contraseña" data-target="#password">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="generate-password" title="Generar contraseña segura">
                                <i class="fas fa-key"></i>
                            </button>
                            <button type="button" class="btn btn-outline-success" id="copy-password" title="Copiar contraseña" style="display: none;">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                       
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1" title="Mostrar/Ocultar contraseña" data-target="#password_confirmation">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <!-- Mensaje de estado de la contraseña -->
                        <div id="password-status" class="alert alert-info" style="display: none; margin-top: 32px;">
                            <i class="fas fa-info-circle"></i> <span id="password-message"></span>
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
                            <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono') }}">
                        </div>
                        @error('telefono')
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
            </div>
            
            <!-- Datos Profesionales -->
            <div class="form-section">
                <h6 class="form-section-title">Información Profesional</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="especialidad" class="form-label">Especialidad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('especialidad') is-invalid @enderror" id="especialidad" name="especialidad" value="{{ old('especialidad') }}" required>
                        @error('especialidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="licencia_medica" class="form-label">Licencia Médica <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" class="form-control @error('licencia_medica') is-invalid @enderror" id="licencia_medica" name="licencia_medica" value="{{ old('licencia_medica') }}" required placeholder="Ej: MSP-1234567">
                        </div>
                        @error('licencia_medica')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Número de registro del Ministerio de Salud Pública (MSP) o entidad competente.</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="biografia" class="form-label">Biografía</label>
                        <textarea class="form-control @error('biografia') is-invalid @enderror" id="biografia" name="biografia" rows="4">{{ old('biografia') }}</textarea>
                        @error('biografia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="horario_consulta" class="form-label">Horario de Consulta</label>
                        <input type="text" class="form-control @error('horario_consulta') is-invalid @enderror" id="horario_consulta" name="horario_consulta" value="{{ old('horario_consulta') }}" placeholder="Ej: Lunes a Viernes 8:00 - 16:00">
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
                    <i class="fas fa-save me-1"></i> Guardar Doctor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nombreInput = document.getElementById('nombre');
    const apellidoInput = document.getElementById('apellido');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const generateUsernameBtn = document.getElementById('generate-username');
    const generatePasswordBtn = document.getElementById('generate-password');
    const copyPasswordBtn = document.getElementById('copy-password');
    const passwordStatus = document.getElementById('password-status');
    const passwordMessage = document.getElementById('password-message');

    // Función para limpiar string y remover acentos
    function cleanString(str) {
        return str.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]/g, '');
    }

    // Función para generar nombre de usuario único
    function generateUsername() {
        const nombre = nombreInput.value.trim();
        const apellido = apellidoInput.value.trim();
        
        if (nombre && apellido) {
            const nombreClean = cleanString(nombre);
            const apellidoClean = cleanString(apellido);
            
            // Generar username: primera letra del nombre + apellido + número aleatorio
            const randomNum = Math.floor(Math.random() * 1000);
            const username = nombreClean.charAt(0) + apellidoClean + randomNum;
            
            usernameInput.value = username;
            
            // Mostrar mensaje temporal
            showMessage('Nombre de usuario generado automáticamente', 'success');
        } else {
            showMessage('Complete nombre y apellido para generar el usuario', 'warning');
        }
    }

    // Función para generar contraseña segura
    function generatePassword() {
        const length = 12;
        const lowercase = "abcdefghijklmnopqrstuvwxyz";
        const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const numbers = "0123456789";
        const symbols = "!@#$%^&*()_+-=[]{}|;:,.<>?";
        
        const allChars = lowercase + uppercase + numbers + symbols;
        let password = "";
        
        // Asegurar al menos un carácter de cada tipo
        password += lowercase[Math.floor(Math.random() * lowercase.length)];
        password += uppercase[Math.floor(Math.random() * uppercase.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += symbols[Math.floor(Math.random() * symbols.length)];
        
        // Completar con caracteres aleatorios
        for (let i = password.length; i < length; i++) {
            password += allChars[Math.floor(Math.random() * allChars.length)];
        }
        
        // Mezclar la contraseña
        password = password.split('').sort(() => Math.random() - 0.5).join('');
        
        passwordInput.value = password;
        passwordConfirmationInput.value = password;
        
        // Mostrar botón de copiar
        copyPasswordBtn.style.display = 'block';
        
        showMessage('Contraseña generada y copiada automáticamente', 'success');
        
        // Copiar automáticamente al portapapeles
        copyToClipboard(password);
        
        return password;
    }

    // Función para copiar al portapapeles
    async function copyToClipboard(text) {
        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
            } else {
                // Fallback para navegadores más antiguos
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                document.execCommand('copy');
                textArea.remove();
            }
            return true;
        } catch (error) {
            console.error('Error al copiar al portapapeles:', error);
            return false;
        }
    }

    // Función para mostrar mensajes
    function showMessage(message, type = 'info') {
        passwordMessage.textContent = message;
        passwordStatus.className = `alert alert-${type}`;
        passwordStatus.style.display = 'block';
        
        // Ocultar mensaje después de 3 segundos
        setTimeout(() => {
            passwordStatus.style.display = 'none';
        }, 3000);
    }

    // Event listeners para autogeneración cuando se escriben nombre y apellido
    nombreInput.addEventListener('input', function() {
        if (nombreInput.value && apellidoInput.value) {
            setTimeout(generateUsername, 500); // Delay para evitar generar mientras escribe
        }
    });

    apellidoInput.addEventListener('input', function() {
        if (nombreInput.value && apellidoInput.value) {
            setTimeout(generateUsername, 500);
        }
    });

    // Event listeners para botones manuales
    generateUsernameBtn.addEventListener('click', generateUsername);
    generatePasswordBtn.addEventListener('click', generatePassword);

    // Event listener para copiar contraseña
    copyPasswordBtn.addEventListener('click', async function() {
        const password = passwordInput.value;
        if (password) {
            const success = await copyToClipboard(password);
            if (success) {
                showMessage('Contraseña copiada al portapapeles', 'success');
                
                // Cambiar icono temporalmente
                const icon = copyPasswordBtn.querySelector('i');
                icon.className = 'fas fa-check';
                setTimeout(() => {
                    icon.className = 'fas fa-copy';
                }, 1000);
            } else {
                showMessage('Error al copiar la contraseña', 'danger');
            }
        }
    });

    // Funcionalidad para mostrar/ocultar contraseñas (basado en la lógica de pacientes)
    const togglePasswordButtons = document.querySelectorAll('.password-toggle');
    if (togglePasswordButtons) {
        togglePasswordButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                let passwordField;
                
                if (targetId) {
                    // Si tiene data-target, usar ese selector
                    passwordField = document.querySelector(targetId);
                } else {
                    // Si no tiene data-target, buscar en el mismo input-group
                    passwordField = this.parentElement.querySelector('input[type="password"], input[type="text"]');
                }
                
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

    // Generar contraseña inicial automáticamente
    generatePassword();

    // Generar username inicial si hay valores previos
    if (nombreInput.value && apellidoInput.value) {
        generateUsername();
    }

    // Sincronizar confirmación de contraseña cuando se cambia la contraseña principal
    passwordInput.addEventListener('input', function() {
        if (passwordInput.value === passwordConfirmationInput.value) {
            passwordConfirmationInput.value = passwordInput.value;
        }
    });
});
</script>

<style>
.form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e9ecef;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #007bff;
}

.btn-outline-primary:hover, .btn-outline-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert {
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

#password-status {
    transition: all 0.3s ease;
}

.input-group .btn {
    border-left: 0;
}

.input-group .btn:first-of-type {
    border-left: 1px solid #ced4da;
}
</style>
@endsection