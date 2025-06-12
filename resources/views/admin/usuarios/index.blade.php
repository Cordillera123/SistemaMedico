@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios')

@section('page-title', 'Gestión de Usuarios')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')

{{-- Mensajes de éxito o error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Error:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Estadísticas --}}
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        <p class="mb-0">Total Usuarios</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['activos'] }}</h4>
                        <p class="mb-0">Usuarios Activos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['bloqueados'] }}</h4>
                        <p class="mb-0">Usuarios Bloqueados</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-lock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['con_intentos'] }}</h4>
                        <p class="mb-0">Con Intentos Fallidos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros y Búsqueda --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-filter me-2"></i>
            Filtros y Búsqueda
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="buscar" class="form-label">Buscar Usuario</label>
                <input type="text" class="form-control" id="buscar" name="buscar" 
                       value="{{ request('buscar') }}" placeholder="Nombre, email, username...">
            </div>
            <div class="col-md-2">
                <label for="role_id" class="form-label">Rol</label>
                <select class="form-select" id="role_id" name="role_id">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->nombre) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="">Todos</option>
                    <option value="activos" {{ request('estado') == 'activos' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivos" {{ request('estado') == 'inactivos' ? 'selected' : '' }}>Inactivos</option>
                    <option value="bloqueados" {{ request('estado') == 'bloqueados' ? 'selected' : '' }}>Bloqueados</option>
                    <option value="con_intentos" {{ request('estado') == 'con_intentos' ? 'selected' : '' }}>Con Intentos Fallidos</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i> Buscar
                </button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Limpiar
                </a>
            </div>
            <div class="col-md-3 d-flex align-items-end justify-content-end">
                <form action="{{ route('admin.usuarios.limpiar-bloqueos') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-info me-2" 
                            onclick="return confirm('¿Limpiar todos los bloqueos expirados?')">
                        <i class="fas fa-broom me-1"></i> Limpiar Bloqueos Expirados
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>

{{-- Acciones Masivas --}}
@php
// CORREGIDO: Filtrar la colección después de cargarla, no como query
$usuariosBloqueados = $usuarios->filter(function($usuario) {
    return $usuario->estaBloqueado();
})->count();
@endphp
@if($usuariosBloqueados > 0)
<div class="card mb-4">
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="fas fa-cogs me-2"></i>
            Acciones Masivas ({{ $usuariosBloqueados }} usuarios bloqueados)
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.usuarios.desbloquear-masivo') }}" method="POST" id="accionesMasivas">
            @csrf
            <div class="d-flex align-items-center">
                <button type="button" id="seleccionarTodos" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fas fa-check-square me-1"></i> Seleccionar Todos los Bloqueados
                </button>
                <button type="submit" class="btn btn-sm btn-success me-2" id="btnDesbloquearSeleccionados" disabled>
                    <i class="fas fa-unlock me-1"></i> Desbloquear Seleccionados
                </button>
                <span class="text-muted" id="contadorSeleccionados">0 usuarios seleccionados</span>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Lista de Usuarios --}}
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>
            Lista de Usuarios ({{ $usuarios->total() }})
        </h5>
    </div>
    <div class="card-body">
        @if($usuarios->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Seguridad</th>
                            <th>Último Acceso</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                            <tr class="{{ $usuario->estaBloqueado() ? 'table-danger' : ($usuario->activo ? '' : 'table-warning') }}">
                                <td>
                                    @if($usuario->estaBloqueado())
                                        <input type="checkbox" name="usuarios[]" value="{{ $usuario->id }}" 
                                               class="form-check-input checkbox-usuario">
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-initial rounded-circle bg-{{ $usuario->isAdmin() ? 'danger' : ($usuario->isDoctor() ? 'primary' : 'success') }}">
                                                <i class="fas fa-{{ $usuario->isAdmin() ? 'user-shield' : ($usuario->isDoctor() ? 'user-md' : 'user') }}"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $usuario->nombre_completo }}</div>
                                            <small class="text-muted">{{ $usuario->email }}</small><br>
                                            <small class="text-muted">{{ '@' . $usuario->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $usuario->isAdmin() ? 'danger' : ($usuario->isDoctor() ? 'primary' : 'success') }}">
                                        {{ ucfirst($usuario->role->nombre) }}
                                    </span>
                                </td>
                                <td>
                                    @if($usuario->estaBloqueado())
                                        @php $infoBloqueo = $usuario->getInfoBloqueo() @endphp
                                        <span class="badge bg-danger">
                                            <i class="fas fa-lock me-1"></i> Bloqueado
                                        </span>
                                        <br><small class="text-danger">{{ $infoBloqueo['tiempo_legible'] }} restantes</small>
                                    @elseif(!$usuario->activo)
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-user-slash me-1"></i> Desactivado
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="fas fa-user-check me-1"></i> Activo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($usuario->intentos_fallidos > 0)
                                        <span class="badge bg-warning">
                                            {{ $usuario->intentos_fallidos }} intentos fallidos
                                        </span>
                                    @else
                                        <span class="text-muted">Sin intentos fallidos</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $usuario->updated_at ? $usuario->updated_at->format('d/m/Y H:i') : 'Nunca' }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        {{-- Ver detalles --}}
                                        <a href="{{ route('admin.usuarios.show', $usuario->id) }}" 
                                           class="btn btn-sm btn-outline-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Desbloquear usuario --}}
                                        @if($usuario->estaBloqueado())
                                            <form action="{{ route('admin.usuarios.desbloquear', $usuario->id) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                        title="Desbloquear usuario"
                                                        onclick="return confirm('¿Desbloquear a {{ $usuario->nombre_completo }}?')">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Resetear intentos fallidos --}}
                                        @if($usuario->intentos_fallidos > 0)
                                            <form action="{{ route('admin.usuarios.resetear-intentos', $usuario->id) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" 
                                                        title="Resetear intentos fallidos"
                                                        onclick="return confirm('¿Resetear intentos fallidos de {{ $usuario->nombre_completo }}?')">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Activar/Desactivar usuario --}}
                                         @if($usuario->id !== auth()->id()) {{-- No permitir que el admin se desactive a sí mismo --}}
                                            <form action="{{ route('admin.usuarios.toggle-activo', $usuario->id) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-{{ $usuario->activo ? 'outline-danger' : 'outline-success' }}" 
                                                        title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }} usuario"
                                                        onclick="return confirm('¿{{ $usuario->activo ? 'Desactivar' : 'Activar' }} a {{ $usuario->nombre_completo }}?')">
                                                    @if($usuario->activo)
                                                        <i class="fas fa-user-times"></i> {{-- Usuario activo → mostrar ícono para desactivar --}}
                                                    @else
                                                        <i class="fas fa-user-check"></i> {{-- Usuario inactivo → mostrar ícono para activar --}}
                                                    @endif
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            
{{-- Paginación Mejorada --}}
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="d-flex align-items-center">
                    <small class="text-muted me-3">
                        Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} 
                        de {{ $usuarios->total() }} usuarios
                    </small>
                    
                    {{-- Selector de resultados por página --}}
                    <div class="d-flex align-items-center">
                        <small class="text-muted me-2">Mostrar:</small>
                        <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                
                {{-- Paginación personalizada --}}
                <div class="pagination-wrapper">
                    @if ($usuarios->hasPages())
                        <nav aria-label="Navegación de usuarios">
                            <ul class="pagination pagination-sm mb-0">
                                {{-- Botón Anterior --}}
                                @if ($usuarios->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $usuarios->previousPageUrl() }}" rel="prev">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Números de página --}}
                                @php
                                    $start = max($usuarios->currentPage() - 2, 1);
                                    $end = min($start + 4, $usuarios->lastPage());
                                    $start = max($end - 4, 1);
                                @endphp

                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $usuarios->url(1) }}">1</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endif

                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i == $usuarios->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $i }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $usuarios->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endif
                                @endfor

                                @if($end < $usuarios->lastPage())
                                    @if($end < $usuarios->lastPage() - 1)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $usuarios->url($usuarios->lastPage()) }}">{{ $usuarios->lastPage() }}</a>
                                    </li>
                                @endif

                               {{-- Botón Siguiente --}}
                @if ($usuarios->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $usuarios->nextPageUrl() }}" rel="next">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5>No se encontraron usuarios</h5>
                <p class="text-muted">No hay usuarios que coincidan con los filtros aplicados.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto ocultar alertas después de 5 segundos
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            if (!alert.classList.contains('alert-info')) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        });
    }, 5000);

    // Gestión de checkboxes para acciones masivas
    const selectAll = document.getElementById('selectAll');
    const checkboxesUsuarios = document.querySelectorAll('.checkbox-usuario');
    const btnDesbloquear = document.getElementById('btnDesbloquearSeleccionados');
    const contadorSeleccionados = document.getElementById('contadorSeleccionados');
    const btnSeleccionarTodos = document.getElementById('seleccionarTodos');

    function actualizarContador() {
        const seleccionados = document.querySelectorAll('.checkbox-usuario:checked').length;
        contadorSeleccionados.textContent = seleccionados + ' usuarios seleccionados';
        
        if (btnDesbloquear) {
            btnDesbloquear.disabled = seleccionados === 0;
        }
    }

    // Seleccionar/deseleccionar todos
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxesUsuarios.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            actualizarContador();
        });
    }

    // Botón seleccionar todos los bloqueados
    if (btnSeleccionarTodos) {
        btnSeleccionarTodos.addEventListener('click', function() {
            checkboxesUsuarios.forEach(checkbox => {
                checkbox.checked = true;
            });
            if (selectAll) selectAll.checked = checkboxesUsuarios.length > 0;
            actualizarContador();
        });
    }

    // Actualizar contador cuando cambian checkboxes individuales
    checkboxesUsuarios.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const totalSeleccionados = document.querySelectorAll('.checkbox-usuario:checked').length;
            if (selectAll) {
                selectAll.checked = totalSeleccionados === checkboxesUsuarios.length;
            }
            actualizarContador();
        });
    });

    // Confirmar acción masiva
    const formAccionesMasivas = document.getElementById('accionesMasivas');
    if (formAccionesMasivas) {
        formAccionesMasivas.addEventListener('submit', function(e) {
            const seleccionados = document.querySelectorAll('.checkbox-usuario:checked').length;
            if (seleccionados === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un usuario.');
                return;
            }
            
            if (!confirm(`¿Está seguro de desbloquear ${seleccionados} usuario(s)?`)) {
                e.preventDefault();
            }
        });
    }

    // Inicializar contador
    actualizarContador();
});

// Función para cambiar resultados por página
function changePerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page'); // Reset a página 1
    window.location.href = url.toString();
}
</script>
@endpush

@push('styles')
<style>
.avatar {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.avatar-sm {
    width: 2rem;
    height: 2rem;
}

.avatar-initial {
    color: white;
    font-size: 0.875rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1);
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    margin: 0 0.125rem;
    border: 1px solid #dee2e6;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.15s ease-in-out;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
    transform: translateY(-1px);
}

.pagination .page-item.active .page-link {
    background-color: #20c997;
    border-color: #20c997;
    color: white;
    font-weight: 600;
}

.pagination .page-item.disabled .page-link {
    color: #adb5bd;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

/* Flechas de navegación más pequeñas y elegantes */
.pagination .page-link[rel="prev"],
.pagination .page-link[rel="next"] {
    padding: 0.5rem 0.875rem;
    font-size: 1rem;
    font-weight: 500;
}

/* Estilo para dispositivos móviles */
@media (max-width: 768px) {
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
    }
}
</style>
@endpush