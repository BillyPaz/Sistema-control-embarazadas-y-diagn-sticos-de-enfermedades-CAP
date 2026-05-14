<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];


try {
    $query = "INSERT INTO permiso(descripcion, observaciones)
              VALUES(:nombre,:descripcion)";
    $stmt = $conn->prepare($query);
      $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al insertar: ' . $e->getMessage()]);
}

?>