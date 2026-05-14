<?php
include(__DIR__."/../../SETTINGS/php/bd.php"); // Ajusta según tu ruta real
$conn = ConexionBD();
if (isset($_GET['idRol'])) {
    $idRol = $_GET['idRol'];

    $query = "SELECT p.descripcion 
              FROM permiso p
              INNER JOIN rol_permiso rp ON p.id_permiso = rp.id_permiso
              WHERE rp.id_rol = :idRol";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
    $stmt->execute();

    $permisos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'success' => true,
        'permisos' => $permisos
    ]);
} else {
    echo json_encode([
        'success' => false,
        'permisos' => []
    ]);
}
?>
