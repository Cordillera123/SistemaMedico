@extends('layouts.dashboard')

@section('title', 'Buscar Resultados por Cédula')

@section('page-title', 'Buscar Resultados por Cédula')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('doctor.resultados.index') }}">Resultados</a></li>
        <li class="breadcrumb-item active" aria-current="page">Búsqueda por Cédula</li>
    </ol>
</nav>
@endsection

@section('dashboard-content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Buscar Resultados por Cédula de Paciente</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    <input type="text" class="form-control" placeholder="Ingrese cédula del paciente..." id="searchCedula">
                    <button class="btn btn-primary" type="button" id="btnSearchCedula">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
                <small class="form-text text-muted">Ingrese el número de cédula del paciente para buscar todos sus resultados médicos.</small>
            </div>
        </div>

        <!-- Información del paciente (se muestra cuando se encuentra) -->
        <div id="pacienteInfo" class="mb-4" style="display: none;">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3">
                            <div class="avatar-initial rounded-circle bg-primary">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-1" id="pacienteNombre"></h5>
                            <p class="mb-0">
                                <span class="badge bg-light text-dark" id="pacienteCedula"></span>
                            </p>
                        </div>
                        <div class="ms-auto">
                            <a href="#" class="btn btn-sm btn-success" id="btnNuevoResultado">
                                <i class="fas fa-file-upload me-1"></i> Subir Nuevo Resultado
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loader (se muestra durante la búsqueda) -->
        <div id="searchLoader" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
            <p class="mt-2">Buscando resultados, por favor espere...</p>
        </div>

        <!-- Mensaje cuando no se encuentra el paciente -->
        <div id="notFoundMessage" class="alert alert-warning" style="display: none;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="notFoundText">No se encontró ningún paciente con la cédula proporcionada.</span>
        </div>

        <!-- Resultados del paciente -->
        <div id="resultadosContainer" style="display: none;">
            <h5 class="mt-4 mb-3">Resultados Médicos</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="resultados-table">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="resultadosBody">
                        <!-- Aquí se cargarán los resultados dinámicamente -->
                    </tbody>
                </table>
            </div>
            
            <!-- Mensaje cuando no hay resultados -->
            <div id="noResultsMessage" class="alert alert-info" style="display: none;">
                <i class="fas fa-info-circle me-2"></i>
                Este paciente no tiene resultados médicos registrados.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchCedula = document.getElementById('searchCedula');
        const btnSearchCedula = document.getElementById('btnSearchCedula');
        const pacienteInfo = document.getElementById('pacienteInfo');
        const pacienteNombre = document.getElementById('pacienteNombre');
        const pacienteCedula = document.getElementById('pacienteCedula');
        const btnNuevoResultado = document.getElementById('btnNuevoResultado');
        const searchLoader = document.getElementById('searchLoader');
        const notFoundMessage = document.getElementById('notFoundMessage');
        const notFoundText = document.getElementById('notFoundText');
        const resultadosContainer = document.getElementById('resultadosContainer');
        const resultadosBody = document.getElementById('resultadosBody');
        const noResultsMessage = document.getElementById('noResultsMessage');
        
        // Función para realizar la búsqueda
        btnSearchCedula.addEventListener('click', function() {
            const cedula = searchCedula.value.trim();
            
            if (!cedula) {
                alert('Por favor ingrese un número de cédula para buscar.');
                return;
            }
            
            // Mostrar loader y ocultar otros elementos
            searchLoader.style.display = 'block';
            pacienteInfo.style.display = 'none';
            notFoundMessage.style.display = 'none';
            resultadosContainer.style.display = 'none';
            
            // Realizar la petición AJAX
            fetch(`{{ route('doctor.resultados.por-cedula') }}?cedula=${cedula}`)
                .then(response => response.json())
                .then(data => {
                    // Ocultar loader
                    searchLoader.style.display = 'none';
                    
                    if (data.success) {
                        // Mostrar información del paciente
                        pacienteNombre.textContent = data.paciente.nombre_completo;
                        pacienteCedula.textContent = 'Cédula: ' + data.paciente.cedula;
                        btnNuevoResultado.href = "{{ route('doctor.resultados.create') }}?paciente_id=" + data.paciente.id;
                        pacienteInfo.style.display = 'block';
                        
                        // Mostrar contenedor de resultados
                        resultadosContainer.style.display = 'block';
                        
                        // Llenar la tabla con los resultados
                        if (data.resultados && data.resultados.length > 0) {
                            resultadosBody.innerHTML = '';
                            
                            data.resultados.forEach(resultado => {
                                const row = document.createElement('tr');
                                
                                // Estado (visto/no visto)
                                const tdEstado = document.createElement('td');
                                const badgeEstado = document.createElement('span');
                                
                                if (resultado.visto) {
                                    badgeEstado.className = 'badge status-badge status-active';
                                    badgeEstado.innerHTML = '<i class="fas fa-check-circle me-1"></i> Visto';
                                } else {
                                    badgeEstado.className = 'badge status-badge status-pending';
                                    badgeEstado.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i> Nuevo';
                                }
                                
                                tdEstado.appendChild(badgeEstado);
                                row.appendChild(tdEstado);
                                
                                // Título
                                const tdTitulo = document.createElement('td');
                                tdTitulo.innerHTML = `
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar avatar-sm">
                                                <div class="avatar-initial rounded-circle bg-primary">
                                                    <i class="fas fa-file-medical"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <h6 class="mb-0">${resultado.titulo}</h6>
                                        </div>
                                    </div>
                                `;
                                row.appendChild(tdTitulo);
                                
                                // Tipo
                                const tdTipo = document.createElement('td');
                                tdTipo.innerHTML = `<span class="badge bg-light text-dark">${resultado.tipo}</span>`;
                                row.appendChild(tdTipo);
                                
                                // Fecha
                                const tdFecha = document.createElement('td');
                                tdFecha.innerHTML = resultado.fecha;
                                row.appendChild(tdFecha);
                                
                                // Acciones
                                const tdAcciones = document.createElement('td');
                                tdAcciones.innerHTML = `
                                    <div class="action-buttons">
                                        <a href="${resultado.url_ver}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="${resultado.url_descargar}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Descargar PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                `;
                                row.appendChild(tdAcciones);
                                
                                resultadosBody.appendChild(row);
                            });
                            
                            noResultsMessage.style.display = 'none';
                        } else {
                            // No hay resultados
                            resultadosBody.innerHTML = '';
                            noResultsMessage.style.display = 'block';
                        }
                    } else {
                        // No se encontró el paciente
                        notFoundText.textContent = data.message;
                        notFoundMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchLoader.style.display = 'none';
                    notFoundText.textContent = 'Ocurrió un error al buscar. Por favor intente nuevamente.';
                    notFoundMessage.style.display = 'block';
                });
        });
        
        // También buscar al presionar Enter en el campo de búsqueda
        searchCedula.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                btnSearchCedula.click();
            }
        });
    });

      // Comprobar si hay una cédula en los parámetros de la URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const cedula = urlParams.get('cedula');
        
        if (cedula) {
            // Establecer el valor en el campo de búsqueda
            document.getElementById('searchCedula').value = cedula;
            
            // Ejecutar la búsqueda automáticamente
            document.getElementById('btnSearchCedula').click();
        }
    });
</script>
@endsection