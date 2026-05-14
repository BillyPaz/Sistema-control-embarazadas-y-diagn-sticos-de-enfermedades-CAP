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
    <title>Gestión de Roles y Permisos</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styleMenu.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        
        .info-card {
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            border: none;
            transition: transform 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .badge-custom {
            background-color: var(--secondary-color);
            padding: 5px 10px;
            border-radius: 20px;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
        }
        
        .nav-pills .nav-link {
            color: var(--secondary-color);
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .permission-item {
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary-color);
        }
        
        .role-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .role-card:hover, .role-card.selected {
            background-color: #e9ecef;
            border-color: var(--primary-color);
        }
        
        .role-card.selected {
            border: 2px solid var(--primary-color);
        }
        
        .permission-category {
            background-color: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            font-weight: 600;
        }
        .custom-swal-popup .swal2-timer-progress-bar {
  background: #ffffff !important; /* Color personalizado (ejemplo: naranja) */
}
    </style>
</head>
<body>
      <?php include("../MENU/menuVIH.php") ?>

<main>
  <header class="header" >
    <div class="header-content container">
        <div class="header-txt">
            <h1>GESTION ROLES</h1>
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
                            <li class="breadcrumb-item active text-white" aria-current="page">Roles y Permisos</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalAsignarRol">
                        <i class="bi bi-person-gear"></i> Asignar Rol a Usuario
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="container mb-5">
        <!-- Estadísticas rápidas -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card info-card stat-card">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-number" id="rolesCountText"></div>
                        <div class="text-muted">Roles del Sistema</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card info-card stat-card">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="stat-number" id="permisosCountText" ></div>
                        <div class="text-muted">Permisos Totales</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card info-card stat-card">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="stat-number">12</div>
                        <div class="text-muted">Usuarios con Roles</div>
                    </div>
                </div>
            </div>
           
        </div>

        <!-- Pestañas de navegación -->
        <ul class="nav nav-pills mb-4" id="rolesTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="roles-list-tab" data-bs-toggle="pill" data-bs-target="#roles-list" type="button">
                    <i class="bi bi-list-ul"></i> Lista de Roles
                </button>
            </li>
            
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="assignments-tab" data-bs-toggle="pill" data-bs-target="#assignments" type="button">
                    <i class="bi bi-person-gear"></i> Asignaciones
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="rolesTabsContent">
            <!-- Pestaña de Lista de Roles -->
            <div class="tab-pane fade show active" id="roles-list" role="tabpanel">
                <div class="row">
                    <div class="col-md-5 mb-4">
                        <div class="card info-card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h4 class="mb-0"><i class="bi bi-list-ul"></i> Roles del Sistema</h4>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoRol">
                                    <i class="bi bi-plus-circle"></i> Nuevo Rol
                                </button>
                            </div>
                            <div class="card-body" id="">
                                <div class="list-group" id="lista-roles">
                                   
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 mb-4">
                        <div class="card info-card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h4 class="mb-0"><i class="bi bi-shield-check"></i> Permisos del Rol: <span id="nombre-rol-seleccionado">Seleccione</span></h4>
                               <input type="text" id="idRolAsignacion" hidden >
                                <button class="btn btn-outline-primary btn-sm" id="btn-editar-permisos">
                                    <i class="bi bi-pencil"></i> Editar Permisos
                                </button>
                            </div>
                            <div class="card-body" id="contenedor-permisos" >
                                <div id="mensaje-seleccione-rol" class="alert alert-info text-center">
  Seleccione un rol para mostrar sus permisos asignados.
</div>
                                <div class="d-flex justify-content-end mt-3" id="contenedorButton" >
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
      
            
            <!-- Pestaña de Asignaciones -->
            <div class="tab-pane fade" id="assignments" role="tabpanel">
                <div class="card info-card">
                    <div class="card-header bg-white">
                        <h4 class="mb-0"><i class="bi bi-person-gear"></i> Asignaciones de Roles a Usuarios</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Rol Actual</th>
                                        <th>Fecha de Asignación</th>
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
        </div>
    </div>

    <!-- Modal para asignar rol a usuario -->
    <div class="modal fade" id="modalAsignarRol" tabindex="-1" aria-labelledby="modalAsignarRolLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h2 class="modal-title fs-5"><i class="bi bi-person-gear"></i> Asignar Rol a Usuario</h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAsignarRol">
                        <div class="mb-3">
                            <label for="selectUsuario" class="form-label fw-semibold">Seleccionar Usuario</label>
                            <select class="form-select" id="selectUsuario" >
                               
                            </select>
                            <b><span style="color: red;" id="errorSelectUsuario" ></span></b>
                        </div>
                        
                        <div class="mb-3">
                            <label for="selectRol" class="form-label fw-semibold">Seleccionar Rol</label>
                            <select class="form-select" id="selectRol" required>
                              
                            </select>
                            <b><span style="color: red;" id="errorSelectRol" ></span></b>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Permisos que se asignarán:</label>
                            <div class="border rounded p-3 bg-light" id="permisos-preview">
                                <p class="text-muted mb-0">Seleccione un rol para ver los permisos que se asignarán</p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarAsignacion" onclick="asignarRol()" >Asignar Rol</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo rol -->
    <div class="modal fade" id="modalNuevoRol" tabindex="-1" aria-labelledby="modalNuevoRolLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h2 class="modal-title fs-5"><i class="bi bi-plus-circle"></i> Crear Nuevo Rol</h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoRol">
                        <div class="mb-3">
                            <label for="nombreRol" class="form-label">Nombre del Rol</label>
                            <input type="text" class="form-control" id="nombreRol" placeholder="Ej: Coordinador de campo" required>
                            <b><span id="errorRol" style="color: red;" ></span></b>
                        </div>
                        <div class="mb-3">
                            <label for="descripcionRol" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcionRol" rows="2" placeholder="Breve descripción de las funciones de este rol"></textarea>
                            
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btnSaveRol">Crear Rol</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar rol del usuario -->
<div class="modal fade" id="modalEditarRol" tabindex="-1" aria-labelledby="modalEditarRolLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarRolLabel">Editar Rol del Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="editIdUsuario">
        
        <div class="mb-3">
          <label for="editNombreUsuario" class="form-label">Usuario</label>
          <span type="text" class="form-control" id="editNombreUsuario" disabled></span>
        </div>

        <div class="mb-3">
          <label for="editRol" class="form-label">Rol</label>
          <select class="form-select" id="editRol">
            <!-- Se llena dinámicamente -->
          </select>
          <span class="text-danger" id="errorEditRol"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardarRolEditado()">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/script-rol.js" ></script>  

    <script>
        // Datos de ejemplo para permisos por rol
     

        // Inicializar el dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Selección de roles
         const listaRoles = document.getElementById('lista-roles');

    listaRoles.addEventListener('click', function (e) {
        const card = e.target.closest('.role-card');
        if (!card) return; // Hiciste clic fuera de un card

        e.preventDefault();

      
        document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));

        // Aplicar selección actual
        card.classList.add('selected');

        // Obtener información del rol
        const roleId = card.getAttribute('data-role-id');
        const roleName = card.querySelector('h5').textContent;
        
        // Mostrar nombre del rol
        document.getElementById('nombre-rol-seleccionado').textContent = roleName;

        // Aquí puedes cargar permisos vía AJAX
        console.log(`Rol seleccionado: ${roleName} (ID: ${roleId})`);
    });
           
    
            // Habilitar edición de permisos
            document.getElementById('btn-editar-permisos').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.permission-item input[type="checkbox"]');
                const btnGuardar = document.getElementById('btn-guardar-permisos');
                
                checkboxes.forEach(checkbox => {
                    checkbox.disabled = !checkbox.disabled;
                });
                
                btnGuardar.disabled = !btnGuardar.disabled;
                
                if (!btnGuardar.disabled) {
                    this.innerHTML = '<i class="bi bi-x-circle"></i> Cancelar Edición';
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-outline-danger');
                } else {
                    this.innerHTML = '<i class="bi bi-pencil"></i> Editar Permisos';
                    this.classList.remove('btn-outline-danger');
                    this.classList.add('btn-outline-primary');
                }
            });
        });
    </script>
</body>
</html>