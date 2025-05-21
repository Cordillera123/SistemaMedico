@extends('layouts.auth')

@section('title', 'Recuperar Contraseña - Sistema Hospitalario')

@section('auth-title', 'Recuperar Contraseña')

@section('auth-content')
<div class="text-center mb-4">
    <p class="text-muted">
        Ingresa tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
    </p>
</div>

<form class="auth-form" method="POST" action="{{ route('password.email') }}">
    @csrf
    
    <div class="form-group">
        <label for="email" class="form-label">Correo Electrónico</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Ingrese su correo electrónico" required autofocus>
        </div>
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-2"></i> Enviar Enlace de Recuperación
        </button>
    </div>
</form>

<div class="text-center mt-3">
    <a href="{{ route('login') }}" class="auth-forgot-link">
        <i class="fas fa-arrow-left me-1"></i> Volver a Iniciar Sesión
    </a>
</div>
@endsection