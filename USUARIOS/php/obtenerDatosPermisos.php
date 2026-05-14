<?php
session_start();
if(isset($_SESSION['user'])){
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$query2 = "SELECT * FROM permiso";
$stmt2= $conn->prepare($query2);
$stmt2->execute();
$listPermisos = $stmt2->fetchAll(PDO::FETCH_ASSOC);




$query3 ="SELECT
    COUNT(*) AS total_permisos,
    COUNT(CASE WHEN p.activo = 1 THEN 1 END) AS permisos_activos,
    COUNT(CASE WHEN p.activo = 0 THEN 1 END) AS permisos_inactivos
FROM permiso p;";
$stmt3= $conn->prepare($query3);
$stmt3->execute();
$conteoPermisos = $stmt3->fetch(PDO::FETCH_ASSOC);




echo json_encode([
    'success'=>true,
       'listPermisos'=>$listPermisos,
    'totalPermisos'=>$conteoPermisos
]);
}


?>