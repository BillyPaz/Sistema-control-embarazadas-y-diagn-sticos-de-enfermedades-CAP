<?php
include(__DIR__."/conexion.php");

$buscar = isset($_GET['term']) ? $_GET['term'] : '';

$sql = "SELECT p.id_pacientes, p.dpi_pacientes, p.nombres_pacientes, p.apellidos_pacientes
        from pacientes p
        left join registro_tuberculosis rt on p.id_pacientes = rt.id_pacientes
        where rt.id_pacientes is null";

if ($buscar != '') {
  $buscar = $conn->real_escape_string($buscar);
  $sql .= " AND (p.nombres_pacientes LIKE '%$buscar%' OR p.dpi_pacientes LIKE '%$buscar%')";
}

$resultado = $conn->query($sql);

$pacientes = [];
while ($row = $resultado->fetch_assoc()) {
  $pacientes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($pacientes);
?>
