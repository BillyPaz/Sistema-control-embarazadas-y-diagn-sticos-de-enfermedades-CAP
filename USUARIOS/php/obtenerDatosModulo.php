<?php
session_start();

if(isset($_SESSION['user'])){
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();


$query ="SELECT * FROM modulo";

$stmt = $conn->prepare($query);
$stmt->execute();
$modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$query2 = "SELECT COUNT(*) AS total_modulos,
    COUNT(CASE WHEN m.activo = 1 THEN 1 END) AS modulo_activos,
    COUNT(CASE WHEN m.activo = 0 THEN 1 END) AS modulo_inactivos from modulo m";
$stm2 = $conn->prepare($query2);
$stm2->execute();
$totalModulos = $stm2->fetch(PDO::FETCH_ASSOC);


echo json_encode([
    'success'=>true,
    'modulos'=>$modulos,
    'totalModulos'=>$totalModulos
]);
}
?>