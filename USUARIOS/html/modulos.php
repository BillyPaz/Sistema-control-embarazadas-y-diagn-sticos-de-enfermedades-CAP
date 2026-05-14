<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Módulos - Sistema de Viviendas</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
     <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/styleMenu.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 10px;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .card {
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
        }
        
        .module-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .module-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-left-color: var(--primary-color);
        }
        
        .icon-preview {
            font-size: 1.5rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
        }
        
        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-title {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .action-btn {
            padding: 0.3rem 0.6rem;
            border-radius: 5px;
            margin-right: 0.3rem;
        }
    </style>
</head>
<body>
<?php include("../MENU/menuVIH.php") ?>



<main>
    <header class="header" ><div class="header-content container">
        <div class="header-txt">
            <h1>GESTION MODULOS</h1>
        </div>
    </div>
    </header>

    <!-- Header -->
    <header class="dashboard-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-white">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-white">Administración</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Módulos</li>
                        </ol>
                    </nav>
                </div>
                <div>
                     <a href="../../MENU/html/index.html" class="btn btn-light me-2">
                        <i class="fa-solid fa-house"></i> Inicio
                    </a>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalModulo">
                        <i class="bi bi-plus-circle me-1"></i> Nuevo Módulo
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="container mb-5">
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon text-primary">
                        <i class="bi bi-puzzle"></i>
                    </div>
                    <div class="stat-number" id="total-modulos"></div>
                    <div class="stat-title">Total Módulos</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon text-success">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="stat-number" id="totalModuloActivo"></div>
                    <div class="stat-title">Módulos Activos</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon text-info">
                        <i class="bi bi-collection"></i>
                    </div>
                    <div class="stat-number" id="totalModuloInactivo" ></div>
                    <div class="stat-title">Módulos Inactivos</div>
                </div>
            </div>
            
        </div>

        <div class="row">
            <!-- Lista de Módulos -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-list-check me-2"></i> Módulos del Sistema</span>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" class="form-control form-control-sm" placeholder="Buscar módulo..." id="searchModulos">
                            <button class="btn btn-outline-secondary btn-sm" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaModulos">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Módulo</th>
                                        <th>Icono</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel lateral -->
            <div class="col-md-4">
                <!-- Información -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Información sobre Módulos</h6>
                    </div>
                    <div class="card-body">
                        <p class="small">
                            Los <strong>módulos</strong> organizan las funcionalidades del sistema en grupos lógicos.
                            Cada módulo puede contener múltiples permisos que controlan el acceso a sus funciones.
                        </p>
                        
                        <div class="alert alert-warning small">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Importante:</strong> Al desactivar un módulo, todos sus permisos asociados se desactivarán automáticamente.
                        </div>
                        
                        <h6 class="mt-4">Tipos de Módulos:</h6>
                        <ul class="small ps-3">
                            <li><strong>Administrador:</strong> Contienen funcionalidades centrales del sistema</li>
                            <li><strong>Técnicos:</strong> Funcionalidades complementarias</li>
                            <li><strong>Enfermeros:</strong> Configuración y administración</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal para crear módulo -->
    <div class="modal fade" id="modalModulo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-puzzle me-2"></i> <span id="modalTitulo">Nuevo Módulo</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formModulo">
                    <div class="modal-body">
                        <input type="hidden" id="moduloId">
                        
                            <div class="mb-3">
                                <label for="moduloNombre" class="form-label">Nombre del Módulo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="moduloNombre" required placeholder="Ej: Viviendas">
                                <span style="color: red;" id="errorNombreModulo" ></span>
                            </div>
                        
                        <div class="mb-3">
                            <label for="moduloDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="moduloDescripcion" rows="2" placeholder="Breve descripción del módulo"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="moduloIcono" class="form-label">Icono</label>
                                    <select class="form-select" id="moduloIcono">
                                        <option value="bi-house-door">Casa (viviendas)</option>
                                        <option value="bi-people-fill">Personas (familias)</option>
                                        <option value="bi-person-gear">Usuario (usuarios)</option>
                                        <option value="bi-graph-up">Gráfico (reportes)</option>
                                        <option value="bi-gear-fill">Engranaje (configuración)</option>
                                        <option value="bi-file-earmark">Archivo (documentos)</option>
                                        <option value="bi-calendar">Calendario (eventos)</option>
                                        <option value="bi-chat">Chat (mensajes)</option>
                                    </select>
                                </div>
                            </div>
                           
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Vista previa:</label>
                            <div class="d-flex align-items-center p-3 border rounded bg-light">
                                <div class="icon-preview me-3">
                                    <i id="iconPreview" class="bi bi-house-door"></i>
                                </div>
                                <div>
                                    <div id="nombrePreview" class="fw-bold">Nombre del módulo</div>
                                    <div id="descripcionPreview" class="text-muted small">Descripción del módulo</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btnSaveModulo " >Guardar Módulo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Modal para editar módulo -->
    <div class="modal fade" id="modalModuloEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-puzzle me-2"></i> <span id="modalTitulo">Editar Módulo</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formModulo">
                    <div class="modal-body">
                        <input type="text" id="idModuloIdEdit">
                        
                        <div class="mb-3">
                            <label for="moduloNombre" class="form-label">Nombre del Módulo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="moduloNombreEdit" required placeholder="Ej: Viviendas">
                        </div>
                        
                        <div class="mb-3">
                            <label for="moduloDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="moduloDescripcionEdit" rows="2" placeholder="Breve descripción del módulo"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="moduloIcono" class="form-label">Icono</label>
                                    <select class="form-select" id="moduloIconoEdit">
                                        <option value="bi-house-door">Casa (viviendas)</option>
                                        <option value="bi-people-fill">Personas (familias)</option>
                                        <option value="bi-person-gear">Usuario (usuarios)</option>
                                        <option value="bi-graph-up">Gráfico (reportes)</option>
                                        <option value="bi-gear-fill">Engranaje (configuración)</option>
                                        <option value="bi-file-earmark">Archivo (documentos)</option>
                                        <option value="bi-calendar">Calendario (eventos)</option>
                                        <option value="bi-chat">Chat (mensajes)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <label class="form-check-label" for="activo">
                                    Usuario activo
                                </label>
                                <div class="form-check form-switch">
                                                <input class="form-check-input" id="estadoModulo" type="checkbox">
                                            </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Vista previa:</label>
                            <div class="d-flex align-items-center p-3 border rounded bg-light">
                                <div class="icon-preview me-3">
                                    <i id="iconPreviewEdit" class="bi bi-house-door"></i>
                                </div>
                                <div>
                                    <div id="nombrePreviewEdit" class="fw-bold">Nombre del módulo</div>
                                    <div id="descripcionPreviewEdit" class="text-muted small">Descripción del módulo</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary btnEditModuloSave" >Guardar Módulo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
     <script src="script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {


        
        // Actualizar vista previa
        function actualizarVistaPrevia() {
            $('#nombrePreview').text($('#moduloNombre').val() || 'Nombre del módulo');
            $('#descripcionPreview').text($('#moduloDescripcion').val() || 'Descripción del módulo');
            $('#iconPreview').removeClass().addClass('bi ' + $('#moduloIcono').val());
        }
        
        // Eventos para actualizar vista previa
        $('#moduloNombre, #moduloDescripcion').on('input', actualizarVistaPrevia);
        $('#moduloIcono').change(actualizarVistaPrevia);
        
        // Inicializar vista previa
        actualizarVistaPrevia();
        



function actualizarVistaPreviaEdit() {
        $('#nombrePreviewEdit').text($('#moduloNombreEdit').val() || 'Nombre del módulo');
        $('#descripcionPreviewEdit').text($('#moduloDescripcionEdit').val() || 'Descripción del módulo');
        $('#iconPreviewEdit').removeClass().addClass('bi ' + $('#moduloIconoEdit').val());
    }

    $('#moduloNombreEdit, #moduloDescripcionEdit').on('input', actualizarVistaPreviaEdit);
    $('#moduloIconoEdit').on('change', actualizarVistaPreviaEdit);

    
        // Manejar envío del formulario
        $('#formModulo').on('submit', function(e) {
            e.preventDefault();
            
            // Simular guardado exitoso
            const modal = bootstrap.Modal.getInstance($('#modalModulo')[0]);
            modal.hide();
            
            // Mostrar mensaje de éxito
            alert('Módulo guardado correctamente');
            
            // Aquí iría la lógica real para guardar en la base de datos
            console.log('Guardando módulo:', {
                nombre: $('#moduloNombre').val(),
                descripcion: $('#moduloDescripcion').val(),
                icono: $('#moduloIcono').val(),
                orden: $('#moduloOrden').val()
            });
        });
        
        // Buscador de módulos
        $('#searchModulos').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#tablaModulos tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        
        // Configurar modal para edición
        $('#modalModulo').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const esEdicion = button.closest('tr').length > 0;
            
            if (esEdicion) {
                const fila = button.closest('tr');
                const nombre = fila.find('.fw-bold').text();
                const descripcion = fila.find('.text-muted').text();
                
                $('#modalTitulo').text('Editar Módulo');
                $('#moduloNombre').val(nombre);
                $('#moduloDescripcion').val(descripcion);
                // Aquí cargarías los demás valores desde la fila
            } else {
                $('#modalTitulo').text('Nuevo Módulo');
                $('#formModulo')[0].reset();
                actualizarVistaPrevia();
            }
        });
    });
    </script>
<script src="../js/script-modulos.js" ></script> 
</body>
</html>