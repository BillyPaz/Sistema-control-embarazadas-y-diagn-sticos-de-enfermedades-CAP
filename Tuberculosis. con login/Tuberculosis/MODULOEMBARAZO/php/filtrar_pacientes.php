<?php
require_once '../../SETTINGS/php/conexion.php';
header('Content-Type: application/json');

function numOrNull($v, $def=null){
  if($v===null || $v==='') return $def;
  return is_numeric($v) ? $v+0 : $def;
}

try{
  $mode = isset($_GET['mode']) ? $_GET['mode'] : '';
  $min  = numOrNull($_GET['min'] ?? null, 0);
  $max  = numOrNull($_GET['max'] ?? null, 9999);

  if($mode!=='cb' && $mode!=='imc'){
    echo json_encode(['success'=>false,'message'=>'Parámetro mode inválido (cb|imc)']); exit;
  }

  $conexion = new Conexion();
  $conn = $conexion->getConnection();
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  // Subconsulta: último control por registro (embarazo)
  // rn=1 => fila más reciente (por FECHA_VISITA desc, y si es null, por ID desc)
  // MySQL 8+ (tu server 8.0.42): soporta window functions.
  $base = "
    WITH ult AS (
      SELECT
        sp.*,
        ROW_NUMBER() OVER (
          PARTITION BY sp.ID_REGISTRO_CLINICO_PRENATAL
          ORDER BY COALESCE(sp.FECHA_VISITA,'1900-01-01') DESC, sp.ID_SEGUIMIENTO_PRENATAL DESC
        ) AS rn
      FROM seguimiento_prenatal sp
    )
    SELECT
      CONCAT(p.NOMBRES_PACIENTES,' ',p.APELLIDOS_PACIENTES) AS paciente,
      p.DPI_PACIENTES AS dpi,
      p.TELEFONO AS telefono,
      p.DIRECCION AS direccion,
      u.SEMANAS_EMBARAZO_FUR AS semanas,
      u.CIRCUNFERENCIA_BRAZO AS circ_brazo,
      u.MASA_CORPORAL AS imc,
      u.FECHA_VISITA AS fecha_visita,
      r.NO_EXPEDIENTE AS no_expediente
    FROM ult u
    JOIN registro_clinico_prenatal r ON r.ID_REGISTRO_CLINICO_PRENATAL=u.ID_REGISTRO_CLINICO_PRENATAL
    JOIN pacientes p ON p.ID_PACIENTES = r.id_pacientes
    WHERE u.rn=1
  ";

  // Armamos el WHERE por modo
  if($mode==='cb'){
    // < 12 semanas + rango de circunferencia de brazo
    $sql = $base . " AND (u.SEMANAS_EMBARAZO_FUR IS NOT NULL AND u.SEMANAS_EMBARAZO_FUR < 12)
                     AND (u.CIRCUNFERENCIA_BRAZO BETWEEN ? AND ?)
                     ORDER BY u.SEMANAS_EMBARAZO_FUR ASC, u.CIRCUNFERENCIA_BRAZO ASC";
  } else {
    // ≥ 12 semanas + rango de IMC (usamos MASA_CORPORAL como IMC)
    $sql = $base . " AND (u.SEMANAS_EMBARAZO_FUR IS NOT NULL AND u.SEMANAS_EMBARAZO_FUR >= 12)
                     AND (u.MASA_CORPORAL BETWEEN ? AND ?)
                     ORDER BY u.SEMANAS_EMBARAZO_FUR ASC, u.MASA_CORPORAL ASC";
  }

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("dd", $min, $max);
  $stmt->execute();
  $res = $stmt->get_result();

  $rows=[];
  while($r = $res->fetch_assoc()){
    $rows[] = $r;
  }

  echo json_encode(['success'=>true,'rows'=>$rows]);

  $stmt->close();
  $conexion->close();

}catch(Throwable $e){
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
