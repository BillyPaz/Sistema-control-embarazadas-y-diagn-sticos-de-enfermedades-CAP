<?php
require_once '../../SETTINGS/php/conexion.php';
header('Content-Type: application/json');

$idRegistro = isset($_GET['idRegistro']) ? intval($_GET['idRegistro']) : 0;
if ($idRegistro <= 0) { echo json_encode([]); exit; }

$conexion = new Conexion();
$conn = $conexion->getConnection();


$sql = "select
          control_num,
          fur,
          fecha_probable_parto,
          circunferencia_brazo,
          masa_corporal,
          fecha_visita,
          sintoma_peligro,
          presion_arterial,
          temperatura_corporal,
          peso_libras,
          respiraciones_minuto,
          fecuencia_cardiaca,
          hemoglobina,
          orina,
          vdrl,
          vih,
          papanicolao,
          infecciones,
          semanas_embarazo_fur,
          problema_detectado,
          sulfato_ferroso,
          acido_folico,
          vacuna_dosis
        from seguimiento_prenatal
        where id_registro_clinico_prenatal = ?
        order by control_num asc";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idRegistro);
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($r = $res->fetch_assoc()) { $out[] = $r; }

echo json_encode($out);
$stmt->close();
$conexion->close();
