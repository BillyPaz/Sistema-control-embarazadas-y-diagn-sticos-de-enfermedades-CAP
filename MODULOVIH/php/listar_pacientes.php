<?php
header('Content-Type: application/json'); 

include __DIR__.'/conexion.php';
$conn = ConexionBD();
$query = "SELECT 
            p.id_pacientes,
            CONCAT(p.nombres_pacientes, ' ', p.apellidos_pacientes) AS paciente, 
            p.fecha_nacimiento, 
            mv.servicio_envio,
            mv.servicio_refiere, 
            mv.peso, 
            mv.talla, 
            mv.pulso, 
            mv.fecha_traslado 
          FROM modulo_vih mv
          INNER JOIN pacientes p ON p.id_pacientes = mv.id_paciente";

$stmt = $conn->prepare($query);
$stmt->execute();

$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver la respuesta en formato JSON
echo json_encode([
    'pacientes' => $pacientes // Asegúrate de que coincida con JS: data.pacientes
]);
