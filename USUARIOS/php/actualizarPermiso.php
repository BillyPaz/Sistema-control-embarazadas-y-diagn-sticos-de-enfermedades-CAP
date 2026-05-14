<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$idPermiso = $_POST['idPermiso'];
$nombrePermiso = $_POST['nombrePermiso'];

$estado = $_POST['estado'];
$descripcion = $_POST['descripcion'];


$query = "UPDATE permiso set descripcion = :nombrePermiso, observaciones = :descripcion, activo = :estado where id_permiso = :idPermiso";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idPermiso', $idPermiso);
$stmt->bindParam(':nombrePermiso',$nombrePermiso);
$stmt->bindParam(':estado', $estado);
$stmt->bindParam(':descripcion', $descripcion);


$stmt->execute();

echo json_encode([
    'success'=>true
]);






?>
