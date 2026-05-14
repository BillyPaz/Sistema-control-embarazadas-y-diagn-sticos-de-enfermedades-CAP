<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php?o=1'); 
  exit;
}
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
    <div style="display:flex; gap:10px; align-items:center;">
  <span style="font-weight:600; color:#02457a;">
    <?php
      echo htmlspecialchars($_SESSION['user']['nombre'].' '.$_SESSION['user']['apellido']);
    ?>
  </span>
  <a class="btn" href="php/logout.php">Cerrar sesión</a>
</div>
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
    
    <div class="services-1">
        <img src="imagenes/user-solid-full.svg" alt=""> 
    <a href="pacientes.php">PACIENTES</a>
    </div>
    <div class="services-1">
        <img src="imagenes/lungs-solid-full.svg" alt=""> 
    <a href="pacientesT.php">PACIENTES CON TUBERCULOSIS</a>
    </div>
     <div class="services-1">
        <img src="imagenes/virus-solid-full.svg" alt=""> 
    <a href="MODULOVIH/html/index.html">PACIENTES CON VIH</a>
    </div>
    <div class="services-1">
       <img src="imagenes/person-pregnant-solid-full.svg" alt=""> 
    <a href="MODULOEMBARAZO/html/menu_embarazo.php">PACIENTES CON EMBARAZOS</a>
    </div>
</div>
</main>
</body>
</html>
