<?php
include(__DIR__."/conexion.php");
$conn = ConexionBD();

$idPaciente = $_GET['idPaciente']; 

$query = "SELECT * FROM modulo_vih 
inner join pacientes on modulo_vih.id_paciente = pacientes.id_pacientes
WHERE id_paciente = :idPaciente";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idPaciente', $idPaciente, PDO::PARAM_INT);
$stmt->execute();
$detalle = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode([
    'success' => true,
    'detalle' => $detalle
]); 


?>