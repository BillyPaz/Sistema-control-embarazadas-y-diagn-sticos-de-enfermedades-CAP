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
$password = $_POST['contrasena'];


$query = "SELECT COUNT(*) FROM usuario WHERE correo = :correo";
$stmt = $conn->prepare($query);
$stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
$stmt->execute();
$existeCorreo = $stmt->fetchColumn();

if ($existeCorreo > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'El correo ya está registrado'
    ]);
    exit;
}
else{


$query = "INSERT INTO usuario (nombre_usuario, apellido_usuario, direccion, telefono, correo, password) 
VALUES (:nombre_usuario, :apellido_usuario, :direccion, :telefono, :correo, :password)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':nombre_usuario', $nombre_usuario);
$stmt->bindParam(':apellido_usuario', $apellido_usuario);
$stmt->bindParam(':direccion', $direccion);
$stmt->bindParam(':telefono', $telefono);
$stmt->bindParam(':correo', $correo);
$stmt->bindParam(':password',$password);


if($stmt->execute()){
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false));
}
}
?>