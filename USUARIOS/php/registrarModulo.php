<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$nombreModulo = $_POST['nombreModulo'];
$descripcion = $_POST['descripcion'];
$icono = $_POST['icono'];

$query = "INSERT INTO modulo(nombre, descripcion, icono)values(:nombreModulo, :descripcion, :icono)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':nombreModulo', $nombreModulo);
$stmt->bindParam(':descripcion', $descripcion);
$stmt->bindParam(':icono', $icono);
$stmt->execute();

if($stmt){
    echo json_encode([
        'success'=>true
    ]);
}
else{
    echo json_encode([
        'success'=>false
    ]);
}
?>