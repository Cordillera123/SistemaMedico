import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inicializar popovers de Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Control de la barra lateral en dispositivos móviles
    const toggleSidebarBtn = document.querySelector('.toggle-sidebar');
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content-wrapper');
    
    if (toggleSidebarBtn && sidebar) {
        toggleSidebarBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            
            // Añadir/quitar overlay para cerrar el sidebar al hacer clic fuera
            if (sidebar.classList.contains('show')) {
                const overlay = document.createElement('div');
                overlay.classList.add('sidebar-overlay');
                document.body.appendChild(overlay);
                
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.remove();
                });
            } else {
                const overlay = document.querySelector('.sidebar-overlay');
                if (overlay) {
                    overlay.remove();
                }
            }
        });
    }
    
    // Auto-cierre de alertas después de 5 segundos
    const autoCloseAlerts = document.querySelectorAll('.alert-dismissible:not(.alert-persistent)');
    autoCloseAlerts.forEach(function(alert) {
        setTimeout(function() {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        }, 5000);
    });
    
    // Confirmación para eliminar registros
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const message = this.getAttribute('data-confirm') || '¿Estás seguro de que deseas eliminar este registro?';
            
            if (confirm(message)) {
                // Si el botón está dentro de un formulario, enviar el formulario
                const form = this.closest('form');
                if (form) {
                    form.submit();
                } else {
                    // Si el botón tiene un href, navegar a esa URL
                    const href = this.getAttribute('href');
                    if (href) {
                        window.location.href = href;
                    }
                }
            }
        });
    });
    
    // Vista previa de imágenes antes de subir
    const imageInputs = document.querySelectorAll('.image-upload-input');
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const preview = document.querySelector(this.getAttribute('data-preview')) || this.nextElementSibling;
            
            if (preview) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            }
        });
    });
    
    // Toggle para mostrar/ocultar contraseña
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const input = document.querySelector(this.getAttribute('data-target')) || this.previousElementSibling;
            
            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                // Cambiar el icono
                this.innerHTML = type === 'password' 
                    ? '<i class="fas fa-eye"></i>' 
                    : '<i class="fas fa-eye-slash"></i>';
            }
        });
    });
    
    // Desactivar botones de envío después de hacer clic para evitar envíos duplicados
    const forms = document.querySelectorAll('form:not(.no-submit-disable)');
    forms.forEach(function(form) {
        form.addEventListener('submit', function() {
            const submitButtons = this.querySelectorAll('button[type="submit"], input[type="submit"]');
            
            submitButtons.forEach(function(button) {
                button.disabled = true;
                
                // Si tiene un spinner, mostrarlo
                const spinner = button.querySelector('.spinner-border, .spinner-grow');
                if (spinner) {
                    spinner.style.display = 'inline-block';
                }
                
                // Si tiene texto, cambiarlo
                const text = button.getAttribute('data-loading-text');
                if (text) {
                    button.setAttribute('data-original-text', button.innerHTML);
                    button.innerHTML = text;
                }
            });
        });
    });
    
    // Inicializar datepickers si existen
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.date-picker', {
            dateFormat: 'Y-m-d',
            locale: 'es'
        });
        
        flatpickr('.datetime-picker', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            locale: 'es'
        });
    }
    
    // Inicializar select2 si existe
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            language: 'es',
            placeholder: 'Seleccione una opción',
            allowClear: true
        });
    }
    
    // Funcionalidad de búsqueda en tablas
    const tableSearchInputs = document.querySelectorAll('.table-search');
    tableSearchInputs.forEach(function(input) {
        input.addEventListener('keyup', function() {
            const tableId = this.getAttribute('data-table');
            const table = document.getElementById(tableId);
            
            if (table) {
                const searchText = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(function(row) {
                    const cells = row.querySelectorAll('td');
                    let found = false;
                    
                    cells.forEach(function(cell) {
                        if (cell.textContent.toLowerCase().indexOf(searchText) > -1) {
                            found = true;
                        }
                    });
                    
                    row.style.display = found ? '' : 'none';
                });
                
                // Mostrar mensaje si no hay resultados
                const noResultsMessage = table.nextElementSibling;
                if (noResultsMessage && noResultsMessage.classList.contains('no-results-message')) {
                    let visibleRows = 0;
                    rows.forEach(function(row) {
                        if (row.style.display !== 'none') {
                            visibleRows++;
                        }
                    });
                    
                    noResultsMessage.style.display = visibleRows === 0 ? 'block' : 'none';
                }
            }
        });
    });
    
    // Para los botones que abren modal dinámicamente
    const dynamicModalButtons = document.querySelectorAll('[data-modal-url]');
    dynamicModalButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-modal-url');
            const modalId = this.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            
            if (url && modal) {
                const modalBody = modal.querySelector('.modal-body');
                
                if (modalBody) {
                    modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
                    
                    fetch(url)
                        .then(response => response.text())
                        .then(html => {
                            modalBody.innerHTML = html;
                            
                            // Reinicializar componentes dentro del modal si es necesario
                            const modalTooltips = modalBody.querySelectorAll('[data-bs-toggle="tooltip"]');
                            modalTooltips.forEach(function(el) {
                                new bootstrap.Tooltip(el);
                            });
                            
                            // Otras inicializaciones específicas para el contenido del modal
                        })
                        .catch(error => {
                            modalBody.innerHTML = '<div class="alert alert-danger">Error al cargar el contenido</div>';
                            console.error('Error loading modal content:', error);
                        });
                }
            }
        });
    });
    
    // Manejo de pestañas que deben recordar su estado
    const rememberTabs = document.querySelectorAll('[data-bs-toggle="tab"][data-remember]');
    
    // Restaurar la pestaña activa guardada
    rememberTabs.forEach(function(tab) {
        const tabGroup = tab.getAttribute('data-remember');
        const activeTab = localStorage.getItem('activeTab_' + tabGroup);
        
        if (activeTab && activeTab === tab.getAttribute('href')) {
            const tabEl = new bootstrap.Tab(tab);
            tabEl.show();
        }
    });
    
    // Guardar la pestaña activa cuando cambia
    rememberTabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(e) {
            const tabGroup = this.getAttribute('data-remember');
            localStorage.setItem('activeTab_' + tabGroup, this.getAttribute('href'));
        });
    });
    
    // Manejar notificaciones en tiempo real (ejemplo con Socket.io)
    if (typeof io !== 'undefined' && window.userId) {
        const socket = io(window.socketUrl || '');
        
        // Conectar al canal del usuario
        socket.on('connect', function() {
            socket.emit('join', 'user.' + window.userId);
        });
        
        // Escuchar notificaciones
        socket.on('notification', function(data) {
            // Actualizar contador de notificaciones
            const counter = document.querySelector('#notification-counter');
            if (counter) {
                const count = parseInt(counter.textContent || '0') + 1;
                counter.textContent = count;
                counter.style.display = 'inline-block';
            }
            
            // Mostrar notificación tipo toast
            const toastContainer = document.getElementById('toast-container');
            if (toastContainer && data.message) {
                const toast = document.createElement('div');
                toast.classList.add('toast', 'show');
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');
                
                toast.innerHTML = `
                    <div class="toast-header">
                        <strong class="me-auto">${data.title || 'Notificación'}</strong>
                        <small>${data.time || 'Ahora'}</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${data.message}
                    </div>
                `;
                
                toastContainer.appendChild(toast);
                
                // Eliminar después de 5 segundos
                setTimeout(function() {
                    toast.remove();
                }, 5000);
            }
            
            // Reproducir sonido de notificación si está habilitado
            const notificationSound = document.getElementById('notification-sound');
            if (notificationSound && window.notificationSoundEnabled) {
                notificationSound.play();
            }
        });
    }
});