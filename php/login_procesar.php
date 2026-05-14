<?php
session_start();
require_once __DIR__.'/../SETTINGS/php/conexion.php';

// Config correo
$SMTP_USER = 'josegab.mp@gmail.com';
$SMTP_PASS = str_replace(' ', '', 'djyw nuzq wckt qpre'); // quita espacios

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../index.php');
  exit;
}

$correo   = isset($_POST['correo'])   ? trim($_POST['correo'])   : '';
$password = isset($_POST['password']) ? $_POST['password']       : '';


try{
  $conexion = new Conexion();
  $conn = $conexion->getConnection();

  $sql = "SELECT id_usuario, nombre_usuario, apellido_usuario, correo, password, estado_usuario
          FROM usuario
          WHERE correo = ?
          LIMIT 1";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('s', $correo);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res && $res->num_rows === 1) {
    $u = $res->fetch_assoc();

    

    // Comparación en plano (según tu requerimiento)
    if ($password === $u['password']) {

      // Generar código de 6 dígitos (10 minutos de vigencia)
      $token6 = (string)random_int(100000, 999999);
      $expira = date('Y-m-d H:i:s', time() + 10*60);

      // Guardar en password_resets (reutilización)
      $ins = $conn->prepare("INSERT INTO password_resets (correo, token, expira, usado) VALUES (?,?,?,0)");
      $ins->bind_param('sss', $correo, $token6, $expira);
      $ins->execute();
      $reset_id = $conn->insert_id;

      // Guardar info temporal de 2FA en sesión
      $_SESSION['2fa'] = [
        'id_usuario' => (int)$u['id_usuario'],
        'nombre'     => $u['nombre_usuario'],
        'apellido'   => $u['apellido_usuario'],
        'correo'     => $u['correo'],
        'reset_id'   => (int)$reset_id
      ];

      // Enviar email con PHPMailer
      require_once '../PHPMAILER/src/PHPMailer.php';
      require_once '../PHPMAILER/src/SMTP.php';
      require_once '../PHPMAILER/src/Exception.php';

      $mail = new PHPMailer\PHPMailer\PHPMailer(true);
      try{
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $SMTP_USER;
        $mail->Password   = $SMTP_PASS;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($SMTP_USER, 'Sistema de Salud');
        $mail->addAddress($correo);

        $mail->isHTML(true);
        $mail->Subject = 'Tu código de acceso (10 min)';
        $mail->Body    = "
          <p>Tu código de acceso es:</p>
          <p style='font-size:24px; font-weight:bold; letter-spacing:3px;'>$token6</p>
          <p>Vence en 10 minutos.</p>
        ";

        $mail->send();
      } catch(Throwable $e){
        // Si falla el envío, igual mostramos el paso de 2FA (puedes manejar log)
      }

      header('Location: ../doblefactor.php?info=1');
      exit;
    }
  }

  header('Location: ../index.php');
  exit;

}catch(Throwable $e){
  header('Location: ../index.php');
  exit;
}
