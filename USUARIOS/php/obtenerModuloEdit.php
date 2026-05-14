<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$idModulo = $_GET['idModulo'];

$query = "SELECT * FROM modulo where id_modulo = :idModulo";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idModulo', $idModulo);
$stmt->execute();
$datoModulo = $stmt->fetch(PDO::FETCH_ASSOC);
if($datoModulo){
    echo json_encode([
        'success'=>true,
        'datoModulo'=>$datoModulo
    ]);
}


?>