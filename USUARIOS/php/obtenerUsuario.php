<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$idUsuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null;

$query ="SELECT * FROM usuario WHERE id_usuario = :id_usuario";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'usuario' => $usuario
]);

?>