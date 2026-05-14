<?php
include(__DIR__."/conexion.php");


if (!isset($_GET["id_paciente"])) {
    die("Error: No se seleccionó ningún paciente.");
}

$id_paciente = intval($_GET["id_paciente"]);

$sql = "SELECT * FROM registro_tuberculosis WHERE id_pacientes = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$res = $stmt->get_result();
$registrado = $res->num_rows > 0;
$stmt->close();

if (!$registrado) {
    die("El paciente no está registrado en el módulo de tuberculosis.");
}

$sql = "SELECT * FROM Seguimiento_medicamentos_tb_tdo WHERE id_pacientes = ?";
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
            (id_pacientes, id_fase, estado_seguimiento, dosis_recibida, dosis_pendientes, observaciones, fecha_inicio)
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
            <?php 
            $fase = $conn->query("SELECT id_fase, nombre_fase FROM fase_tb WHERE id_fase = 1");
            if ($row = $fase->fetch_assoc()) {
                echo "<option value='".$row['id_fase']."' selected>".$row['nombre_fase']."</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label>Dosis recibida:</label>
        <input 
            type="number" 
            name="dosis_recibida" 
            id="dosis_recibida" 
            min="1" 
            max="1" 
            value="1" 
            readonly
        >
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const faseSelect = document.getElementById("fase");
        const dosisInput = document.getElementById("dosis_recibida");
        const estadoInput = document.getElementById("estado_seguimiento");
        const dosisPendientesInput = document.getElementById("dosis_pendientes");

        function actualizarEstado() {
            const fase = parseInt(faseSelect.value);
            const dosis = parseInt(dosisInput.value) || 0;

            let total = 0;
            if (fase === 1) total = 50;

            let estado = "INICIO";
            let pendientes = total - dosis;

            if (dosis >= total) {
                estado = "FINALIZADO";
                pendientes = 0;
            } else if (dosis > 5) {
                estado = "EN PROCESO";
            }

            estadoInput.value = estado;
            dosisPendientesInput.value = pendientes >= 0 ? pendientes : 0;
        }

        actualizarEstado();

        if (faseSelect) {
            faseSelect.addEventListener("click", (e) => {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Advertencia",
                    text: "Este campo no puede ser modificado.",
                    confirmButtonText: "Entendido",
                    confirmButtonColor: "#3085d6"
                });
            });
        }

        if (dosisInput) {
            dosisInput.addEventListener("click", (e) => {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Advertencia",
                    text: "Este campo no puede ser modificado.",
                    confirmButtonText: "Entendido",
                    confirmButtonColor: "#3085d6"
                });
            });
        }
    });
</script>

    <?php else: ?>
        <?php include("tratamientos.php"); ?>
    <?php endif; ?>

    <a href="../menu.php" class="btn">Volver al Inicio</a>

    <script src="js/tratamientos.js"></script>
    <script src="js/estado.js"></script>
</body>
</html>
