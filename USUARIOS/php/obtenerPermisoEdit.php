<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$idPermiso = $_GET['idPermiso'];

$query = "SELECT * FROM permiso where id_permiso = :idPermiso";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idPermiso', $idPermiso);
$stmt->execute();
$permiso = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success'=>true,
    'permiso'=>$permiso
])
?>