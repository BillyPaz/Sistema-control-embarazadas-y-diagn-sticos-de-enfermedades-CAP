<?php
include("conexion.php");

if (!isset($_GET["id_paciente"])) {
    die("Error: No se seleccionó ningún paciente.");
}

$id_paciente = intval($_GET["id_paciente"]);

$sql = "SELECT NOMBRES_PACIENTES, APELLIDOS_PACIENTES FROM PACIENTES WHERE ID_PACIENTES = $id_paciente";
$result = $conn->query($sql);
$paciente = $result->fetch_assoc();

$pacienteRegistrado = false;

if ($id_paciente) {
    $query = $conn->prepare("SELECT ID_PACIENTES FROM registro_tuberculosis WHERE ID_PACIENTES = ?");
    $query->bind_param("i", $id_paciente);
    $query->execute();
    $query->store_result();
    if ($query->num_rows > 0) {
        $pacienteRegistrado = true;
    }
    $query->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_tratamiento'])) {
    $fecha_inicio = $_POST['fecha_inicio'];
    $id_fase = intval($_POST['id_fase']);
    $dosis_recibida = intval($_POST['dosis_recibida']);
    $observaciones = $_POST['observaciones'];

    $total_dosis = ($id_fase == 1) ? 50 : 75;
    $dosis_pendientes = $total_dosis - $dosis_recibida;

    if ($dosis_pendientes <= 0) {
        $estado = "FINALIZADO";
        $dosis_pendientes = 0;
    } elseif ($dosis_recibida > 0) {
        $estado = "EN PROCESO";
    } else {
        $estado = "INICIO";
    }

    $sql = "INSERT INTO Seguimiento_medicamentos_tb_TDO 
            (ID_PACIENTES, ID_FASE, ESTADO_SEGUIMIENTO, DOSIS_RECIBIDA, DOSIS_PENDIENTES, OBSERVACIONES, FECHA_INICIO)
            VALUES (?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iississ", $id_paciente, $id_fase, $estado, $dosis_recibida, $dosis_pendientes, $observaciones, $fecha_inicio);

    if ($stmt->execute()) {
        echo "<script>alert('Tratamiento guardado correctamente');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

$sqlTratamiento = "SELECT COUNT(*) AS total FROM Seguimiento_medicamentos_tb_TDO WHERE ID_PACIENTES = $id_paciente";
$resultTratamiento = $conn->query($sqlTratamiento);
$rowTratamiento = $resultTratamiento->fetch_assoc();
$tieneTratamiento = ($rowTratamiento['total'] > 0);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Tuberculosis</title>
    <link rel="stylesheet" href="css/tuberculosis.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header>
    <h1>Módulo de Tuberculosis</h1>
    <p>Centro Médico - Registro y Seguimiento de Pacientes</p>
</header>

  <!-- <a href="pacientes.php" class="btn" id="btnVolver">Volver</a>-->

<main>
    <nav class="tabs">
        <button class="tab active" data-tab="registro">Registro Pacientes</button>
        <button class="tab" data-tab="notificacion">Ficha de Notificación</button>
        <button class="tab" data-tab="tratamiento">Tratamiento y Seguimiento</button>
        <button class="tab" data-tab="reportes">Reportes</button>
    </nav>

    <section id="registro" class="tab-content active">
        <h2>Registro de Pacientes</h2>
            <form action="guardar_registro_tuberculosis.php" method="POST">
                <input type="hidden" name="id_paciente" value="<?= $id_paciente ?>">
                <div class="form-group">
                    <label>Fecha de referencia:</label>
                    <input type="date" name="fecha_referencia" required>
                </div>
                <div class="form-group">
                    <label>Tipos de Paciente:</label>
                    <select name="id_tipo_paciente" required>
                        <?php 
                        $sql = "SELECT id_tipo_paciente, descripcion FROM tipo_paciente_tb"; 
                        $result = $conn->query($sql); 
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='".$row['id_tipo_paciente']."'>".$row['descripcion']."</option>";
                        } 
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Información VIH:</label>
                    <div class="checklist">
                        <?php 
                        $sql = "SELECT id_prueba_VIH, descripcion FROM prueba_VIH"; 
                        $result = $conn->query($sql); 
                        while ($row = $result->fetch_assoc()) {
                            echo "<label><input type='radio' name='id_prueba_VIH' value='".$row['id_prueba_VIH']."'> ".$row['descripcion']."</label><br>";
                        } 
                        ?>
                    </div>
                </div>
                <fieldset>
                    <legend>Servicio que refiere</legend>
                    <input type="text" name="area_que_refiere" placeholder="Área" required>
                    <input type="text" name="distrito_que_refiere" placeholder="Distrito" required>
                    <input type="text" name="servicio_que_refiere" placeholder="Servicio" required>
                </fieldset>
                <fieldset>
                    <legend>Servicio al que refiere</legend>
                    <input type="text" name="area_alque_refiere" placeholder="Área" required>
                    <input type="text" name="distrito_alque_refiere" placeholder="Distrito" required>
                    <input type="text" name="servicio_alque_refiere" placeholder="Servicio" required>
                </fieldset>
                <div class="form-group">
                    <label>Motivo de la referencia:</label>
                    <textarea name="motivo_referencia" placeholder="Escriba el motivo..." required></textarea>
                </div>
                <div class="form-group">
                    <label>¿Rechazo la prueba?</label>
                    <select name="rechazo" required>
                        <option value="No">No</option>
                        <option value="Sí">Sí</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha de realización de la prueba:</label>
                    <input type="date" name="fecha_realizacion_prueba">
                </div>
                <div class="form-group">
                    <label>Resultado de la prueba:</label>
                    <select name="resultado_prueba">
                        <option value="">-- Seleccione --</option>
                        <option value="Negativo">Negativo</option>
                        <option value="Positivo">Positivo</option>
                        <option value="Pendiente">Pendiente</option>
                    </select>
                </div>
                <button type="submit">Guardar</button>
            </form>
    </section>

    <section id="notificacion" class="tab-content">
        <h2>Ficha de Notificación</h2>
        <form>
            <div class="form-group">
                <label>Fecha de notificación:</label>
                <input type="date">
            </div>
            <div class="form-group">
                <label>Servicio que notifica:</label>
                <input type="text" placeholder="Área / Distrito / Servicio">
            </div>
            <div class="form-group">
                <label>Teléfono:</label>
                <input type="tel" placeholder="Ingrese el teléfono">
            </div>
            <div class="form-group">
                <label>Persona que notifica:</label>
                <input type="text" placeholder="Nombre y cargo">
            </div>
            <div class="form-group">
                <label>Condición del paciente:</label>
                <select>
                    <option>Vivo</option>
                    <option>Muerto</option>
                </select>
            </div>
            <button type="submit">Guardar</button>
        </form>
    </section>

    <section id="tratamiento" class="tab-content">
        <h2>Tratamiento y Seguimiento</h2>
        <form method="POST">
            <div class="form-group">
                <label>Fecha de inicio:</label>
                <input type="date" name="fecha_inicio" required>
            </div>
            <div class="form-group">
                <label>Fase del tratamiento:</label>
                <select name="id_fase" id="fase" required>
                    <option value="">-- Seleccione Fase --</option>
                    <?php 
                    $fases = $conn->query("SELECT ID_FASE, NOMBRE_FASE FROM Fase_tb"); 
                    while ($row = $fases->fetch_assoc()) {
                        echo "<option value='".$row['ID_FASE']."'>".$row['NOMBRE_FASE']."</option>";
                    } 
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Dosis recibida:</label>
                <input type="number" name="dosis_recibida" id="dosis_recibida" min="0" required>
            </div>
            <div class="form-group">
                <label>Dosis pendientes:</label>
                <input type="number" name="dosis_pendientes" id="dosis_pendientes" readonly>
            </div>
            <div class="form-group">
                <label>Estado del tratamiento:</label>
                <input type="text" name="estado_seguimiento" id="estado_seguimiento" readonly>
            </div>
            <div class="form-group">
                <label>Resumen del diagnóstico y tratamiento:</label>
                <textarea name="observaciones" required></textarea>
            </div>
            <button type="submit" name="guardar_tratamiento">Guardar</button>
        </form>
    </section>

    <section id="reportes" class="tab-content">
        <h2>Reportes</h2>
        <form>
            <div class="form-group">
                <label>Búsqueda por nombre:</label>
                <input type="text" placeholder="Ingrese el nombre">
            </div>
            <div class="form-group">
                <label>Búsqueda por fecha de ingreso:</label>
                <input type="date">
            </div>
            <button type="submit">Buscar</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>DPI</th>
                    <th>Fase</th>
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nahomi Quan</td>
                    <td>123456789</td>
                    <td>Fase 1</td>
                    <td>2025-09-02</td>
                </tr>
                <tr>
                    <td>Yeison Maldonado</td>
                    <td>123456567</td>
                    <td>Fase 2</td>
                    <td>2025-09-06</td>
                </tr>
            </tbody>
        </table>
    </section>
</main>

<script>
        const pacienteRegistrado = <?= $pacienteRegistrado ? 'true' : 'false' ?>;

            document.addEventListener("DOMContentLoaded", () => {
                const tabs = document.querySelectorAll(".tab");

                tabs.forEach(tab => {
                    tab.addEventListener("click", (e) => {
                        const target = tab.dataset.tab;

                        if (target === "tratamiento" && !pacienteRegistrado) {
                            e.preventDefault(); 
                            Swal.fire({
                                icon: 'error',
                                title: 'Paciente no ingresado en Tuberculosis',
                                text: '¿Deseas agregarlo primero?',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, registrar',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "tuberculosis.php?id_paciente=<?= $id_paciente ?>&tab=registro";
                                }
                            });
                        }
                    });
                });
            });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const tieneTratamiento = <?= $tieneTratamiento ? 'true' : 'false' ?>;
            const btnVolver = document.getElementById("btnVolver");

            btnVolver.addEventListener("click", function(e) {
                if (!tieneTratamiento) {
                    e.preventDefault(); 
                    Swal.fire({
                        title: 'Advertencia',
                        text: 'Este paciente no tiene un tratamiento asignado. ¿Deseas salir?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "pacientes.php";
                        }
                    });
                }
            
            });
        });
    </script>

<script src="js/tuberculosis.js"></script>
<script src="js/tratamientos.js"></script>
<script src="js/estado.js"></script>
</body>
</html>
