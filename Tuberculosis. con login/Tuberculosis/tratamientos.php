<?php
include("conexion.php");

if (!isset($_GET['id_paciente'])) die("Error: No se seleccionó ningún paciente.");
$id_paciente = intval($_GET['id_paciente']);

$sqlPaciente = "SELECT NOMBRES_PACIENTES, APELLIDOS_PACIENTES FROM PACIENTES WHERE ID_PACIENTES = ?";
$stmtPaciente = $conn->prepare($sqlPaciente);
$stmtPaciente->bind_param("i", $id_paciente);
$stmtPaciente->execute();
$paciente = $stmtPaciente->get_result()->fetch_assoc();
$stmtPaciente->close();
if (!$paciente) die("Paciente no encontrado.");

$sqlTratamiento = "SELECT t.ID_SEGUIMIENTO, t.ID_FASE, t.DOSIS_RECIBIDA, t.DOSIS_PENDIENTES, f.NOMBRE_FASE, f.TOTAL_DOSIS, t.FECHA_INICIO
                   FROM Seguimiento_medicamentos_tb_TDO t
                   JOIN Fase_tb f ON t.ID_FASE = f.ID_FASE
                   WHERE t.ID_PACIENTES = ?
                   ORDER BY t.FECHA_INICIO DESC
                   LIMIT 1";
$stmtTratamiento = $conn->prepare($sqlTratamiento);
$stmtTratamiento->bind_param("i", $id_paciente);
$stmtTratamiento->execute();
$tratamiento = $stmtTratamiento->get_result()->fetch_assoc();
$stmtTratamiento->close();
if (!$tratamiento) die("No se encontró tratamiento para este paciente.");

setlocale(LC_TIME, "es_ES.UTF-8");
$anio = intval(date("Y"));
$mes = intval(date("m"));
$mesNombre = strtoupper(strftime("%B", mktime(0, 0, 0, $mes, 1, $anio)));
$totalDias = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
$fechaHoy = date("Y-m-d");

$sqlHistorial = "SELECT NUMERO_DOSIS, DATE(FECHA_REGISTRO) AS FECHA_REGISTRO
                 FROM Calendario_seguimiento_tb
                 WHERE ID_SEGUIMIENTO = ?
                 ORDER BY FECHA_REGISTRO ASC";
$stmtHistorial = $conn->prepare($sqlHistorial);
$stmtHistorial->bind_param("i", $tratamiento['ID_SEGUIMIENTO']);
$stmtHistorial->execute();
$resultHistorial = $stmtHistorial->get_result();

$dosisPorDia = []; 
while ($row = $resultHistorial->fetch_assoc()) {
    $fecha = $row['FECHA_REGISTRO']; 
    $dosisPorDia[$fecha] = intval($row['NUMERO_DOSIS']);
}
$stmtHistorial->close();

