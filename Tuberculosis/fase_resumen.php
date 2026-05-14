<?php
include(__DIR__."/conexion.php");

$id = intval($_GET['id_paciente']);

$sql = "SELECT f.nombre_fase, s.dosis_recibida, s.dosis_pendientes
        FROM seguimiento_medicamentos_tb_tdo s
        JOIN fase_tb f ON s.ID_FASE = f.ID_FASE
        WHERE s.id_pacientes = $id
        ORDER BY s.fecha_inicio DESC
        LIMIT 1";

$result = $conn->query($sql);
if ($result->num_rows === 0) {
    echo json_encode(["fase" => "No registrado", "recibidas" => 0, "pendientes" => 0]);
} else {
    $row = $result->fetch_assoc();
    echo json_encode([
        "fase" => $row['nombre_fase'],
        "recibidas" => $row['dosis_recibida'],
        "pendientes" => $row['dosis_pendientes']
    ]);
}
?>
