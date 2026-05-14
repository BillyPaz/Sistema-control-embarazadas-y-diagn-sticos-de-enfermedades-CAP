<?php
session_start();
require_once '../SETTINGS/php/conexion.php';

$SMTP_USER = 'josegab.mp@gmail.com';
$SMTP_PASS = str_replace(' ', '', 'djyw nuzq wckt qpre'); 
$SITE_URL  = 'http://localhost/Tuberculosis'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../recuperar.php');
  exit;
}

$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
if ($correo === '') {
  header('Location: ../recuperar.php?e=1');
  exit;
}

try{
  $conexion = new Conexion();
  $conn = $conexion->getConnection();

  $sql = "SELECT id_usuario, estado_usuario FROM usuario WHERE correo = ? LIMIT 1";
  $st  = $conn->prepare($sql);
  $st->bind_param('s', $correo);
  $st->execute();
  $rs = $st->get_result();

  if ($rs && $rs->num_rows === 1) {
    $u = $rs->fetch_assoc();
    if ((int)$u['estado_usuario'] === 1) {
      $token6 = (string)random_int(100000, 999999);
      $expira = date('Y-m-d H:i:s', time() + 15*60); 

      $ins = $conn->prepare("INSERT INTO password_resets (correo, token, expira, usado) VALUES (?,?,?,0)");
      $ins->bind_param('sss', $correo, $token6, $expira);
      $ins->execute();
      $reset_id = $conn->insert_id;

      $_SESSION['reset_correo'] = $correo;
      $_SESSION['reset_id']     = $reset_id;

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
        $mail->Subject = 'Código de verificación (15 min)';
        $mail->Body    = "
          <p>Tu código de verificación es:</p>
          <p style='font-size:24px; font-weight:bold; letter-spacing:3px;'>$token6</p>
          <p>Vence en 15 minutos.</p>
        ";

        $mail->send();
      } catch(Throwable $e){
    
      }
    }
  }
 
  header('Location: ../verify_token.php');
  exit;

}catch(Throwable $e){
  header('Location: ../recuperar.php?e=1');
  exit;
}
