@extends('layouts.auth')

@section('title', 'Iniciar Sesión - Sistema Hospitalario')

@section('auth-title', 'Iniciar Sesión')

@section('auth-content')
<form class="auth-form" method="POST" action="{{ route('login') }}" novalidate>
    @csrf
    
    <div class="form-group">
        <label for="username" class="form-label">Usuario o Email</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-user"></i>
            </span>
            <input type="text" 
                   class="form-control @error('username') is-invalid @enderror" 
                   id="username" 
                   name="username" 
                   value="{{ old('username') }}" 
                   placeholder="Ingrese su usuario o email"
                   required 
                   autofocus
                   autocomplete="username">
        </div>
        @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <label for="password" class="form-label">Contraseña</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   id="password" 
                   name="password" 
                   placeholder="Ingrese su contraseña"
                   required
                   autocomplete="current-password">
            <button type="button" class="password-toggle" aria-label="Mostrar/ocultar contraseña">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-check">
        <input class="form-check-input" 
               type="checkbox" 
               name="remember" 
               id="remember" 
               {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">
            Recordar sesión
        </label>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <span class="btn-text">
                <i class="fas fa-sign-in-alt"></i>
                Iniciar Sesión
            </span>
            <div class="btn-loader d-none">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                Iniciando sesión...
            </div>
        </button>
    </div>
</form>

<div class="text-center">
    <a href="{{ route('password.request') }}" class="auth-forgot-link">
        <i class="fas fa-key"></i>¿Olvidaste tu contraseña?
    </a>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // Variables
    const passwordToggle = document.querySelector('.password-toggle');
    const passwordInput = document.querySelector('#password');
    const form = document.querySelector('.auth-form');
    const submitBtn = document.querySelector('.btn-primary');
    const inputs = document.querySelectorAll('.form-control');
    
    // Toggle de visibilidad de contraseña
    if (passwordToggle && passwordInput) {
        passwordToggle.addEventListener('click', function() {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            const newType = isPassword ? 'text' : 'password';
            
            passwordInput.setAttribute('type', newType);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
            
            // Cambiar aria-label para accesibilidad
            this.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    }
    
    // Estado de carga en el formulario
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Validación básica antes de enviar
            let isValid = true;
            inputs.forEach(input => {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
            
            // Mostrar estado de carga
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoader = submitBtn.querySelector('.btn-loader');
            
            if (btnText && btnLoader) {
                btnText.classList.add('d-none');
                btnLoader.classList.remove('d-none');
                submitBtn.disabled = true;
                submitBtn.style.cursor = 'not-allowed';
            }
        });
    }
    
    // Validación en tiempo real
    inputs.forEach(input => {
        // Validación al perder el foco
        input.addEventListener('blur', function() {
            validateInput(this);
        });
        
        // Limpiar errores al escribir
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
                
                // Validar específicamente el email si es el campo username
                if (this.name === 'username') {
                    const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
                    const isValidUsername = /^[a-zA-Z0-9_]{3,}$/.test(this.value);
                    
                    if (isEmail || isValidUsername) {
                        this.classList.add('is-valid');
                    }
                } else if (this.value.trim().length >= 6) {
                    this.classList.add('is-valid');
                }
            }
        });
        
        // Validación específica para Enter
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                validateInput(this);
                
                // Si es el último campo, enviar formulario
                const inputs = Array.from(form.querySelectorAll('.form-control'));
                const currentIndex = inputs.indexOf(this);
                
                if (currentIndex === inputs.length - 1) {
                    form.dispatchEvent(new Event('submit'));
                } else {
                    // Enfocar siguiente campo
                    inputs[currentIndex + 1]?.focus();
                }
            }
        });
    });
    
    // Función de validación
    function validateInput(input) {
        const value = input.value.trim();
        
        if (!value) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            return false;
        }
        
        // Validaciones específicas
        if (input.name === 'username') {
            const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            const isValidUsername = /^[a-zA-Z0-9_]{3,}$/.test(value);
            
            if (!isEmail && !isValidUsername) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                return false;
            }
        }
        
        if (input.name === 'password' && value.length < 6) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            return false;
        }
        
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        return true;
    }
    
    // Limpiar formulario si hay errores del servidor
    const hasServerErrors = document.querySelector('.invalid-feedback');
    if (hasServerErrors) {
        // Remover estados de carga si hay errores
        const btnText = submitBtn?.querySelector('.btn-text');
        const btnLoader = submitBtn?.querySelector('.btn-loader');
        
        if (btnText && btnLoader) {
            btnText.classList.remove('d-none');
            btnLoader.classList.add('d-none');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.cursor = 'pointer';
            }
        }
    }
    
    // Mejorar accesibilidad con teclado
    document.addEventListener('keydown', function(e) {
        // Alt + L para enfocar login
        if (e.altKey && e.key === 'l') {
            e.preventDefault();
            document.querySelector('#username')?.focus();
        }
        
        // Escape para limpiar formulario
        if (e.key === 'Escape') {
            inputs.forEach(input => {
                input.classList.remove('is-invalid', 'is-valid');
            });
        }
    });
    
    // Auto-enfocar primer campo al cargar
    setTimeout(() => {
        document.querySelector('#username')?.focus();
    }, 100);
    
    // Prevenir envío múltiple del formulario
    let isSubmitting = false;
    if (form) {
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
        });
    }
});
</script>
@endpush
@endsection