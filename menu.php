
<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
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
<!DOCTYPE html>
<html lang="es">
<head>
<script src="https://kit.fontawesome.com/eb496ab1a0.js" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css2?family=Italiana&display=swap" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="header-txt">
                <span>Nuevo Progreso, San Marcos</span>
                <h1>PUESTO DE SALUD<br></h1>
                <p>
                   VISION: Promover un estilo de vida saludable para todas las personas, familias y comunidades, 
                   con participación activa de la población y espacios saludables. Esto se lograría a través
                   de un sistema que ofrece servicios de salud de calidad, oportunos, eficientes, equitativos e integrales, 
                   incluyendo el trabajo social y la atención culturalmente pertinente para mejorar el bienestar de la 
                   población guatemalteca. 
                </p>
        </div>

        <div class="header-img">
            <img src="imagenes/PORTADA.png" alt="">
        </div>
        </div>
    </header>
   <main class="services container">
    <h2></2>
    <div class="services-content">
    
    <div data-permiso="PACIENTES" <?php if(!in_array('Pacientes',$permisos)) echo 'style="display:none"' ?> class="services-1">
        <img src="imagenes/user-solid-full.svg" alt=""> 
    <a href="MODULOPACIENTES/pacientes.php">PACIENTES</a>
    </div>
    <div  data-permiso="TUBERCULOSIS" <?php if(!in_array('Tuberculosis',$permisos))echo 'style="display:none"' ?> class="services-1">
        <img src="imagenes/lungs-solid-full.svg" alt=""> 
    <a href="Tuberculosis/pacientesT.php">PACIENTES CON TUBERCULOSIS</a>
    </div>
     <div data-permiso="VIH" <?php if(!in_array('Vih',$permisos))echo 'style="display:none"' ?> class="services-1">
        <img src="imagenes/virus-solid-full.svg" alt=""> 
    <a href="MODULOVIH/html/index.php">PACIENTES CON VIH</a>
    </div>
    <div data-permiso="Embarazo" <?php if(!in_array('Embarazo',$permisos))echo 'style="display:none"' ?> class="services-1">
       <img src="imagenes/person-pregnant-solid-full.svg" alt=""> 
    <a href="MODULOEMBARAZO/html/menu_embarazo.php">PACIENTES CON EMBARAZOS</a>
    </div>
     <div data-permiso="USUARIOS" <?php if(!in_array('Usuarios',$permisos))echo 'style="display:none"' ?> class="services-1">
       <img src="imagenes/users-solid-full.svg" alt=""> 
    <a href="USUARIOS/html/index.php">USUARIOS</a>
    </div>
</div>
</main>
</body>
</html>
