<?php
session_start();
if (isset($_SESSION['user'])) {
  header('Location: menu.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperar contraseña</title>
  <link rel="stylesheet" href="css/pacientes.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .header{
      background-image: url(imagenes/bg.png);
      background-position: center center; background-repeat: no-repeat; background-size: cover;
      display: flex; min-height: 45vh; align-items: center;
    }
    .header-txt h1{ font-size: 52px; line-height: 60px; margin-left: 180px; margin-bottom: 0; color:#02457a; }
    .wrap{ width:min(820px, 92%); margin: -70px auto 60px auto; }
    .cardx{ background:#fff; border-radius:20px; box-shadow:0 4px 8px rgba(0,0,0,.2); padding:28px 26px; }
    .brand{ font-family:"Roboto",sans-serif; font-weight:700; color:#02457a; font-size:24px; }
    .form-label{ font-weight:600; color:#0f2a44 } .form-control{ border-radius:10px; }
    .btn-main{ background:#02457a; color:#fff; border:none; border-radius:10px; font-weight:700; }
    .btn-main:hover{ background:#018ABE; }
    a.lnk{ color:#02457a; text-decoration:none; font-weight:600; } a.lnk:hover{ text-decoration:underline; }
  </style>
</head>
<body>
<header class="header">
  <div class="header-content container">
    <div class="header-txt"><h1>Recuperar contraseña</h1></div>
  </div>
</header>

<main class="wrap">
  <div class="cardx">
    <div class="brand mb-2">Sistema de Salud</div>
    <p class="text-muted">Ingresa tu correo y te enviaremos un <b>código de 6 dígitos</b>.</p>

    <?php if(isset($_GET['e']) && $_GET['e']==='1'): ?>
      <div class="alert alert-danger py-2">Ocurrió un error. Intenta nuevamente.</div>
    <?php endif; ?>

    <form method="post" action="php/enviar.php" autocomplete="off">
      <div class="mb-3">
        <label class="form-label" for="correo">Correo electrónico</label>
        <input type="email" class="form-control" id="correo" name="correo" placeholder="tucorreo@dominio.com" required>
      </div>
      <div class="d-grid gap-2">
        <button class="btn btn-main" type="submit">Enviar código</button>
      </div>
    </form>

    <div class="mt-3">
      <a class="lnk" href="login.php">&larr; Volver a Ingresar</a>
    </div>
  </div>
</main>
</body>
</html>
