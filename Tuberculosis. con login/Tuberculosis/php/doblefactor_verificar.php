<?php
session_start();
require_once '../SETTINGS/php/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['2fa'])) {
  header('Location: ../login.php');
  exit;
}

$token  = isset($_POST['token']) ? trim($_POST['token']) : '';
$correo = $_SESSION['2fa']['correo'] ?? '';

if ($token === '' || !preg_match('/^\d{6}$/', $token) || $correo === '') {
  header('Location: ../doblefactor.php?e=1');
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

      // Cargar roles (como en tu login original)
      $roles = [];
      $qRoles = "SELECT r.id_rol, r.descripcion
                 FROM rol_usuario ru
                 JOIN rol r ON r.id_rol = ru.id_rol
                 WHERE ru.id_usuario = ?";
      $sr = $conn->prepare($qRoles);
      $sr->bind_param('i', $_SESSION['2fa']['id_usuario']);
      $sr->execute();
      $rr = $sr->get_result();
      while ($rw = $rr->fetch_assoc()) { $roles[] = $rw; }

      // Crear sesión final
      $_SESSION['user'] = [
        'id'       => $_SESSION['2fa']['id_usuario'],
        'nombre'   => $_SESSION['2fa']['nombre'],
        'apellido' => $_SESSION['2fa']['apellido'],
        'correo'   => $_SESSION['2fa']['correo'],
        'roles'    => $roles
      ];

      // Limpiar token y estado 2FA
      $upd = $conn->prepare("UPDATE password_resets SET usado=1 WHERE id=? LIMIT 1");
      $idRow = (int)$row['id'];
      $upd->bind_param('i', $idRow);
      $upd->execute();

      unset($_SESSION['2fa']);

      header('Location: ../index.php');
      exit;
    }
  }

  header('Location: ../doblefactor.php?e=1');
  exit;

}catch(Throwable $e){
  header('Location: ../doblefactor.php?e=1');
  exit;
}
