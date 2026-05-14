<?php
session_start();
require_once '../SETTINGS/php/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !isset($_SESSION['reset_ok']) ||
    !isset($_SESSION['reset_correo']) ||
    !isset($_SESSION['reset_token']) ||
    !isset($_SESSION['reset_id'])) {
  header('Location: ../recuperar.php');
  exit;
}

$correo = $_SESSION['reset_correo'];
$token  = $_SESSION['reset_token'];
$rid    = (int)$_SESSION['reset_id'];

$pass1 = isset($_POST['pass1']) ? $_POST['pass1'] : '';
$pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';

if ($pass1 === '' || $pass2 === '' || $pass1 !== $pass2) {
  header('Location: reset.php?e=1');
  exit;
}

try{
  $conexion = new Conexion();
  $conn = $conexion->getConnection();

  $sql = "SELECT id, expira, usado FROM password_resets
          WHERE id = ? AND correo = ? AND token = ? AND usado = 0 LIMIT 1";
  $st  = $conn->prepare($sql);
  $st->bind_param('iss', $rid, $correo, $token);
  $st->execute();
  $rs = $st->get_result();

  if (!$rs || $rs->num_rows !== 1) {
    header('Location: reset.php?e=1');
    exit;
  }
  $row = $rs->fetch_assoc();
  if (strtotime($row['expira']) < time()) {
    header('Location: reset.php?e=1');
    exit;
  }

  $upd = $conn->prepare("UPDATE usuario SET password=? WHERE correo=? LIMIT 1");
  $upd->bind_param('ss', $pass1, $correo);
  $ok1 = $upd->execute();

  $upd2 = $conn->prepare("UPDATE password_resets SET usado=1 WHERE id=? LIMIT 1");
  $upd2->bind_param('i', $rid);
  $ok2 = $upd2->execute();

  unset($_SESSION['reset_ok'], $_SESSION['reset_correo'], $_SESSION['reset_token'], $_SESSION['reset_id']);

  if ($ok1 && $ok2) {
    header('Location: ../login.php?ok=1');
    exit;
  } else {
    header('Location: reset.php?e=1');
    exit;
  }

}catch(Throwable $e){
  header('Location: reset.php?e=1');
  exit;
}
