<?php
include(__DIR__."/conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Verificar conexión
if (!$conn) {
    die("Error de conexión: " . $conn->connect_error);
}

if (!isset($_GET['id_paciente'])) die("Error: No se seleccionó ningún paciente.");
$id_paciente = intval($_GET['id_paciente']);

$sqlpaciente = "SELECT nombres_pacientes, apellidos_pacientes FROM pacientes WHERE id_pacientes = ?";
$stmtpaciente = $conn->prepare($sqlpaciente);
if (!$stmtpaciente) {
    die("Error en preparación: " . $conn->error);
}
$stmtpaciente->bind_param("i", $id_paciente);
$stmtpaciente->execute();
$resultadoPaciente = $stmtpaciente->get_result();
$paciente = $resultadoPaciente->fetch_assoc();
$stmtpaciente->close();

if (!$paciente) die("Paciente no encontrado.");

$sqltratamiento = "SELECT t.id_seguimiento, t.id_pacientes, t.id_fase, t.dosis_recibida, t.dosis_pendientes, f.nombre_fase, f.total_dosis, t.fecha_inicio
                   FROM seguimiento_medicamentos_tb_tdo t
                   JOIN fase_tb f ON t.id_fase = f.id_fase
                   WHERE t.id_pacientes = ?
                   ORDER BY t.fecha_inicio DESC
                   LIMIT 1";

$stmtTratamiento = $conn->prepare($sqltratamiento);
if (!$stmtTratamiento) {
    die("Error en preparación: " . $conn->error);
}
$stmtTratamiento->bind_param("i", $id_paciente);
$stmtTratamiento->execute();
$resultadoTratamiento = $stmtTratamiento->get_result();
$tratamiento = $resultadoTratamiento->fetch_assoc();
$stmtTratamiento->close();

if (!$tratamiento) die("No se encontró tratamiento para este paciente.");

// Configuración regional
setlocale(LC_TIME, "es_ES.UTF-8");

$anio = isset($_GET['anio']) ? intval($_GET['anio']) : intval(date("Y"));
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : intval(date("m"));

if ($mes < 1) {
    $mes = 12;
    $anio--;
} elseif ($mes > 12) {
    $mes = 1;
    $anio++;
}
$fmt = new IntlDateFormatter('es_ES', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'LLLL');
$mesNombre = strtoupper($fmt->format(mktime(0, 0, 0, $mes, 1, $anio)));

$totalDias = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
$fechaHoy = date("Y-m-d");

// Navegación entre meses
$mesAnterior = $mes - 1;
$anioAnterior = $anio;
if ($mesAnterior < 1) {
    $mesAnterior = 12;
    $anioAnterior--;
}

$mesSiguiente = $mes + 1;
$anioSiguiente = $anio;
if ($mesSiguiente > 12) {
    $mesSiguiente = 1;
    $anioSiguiente++;
}

// Validar si el mes anterior es válido
$fechaInicioTratamiento = new DateTime($tratamiento['fecha_inicio']);
$mesInicio = intval($fechaInicioTratamiento->format('m'));
$anioInicio = intval($fechaInicioTratamiento->format('Y'));
$mesAnteriorValido = !($anioAnterior < $anioInicio || ($anioAnterior == $anioInicio && $mesAnterior < $mesInicio));

// Obtener historial de dosis
$sqlhistorial = "SELECT numero_dosis, DATE(fecha_registro) as fecha_registro
                 FROM calendario_seguimiento_tb
                 WHERE id_seguimiento = ?
                 ORDER BY fecha_registro ASC";
$stmthistorial = $conn->prepare($sqlhistorial);
if (!$stmthistorial) {
    die("Error en preparación: " . $conn->error);
}
$stmthistorial->bind_param("i", $tratamiento['id_seguimiento']);
$stmthistorial->execute();
$resulthistorial = $stmthistorial->get_result();

$dosisPorDia = []; 
while ($row = $resulthistorial->fetch_assoc()) {
    $fecha = $row['fecha_registro']; 
    $dosisPorDia[$fecha] = intval($row['numero_dosis']);
}
$stmthistorial->close();

// Procesar dosis acumuladas y fechas críticas
$acumulado = 0;
$fechasCriticas = [];
$dosisAcumuladasPorFecha = [];

$fase = strtoupper(trim($tratamiento['nombre_fase']));
$criticas = [];

if ($fase === 'FASE 1') {
    $criticas = [25, 45];
} elseif ($fase === 'FASE 2') {
    $criticas = [25, 50, 75, 100];
}

$dosisRecibidas = intval($tratamiento['dosis_recibida']);
$acumulado = 0;

foreach ($dosisPorDia as $fecha => $dosis) {
    $acumulado += $dosis;
    $dosisAcumuladasPorFecha[$fecha] = $acumulado;

    if (in_array($acumulado, $criticas)) {
        $fechasCriticas[] = $fecha;
    }
}

// Validar y registrar dosis automática si es necesario
$sqlvalidarhoy = "SELECT COUNT(*) as cantidad FROM calendario_seguimiento_tb WHERE id_seguimiento = ? AND DATE(fecha_registro) = CURDATE()";
$stmtvalidarhoy = $conn->prepare($sqlvalidarhoy);
if (!$stmtvalidarhoy) {
    die("Error en preparación: " . $conn->error);
}
$stmtvalidarhoy->bind_param("i", $tratamiento['id_seguimiento']);
$stmtvalidarhoy->execute();
$resultadoValidar = $stmtvalidarhoy->get_result();
$cantidadhoy = intval($resultadoValidar->fetch_assoc()['cantidad']);
$stmtvalidarhoy->close();

if (
    $cantidadhoy === 0 &&
    intval($tratamiento['dosis_pendientes']) > 0 &&
    intval($tratamiento['dosis_recibida']) < intval($tratamiento['total_dosis'])
) {
    $idSeguimiento = $tratamiento['id_seguimiento'];
    $nueva_dosis = 1;
    $observaciones = "Hoy se le dio una nueva dosis al paciente.";

    $sqlInsertAuto = "INSERT INTO calendario_seguimiento_tb (id_seguimiento, numero_dosis, fecha_registro)
                      VALUES (?, ?, NOW())";
    $stmtInsertAuto = $conn->prepare($sqlInsertAuto);
    if ($stmtInsertAuto) {
        $stmtInsertAuto->bind_param("ii", $idSeguimiento, $nueva_dosis);
        $stmtInsertAuto->execute();
        $stmtInsertAuto->close();
    }

    $totalRecibidas = intval($tratamiento['dosis_recibida']) + 1;
    $totalPendientes = max(0, intval($tratamiento['total_dosis']) - $totalRecibidas);

    // CORRECCIÓN: Variables bien escritas
    $sqlupdateauto = "UPDATE seguimiento_medicamentos_tb_tdo 
                      SET dosis_recibida = ?, dosis_pendientes = ?, observaciones = ?
                      WHERE id_seguimiento = ?";
    $stmtupdateauto = $conn->prepare($sqlupdateauto);
    if ($stmtupdateauto) {
        $stmtupdateauto->bind_param("iisi", $totalRecibidas, $totalPendientes, $observaciones, $idSeguimiento);
        $stmtupdateauto->execute();
        $stmtupdateauto->close();
        
        // Actualizar el array de tratamiento
        $tratamiento['dosis_recibida'] = $totalRecibidas;
        $tratamiento['dosis_pendientes'] = $totalPendientes;
    }
}

$mostrarCalendario = true;
if ($anio < $anioInicio || ($anio == $anioInicio && $mes < $mesInicio)) {
    echo '<script>
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
            icon: "info",
            title: "Sin información",
            text: "No hay datos disponibles antes de la fecha de inicio del tratamiento.",
            confirmButtonText: "Entendido"
        });
    });
    </script>';
    $mostrarCalendario = false;
}

