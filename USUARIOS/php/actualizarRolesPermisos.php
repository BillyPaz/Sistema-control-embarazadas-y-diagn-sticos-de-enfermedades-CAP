<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['idRol'])) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

$idRol = (int)$data['idRol'];
$permisos = isset($data['permisos']) && is_array($data['permisos']) ? $data['permisos'] : [];

// 🔹 Eliminar todos los permisos actuales del rol
$queryDel = "DELETE FROM rol_permiso WHERE id_rol = :idRol";
$stmtDel = $conn->prepare($queryDel);
$stmtDel->bindParam(':idRol', $idRol, PDO::PARAM_INT);
$stmtDel->execute();

// 🔹 Insertar los nuevos permisos, evitando duplicados
if (!empty($permisos)) {
    $queryIns = "INSERT INTO rol_permiso (id_rol, id_permiso) VALUES (:idRol, :idPermiso)";
    $stmtIns = $conn->prepare($queryIns);

    foreach (array_unique($permisos) as $permisoId) {
        $stmtIns->bindParam(':idRol', $idRol, PDO::PARAM_INT);
        $stmtIns->bindParam(':idPermiso', $permisoId, PDO::PARAM_INT);
        $stmtIns->execute();
    }
}

echo json_encode([
    'success' => true,
    'deleted' => $stmtDel->rowCount(),
    'inserted' => count($permisos)
]);
?>
