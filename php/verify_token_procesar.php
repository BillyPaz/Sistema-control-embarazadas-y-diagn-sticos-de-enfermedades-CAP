<?php
session_start();
require_once __DIR__.'/../SETTINGS/php/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['reset_correo'])) {
  header('Location: ../recuperar.php');
  exit;
}

$correo = $_SESSION['reset_correo'];
$token  = isset($_POST['token']) ? trim($_POST['token']) : '';

if ($token === '' || !preg_match('/^\d{6}$/', $token)) {
  header('Location: ../verify_token.php?e=1');
  exit;
}

try{
  $conexion = new Conexion();
  $conn = $conexion->getConnection();

  $sql = "SELECT id, expira, usado FROM password_resets
          WHERE correo = ? AND token = ? AND usado = 0
          ORDER BY id DESC LIMIT 1";
  $st  = $conn->prepare($sql);
  $st->bind_param('ss', $correo, $token);
  $st->execute();
  $rs = $st->get_result();

  if ($rs && $rs->num_rows === 1) {
    $row = $rs->fetch_assoc();
    if (strtotime($row['expira']) >= time()) {
      $_SESSION['reset_ok']    = true;
      $_SESSION['reset_token'] = $token;
      $_SESSION['reset_id']    = (int)$row['id'];
      header('Location: reset.php');
      exit;
    }
  }

  header('Location: ../verify_token.php?e=1');
  exit;

}catch(Throwable $e){
  header('Location: ../verify_token.php?e=1');
  exit;
}
