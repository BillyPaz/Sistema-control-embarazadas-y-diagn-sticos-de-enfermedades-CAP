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
    <title>Dashboard de Usuarios</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/styleMenu.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 10px;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: #333;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-title {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
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
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .badge-status {
            padding: 0.5rem 0.8rem;
            border-radius: 20px;
            font-weight: 500;
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
        
        .recent-activity-item {
            border-left: 3px solid var(--primary-color);
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
        }
        
        .filter-section {
            background: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }
        
        .table-responsive {
            border-radius: var(--border-radius);
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 0.5rem 1rem;
        }
        
        /* Custom colors for status badges */
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
        
        .badge-supervisor {
            background-color: #9b59b6;
            color: white;
        }
        
        .badge-user {
            background-color: #3498db;
            color: white;
        }
        
        /* Custom chart styles */
        .chart-container {
            position: relative;
            height: 250px;
        }
        
        /* Custom tabs */
        .nav-pills .nav-link {
            border-radius: 20px;
            margin-right: 0.5rem;
            padding: 0.5rem 1.2rem;
            color: #555;
            font-weight: 500;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        .alert-auto-close {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            animation: fadeOut 5s forwards;
        }
        .custom-swal-popup .swal2-timer-progress-bar {
  background: #ffffff !important; /* Color personalizado (ejemplo: naranja) */
}
.requirement-list {
      list-style: none;
      padding: 0;
      margin: 0 0 20px 0;
    }
    
    .requirement-item {
      display: flex;
      align-items: center;
      margin-bottom: 8px;
      font-size: 0.85rem;
    }
    
    .requirement-item i {
      margin-right: 10px;
      width: 16px;
      text-align: center;
    }
    
    .requirement-valid {
      color: var(--success-color);
    }
    
    .requirement-invalid {
      color: #dc3545;
    }
    
    .password-match {
      font-size: 0.85rem;
      margin-top: 5px;
    }
    
    .match-valid {
      color: var(--success-color);
    }
    
    .match-invalid {
      color: #dc3545;
    }
    

    </style>
</head>
<body>
<?php include("../MENU/menuVIH.php") ?>
<main>

     <header class="header" ><div class="header-content container">
        <div class="header-txt">
            <h1>USUARIOS</h1>
        </div>
    </div>
    </header>

    <header class="dashboard-header">
        <div class="container">
            <div id="alertContainer"></div>
                     <a href="../../MENU/html/index.html" class="btn btn-light me-2">
                        <i class="fa-solid fa-house"></i> Inicio
                    </a>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                        <i class="bi bi-plus-circle me-1"></i> Nuevo Usuario
                    </button>
                    
                </div>
            </div>
        </div>
        
    </header>

    <div class="container mb-5">
        <!-- Estadísticas rápidas -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-number " id="totalUsuarios" ></div>
                    <div class="stat-title">Usuarios Totales</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-success">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div class="stat-number" id="totalActivosText" ></div>
                    <div class="stat-title">Usuarios Activos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-info">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div class="stat-number" id="totalAdmonText" ></div>
                    <div class="stat-title">Administradores</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stat-number" id="totalInactivosText" ></div>
                    <div class="stat-title">Usuarios Inactivos</div>
                </div>
            </div>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-3 mb-2">
                    <input type="text" class="form-control" placeholder="Buscar usuario..." id="searchInput">
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-select" id="roleFilter">
                        <option value="">Todos los roles</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Supervisor">Técnico</option>
                        <option value="Usuario">Usuario</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2 text-end">
                    <button class="btn btn-outline-secondary" id="resetFilters">
                        <i class="bi bi-arrow-repeat"></i> Restablecer
                    </button>
                </div>
            </div>
        </div>


            <!-- Tabla de usuarios -->
            <div class="">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-table me-2"></i> Lista de Usuarios</span>
                        <span class="badge bg-primary rounded-pill" id="totalUsText" ></span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Rol</th>
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

    <!-- Modal para nuevo usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i> Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" id="formNuevoUsuario">
                    <!-- Información Personal -->
                    <div class="col-md-6">
                        <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombres" required>
                        <span class="text-danger small" id="errorUsuarioNombre"></span>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="apellidos" required>
                        <span class="text-danger small" id="errorUsuarioApellido"></span>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="direccion" class="form-label">Dirección <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="direccion" required>
                        <span class="text-danger small" id="errorUsuarioDireccion"></span>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="telefono" required>
                        <span class="text-danger small" id="errorUsuarioTelefono"></span>
                    </div>

                    <!-- Información de Contacto -->
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" required>
                        <span class="text-danger small" id="errorUsuarioCorreo"></span>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="emailRecuperacion" class="form-label">Correo de recuperación <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="emailRecuperacion" required>
                        <span class="text-danger small" id="errorUsuarioCorreoRecuperacion"></span>
                    </div>

                    <!-- Contraseñas -->
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="text-danger small" id="errorUsuarioContrasena"></span>
                        
                        <!-- Indicadores de fortaleza de contraseña -->
                        <div class="mt-2">
                            <ul class="requirement-list list-unstyled small">
                                <li class="requirement-item">
                                    <i class="fas fa-check requirement-invalid" id="reqLength"></i>
                                    <span>Mínimo 8 caracteres</span>
                                </li>
                                <li class="requirement-item">
                                    <i class="fas fa-check requirement-invalid" id="reqUpper"></i>
                                    <span>Al menos una mayúscula</span>
                                </li>
                                <li class="requirement-item">
                                    <i class="fas fa-check requirement-invalid" id="reqLower"></i>
                                    <span>Al menos una minúscula</span>
                                </li>
                                <li class="requirement-item">
                                    <i class="fas fa-check requirement-invalid" id="reqNumber"></i>
                                    <span>Al menos un número</span>
                                </li>
                                <li class="requirement-item">
                                    <i class="fas fa-check requirement-invalid" id="reqSpecial"></i>
                                    <span>Al menos un carácter especial</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="confirmPassword" class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="text-danger small" id="errorUsuarioConfirmContrasena"></span>
                        
                        <!-- Indicador de coincidencia -->
                        <div class="mt-2">
                            <div class="password-match" id="passwordMatch">
                                <!-- Mensaje de coincidencia se mostrará aquí -->
                            </div>
                        </div>
                    </div>

                    <!-- Estado del Usuario -->
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="estadoUsuario" checked>
                            <label class="form-check-label" for="estadoUsuario">
                                Usuario activo
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
                <button type="button" id="btnAddUsuario" disabled class="btn btn-primary btnAgregarUsuario">
                    <i class="bi bi-person-plus me-1"></i> Crear Usuario
                </button>
            </div>
        </div>
    </div>
</div>
    <!-- MODAL EDITAR usuario -->
<div class="modal fade" id="EditUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-gear"></i> Editar Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body">
                <form id="formEditarHabitante">
                    <input type="text" id="idUsuarioEdit">
                    
                  
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-person"></i> Nombre
                            </label>
                            <input type="text" class="form-control shadow-sm" id="nombreUsuarioEdit" >
                            <div class="invalid-feedback">Por favor ingrese el nombre</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-person"></i> Apellido
                            </label>
                            <input type="text" class="form-control shadow-sm" id="apellidoUsuarioEdit" required>
                            <div class="invalid-feedback">Por favor ingrese el apellido</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar"></i> Dirección
                            </label>
                            <input type="text" class="form-control shadow-sm" id="direccionUsuarioEdit" required>
                            <div class="invalid-feedback">Por favor seleccione la fecha de nacimiento</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar"></i> Correo Electrónico
                            </label>
                            <input type="text" class="form-control shadow-sm" id="correoElectronicoUsuarioEdit" required>
                            <div class="invalid-feedback">Por favor seleccione la fecha de nacimiento</div>
                        </div>
                        
                        
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-telephone"></i> Teléfono
                            </label>
                            <input type="tel" class="form-control shadow-sm" id="telefonoUsuarioEdit" placeholder="+505 8888-8888">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-card-heading"></i> Estado del usuario
                            </label>
                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="estadoUsuarioEdit" type="checkbox">
                                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                       
                    </div>
                  
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" onclick="updateUsuario()" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Actualizar Usuario
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Scripts -->
 <script src="script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
         <script src="../js/script-index.js"></script>
 <!--    <script src="../js/script-index.js" ></script>-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            
            
            // Filtros personalizados
            $('#searchInput').on('keyup', function() {
                $('#usersTable').DataTable().search(this.value).draw();
            });
            
            $('#roleFilter').on('change', function() {
                $('#usersTable').DataTable().column(1).search(this.value).draw();
            });
            
            $('#statusFilter').on('change', function() {
                $('#usersTable').DataTable().column(2).search(this.value).draw();
            });
            
            $('#resetFilters').on('click', function() {
                $('#searchInput').val('');
                $('#roleFilter').val('');
                $('#statusFilter').val('');
                $('#usersTable').DataTable().search('').columns().search('').draw();
            });
            
            // Gráfico de distribución de roles
            const ctx = document.getElementById('rolesChart').getContext('2d');
            const rolesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Administradores', 'Supervisores', 'Usuarios', 'Inactivos'],
                    datasets: [{
                        data: [5, 3, 16, 3],
                        backgroundColor: [
                            '#4361ee',
                            '#9b59b6',
                            '#3498db',
                            '#95a5a6'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Simular carga de datos
            setTimeout(function() {
                // Simular actualización de estadísticas
                $('.stat-number').each(function() {
                    const finalValue = $(this).text();
                    $(this).prop('Counter', 0).animate({
                        Counter: finalValue
                    }, {
                        duration: 1500,
                        easing: 'swing',
                        step: function(now) {
                            $(this).text(Math.ceil(now));
                        }
                    });
                });
            }, 500);
        });
    </script>
</body>
</html>