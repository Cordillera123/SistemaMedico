@extends('layouts.base')

@section('title')
    @yield('title', 'Dashboard - Sistema Hospitalario')
@endsection

@section('styles')
    @vite(['resources/css/app.css', 'resources/css/dashboard.css'])
    @yield('styles')
@endsection

@section('content')
<div class="dashboard-container">
    <!-- Navbar superior -->
    @include('components.navbar')
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar lateral -->
            @include('components.sidebar')
            
            <!-- Contenido principal -->
            <main class="content-wrapper">
                <!-- Mensajes de alerta -->
                @include('components.alerts')
                
                <!-- Cabecera de la página -->
                <div class="page-header">
                    <h1 class="page-title">@yield('page-title')</h1>
                    <div class="page-breadcrumb">
                        @yield('breadcrumb')
                    </div>
                </div>
                
                <!-- Contenido específico de la página -->
                @yield('dashboard-content')
            </main>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @yield('scripts')
@endsection