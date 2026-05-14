<?php
require_once '../../SETTINGS/php/conexion.php';
header('Content-Type: application/json');

$idRegistro = isset($_GET['idRegistro']) ? intval($_GET['idRegistro']) : 0;
if ($idRegistro <= 0) { echo json_encode([]); exit; }

$conexion = new Conexion();
$conn = $conexion->getConnection();


$sql = "SELECT
          CONTROL_NUM,
          FUR,
          FECHA_PROBABLE_PARTO,
          CIRCUNFERENCIA_BRAZO,
          MASA_CORPORAL,
          FECHA_VISITA,
          SINTOMA_PELIGRO,
          PRESION_ARTERIAL,
          TEMPERATURA_CORPORAL,
          PESO_LIBRAS,
          RESPIRACIONES_MINUTO,
          FECUENCIA_CARDIACA,
          HEMOGLOBINA,
          ORINA,
          VDRL,
          VIH,
          PAPANICOLAO,
          INFECCIONES,
          SEMANAS_EMBARAZO_FUR,
          PROBLEMA_DETECTADO,
          SULFATO_FERROSO,
          ACIDO_FOLICO,
          VACUNA_DOSIS
        FROM seguimiento_prenatal
        WHERE ID_REGISTRO_CLINICO_PRENATAL = ?
        ORDER BY CONTROL_NUM ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idRegistro);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($r = $res->fetch_assoc()) { $out[] = $r; }

echo json_encode($out);
$stmt->close();
$conexion->close();
