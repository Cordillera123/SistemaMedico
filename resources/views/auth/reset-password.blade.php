
@extends('layouts.auth')

@section('title', 'Restablecer Contraseña - Sistema Hospitalario')

@section('auth-title', 'Restablecer Contraseña')

@section('auth-content')
<form class="auth-form" method="POST" action="{{ route('password.update') }}">
    @csrf
    
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">
    
    <div class="form-group">
        <label for="email-display" class="form-label">Correo Electrónico</label>
        <input type="text" class="form-control" id="email-display" value="{{ $email }}" readonly disabled>
    </div>
    
    <div class="form-group">
        <label for="password" class="form-label">Nueva Contraseña</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Ingrese su nueva contraseña" required autofocus>
            <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">La contraseña debe tener al menos 8 caracteres.</small>
    </div>
    
    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirme su nueva contraseña" required>
            <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i> Restablecer Contraseña
        </button>
    </div>
</form>

<div class="text-center mt-3">
    <a href="{{ route('login') }}" class="auth-forgot-link">
        <i class="fas fa-arrow-left me-1"></i> Volver a Iniciar Sesión
    </a>
</div>
@endsection