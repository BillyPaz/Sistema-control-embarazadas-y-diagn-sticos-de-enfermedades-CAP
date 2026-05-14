<?php
require_once '../../SETTINGS/php/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success'=>false,'message'=>'Método inválido']); exit;
}

$payload   = json_decode(file_get_contents('php://input'), true);
$idRegistro = isset($payload['idRegistro']) ? (int)$payload['idRegistro'] : 0;
$controles  = $payload['controles'] ?? [];

if ($idRegistro <= 0) {
  echo json_encode(['success'=>false,'message'=>'Registro clínico inválido']); exit;
}

function nullIfEmpty($v) {
  if ($v === null) return null;
  $v = trim((string)$v);
  return $v === '' ? null : $v;
}
function toNullInt($v) {
  if ($v === null || $v === '') return null;
  return (int)$v;
}
function toNullFloat($v) {
  if ($v === null || $v === '') return null;
  return (float)$v;
}

try {
  $conexion = new Conexion();
  $conn = $conexion->getConnection();
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  $conn->begin_transaction();

  // Traer FUR del registro clínico
  $fur = null;
  $stmtF = $conn->prepare("SELECT FUR FROM registro_clinico_prenatal WHERE ID_REGISTRO_CLINICO_PRENATAL=?");
  $stmtF->bind_param("i", $idRegistro);
  $stmtF->execute();
  $resF = $stmtF->get_result()->fetch_assoc();
  if ($resF && !empty($resF['FUR'])) $fur = $resF['FUR'];
  $stmtF->close();

  // UPSERT por control (ahora con los nuevos campos)
$sql = "insert into seguimiento_prenatal
          (control_num, fur, fecha_probable_parto,
           circunferencia_brazo, masa_corporal, fecha_visita,
           sintoma_peligro, presion_arterial, temperatura_corporal, peso_libras,
           respiraciones_minuto, fecuencia_cardiaca, hemoglobina,
           orina, vdrl, vih, papanicolao, infecciones,
           problema_detectado, sulfato_ferroso, acido_folico, vacuna_dosis,
           semanas_embarazo_fur, id_registro_clinico_prenatal)
          values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
          on duplicate key update
            fur=values(fur),
            fecha_probable_parto=values(fecha_probable_parto),
            circunferencia_brazo=values(circunferencia_brazo),
            masa_corporal=values(masa_corporal),
            fecha_visita=values(fecha_visita),
            sintoma_peligro=values(sintoma_peligro),
            presion_arterial=values(presion_arterial),
            temperatura_corporal=values(temperatura_corporal),
            peso_libras=values(peso_libras),
            respiraciones_minuto=values(respiraciones_minuto),
            fecuencia_cardiaca=values(fecuencia_cardiaca),
            hemoglobina=values(hemoglobina),
            orina=values(orina),
            vdrl=values(vdrl),
            vih=values(vih),
            papanicolao=values(papanicolao),
            infecciones=values(infecciones),
            problema_detectado=values(problema_detectado),
            sulfato_ferroso=values(sulfato_ferroso),
            acido_folico=values(acido_folico),
            vacuna_dosis=values(vacuna_dosis),
            semanas_embarazo_fur=values(semanas_embarazo_fur)";

  // 24 parámetros
  // tipos: i s s d d s s s d d i i d s s i s s s i i i i i
  $types = "issddsssddiidssisssiiiii";
  $stmt  = $conn->prepare($sql);

  $insertados = 0;

  foreach ($controles as $c) {
    $num = isset($c['controlNum']) ? (int)$c['controlNum'] : 0;
    if ($num < 1 || $num > 8) continue;

    // si todo vacío, no guardamos
    $allEmpty = (
      nullIfEmpty($c['fechaVisita'] ?? null) === null &&
      nullIfEmpty($c['pa'] ?? null) === null &&
      toNullFloat($c['circBrazo'] ?? null) === null &&
      toNullFloat($c['masa'] ?? null) === null &&
      toNullFloat($c['temp'] ?? null) === null &&
      toNullFloat($c['peso'] ?? null) === null &&
      toNullInt($c['resp'] ?? null) === null &&
      toNullInt($c['fc'] ?? null) === null &&
      toNullFloat($c['hb'] ?? null) === null &&
      nullIfEmpty($c['orina'] ?? null) === null &&
      nullIfEmpty($c['vdrl'] ?? null) === null &&
      nullIfEmpty($c['problema'] ?? null) === null &&
      toNullInt($c['sf'] ?? null) === null &&
      toNullInt($c['af'] ?? null) === null &&
      nullIfEmpty($c['sintomaPeligro'] ?? null) === null &&
      toNullInt($c['vih'] ?? null) === null &&
      nullIfEmpty($c['papanicolao'] ?? null) === null &&
      nullIfEmpty($c['infecciones'] ?? null) === null &&
      toNullInt($c['vacunaDosis'] ?? null) === null
    );
    if ($allEmpty) continue;

    // variables
    $CONTROL_NUM = $num;                   // i
    $FUR         = $fur;                   // s
    $FPP         = null;                   // s
    if ($FUR) {
      $fppDate = new DateTime($FUR);
      $fppDate->modify('+280 day');
      $FPP = $fppDate->format('Y-m-d');
    }

    $CIRC_BRAZO   = toNullFloat($c['circBrazo'] ?? null);        // d
    $MASA_CORP    = toNullFloat($c['masa'] ?? null);             // d
    $FECHA_VIS    = nullIfEmpty($c['fechaVisita'] ?? null);      // s
    $SINT_PELIGRO = nullIfEmpty($c['sintomaPeligro'] ?? null);   // s  ('SI'/'NO')
    $PA           = nullIfEmpty($c['pa'] ?? null);               // s
    $TEMP_CORP    = toNullFloat($c['temp'] ?? null);             // d
    $PESO_LIB     = toNullFloat($c['peso'] ?? null);             // d
    $RESP_MIN     = toNullInt($c['resp'] ?? null);               // i
    $FCARD        = toNullInt($c['fc'] ?? null);                 // i
    $HEMO         = toNullFloat($c['hb'] ?? null);               // d
    $ORINA        = nullIfEmpty($c['orina'] ?? null);            // s
    $VDRL         = nullIfEmpty($c['vdrl'] ?? null);             // s
    $VIH          = toNullInt($c['vih'] ?? null);                // i (1 sí, 0 no)
    $PAPANI       = nullIfEmpty($c['papanicolao'] ?? null);      // s
    $INFECC       = nullIfEmpty($c['infecciones'] ?? null);      // s
    $PROBLEMA     = nullIfEmpty($c['problema'] ?? null);         // s
    $SF           = toNullInt($c['sf'] ?? null);                 // i
    $AF           = toNullInt($c['af'] ?? null);                 // i
    $VAC_DOSIS    = toNullInt($c['vacunaDosis'] ?? null);        // i

    // semanas
    $SEMANAS = null;                                            // i
    if ($FUR && $FECHA_VIS) {
      $d1 = new DateTime($FUR);
      $d2 = new DateTime($FECHA_VIS);
      $days = $d1->diff($d2)->days;
      $SEMANAS = (int) floor($days / 7);
      if ($SEMANAS < 0) $SEMANAS = 0;
    }

    $ID_REG = $idRegistro;                                     // i

    // bind
    $stmt->bind_param(
      $types,
      $CONTROL_NUM,  // 1 i
      $FUR,          // 2 s
      $FPP,          // 3 s
      $CIRC_BRAZO,   // 4 d
      $MASA_CORP,    // 5 d
      $FECHA_VIS,    // 6 s
      $SINT_PELIGRO, // 7 s
      $PA,           // 8 s
      $TEMP_CORP,    // 9 d
      $PESO_LIB,     //10 d
      $RESP_MIN,     //11 i
      $FCARD,        //12 i
      $HEMO,         //13 d
      $ORINA,        //14 s
      $VDRL,         //15 s
      $VIH,          //16 i
      $PAPANI,       //17 s
      $INFECC,       //18 s
      $PROBLEMA,     //19 s
      $SF,           //20 i
      $AF,           //21 i
      $VAC_DOSIS,    //22 i
      $SEMANAS,      //23 i
      $ID_REG        //24 i
    );

    $stmt->execute();
    $insertados++;
  }

  $stmt->close();
  $conn->commit();

  echo json_encode(['success'=>true, 'message'=>"Controles procesados: $insertados"]);

} catch (Throwable $e) {
  if (isset($conn)) { $conn->rollback(); }
  http_response_code(500);
  echo json_encode([
    'success'=>false,
    'message'=>'Error al guardar controles: '.$e->getMessage()
  ]);
}
