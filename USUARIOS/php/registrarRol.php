<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$nombreRol = $_POST['nombreRol'];
$descripcion = $_POST['descripcion'];
try{


$query = "INSERT INTO rol(descripcion, observaciones)values(:nombreRol, :descripcion)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':nombreRol', $nombreRol);
$stmt->bindParam(':descripcion', $descripcion);
$stmt->execute();

echo json_encode([
    'success'=>true
]);

}
catch(PDOException $e){
    error_log("ERROR",$e->getMessage());
   echo json_encode([
    'success'=>false,
    'error'=>$e->getMessage()
   ]);
}


?>