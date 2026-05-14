<?php
require_once '../../SETTINGS/php/conexion.php';
header('Content-Type: application/json');

$idPaciente = isset($_GET['idPaciente']) ? intval($_GET['idPaciente']) : 0;
if($idPaciente<=0){ echo json_encode([]); exit; }

$conexion = new Conexion();
$conn = $conexion->getConnection();

$sql = "select id_registro_clinico_prenatal, fecha_ingreso, no_expediente, fur
        from registro_clinico_prenatal
        where id_pacientes = ?
        order by fecha_ingreso desc";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$idPaciente);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while($r = $res->fetch_assoc()){ $out[] = $r; }

echo json_encode($out);
$stmt->close();
$conexion->close();
