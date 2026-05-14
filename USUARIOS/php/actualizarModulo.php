<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$idModulo = $_POST['idModulo'];
$nombreModuloEdit = $_POST['nombreModuloEdit'];
$descripcionEdit = $_POST['descripcionEdit'];
$moduloIconoEdit = $_POST['moduloIconoEdit'];
$estado = $_POST['estado'];

$query = "UPDATE modulo set nombre = :nombreModuloEdit, descripcion = :descripcionEdit, icono =:moduloIconoEdit, activo = :estado where id_modulo = :idModulo";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idModulo', $idModulo);
$stmt->bindParam(':nombreModuloEdit', $nombreModuloEdit);
$stmt->bindParam(':descripcionEdit', $descripcionEdit);
$stmt->bindParam(':moduloIconoEdit', $moduloIconoEdit);
$stmt->bindParam(':estado', $estado);
$stmt->execute();

if($stmt){
    echo json_encode([
        'success'=>true
    ]);
}

?>