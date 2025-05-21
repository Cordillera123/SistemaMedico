@extends('layouts.auth')

@section('title', 'Iniciar Sesión - Sistema Hospitalario')

@section('auth-title', 'Iniciar Sesión')

@section('auth-content')
<form class="auth-form" method="POST" action="{{ route('login') }}">
    @csrf
    
    <div class="form-group">
        <label for="username" class="form-label">Usuario o Email</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" placeholder="Ingrese su usuario o email" required autofocus>
        </div>
        @error('username')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <label for="password" class="form-label">Contraseña</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Ingrese su contraseña" required>
            <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                Recordar sesión
            </label>
        </div>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
        </button>
    </div>
</form>

<div class="text-center mt-3">
    <a href="{{ route('password.request') }}" class="auth-forgot-link">
        ¿Olvidaste tu contraseña?
    </a>
</div>
@endsection