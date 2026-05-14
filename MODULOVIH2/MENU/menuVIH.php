    <div class="menu-btn sidebar-btn" id="sidebar-btn">
<i class='bx  bx-menu'  ></i> 
<i class='bx  bx-x'  ></i> 
</div>
<div class="sidebar" id="sidebar">
    <div class="header1">
        <div class="menu-btn" id="menu-btn">
                    <i class='bx  bx-caret-left'  ></i> 
            </div>
                    
          <!--   <div class="brand">
                        <img src="./img1.svg" alt="logo">
                        <span>BIENVENIDO</span>
            </div>-->
    </div>
        <div class="menu-container">
            <!--<div class="search">
                <i class='bx  bx-search-alt'  ></i> 
                <input type="search" placeholder = "Search">
            </div>-->

            <ul class="menu">
            <li class="menu-item menu-item-static active">
                <a  href="../ModuloLogin/admin_dashboard.html" class="menu-link">
                <i class='bx  bx-notification'  ></i> 
                <span>PAGINA PRINCIPAL</span> 
                </a>
            </li>
          
             <li id="btnClientes" data-permiso="CLIENTES" class="menu-item menu-item-static">
                <a  href="../MODULOCLIENTES/clientes.php" class="menu-link">
                <i class="fa-solid fa-bed-pulse fa-2xs"></i> 
                <span>PACIENTES</span> 
                </a>
            </li>
             <li class="menu-item menu-item-dropdown" id="btnVentas" data-permiso="VENTAS">
                <a href="#" class="menu-link">
          <i class="fa-solid fa-lungs fa-2xs" ></i>
                <span> TB</span> 
                <i class='bx  bx-caret-down'  ></i> 
                </a>
                <ul class="sub-menu">
                      <li data-permiso="VENTAS"><a href="../MODULOVENTAS/venta_lotes.php" class="sub-menu-link">VENTAS</a></li>
                      <li data-permiso="VENTAS"><a href="../MODULOVENTAS/reporte_venta.php" class="sub-menu-link">REPORTE</a></li>
                </ul>
            </li>
            <li  id="btnCreditos" data-permiso="CREDITOS" class="menu-item menu-item-dropdown">
                <a href="#" class="menu-link">
                <i class="fa-solid fa-virus fa-2xs"></i>
                <span>VIH</span> 
                <i class='bx  bx-caret-down'  ></i> 
                </a>
                <ul class="sub-menu">
                      <li data-permiso="ABONOS" ><a href="../MODULOCREDITOS/lista_creditos.php" class="sub-menu-link">REPORTE CREDITOS</a></li>
                      <li data-permiso="ABONOS" ><a href="../MODULOCREDITOS/abonos.php"class="sub-menu-link">ABONOS</a></li>
                </ul>
            </li>
            <li class="menu-item menu-item-dropdown" id="btnReservas"  data-permiso="RESERVAS">
                <a href="#" class="menu-link">
                <i class="fa-solid fa-baby-carriage fa-2xs"></i>
                <span>EMBARAZO</span> 
                <i class='bx  bx-caret-down'  ></i> 
                </a>
                <ul class="sub-menu">
                    <!--  <li data-permiso="RESERVAS" ><a href="../MODULORESERVA/reservass.php" class="sub-menu-link">RESERVAS</a></li> -->
                      <li data-permiso="RESERVAS" ><a href="../MODULORESERVA/reporte_reserva.php" class="sub-menu-link">REPORTE</a></li>
                </ul>
            </li>
             <li class="menu-item menu-item-dropdown"  id="btnUsuarios" data-permiso="USUARIOS">
                <a href="#" class="menu-link">
               <i class="fa-solid fa-users fa-2xs"></i>
                <span>USUARIOS</span> 
                <i class='bx  bx-caret-down'  ></i> 
                </a>
                <ul class="sub-menu">
                      <li data-permiso="RESERVAS"><a href="../ModuloUsuarios/usuarios.php" class="sub-menu-link">USUARIOS</a></li>
                      <li data-permiso="RESERVAS"><a href="../ModuloUsuarios/roles.php" class="sub-menu-link">ROLES</a></li>
                      <li data-permiso="RESERVAS"><a href="../ModuloUsuarios/permisos.php" class="sub-menu-link">PERMISOS</a></li>
                </ul>
            </li>
        
              <li class="menu-item menu-item-static active">
                <a  href="../ModuloLogin/logout.php" class="menu-link">
                <i class="fa-solid fa-rectangle-xmark fa-2xs"></i>
                <span>CERRAR SESION</span> 
                </a>
            </li>
    </ul>
</div>  
<div class="footer">
 <!-- <div class="user">
        <div class=user-img>
            <img src="" alt="user">
        </div>
        <div class="user-data">
            <span class="name"></span>
            <span class="email"></span>
        </div>

        <div class="user-icon">
        <i class='bx  bx-arrow-in-right-square-half'  ></i> 
        </div>
    </div>-->
    </div>  
 </div>
                        
 <script src="script.js"></script>

    <script>
addEventListener("DOMContentLoaded", function(){
    $.ajax({
        url: 'get-session.php', // Archivo que devuelve la sesión
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const permisos = response.permissions.split(',').map(p => p.trim());
                const userID = response.user_id;
                
                console.log(permisos);
                $('#userID').val(userID);

                if (permisos.includes('VENTAS')) {
                    $('#btnVentas').show();
                }
                if (permisos.includes('MAPA')) {
                    $('#btnMapa').show();
                }
                if (permisos.includes('LOTE')) {
                    $('#btnLotes').show();
                }
                if (permisos.includes('RESERVAS')) {
                    $('#btnReservas').show();
                }
                if (permisos.includes('USUARIOS')) {
                    $('#btnUsuarios').show();
                }
                if (permisos.includes('ROLES')) {
                    $('#btnRoles').show();
                }
                if (permisos.includes('PERMISOS')) {
                    $('#btnPermisos').show();
                }
          
                if (permisos.includes('CREDITOS')) {
                    $('#btnCreditos').show();
                }
                if(permisos.includes('CLIENTES')){
                    $("#btnClientes").show();
                };
            } else {
             window.location.href ="../index.php"
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud AJAX:", error);
        }
    });
});

</script>
