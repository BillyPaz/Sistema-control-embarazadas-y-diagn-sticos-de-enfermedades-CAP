<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../../index.php");
    exit();
}

// TERCERO: Lógica de permisos (si es necesaria aquí)
$permisos = [];
if (!empty($_SESSION['user']['roles'])) {
    foreach ($_SESSION['user']['roles'] as $rol) {
        if (!empty($rol['permisos'])) {
            $permisos[] = $rol['permisos'];
        }
    }
}
$permisos = array_unique($permisos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Permisos - Sistema de Viviendas</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
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
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .module-section {
            margin-bottom: 2rem;
        }
        
        .module-header {
            padding: 1rem 1.5rem;
            background-color: #e9ecef;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .module-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: var(--primary-color);
        }
        
        .permission-item {
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: var(--border-radius);
            margin-bottom: 0.5rem;
            background: white;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .permission-item:hover {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
        }
        
        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-right: 1rem;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .permission-details {
            flex-grow: 1;
        }
        
        .permission-name {
            font-weight: 600;
            margin-bottom: 0.2rem;
        }
        
        .permission-desc {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .action-btn {
            padding: 0.3rem 0.6rem;
            border-radius: 5px;
            margin-left: 0.5rem;
        }
        
        .search-box {
            max-width: 300px;
        }
         .badge-active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .badge-inactive {
            background-color: #b11703;
            color: white;
        }
        
        .badge-admin {
            background-color: var(--primary-color);
            color: white;
        }
        
        
    </style>
</head>
<body>
    <?php include("../MENU/menuVIH.php") ?>

<main>
     <header class="header" ><div class="header-content container">
        <div class="header-txt">
            <h1>GESTION PERMISOS</h1>
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
                            <li class="breadcrumb-item"><a href="gestion-roles.html" class="text-white">Roles</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Permisos</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="../../MENU/html/index.html" class="btn btn-light me-2">
                        <i class="fa-solid fa-house"></i> Inicio
                    </a>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalPermiso">
                        <i class="bi bi-plus-circle me-1"></i> Nuevo Permiso
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
                    <div class="stat-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="stat-number" id="totalPermisosText" ></div>
                    <div class="stat-title">Permisos Totales</div>
                </div>
            </div>
          
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-number" id="permisoActivoText" ></div>
                    <div class="stat-title">Permisos Activos</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-gear"></i>
                    </div>
                    <div class="stat-number" id="permisoInactivoText" ></div>
                    <div class="stat-title">Permisos Inactivos</div>
                </div>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2">
                        <select class="form-select" id="filterModulo">
                            <option value="">Todos los módulos</option>
                            <option value="viviendas">Viviendas</option>
                            <option value="familias">Familias</option>
                            <option value="habitantes">Habitantes</option>
                            <option value="usuarios">Usuarios</option>
                            <option value="croquis">Croquis</option>
                            <option value="departamentos">Departamentos</option>
                            
                            
                        </select>
                    </div>
                  
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Lista de Permisos -->
            <div class="col-md-8" id="contenedor-modulos">
                         <div class="">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-table me-2"></i> Lista de Usuarios</span>
                        <span class="badge bg-primary rounded-pill" id="totalUsText" ></span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="permisoTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Permiso</th>
                                         <th>Observaciones</th>
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

                
              

                <!-- Más módulos (Usuarios, Reportes, Configuración) -->
                <div class="text-center">
                
                </div>
            </div>

            <!-- Panel lateral -->
            <div class="col-md-4">
                <!-- Información -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Información sobre Permisos</h6>
                    </div>
                    <div class="card-body">
                        <p class="small">
                            Los <strong>permisos</strong> controlan el acceso a funcionalidades específicas del sistema.
                            Cada permiso puede asignarse a uno o más roles de usuario.
                        </p>
                        
                        <div class="alert alert-info small">
                            <i class="bi bi-lightbulb"></i>
                            <strong>Tip:</strong> Los permisos principales (Gestionar) suelen incluir automáticamente
                            los permisos secundarios (Crear, Editar, Eliminar, Ver).
                        </div>
                        
                        <h6 class="mt-4">Tipos de Permisos:</h6>
                        <ul class="small ps-3">
                            <li><strong>Administrador:</strong> Control completo sobre un módulo</li>
                            <li><strong>Operacionales:</strong> Permisos específicos (crear, editar, eliminar)</li>
                            <li><strong>Visualización:</strong> Solo permiten ver información</li>
                            <li><strong>Sistema:</strong> Permisos especiales de administración</li>
                        </ul>
                    </div>
                </div>

               
            </div>
        </div>
    </div>

    <!-- Modal para crear permiso -->
    <div class="modal fade" id="modalPermiso" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-primary " style="color:white" >
                    <h5 class="modal-title"><i class="bi bi-shield-plus me-2"></i> <span id="modalTitulo">Nuevo Permiso</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPermiso">
                    <div class="modal-body">
                        <input type="hidden" id="permisoId">
                        
                        <div class="mb-3">
                            <label for="permisoNombre" class="form-label">Nombre del Permiso <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="permisoNombre" required placeholder="Ej: Crear viviendas">
                            <b><span id="errorNombrePermiso" style="color: red;" ></span></b>
                        </div>
                        
                        <div class="mb-3">
                            <label for="permisoDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="permisoDescripcion" rows="2" placeholder="Descripción de lo que permite este permiso"></textarea>
                        </div>
                        
                  
                     
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary btnSavePermiso ">Guardar Permiso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

     <div class="modal fade" id="modalEditarPermiso" tabindex="-1" aria-labelledby="modalEditarPermisoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h2 class="modal-title fs-5"><i class="fa-solid fa-pen-to-square"></i> Editar Permiso</h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarPermiso">
                        <input type="text" id="id_permisoEdit">
                        
                        <div class="row g-3">
                            <!-- Módulo -->
                            
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label for="nombre" class="form-label fw-semibold required-field">
                                    <i class="bi bi-card-heading"></i> Nombre
                                </label>
                                <input type="text" class="form-control shadow-sm" id="nombrePermisoEdit" placeholder="Ej: Crear Viviendas" required>
                                <b> <span id="errorNombrePermisoEdit" style="color: red;" ></span></b>
                            </div>
                            
                       
                            <!-- Estado -->
                            <div class="col-md-6">
                                <label for="activo" class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on"></i> Estado
                                </label>
                               <div class="form-check form-switch">
                                                <input class="form-check-input" id="estadoPermisoEdit" type="checkbox">
                                            </div>
                            </div>
                            
                            <!-- Descripción -->
                            <div class="col-12">
                                <label for="descripcion" class="form-label fw-semibold">
                                    <i class="bi bi-textarea-t"></i> Descripción
                                </label>
                                <textarea class="form-control shadow-sm" id="descripcionPermisoEdit" rows="3" placeholder="Breve descripción del permiso..."></textarea>
                            </div>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                            <button type="button" class="btn btn-primary btnActualizarPermiso">
                                <i class="bi bi-save"></i> Actualizar Permiso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="../js/script-permisos.js" ></script>
    <script>
    $(document).ready(function() {
        // Contador de permisos seleccionados
        function actualizarContador() {
            const seleccionados = $('.form-check-input:checked').length;
            $('#selectedCount').text(seleccionados);
        }
        
        // Actualizar contador cuando cambia cualquier checkbox
        $('.form-check-input').change(actualizarContador);
        
        // Seleccionar todos los permisos
        $('#selectAllPerms').click(function() {
            $('.form-check-input').prop('checked', true);
            actualizarContador();
        });
        
        // Deseleccionar todos los permisos
        $('#deselectAllPerms').click(function() {
            $('.form-check-input').prop('checked', false);
            actualizarContador();
        });
        
        // Filtrar permisos por módulo
        $('#filterModulo').change(function() {
            const modulo = $(this).val();
            if (modulo) {
                $('.module-section').hide();
                $(`.module-section:has(.module-header h5:contains('${modulo.charAt(0).toUpperCase() + modulo.slice(1)}'))`).show();
            } else {
                $('.module-section').show();
            }
        });
        
        // Buscar permisos
        $('#searchPermisos').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('.permission-item').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        
        
        // Inicializar contador
        actualizarContador();
    });
    </script>
</body>
</html>