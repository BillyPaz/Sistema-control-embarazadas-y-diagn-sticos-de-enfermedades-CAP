<?php
include(__DIR__."/conexion.php");


$data = json_decode(file_get_contents("php://input"), true);
$id_seguimiento = intval($data['id_seguimiento']);
$resultado = $data['resultado'];
$fecha = date("Y-m-d");

// Obtener datos del seguimiento actual
$sql = "SELECT ID_PACIENTES FROM seguimiento_medicamentos_tb_tdo WHERE id_seguimiento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_seguimiento);
$stmt->execute();
$id_paciente = $stmt->get_result()->fetch_assoc()['dpi_pacientes'];
$stmt->close();

// Insertar evaluación
$sqleval = "insert into evaluaciones_tb (id_seguimiento, resultado, fecha_evaluacion) values (?, ?, ?)";
$stmteval = $conn->prepare($sqleval);
$stmteval->bind_param("iss", $id_seguimiento, $resultado, $fecha);
$stmteval->execute();
$stmteval->close();


// Determinar nueva fase y dosis
if ($resultado === 'NEGATIVO') {
    $id_fase = 2; 
    $total_dosis = 75;
    $mensaje = "El paciente ahora pasa a Fase 2, y sus dosis ahora son 75.";
} else {
    $id_fase = 3; 
    $total_dosis = 105;
    $mensaje = "Según los resultados, el paciente sigue en Fase 1, pero ahora las dosis son 105.";
}

// Crear nuevo seguimiento
$sqlnuevo = "insert into seguimiento_medicamentos_tb_tdo (id_pacientes, id_fase, fecha_inicio, dosis_recibida, dosis_pendientes, observaciones)
             values (?, ?, now(), 0, ?, ?)";
$stmtnuevo = $conn->prepare($sqlnuevo);
$obs = "nuevo tratamiento iniciado tras evaluación clínica.";
$stmtnuevo->bind_param("iiis", $id_paciente, $id_fase, $total_dosis, $obs);
$stmtnuevo->execute();
$stmtnuevo->close();

// Responder al frontend
echo json_encode(["mensaje" => $mensaje]);
?>
