<?php
include("conexion.php");    
$conn = ConexionBD();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    try {
        $conn->beginTransaction(); // ✅ Agregado

        $sql = "INSERT INTO modulo_vih 
                (servicio_envio, servicio_refiere, fecha_traslado, historial_enfermedad,
                peso, talla, pulso, presion_arterial, frecuencia_respiratoria, tension_arterial,
                examenes_realizados, motivo_referencia, impresion_clinica, id_paciente) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data['servicioEnvia'], $data['servicioRefiere'], $data['fechaTraslado'],
            $data['historiaProblemaActual'], $data['peso'], $data['talla'], $data['pulso'],
            $data['presionArterial'], $data['frecuenciaRespiratoria'], $data['tensionArterial'],
            $data['examenesRealizados'], $data['motivoReferencia'],$data['impresionClinica'], $data['idPaciente']
        ]);



        $conn->commit(); // ✅ Ya no lanza error

        echo json_encode([
            'success' => true,
            'message' => 'Registro prenatal guardado correctamente'
        ]);
    } catch (Exception $e) {
        $conn->rollBack(); // ✅ Ahora funciona porque hiciste beginTransaction()

        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar el registro: ' . $e->getMessage()
        ]);
    }

    $conn = null; // ✅ Correcto para cerrar PDO
}
?>
