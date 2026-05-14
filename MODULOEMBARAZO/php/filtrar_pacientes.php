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

  // subconsulta: último control por registro (embarazo)
  // rn=1 => fila más reciente (por fecha_visita desc, y si es null, por id desc)
  // mysql 8+ (tu server 8.0.42): soporta window functions.
  $base = "
    with ult as (
      select
        sp.*,
        row_number() over (
          partition by sp.id_registro_clinico_prenatal
          order by coalesce(sp.fecha_visita,'1900-01-01') desc, sp.id_seguimiento_prenatal desc
        ) as rn
      from seguimiento_prenatal sp
    )
    select
      concat(p.nombres_pacientes,' ',p.apellidos_pacientes) as paciente,
      p.dpi_pacientes as dpi,
      p.telefono as telefono,
      p.direccion as direccion,
      u.semanas_embarazo_fur as semanas,
      u.circunferencia_brazo as circ_brazo,
      u.masa_corporal as imc,
      u.fecha_visita as fecha_visita,
      r.no_expediente as no_expediente
    from ult u
    join registro_clinico_prenatal r on r.id_registro_clinico_prenatal=u.id_registro_clinico_prenatal
    join pacientes p on p.id_pacientes = r.id_pacientes
    where u.rn=1
  ";

  // armamos el where por modo
  if($mode==='cb'){
    // < 12 semanas + rango de circunferencia de brazo
    $sql = $base . " and (u.semanas_embarazo_fur is not null and u.semanas_embarazo_fur < 12)
                     and (u.circunferencia_brazo between ? and ?)
                     order by u.semanas_embarazo_fur asc, u.circunferencia_brazo asc";
  } else {
    // ≥ 12 semanas + rango de imc (usamos masa_corporal como imc)
    $sql = $base . " and (u.semanas_embarazo_fur is not null and u.semanas_embarazo_fur >= 12)
                     and (u.masa_corporal between ? and ?)
                     order by u.semanas_embarazo_fur asc, u.masa_corporal asc";
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
