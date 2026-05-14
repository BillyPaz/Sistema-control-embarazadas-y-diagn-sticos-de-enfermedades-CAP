<?php
include("conexion.php");

$idPaciente = $_POST['id_paciente'];

$sql = "SELECT COUNT(*) as total FROM registro_tuberculosis WHERE id_pacientes = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idPaciente);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode(['existe' => $data['total'] > 0]);
?>
