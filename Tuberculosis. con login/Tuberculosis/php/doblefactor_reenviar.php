<?php
session_start();
require_once '../SETTINGS/php/conexion.php';

// Config correo
$SMTP_USER = 'josegab.mp@gmail.com';
$SMTP_PASS = str_replace(' ', '', 'djyw nuzq wckt qpre');

if (!isset($_SESSION['2fa'])) {
  header('Location: ../login.php');
  exit;
}

$correo = $_SESSION['2fa']['correo'] ?? '';
if ($correo === '') {
  header('Location: ../login.php');
  exit;
}

try{
  $conexion = new Conexion();
  $conn = $conexion->getConnection();

  // Invalidar tokens anteriores no usados (opcional, pero recomendado)
  $conn->query("UPDATE password_resets SET usado=1 WHERE correo='".$conn->real_escape_string($correo)."' AND usado=0");

  // Nuevo token
  $token6 = (string)random_int(100000, 999999);
  $expira = date('Y-m-d H:i:s', time() + 10*60);

  $ins = $conn->prepare("INSERT INTO password_resets (correo, token, expira, usado) VALUES (?,?,?,0)");
  $ins->bind_param('sss', $correo, $token6, $expira);
  $ins->execute();

  // Email
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
    $mail->Subject = 'Tu nuevo código de acceso (10 min)';
    $mail->Body    = "
      <p>Tu nuevo código es:</p>
      <p style='font-size:24px; font-weight:bold; letter-spacing:3px;'>$token6</p>
      <p>Vence en 10 minutos.</p>
    ";

    $mail->send();
  } catch(Throwable $e){
    // logging opcional
  }

  header('Location: ../doblefactor.php?r=1');
  exit;

}catch(Throwable $e){
  header('Location: ../doblefactor.php?e=1');
  exit;
}
