<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();


$idUsuario = $_POST['idUsuario'] ?? null;

if (!$idUsuario) {
    echo json_encode(['success' => false, 'message' => 'ID de usuario no recibido']);
    exit;
}

try {

    $stmt = $conn->prepare("DELETE FROM rol_usuario WHERE id_usuario = :idUsuario");
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
