<?php
include(__DIR__."/../../SETTINGS/php/bd.php");

$idUsuario = $_POST['idUsuario'] ?? null;
$idRol = $_POST['idRol'] ?? null;

$response = ['success' => false];

if (!$idUsuario || !$idRol) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos.'
    ]);
    exit;
}

try {
    $conn = ConexionBD();

    // Verificar si ya tiene una asignación de rol
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM rol_usuario WHERE id_usuario = :idUsuario");
    $checkStmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $checkStmt->execute();
    $existe = $checkStmt->fetchColumn();


    if ($existe) {
        // Ya tiene rol, actualizamos
        $updateStmt = $conn->prepare("
            UPDATE rol_usuario 
            SET id_rol = :idRol
            WHERE id_usuario = :idUsuario
        ");
        $updateStmt->execute([
            ':idRol' => $idRol,
            ':idUsuario' => $idUsuario
        ]);
    } else {
        // No tiene rol, insertamos
        $insertStmt = $conn->prepare("
            INSERT INTO rol_usuario (id_usuario, id_rol) 
            VALUES (:idUsuario, :idRol)
        ");
        $insertStmt->execute([
            ':idUsuario' => $idUsuario,
            ':idRol' => $idRol
        ]);
    }

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
