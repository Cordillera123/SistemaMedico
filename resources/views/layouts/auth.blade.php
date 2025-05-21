
@extends('layouts.base')

@section('styles')
    @vite(['resources/css/auth.css'])
@endsection

@section('content')
<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="logo text-center mb-4">
                            <h1 class="hospital-name">Sistema Hospitalario</h1>
                        </div>
                        <h2 class="auth-title text-center">@yield('auth-title')</h2>
                    </div>
                    
                    <div class="auth-body">
                        <!-- Mensajes de alerta -->
                        @include('components.alerts')
                        
                        <!-- Contenido específico de la página de autenticación -->
                        @yield('auth-content')
                    </div>
                    
                    <div class="auth-footer text-center mt-4">
                        <p class="copyright">&copy; {{ date('Y') }} Sistema Hospitalario. Todos los derechos reservados.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection