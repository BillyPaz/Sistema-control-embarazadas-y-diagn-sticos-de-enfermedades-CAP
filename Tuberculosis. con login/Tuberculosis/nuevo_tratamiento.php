<?php
include("conexion.php");

if (!isset($_GET["id_paciente"])) {
    die("Error: No se seleccionó ningún paciente.");
}

$id_paciente = intval($_GET["id_paciente"]);

$sql = "SELECT * FROM registro_tuberculosis WHERE ID_PACIENTES = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$res = $stmt->get_result();
$registrado = $res->num_rows > 0;
$stmt->close();

if (!$registrado) {
    die("El paciente no está registrado en el módulo de tuberculosis.");
}

$sql = "SELECT * FROM Seguimiento_medicamentos_tb_TDO WHERE ID_PACIENTES = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$res = $stmt->get_result();
$tieneTratamiento = $res->num_rows > 0;
$stmt->close();

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

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Tratamiento</title>
    <link rel="stylesheet" href="css/tuberculosis.css">
</head>
<body>
    <h2>Tratamiento y Seguimiento</h2>

    <?php if (!$tieneTratamiento): ?>
        <form method="POST">
            <input type="hidden" name="id_paciente" value="<?= $id_paciente ?>">

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
                <label>Observaciones:</label>
                <textarea name="observaciones" required></textarea>
            </div>

            <button type="submit" name="guardar_tratamiento">Guardar</button>
        </form>

    <?php else: ?>
        <?php include("tratamientos.php"); ?>
    <?php endif; ?>

    <a href="../menu.php" class="btn">Volver al Inicio</a>

    <script src="js/tratamientos.js"></script>
    <script src="js/estado.js"></script>
</body>
</html>
