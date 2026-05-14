<?php


if (!isset($_SESSION['user'])) {
    header("Location: ../../index.php");
}

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
            <li class="menu-item menu-item-static active"  >
                <a  href="../../menu.php" class="menu-link">
                <i class='bx  bx-notification'  ></i> 
                <span>PAGINA PRINCIPAL</span> 
                </a>
            </li>
          
             <li id="btnClientes" data-permiso="Pacientes" <?php if(!in_array('Pacientes',$permisos)) echo "style='display:none'" ?> class="menu-item menu-item-static">
                <a  href="../../MODULOPACIENTES/pacientes.php" class="menu-link">
                <i class="fa-solid fa-bed-pulse fa-2xs"></i> 
                <span>PACIENTES</span> 
                </a>
            </li>
            <li id="btnClientes" data-permiso="Tuberculosis" <?php if(!in_array('Tuberculosis',$permisos)) echo "style='display:none'"?> class="menu-item menu-item-static">
                <a  href="../../Tuberculosis/pacientesT.php" class="menu-link">
               <i class="fa-solid fa-lungs fa-2xs" ></i>
                <span>TUBERCULOSIS</span> 
                </a>
            </li>
             <li id="btnClientes" data-permiso="Vih" <?php if(!in_array('Vih',$permisos)) echo "style='display:none'"?> class="menu-item menu-item-static">
                <a  href="../../MODULOVIH/html/index.php" class="menu-link">
               <i class="fa-solid fa-virus fa-2xs"></i>
                <span>VIH</span> 
                </a>
            </li>
            <li id="btnClientes" data-permiso="Embarazo" <?php if(!in_array('Embarazo',$permisos)) echo "style='display:none'"?> class="menu-item menu-item-static">
                <a  href="../../MODULOEMBARAZO/html/menu_embarazo.php" class="menu-link">
               <i class="fa-solid fa-baby-carriage fa-2xs"></i>
                <span>EMBARAZO</span> 
                </a>
            </li>
           
           
             <li class="menu-item menu-item-dropdown"  id="btnUsuarios" data-permiso="Usuarios" <?php if(!in_array('Usuarios',$permisos)) echo "style='display:none'"?>>
                <a href="#" class="menu-link">
               <i class="fa-solid fa-users fa-2xs"></i>
                <span>USUARIOS</span> 
                <i class='bx  bx-caret-down'  ></i> 
                </a>
                <ul class="sub-menu">
                      <li data-permiso="RESERVAS"><a href="../../USUARIOS/html/index.php" class="sub-menu-link">USUARIOS</a></li>
                      <li data-permiso="RESERVAS"><a href="../../USUARIOS/html/rolesPermisos.php" class="sub-menu-link">ROLES</a></li>
                      <li data-permiso="RESERVAS"><a href="../../USUARIOS/html/permisos.php" class="sub-menu-link">PERMISOS</a></li>
                </ul>
            </li>
        
              <li class="menu-item menu-item-static active">
                <a  href="../../MODULOLOGIN/logout.php" class="menu-link">
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
                        
