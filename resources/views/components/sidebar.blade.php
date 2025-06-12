<div class="sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            @if(Auth::user()->isAdmin())
                <!-- Menú para Administradores -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}">
                        <i class="fas fa-users-cog"></i>
                        <span>Gestión de Usuarios</span>
                         @php
                            // CORREGIDO: Usar scopes en lugar de métodos
                            $usuariosBloqueados = \App\Models\User::bloqueados()->count();
                            $usuariosConIntentos = \App\Models\User::conIntentosFallidos()->count();
                        @endphp
                        @if($usuariosBloqueados > 0)
                            <span class="badge bg-danger float-end" title="{{ $usuariosBloqueados }} usuarios bloqueados">
                                {{ $usuariosBloqueados }}
                            </span>
                        @elseif($usuariosConIntentos > 0)
                            <span class="badge bg-warning float-end" title="{{ $usuariosConIntentos }} usuarios con intentos fallidos">
                                {{ $usuariosConIntentos }}
                            </span>
                        @endif
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.doctores.*') ? 'active' : '' }}" href="{{ route('admin.doctores.index') }}">
                        <i class="fas fa-user-md"></i>
                        <span>Gestión de Doctores</span>
                    </a>
                </li>
                
                <li class="nav-item">
                   <a class="nav-link {{ request()->routeIs('admin.logs.index') ? 'active' : '' }}" href="{{ route('admin.logs.index') }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Logs del Sistema</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.configuracion') ? 'active' : '' }}" href="{{ route('admin.configuracion') }}">
                        <i class="fas fa-cogs"></i>
                        <span>Configuración</span>
                    </a>
                </li>
                
            @elseif(Auth::user()->isDoctor())
                <!-- Menú para Doctores -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('doctor.pacientes.*') ? 'active' : '' }}" href="{{ route('doctor.pacientes.index') }}">
                        <i class="fas fa-users"></i>
                        <span>Mis Pacientes</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('doctor.resultados.*') && !request()->routeIs('doctor.resultados.buscar-cedula') ? 'active' : '' }}" href="{{ route('doctor.resultados.index') }}">
                        <i class="fas fa-file-medical"></i>
                        <span>Resultados Médicos</span>
                    </a>
                </li>
                
                <!-- Nuevo enlace para búsqueda por cédula -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('doctor.resultados.buscar-cedula') ? 'active' : '' }}" href="{{ route('doctor.resultados.buscar-cedula') }}">
                        <i class="fas fa-id-card"></i>
                        <span>Buscar por Cédula</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('doctor.perfil') ? 'active' : '' }}" href="{{ route('doctor.perfil') }}">
                        <i class="fas fa-user-circle"></i>
                        <span>Mi Perfil</span>
                    </a>
                </li>
                
            @elseif(Auth::user()->isPaciente())
                <!-- Menú para Pacientes -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('paciente.dashboard') ? 'active' : '' }}" href="{{ route('paciente.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('paciente.resultados.*') ? 'active' : '' }}" href="{{ route('paciente.resultados.index') }}">
                        <i class="fas fa-file-medical"></i>
                        <span>Mis Resultados</span>
                        @php
                            $resultadosNoVistos = Auth::user()->paciente->resultadosNoVistos()->count();
                        @endphp
                        @if($resultadosNoVistos > 0)
                            <span class="badge bg-danger float-end">{{ $resultadosNoVistos }}</span>
                        @endif
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('paciente.mi-medico') ? 'active' : '' }}" href="{{ route('paciente.mi-medico') }}">
                        <i class="fas fa-user-md"></i>
                        <span>Mi Médico</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('paciente.notificaciones') ? 'active' : '' }}" href="{{ route('paciente.notificaciones') }}">
                        <i class="fas fa-bell"></i>
                        <span>Notificaciones</span>
                        @php
                            $notificacionesNoLeidas = Auth::user()->notificaciones()->where('leida', false)->count();
                        @endphp
                        @if($notificacionesNoLeidas > 0)
                            <span class="badge bg-danger float-end">{{ $notificacionesNoLeidas }}</span>
                        @endif
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('paciente.perfil') ? 'active' : '' }}" href="{{ route('paciente.perfil') }}">
                        <i class="fas fa-user-circle"></i>
                        <span>Mi Perfil</span>
                    </a>
                </li>
            @endif
        </ul>
        
        <!-- Información de soporte y versión -->
        <div class="sidebar-footer">
            <div class="sidebar-divider"></div>
            <p class="text-muted text-center small">
                &copy; {{ date('Y') }} Sistema Hospitalario<br>
                <small>Versión 1.0</small>
            </p>
        </div>
    </div>
</div>