// Generar días del mes desde el día siguiente al ingreso
$fechaInicio = $tratamiento['fecha_inicio'];
$inicioMes = new DateTime("$anio-$mes-01");
$finMes = clone $inicioMes;
$finMes->modify('last day of this month');

$inicioCalendario = new DateTime($fechaInicio);
$inicioCalendario->modify('+1 day');

if ($inicioCalendario < $inicioMes || $inicioCalendario > $finMes) {
    $inicioCalendario = clone $inicioMes;
}

$diasDelMes = [];
while ($inicioCalendario <= $finMes) {
    $fechaStr = $inicioCalendario->format('Y-m-d');
    $diaNum = $inicioCalendario->format('d');
    $diasDelMes[$fechaStr] = $diaNum;
    $inicioCalendario->modify('+1 day');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tratamiento Paciente</title>
    <link rel="stylesheet" href="css/calendario.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style> 
    /* Contenedor principal */
    .tratamiento-container {
      position: relative;
      max-width: 995px;
      margin: 20px auto;
      background: #ffffff;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      padding: 25px;
      font-family: 'Poppins', sans-serif;
      overflow: hidden;
      z-index: 0;
    }

    /* Borde animado alrededor */
    .tratamiento-container::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: 15px;
      padding: 4px;
      background: linear-gradient(120deg, #00ffcc, #00b3ff, #00ffcc, #00b3ff);
      background-size: 300% 300%;
      animation: bordeFluido 4s linear infinite;
      -webkit-mask: 
        linear-gradient(#fff 0 0) content-box, 
        linear-gradient(#fff 0 0);
      -webkit-mask-composite: destination-out;
      mask-composite: exclude;
      z-index: -2;
    }

    /* Fondo interno */
    .tratamiento-container::after {
      content: "";
      position: absolute;
      inset: 4px;
      border-radius: 11px;
      background: #ffffff;
      z-index: -1;
    }

    /* Animación suave del degradado */
    @keyframes bordeFluido {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Efecto hover */
    .tratamiento-container:hover::before {
      filter: brightness(1.3);
    }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include("../MENU/menuVIH.php") ?>

    <main>
        <div id="contenido"></div>
        <header class="header">
            <div class="header-content container">
                <div class="header-txt">
                    <h1>TRATAMIENTO</h1>
                </div>
            </div>
        </header>
       
        <div class="tratamiento-container">
            <h1>Tratamiento de Paciente</h1>
            <h2><?= htmlspecialchars($paciente['nombres_pacientes'] . " " . $paciente['apellidos_pacientes']) ?></h2>
            
            <div class="datos-paciente">
                <h3>Fase actual: <?= htmlspecialchars($tratamiento['nombre_fase']) ?></h3>
                <p><strong>Dosis recibidas:</strong> <?= intval($tratamiento['dosis_recibida']) ?></p>
                <p><strong>Dosis pendientes:</strong> <?= intval($tratamiento['dosis_pendientes']) ?></p>
            </div>

            <?php if ($tratamiento['dosis_pendientes'] <= 0): ?>
                <p><strong>El tratamiento está completo. No se pueden agregar más dosis.</strong></p>
                <script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        title: "Importante",
                        text: "El paciente seleccionado, según sus exámenes y dosis recibidas, dio positivo o negativo?.",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Negativo",
                        cancelButtonText: "Positivo",
                        confirmButtonColor: "#28a745",
                        cancelButtonColor: "#ff9800",
                        allowOutsideClick: false
                    }).then((result) => {
                        const resultado = result.isConfirmed ? "NEGATIVO" : "POSITIVO";
                        const mensajeFinal = result.isConfirmed
                        ? "El paciente ahora pasa a Fase 2, y sus dosis ahora son 75."
                        : "Según los resultados, el paciente sigue en Fase 1, pero ahora las dosis son 105.";

                        Swal.fire({
                        title: "Información",
                        text: mensajeFinal,
                        icon: "info",
                        confirmButtonText: "Entendido",
                        confirmButtonColor: result.isConfirmed ? "#28a745" : "#ff9800"
                        }).then(() => {
                        fetch("evaluar_fase.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({
                            id_seguimiento: <?= $tratamiento['id_seguimiento'] ?>,
                            resultado: resultado
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            window.location.href = "tratamientos.php?id_paciente=<?= $id_paciente ?>";
                        });
                        });
                    });
                });
                </script>
            <?php elseif ($cantidadhoy > 0): ?>
                <p>Ya se registró una dosis hoy. No se puede agregar otra.</p>
            <?php else: ?>
                <h3>Agregar nueva dosis</h3>
            <?php endif; ?>
             
            <h3>Calendario del tratamiento</h3>
            <div class="referencia">                   
                <div class="referencia-item"><div class="referencia-box referencia-verde"></div> Celda verde = día activo</div>
                <div class="referencia-item"><div class="referencia-box referencia-amarillo"></div> Celda amarillo = domingo</div>
                <div class="referencia-item"><div class="referencia-box referencia-rojo"></div> Celda roja = solicitar una notificacion</div>
            </div>
        </div>
        
        <?php if ($mostrarCalendario): ?>
        <div class="navegacion-meses">
            <?php if ($mesAnteriorValido): ?>
                <a class="btn-mes" href="?id_paciente=<?= $id_paciente ?>&mes=<?= $mesAnterior ?>&anio=<?= $anioAnterior ?>">⬅️ Mes anterior</a>
            <?php else: ?>
                <button class="btn-mes" onclick="mostrarAlertaMesInvalido()">⬅️ Mes anterior</button>
            <?php endif; ?>
            <strong><?= $mesNombre . " " . $anio ?></strong>
            <a class="btn-mes" href="?id_paciente=<?= $id_paciente ?>&mes=<?= $mesSiguiente ?>&anio=<?= $anioSiguiente ?>">Mes siguiente ➡️</a>
            <button class="btn-recargar" onclick="location.reload()">🔄</button>
        </div>        

        <table class="calendario">
            <tr>
                <th>NO</th>
                <th><?= $mesNombre ?></th>
                <?php foreach ($diasDelMes as $fecha => $diaNum): ?>
                    <th><?= $diaNum ?></th>
                <?php endforeach; ?>
            </tr>

            <?php
            $contadorNo = 1;
            foreach ($diasDelMes as $fechaFila => $diaNum):
                $numDosis = $dosisPorDia[$fechaFila] ?? 0;
            ?>
            <tr>
                <td><?= $contadorNo ?></td>
                <?php
                    $acumuladas = $dosisAcumuladasPorFecha[$fechaFila] ?? 0;
                ?>
                <td>
                    <?php
                    if ($fechaFila <= $fechaHoy) {
                        echo $tratamiento['dosis_recibida'] . ' dosis';
                    }
                    ?>
                </td>

                <?php foreach ($diasDelMes as $fechaCol => $diaCol):
                    if ($fechaCol === $fechaFila) {
                        $esDomingo = (date('w', strtotime($fechaCol)) == 0);
                        $clase = '';
                        $contenido = '';
                        $tooltip = '';

                        if ($esDomingo) {
                            $clase = 'domingo';
                            $tooltip = 'Domingo';
                        } elseif (in_array($fechaCol, $fechasCriticas)) {
                            $clase = 'dosis-rojo';
                            $contenido = $numDosis;
                            $tooltip = '⚠️ Dosis crítica';
                        } else {
                            $clase = 'dosis-verde';
                            $contenido = $numDosis > 0 ? $numDosis : '';                            
                            $tooltip = $numDosis > 0 ? 'Dosis registrada' : '';
                        }
                        echo "<td class=\"$clase\" title=\"$tooltip\">$contenido</td>";
                    } else {
                        echo "<td></td>";
                    }
                endforeach; ?>
            </tr>
            <?php $contadorNo++; endforeach; ?>
        </table>
        <?php endif; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const avisoCeldas = document.querySelectorAll('.dosis-rojo');
                avisoCeldas.forEach(function (celda) {
                    celda.addEventListener('click', function () {
                        Swal.fire({
                            title: '⚠️ Atención',
                            text: 'Esta es una dosis crítica. Se requiere seguimiento o notificación médica.',
                            icon: 'warning',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#ff0000',
                            background: '#fff8e1',
                            color: '#333'
                        });
                    });
                });

                const celdasVerdes = document.querySelectorAll('.dosis-verde');
                celdasVerdes.forEach(function (celda) {
                    celda.addEventListener('click', function () {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'Dosis registrada al paciente correctamente',
                            icon: 'success',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#28a745',
                            background: '#fff8e1',
                            color: '#333'
                        });
                    });
                });

                const domingos = document.querySelectorAll('.domingo');
                domingos.forEach(function (celda) {
                    celda.addEventListener('click', function () {
                        Swal.fire({
                            title: 'Atención',
                            text: 'Día domingo, no se proporciona dosis al paciente.',
                            icon: 'warning',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#eed707',
                            background: '#fff8e1',
                            color: '#333'
                        });
                    });
                });
            });

            function mostrarAlertaMesInvalido() {
                Swal.fire({
                    icon: "info",
                    title: "Mes no disponible",
                    text: "No hay información antes de la fecha de inicio del tratamiento.",
                    confirmButtonText: "Entendido"
                });
            }
        </script>
    </body>
</html>