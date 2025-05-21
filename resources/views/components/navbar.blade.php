<nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <i class="fas fa-hospital me-2"></i>
            <span>Sistema Hospitalario</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Elementos de navegación a la izquierda -->
            <ul class="navbar-nav me-auto">
                <!-- Estos elementos pueden cambiar según el rol -->
                @if(Auth::user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                @elseif(Auth::user()->isDoctor())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('doctor.dashboard') }}">Dashboard</a>
                    </li>
                @elseif(Auth::user()->isPaciente())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('paciente.dashboard') }}">Dashboard</a>
                    </li>
                @endif
            </ul>
            
            <!-- Elementos de navegación a la derecha -->
            <ul class="navbar-nav">
                <!-- Botón para abrir/cerrar la barra lateral en móviles -->
                <li class="nav-item d-md-none">
                    <button class="nav-link btn toggle-sidebar" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                </li>
                
                @if(Auth::user()->isPaciente())
                    <!-- Notificaciones (solo para pacientes) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            @php
                                $notificacionesNoLeidas = Auth::user()->notificaciones()->where('leida', false)->count();
                            @endphp
                            @if($notificacionesNoLeidas > 0)
                                <span class="badge bg-danger">{{ $notificacionesNoLeidas }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                            <li>
                                <h6 class="dropdown-header">Notificaciones</h6>
                            </li>
                            @php
                                $notificaciones = Auth::user()->notificaciones()->latest()->take(5)->get();
                            @endphp
                            
                            @if(count($notificaciones) > 0)
                                @foreach($notificaciones as $notificacion)
                                    <li>
                                        <a class="dropdown-item{{ $notificacion->leida ? '' : ' unread' }}" href="{{ route('paciente.notificaciones') }}">
                                            <div class="notification-item">
                                                <div class="notification-icon">
                                                    @if($notificacion->tipo == 'resultado_nuevo')
                                                        <i class="fas fa-file-medical text-primary"></i>
                                                    @else
                                                        <i class="fas fa-bell text-secondary"></i>
                                                    @endif
                                                </div>
                                                <div class="notification-content">
                                                    <p class="notification-text">{{ $notificacion->titulo }}</p>
                                                    <p class="notification-time">{{ $notificacion->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-center" href="{{ route('paciente.notificaciones') }}">
                                        Ver todas las notificaciones
                                    </a>
                                </li>
                            @else
                                <li>
                                    <span class="dropdown-item">No hay notificaciones</span>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                
                <!-- Menú de usuario -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle user-dropdown" href="#" id="userDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-md-inline me-1">{{ Auth::user()->nombre }}</span>
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <div class="dropdown-header d-flex align-items-center">
                                <div class="user-icon me-3">
                                    <i class="fas fa-user-circle fa-2x"></i>
                                </div>
                                <div class="user-info">
                                    <h6 class="mb-0">{{ Auth::user()->nombre_completo }}</h6>
                                    <small class="text-muted">
                                        @if(Auth::user()->isAdmin())
                                            Administrador
                                        @elseif(Auth::user()->isDoctor())
                                            Doctor
                                        @elseif(Auth::user()->isPaciente())
                                            Paciente
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        
                        <!-- Opción de perfil según el rol -->
                        @if(Auth::user()->isAdmin())
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.configuracion') }}">
                                    <i class="fas fa-cogs me-2"></i> Configuración
                                </a>
                            </li>
                        @elseif(Auth::user()->isDoctor())
                            <li>
                                <a class="dropdown-item" href="{{ route('doctor.perfil') }}">
                                    <i class="fas fa-user me-2"></i> Mi Perfil
                                </a>
                            </li>
                        @elseif(Auth::user()->isPaciente())
                            <li>
                                <a class="dropdown-item" href="{{ route('paciente.perfil') }}">
                                    <i class="fas fa-user me-2"></i> Mi Perfil
                                </a>
                            </li>
                        @endif
                        
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>