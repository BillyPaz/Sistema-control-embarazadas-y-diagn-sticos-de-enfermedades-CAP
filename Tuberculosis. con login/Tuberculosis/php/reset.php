<?php
session_start();
if (!isset($_SESSION['reset_ok']) || !isset($_SESSION['reset_correo'])) {
  header('Location: ../recuperar.php');
  exit;
}
$correo = $_SESSION['reset_correo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nueva contraseña</title>
  <link rel="stylesheet" href="../css/pacientes.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .header{
      background-image: url(../imagenes/bg.png);
      background-position: center center; background-repeat: no-repeat; background-size: cover;
      display: flex; min-height: 45vh; align-items: center;
    }
    .header-txt h1{ font-size: 52px; line-height: 60px; margin-left: 180px; margin-bottom: 0; color:#02457a; }
    .wrap{ width:min(820px,92%); margin:-70px auto 60px auto; }
    .cardx{ background:#fff; border-radius:20px; box-shadow:0 4px 8px rgba(0,0,0,.2); padding:28px 26px; }
    .form-label{ font-weight:600; color:#0f2a44 } .form-control{ border-radius:10px; }
    .btn-main{ background:#02457a; color:#fff; border:none; border-radius:10px; font-weight:700; }
    .btn-main:hover{ background:#018ABE; }
    a.lnk{ color:#02457a; text-decoration:none; font-weight:600; } a.lnk:hover{ text-decoration:underline; }
  </style>
</head>
<body>
<header class="header">
  <div class="header-content container">
    <div class="header-txt"><h1>Nueva contraseña</h1></div>
  </div>
</header>

<main class="wrap">
  <div class="cardx">
    <p class="text-muted">Correo: <b><?= htmlspecialchars($correo) ?></b></p>

    <?php if(isset($_GET['e']) && $_GET['e']==='1'): ?>
      <div class="alert alert-danger py-2">No se pudo actualizar la contraseña. Revisa los datos.</div>
    <?php endif; ?>

    <form action="reset_guardar.php" method="post" autocomplete="off">
      <div class="mb-3">
        <label class="form-label" for="pass1">Nueva contraseña</label>
        <input type="password" id="pass1" name="pass1" class="form-control" required minlength="4" placeholder="Mínimo 4 caracteres">
      </div>
      <div class="mb-3">
        <label class="form-label" for="pass2">Confirmar contraseña</label>
        <input type="password" id="pass2" name="pass2" class="form-control" required minlength="4">
      </div>
      <div class="d-grid">
        <button class="btn btn-main" type="submit">Guardar</button>
      </div>
    </form>

    <div class="mt-3">
      <a class="lnk" href="../login.php">&larr; Volver a Ingresar</a>
    </div>
  </div>
</main>
</body>
</html>
