<?php ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Módulo Embarazo — Submenú</title>

  <!-- Iconos (igual que tu archivo original) -->
  <script src="https://kit.fontawesome.com/eb496ab1a0.js" crossorigin="anonymous"></script>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <!-- Estilos del módulo Pacientes (base de diseño a replicar) -->
  <link rel="stylesheet" href="../../css/pacientes.css">
  <!-- Mantengo tu estilos.css por si hay utilidades locales -->
  <link rel="stylesheet" href="../../css/estilos.css">

  <style>
    /* Ajustes mínimos para adaptar las “tarjetas de submenú” al contenedor tipo tabla de pacientes */
    .grid-submenu{
      display:grid; gap:24px;
      grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
      margin: 10px 0 0;
    }
    .card-sub{
      background:#ffffff; border-radius:14px; padding:22px;
      box-shadow: 0px 4px 8px rgba(0,0,0,.08);
      transition: transform .15s ease, box-shadow .15s ease;
      display:flex; flex-direction:column; gap:12px; min-height:170px;
      border:1px solid #eef2f7;
    }
    .card-sub:hover{ transform:translateY(-4px); box-shadow:0 10px 20px rgba(0,0,0,.12); }
    .card-sub i,.card-sub .bx{ font-size:34px; color:#02457a; }
    .card-sub h3{ margin:4px 0 2px 0; color:#001B48; font-family:"Roboto", sans-serif; }
    .card-sub p{ margin:0; color:#555; line-height:1.4; }
    .card-sub a.btn{
      margin-top:auto; align-self:flex-start;
      /* .btn ya viene desde pacientes.css (azul #02457a), se respeta */
      padding:10px 14px; border-radius:10px; font-weight:600; display:inline-flex; align-items:center; gap:8px;
    }

    /* Encabezado secundario (línea similar a .table__header) para agrupar las tarjetas */
    .table__body-title{
      font-size:18px; font-weight:600; color:#02457a; margin: 0 0 12px 2px;
      font-family:"Roboto", sans-serif;
      display:flex; align-items:center; gap:10px;
    }
    .table__body-title .sep{
      height:1px; background:#e8eef6; flex:1; margin-left:8px;
    }

    /* Botón volver con el mismo estilo base de .btn */
    .btn-volver{
      background:#97CADB; color:#001B48; border:none; border-radius:15px; font-weight:bold;
    }
    .btn-volver:hover{ background:#018ABE; color:#fff; }
  </style>
</head>
<body>

  <!-- Header con el MISMO diseño de pacientes (imagen de fondo, tipografías, etc.) -->
  <header class="header">
    <div class="header-content container">
      <div class="header-txt">
        <h1>EMBARAZO</h1>
      </div>
    </div>
  </header>

  <!-- Contenedor tipo “tabla” (mismo look & feel que pacientes) -->
  <main class="table" id="submenu_embarazo">
    <!-- Barra superior con título y botón volver -->
    <section class="table__header">
      <h2>SUBMÓDULO — EMBARAZO</h2>
      <a href="../../menu.php" class="btn btn-volver">
        <i class="fa-solid fa-arrow-left"></i> Volver al inicio
      </a>
    </section>

    <!-- Cuerpo: tarjetas del submenú dentro del mismo “card” visual -->
    <section class="table__body">
      <div class="table__body-title">
        Selecciona una opción
        <span class="sep"></span>
      </div>

      <div class="grid-submenu">

        <!-- submódulo: registro prenatal -->
        <article class="card-sub">
          <i class="fa-solid fa-magnifying-glass"></i>
          <h3>Buscar Paciente para Registro Prenatal</h3>
         
          <a class="btn" href="registro_prenatal.php">
            Abrir <i class="fa-solid fa-arrow-right"></i>
          </a>
        </article>

        <!-- submódulo: seguimiento y control -->
        <article class="card-sub">
          <h3>Seguimiento y Control</h3>
          <a class="btn" href="controles_prenatales.php">
            Abrir <i class="fa-solid fa-arrow-right"></i>
          </a>
        </article>

        <!-- submódulo: reportes -->
        <article class="card-sub">
          <i class="fa-solid fa-chart-column"></i>
          <h3>Reportes de embarazos</h3>
         
          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a class="btn" href="filtro_controles.php">
              Ver controles <i class="fa-solid fa-arrow-right"></i>
            </a>
            <a class="btn" href="../reportes/embarazadas_form.php">
              Ver lista embarazadas <i class="fa-solid fa-arrow-right"></i>
            </a>
          </div>
        </article>

      </div>
    </section>
  </main>

</body>
</html>