$sqlValidarHoy = "SELECT COUNT(*) AS cantidad FROM Calendario_seguimiento_tb WHERE ID_SEGUIMIENTO=? AND DATE(FECHA_REGISTRO)=CURDATE()";
$stmtValidarHoy = $conn->prepare($sqlValidarHoy);
$stmtValidarHoy->bind_param("i", $tratamiento['ID_SEGUIMIENTO']);
$stmtValidarHoy->execute();
$cantidadHoy = intval($stmtValidarHoy->get_result()->fetch_assoc()['cantidad']);
$stmtValidarHoy->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_dosis'])) {
    $nueva_dosis = intval($_POST['nueva_dosis']);
    $observaciones = trim($_POST['observaciones']);

    $stmtValidarHoy2 = $conn->prepare($sqlValidarHoy);
    $stmtValidarHoy2->bind_param("i", $tratamiento['ID_SEGUIMIENTO']);
    $stmtValidarHoy2->execute();
    $cantidadHoyPost = intval($stmtValidarHoy2->get_result()->fetch_assoc()['cantidad']);
    $stmtValidarHoy2->close();

    if ($cantidadHoyPost > 0) {
        echo "<script>alert('Ya se registró una dosis hoy.');</script>";
    } elseif ($nueva_dosis <= 0) {
        echo "<script>alert('Ingrese una dosis válida.');</script>";
    } else {
        $sqlSumBefore = "SELECT COALESCE(SUM(NUMERO_DOSIS),0) AS sum_before FROM Calendario_seguimiento_tb WHERE ID_SEGUIMIENTO = ?";
        $stmtSumBefore = $conn->prepare($sqlSumBefore);
        $stmtSumBefore->bind_param("i", $tratamiento['ID_SEGUIMIENTO']);
        $stmtSumBefore->execute();
        $rowSumBefore = $stmtSumBefore->get_result()->fetch_assoc();
        $sum_before = intval($rowSumBefore['sum_before']);
        $stmtSumBefore->close();

        $sqlInsert = "INSERT INTO Calendario_seguimiento_tb (ID_SEGUIMIENTO, NUMERO_DOSIS, FECHA_REGISTRO) VALUES (?, ?, NOW())";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("ii", $tratamiento['ID_SEGUIMIENTO'], $nueva_dosis);
        $stmtInsert->execute();
        $stmtInsert->close();

        $sqlSumAfter = "SELECT COALESCE(SUM(NUMERO_DOSIS),0) AS sum_after FROM Calendario_seguimiento_tb WHERE ID_SEGUIMIENTO = ?";
        $stmtSumAfter = $conn->prepare($sqlSumAfter);
        $stmtSumAfter->bind_param("i", $tratamiento['ID_SEGUIMIENTO']);
        $stmtSumAfter->execute();
        $rowSumAfter = $stmtSumAfter->get_result()->fetch_assoc();
        $sum_after = intval($rowSumAfter['sum_after']);
        $stmtSumAfter->close();
       
        $dosis_previas_en_seguimiento = intval($tratamiento['DOSIS_RECIBIDA']);
        $total_recibida = $dosis_previas_en_seguimiento + ($sum_after - $sum_before);
        if ($total_recibida < 0) $total_recibida = 0;

        $dosis_pendientes = max(0, intval($tratamiento['TOTAL_DOSIS']) - $total_recibida);
        $sqlUpdate = "UPDATE Seguimiento_medicamentos_tb_TDO SET DOSIS_RECIBIDA=?, DOSIS_PENDIENTES=?, OBSERVACIONES=? WHERE ID_SEGUIMIENTO=?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("iisi", $total_recibida, $dosis_pendientes, $observaciones, $tratamiento['ID_SEGUIMIENTO']);
        $stmtUpdate->execute();
        $stmtUpdate->close();
        echo "<script>alert('Dosis registrada correctamente.');window.location.href='tratamientos.php?id_paciente=$id_paciente';</script>";
        exit;
    }
    $stmtValidarHoy = $conn->prepare($sqlValidarHoy);
    $stmtValidarHoy->bind_param("i", $tratamiento['ID_SEGUIMIENTO']);
    $stmtValidarHoy->execute();
    $cantidadHoy = intval($stmtValidarHoy->get_result()->fetch_assoc()['cantidad']);
    $stmtValidarHoy->close();
}

$fechaInicio = $tratamiento['FECHA_INICIO']; 
$diasHabiles = [];
for ($d = 1; $d <= $totalDias; $d++) {
    $fecha = sprintf("%04d-%02d-%02d", $anio, $mes, $d);
    if (date("w", strtotime($fecha)) != 0 && $fecha >= $fechaInicio) { 
        $diasHabiles[$fecha] = $d;
    }
}

?>

