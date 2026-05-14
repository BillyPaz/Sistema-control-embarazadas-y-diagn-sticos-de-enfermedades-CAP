<?php
include(__DIR__."/conexion.php");


if (!isset($_GET["id_paciente"])) {
    die("Error: No se seleccionó ningún paciente.");
}

$id_paciente = intval($_GET["id_paciente"]);

$sql = "select nombres_pacientes, apellidos_pacientes from pacientes where id_pacientes = $id_paciente";
$result = $conn->query($sql);
$paciente = $result->fetch_assoc();

$pacienteRegistrado = false;

if ($id_paciente) {
    $query = $conn->prepare("select id_pacientes from registro_tuberculosis where id_pacientes = ?");
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

    $sql = "insert into seguimiento_medicamentos_tb_tdo 
            (id_pacientes, id_fase, estado_seguimiento, dosis_recibida, dosis_pendientes, observaciones, fecha_inicio)
            values (?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iississ", $id_paciente, $id_fase, $estado, $dosis_recibida, $dosis_pendientes, $observaciones, $fecha_inicio);

    if ($stmt->execute()) {
        echo "<script>alert('Tratamiento guardado correctamente');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

$sqlTratamiento = "select count(*) as total from seguimiento_medicamentos_tb_tdo where id_pacientes = $id_paciente";
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include("../MENU/menuVIH.php") ?>

<main>

 <div id="contenido" ></div>
    <header class="header" ><div class="header-content container">
        <div class="header-txt">
            <h1>TUBERCULOSIS</h1>
        </div>
    </div>
    </header>


  <!-- <a href="pacientes.php" class="btn" id="btnVolver">Volver</a>-->


    <nav class="tabs">
        <button class="tab active" data-tab="registro">Registro Pacientes</button>        
        <button class="tab" data-tab="tratamiento">Tratamiento y Seguimiento</button>
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
                        $sql = "SELECT id_prueba_vih, descripcion FROM prueba_vih"; 
                        $result = $conn->query($sql); 
                        while ($row = $result->fetch_assoc()) {
                            echo "<label><input type='radio' name='id_prueba_VIH' value='".$row['id_prueba_vih']."'> ".$row['descripcion']."</label><br>";
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
            <label>Resumen del diagnóstico y tratamiento:</label>
            <textarea name="observaciones" required></textarea>
        </div>

        <button type="submit" name="guardar_tratamiento">Guardar</button>
    </form>
</section>
   <script src="script"></script>
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
                if (fase === 2) total = 75; 

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
<script src="script.js"></script>
<script src="js/tuberculosis.js"></script>
<script src="js/tratamientos.js"></script>
<script src="js/estado.js"></script>
</body>
</html>
