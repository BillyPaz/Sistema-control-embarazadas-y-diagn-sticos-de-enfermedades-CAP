<?php
session_start();
if (!isset($_SESSION['2fa'])) {
  header('Location: index.php');
  exit;
}
$correo = $_SESSION['2fa']['correo'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Verificación en dos pasos</title>
  <link rel="stylesheet" href="css/pacientes.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .header{
      background-image: url(imagenes/bg.png);
      background-position:center; background-repeat:no-repeat; background-size:cover;
      display:flex; min-height:45vh; align-items:center;
    }
    .header-txt h1{ font-size:52px; line-height:60px; margin-left:180px; margin-bottom:0; color:#02457a; }
    .wrap{ width:min(820px,92%); margin:-70px auto 60px auto; }
    .cardx{ background:#fff; border-radius:20px; box-shadow:0 4px 8px rgba(0,0,0,.2); padding:28px 26px; }
    .form-label{ font-weight:600; color:#0f2a44 } .form-control{ border-radius:10px; }
    .btn-main{ background:#02457a; color:#fff; border:none; border-radius:10px; font-weight:700; }
    .btn-main:hover{ background:#018ABE; }
    a.lnk{ color:#02457a; text-decoration:none; font-weight:600; } a.lnk:hover{ text-decoration:underline; }
    .code-input{ letter-spacing:6px; font-weight:700; font-size:22px; text-align:center; }
    
    body {
  min-height: 100vh; /* ocupa toda la altura de la ventana */
  display: flex;
  justify-content: center; /* centra horizontalmente */
  align-items: center; /* centra verticalmente */
  margin: 0; /* elimina márgenes por defecto */
}

body, main {
  padding: 0 !important;
  margin: 0 !important;
}
  </style>
</head>
<body>

<main class="wrap">
  <div class="cardx">
    <p class="text-muted">Hemos enviado un código a: <b><?= htmlspecialchars($correo) ?></b></p>

    <?php if(isset($_GET['e']) && $_GET['e']==='1'): ?>
      <div class="alert alert-danger py-2">Código inválido o vencido.</div>
    <?php elseif(isset($_GET['r']) && $_GET['r']==='1'): ?>
      <div class="alert alert-success py-2">Se envió un nuevo código a tu correo.</div>
    <?php endif; ?>

    <form action="php/doblefactor_verificar.php" method="post" autocomplete="off">
      <div class="mb-3">
        <label class="form-label" for="token">Código de 6 dígitos</label>
        <input type="text" maxlength="6" pattern="\d{6}" class="form-control code-input" id="token" name="token" placeholder="••••••" required>
        <div class="form-text">Vigencia: 10 minutos</div>
      </div>
      <div class="d-grid gap-2">
        <button class="btn btn-main" type="submit">Verificar</button>
      </div>
    </form>

    <div class="mt-3 d-flex gap-3">
      <form action="php/doblefactor_reenviar.php" method="post">
        <button class="btn btn-outline-primary btn-sm" type="submit">Reenviar código</button>
      </form>
      <a class="lnk" href="php/logout.php">Cancelar</a>
    </div>
  </div>
</main>
</body>
</html>
