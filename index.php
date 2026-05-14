
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
  <title>Ingresar al sistema</title>
  <link rel="stylesheet" href="css/pacientes.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
       body {
      min-height: 100vh; /* ocupa toda la altura de la ventana */
      display: flex;
      justify-content: center; 
      align-items: center; 
      margin: 0; 
    }
    
    body, main {
  padding: 0 !important;
  margin: 0 !important;
}
    .login-wrapper{
      width:min(920px, 92%);
      margin: -70px auto 60px auto;
    }
    .login-card{
      background:#fff; border-radius:20px;
      box-shadow:0 4px 8px rgba(0,0,0,.2);
      overflow:hidden;
      display:grid;
      grid-template-columns: 1.2fr .8fr;
    }
    .login-left{
      padding:28px 26px;
    }
    .login-right{
      background:#02457a; color:#fff; display:flex; align-items:center; justify-content:center; padding:26px;
    }
    .brand{
      font-family:"Roboto", sans-serif; font-weight:700; color:#02457a; font-size:26px; letter-spacing:.3px;
    }
    .muted{ color:#64748b; }
    .form-label{ font-weight:600; color:#0f2a44 }
    .form-control{ border-radius:10px; }
    .btn-login{
      background:#02457a; color:#fff; border:none; border-radius:10px; font-weight:700;
    }
    .btn-login:hover{ background:#018ABE; }
    .helper-links a{ color:#02457a; text-decoration:none; font-weight:600; }
    .helper-links a:hover{ text-decoration:underline; }
    @media(max-width: 900px){
      .login-card{ grid-template-columns: 1fr; }
      .login-right{ order:-1; min-height:140px; border-bottom-left-radius:20px; border-bottom-right-radius:20px; }
      .header-txt h1{ margin-left:24px; font-size:44px; }
      .login-wrapper{ margin:-50px auto 40px; }
    }
  </style>
</head>
<body>

  <main class="login-wrapper">
    <div class="login-card">
      <div class="login-left">
        <div class="brand mb-2">Sistema de Salud</div>
        <div class="muted mb-3">Accede con tu correo y contraseña</div>

        <?php if (isset($_GET['e']) && $_GET['e']==='1'): ?>
          <div class="alert alert-danger py-2">Credenciales inválidas o usuario inactivo.</div>
        <?php elseif (isset($_GET['o']) && $_GET['o']==='1'): ?>
          <div class="alert alert-warning py-2">Tu sesión expiró. Vuelve a iniciar sesión.</div>
        <?php elseif (isset($_GET['s']) && $_GET['s']==='0'): ?>
          <div class="alert alert-info py-2">Sesión cerrada correctamente.</div>
        <?php endif; ?>

        <form action="php/login_procesar.php" method="post" autocomplete="off">
          <div class="mb-3">
            <label class="form-label" for="correo">Correo electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo" placeholder="tucorreo@dominio.com" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="password">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-login">Ingresar</button>
          </div>
        </form>

        <div class="helper-links mt-3">
          <div class="helper-links mt-3">
            <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
            </div>

        </div>
      </div>

      <div class="login-right">
        <div class="text-center">
          <div style="font-size:46px; font-weight:800; line-height:1;">Bienvenido</div>
          <div class="mt-2" style="opacity:.85">Control de pacientes, VIH, tuberculosis y embarazo</div>
        </div>
      </div>
    </div>
  </main>
 <script src="script.js"></script>
</body>
</html>
