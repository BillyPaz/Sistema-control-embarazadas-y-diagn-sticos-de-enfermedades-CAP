<?php
require_once '../../SETTINGS/php/conexion.php';
header('Content-Type: application/json');

$idPaciente = isset($_GET['idPaciente']) ? intval($_GET['idPaciente']) : 0;
if($idPaciente<=0){ echo json_encode([]); exit; }

$conexion = new Conexion();
$conn = $conexion->getConnection();

$sql = "SELECT ID_REGISTRO_CLINICO_PRENATAL, FECHA_INGRESO, NO_EXPEDIENTE, FUR
        FROM registro_clinico_prenatal
        WHERE id_pacientes = ?
        ORDER BY FECHA_INGRESO DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$idPaciente);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while($r = $res->fetch_assoc()){ $out[] = $r; }

echo json_encode($out);
$stmt->close();
$conexion->close();
