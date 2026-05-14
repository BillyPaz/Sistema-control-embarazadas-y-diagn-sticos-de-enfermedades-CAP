<?php


include("../../SETTINGS/php/conexion.php");
$conn = ConexionBD();

$query = "SELECT p.id_pacientes, CONCAT(p.nombres_pacientes, ' ' , p.apellidos_pacientes) as pacienteNombre, p.fecha_nacimiento, TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) AS edad,  p.direccion, p.dpi_pacientes, ep.descripcion from pacientes p
inner join estado_paciente as ep on ep.id_estado_pacientes = p.id_estado_pacientes";
$stmt = $conn->prepare($query);
$stmt->execute();
$listPacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success'=>true,
    'listPacientes'=>$listPacientes
]);


?>