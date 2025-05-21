@extends('layouts.dashboard')

@section('title', 'Mis Notificaciones')

@section('page-title', 'Mis Notificaciones')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('paciente.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Notificaciones</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Historial de Notificaciones</h5>
        <a href="{{ route('paciente.dashboard') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
        </a>
    </div>
    <div class="card-body">
        @if($notificaciones->count() > 0)
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control table-search" placeholder="Buscar notificaciones..." data-table="notifications-table" id="searchInput">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="tipoFilter">
                        <option value="">Todos los tipos</option>
                        @php
                            $tipos = $notificaciones->pluck('tipo')->unique();
                            $tiposNombres = [
                                'resultado_nuevo' => 'Resultado nuevo',
                                'cita' => 'Cita médica',
                                'sistema' => 'Sistema'
                            ];
                        @endphp
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo }}">{{ $tiposNombres[$tipo] ?? ucfirst($tipo) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="leida">Leídas</option>
                        <option value="no-leida">No leídas</option>
                    </select>
                </div>
            </div>

            <!-- Lista de notificaciones -->
            <div class="notification-list" id="notifications-table">
                @foreach($notificaciones as $notificacion)
                    <div class="notification-item mb-3 p-3 rounded {{ $notificacion->leida ? 'bg-light' : 'bg-light-primary' }}" 
                         data-tipo="{{ $notificacion->tipo }}" 
                         data-status="{{ $notificacion->leida ? 'leida' : 'no-leida' }}">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-md">
                                    <div class="avatar-initial rounded-circle 
                                        @if($notificacion->tipo == 'resultado_nuevo') 
                                            bg-primary
                                        @elseif($notificacion->tipo == 'cita') 
                                            bg-success
                                        @else 
                                            bg-info
                                        @endif">
                                        @if($notificacion->tipo == 'resultado_nuevo')
                                            <i class="fas fa-file-medical"></i>
                                        @elseif($notificacion->tipo == 'cita')
                                            <i class="fas fa-calendar-check"></i>
                                        @else
                                            <i class="fas fa-bell"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 {{ $notificacion->leida ? '' : 'fw-bold' }}">{{ $notificacion->titulo }}</h6>
                                        <p class="mb-1 text-muted">{{ $notificacion->mensaje }}</p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $notificacion->created_at->diffForHumans() }}</small>
                                        @if(!$notificacion->leida)
                                            <span class="badge bg-danger ms-1">Nuevo</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div>
                                        <span class="badge 
                                            @if($notificacion->tipo == 'resultado_nuevo') 
                                                bg-primary
                                            @elseif($notificacion->tipo == 'cita') 
                                                bg-success
                                            @else 
                                                bg-info
                                            @endif">
                                            {{ $tiposNombres[$notificacion->tipo] ?? ucfirst($notificacion->tipo) }}
                                        </span>
                                        <small class="text-muted ms-2">{{ $notificacion->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    
                                    <div>
                                        @if($notificacion->tipo == 'resultado_nuevo' && $notificacion->notificable)
                                            <a href="{{ route('paciente.resultados.show', $notificacion->notificable_id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> Ver resultado
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- No hay resultados -->
            <div id="no-results-message" class="text-center py-5" style="display: none;">
                <div class="mb-3">
                    <i class="fas fa-search fa-3x text-muted"></i>
                </div>
                <h5>No se encontraron notificaciones</h5>
                <p class="text-muted">No hay notificaciones que coincidan con los criterios de búsqueda.</p>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $notificaciones->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-bell-slash fa-4x text-muted"></i>
                </div>
                <h5>No hay notificaciones</h5>
                <p class="text-muted">Actualmente no tienes notificaciones para visualizar.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtro por tipo
        const tipoFilter = document.getElementById('tipoFilter');
        tipoFilter.addEventListener('change', filterNotifications);
        
        // Filtro por estado
        const statusFilter = document.getElementById('statusFilter');
        statusFilter.addEventListener('change', filterNotifications);
        
        // Búsqueda
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', filterNotifications);
        
        function filterNotifications() {
            const tipo = tipoFilter.value;
            const status = statusFilter.value;
            const searchText = searchInput.value.toLowerCase();
            
            const items = document.querySelectorAll('.notification-item');
            let visibleCount = 0;
            
            items.forEach(item => {
                const itemTipo = item.getAttribute('data-tipo');
                const itemStatus = item.getAttribute('data-status');
                const itemText = item.textContent.toLowerCase();
                
                let showItem = true;
                
                if (tipo && itemTipo !== tipo) {
                    showItem = false;
                }
                
                if (status && itemStatus !== status) {
                    showItem = false;
                }
                
                if (searchText && !itemText.includes(searchText)) {
                    showItem = false;
                }
                
                item.style.display = showItem ? '' : 'none';
                
                if (showItem) {
                    visibleCount++;
                }
            });
            
            // Mostrar mensaje si no hay resultados
            const noResultsMessage = document.getElementById('no-results-message');
            if (noResultsMessage) {
                noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }
    });
</script>
@endsection

@section('styles')
<style>
    .bg-light-primary {
        background-color: rgba(37, 99, 235, 0.05);
    }
    
    .notification-item {
        transition: transform 0.2s ease-in-out;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .notification-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    
    @media (max-width: 767.98px) {
        .notification-item .d-flex {
            flex-direction: column;
        }
        
        .notification-item .ms-3 {
            margin-left: 0 !important;
            margin-top: 0.75rem !important;
        }
    }
</style>
@endsection