<!DOCTYPE html>
    <html lang="es">
    <head>
    <meta charset="UTF-8">
    <title>Tratamiento Paciente</title>
    <link rel="stylesheet" href="css/calendario.css">
    </head>
    <body>
     <div id="contenido" ></div>
    <header class="header" ><div class="header-content container">
        <div class="header-txt">
            <h1>TRATAMIENTO</h1>
        </div>
    </div></header>
       <!-- <a href="pacientesT.php" class="btn">Volver</a>-->

      
        <div class="tratamiento-container">

            <h1>Tratamiento de Paciente</h1>
            <h2><?= htmlspecialchars($paciente['NOMBRES_PACIENTES'] . " " . $paciente['APELLIDOS_PACIENTES']) ?></h2>
            
            <div class="datos-paciente">
                <h3>Fase actual: <?= htmlspecialchars($tratamiento['NOMBRE_FASE']) ?></h3>
                <p><strong>Dosis recibidas:</strong> <?= intval($tratamiento['DOSIS_RECIBIDA']) ?></p>
                <p><strong>Dosis pendientes:</strong> <?= intval($tratamiento['DOSIS_PENDIENTES']) ?></p>
            </div>

            <h3>Agregar nueva dosis</h3>
                <?php if ($cantidadHoy > 0): ?>
                    <p>Ya se registró una dosis hoy. No se puede agregar otra.</p>
                <?php else: ?>
                <form method="POST" class="form-dosis">
                    <label>Número de dosis administrada:</label>
                    <input type="number" name="nueva_dosis" value="1" min="1" required>
                    <label>Observaciones:</label>
                    <textarea name="observaciones" required></textarea>
                    <button type="submit" id="btnRegistrar" name="agregar_dosis">Registrar Dosis</button>
                </form>

                <?php endif; ?>

            <h3>Calendario del tratamiento</h3>
                <div class="referencia">
                    <div class="referencia-item"><div class="referencia-box referencia-rojo"></div> Celda roja = no fue administrada</div>
                    <div class="referencia-item"><div class="referencia-box referencia-verde"></div> Celda verde = nuevas dosis administrada</div>
                    <div class="referencia-item"><div class="referencia-box referencia-gris"></div> Celda amarillo = pendiente de administrar</div>
                </div>
        </div>
        
        <table class="calendario">
            <tr>
                <th>NO</th>
                <th><?= $mesNombre ?></th>
                <?php foreach($diasHabiles as $fecha => $diaNum): ?>
                    <th><?= $diaNum ?></th>
                <?php endforeach; ?>
            </tr>

            <?php
            $contadorNo = 1;
            foreach($diasHabiles as $fechaFila => $diaNum):
                $numDosis = $dosisPorDia[$fechaFila] ?? 0;
            ?>
            <tr>
                <td><?= $contadorNo ?></td>
                <td><?= $numDosis > 0 ? $numDosis . ' dosis' : '' ?></td>

                <?php foreach($diasHabiles as $fechaCol => $diaCol):
                    if ($fechaCol === $fechaFila) {
                        if ($numDosis > 0) {
                            $clase = 'dosis-si';
                            $contenido = $numDosis;
                        } elseif (strtotime($fechaCol) < strtotime($fechaHoy)) {
                            $clase = 'dosis-no';
                            $contenido = '';
                        } else {
                            $clase = 'dosis-futuro';
                            $contenido = '';
                        }
                        echo "<td class=\"$clase\">$contenido</td>";
                    } else {               
                        echo "<td></td>";
                    }
                endforeach; ?>
            </tr>
            <?php $contadorNo++; endforeach; ?>
        </table>
       
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector(".form-dosis");
            let confirmado = false; 

            if (form) {
                form.addEventListener("submit", function(e) {
                if (!confirmado) {  
                    e.preventDefault();

                    Swal.fire({
                    title: '⚠️ Advertencia',
                    text: '¿Está seguro de ingresar la nueva dosis? Tenga en cuenta que se bloqueará hasta la próxima fecha.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, registrar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545'
                    }).then((result) => {
                    if (result.isConfirmed) {
                            confirmado = true;   
                            const btn = document.getElementById("btnRegistrar");
                            form.requestSubmit(btn); 
                        }
                    });
                }
                });
            }
            });
        </script>

    </body>
</html>
