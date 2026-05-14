<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$idUsuario = $_POST['idUsuario'];
$idRol = $_POST['idRol'];

try {
    // Verificar si ya existe la asignación
    $checkQuery = "SELECT COUNT(*) FROM rol_usuario WHERE id_usuario = :idUsuario";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':idUsuario', $idUsuario);
    $checkStmt->execute();
    $existe = $checkStmt->fetchColumn();

    if ($existe > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'El usuario ya tiene un rol asignado.'
        ]);
        exit;
    }

    // Si no existe, lo insertamos
    $query = "INSERT INTO rol_usuario(id_usuario, id_rol) VALUES(:idUsuario, :idRol)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':idUsuario', $idUsuario);
    $stmt->bindParam(':idRol', $idRol);
    $stmt->execute();

    echo json_encode([
        'success' => true
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al asignar rol: ' . $e->getMessage()
    ]);
}
