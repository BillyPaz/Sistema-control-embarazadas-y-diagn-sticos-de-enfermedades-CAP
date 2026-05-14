<?php

include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$nombre_usuario = $_POST['nombre_usuario'];
$apellido_usuario = $_POST['apellido_usuario'];
$direccion = $_POST['direccion']; 
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];
$activo = $_POST['activoUs'];
$id_usuario = $_POST['id_usuario'];

$query = "UPDATE usuario set nombre_usuario = :nombre_usuario, apellido_usuario = :apellido_usuario, direccion = :direccion, telefono = :telefono, correo = :correo, estado_usuario = :activo WHERE id_usuario = :id_usuario";
$stmt = $conn->prepare($query);
$stmt->bindParam(':nombre_usuario', $nombre_usuario);
$stmt->bindParam(':apellido_usuario', $apellido_usuario);
$stmt->bindParam(':direccion', $direccion);
$stmt->bindParam(':telefono', $telefono);
$stmt->bindParam(':correo', $correo);
$stmt->bindParam(':activo', $activo);
$stmt->bindParam(':id_usuario', $id_usuario);
if($stmt->execute()){
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false));
}

?>