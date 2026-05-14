<?php
include("conexion.php");

$sql = "SELECT p.ID_PACIENTES, p.NOMBRES_PACIENTES, p.APELLIDOS_PACIENTES, p.DPI_PACIENTES, p.DIRECCION, p.TELEFONO,
               p.FECHA_NACIMIENTO, g.ID_GENERO, g.NOMBRE AS GENERO, 
               e.ID_ESTADO_PACIENTES, e.DESCRIPCION AS ESTADO,
               (SELECT COUNT(*) FROM registro_tuberculosis t WHERE t.id_pacientes = p.ID_PACIENTES) AS tiene_tb
        FROM PACIENTES p
        JOIN GENERO g ON p.ID_GENERO = g.ID_GENERO
        LEFT JOIN ESTADO_PACIENTE e ON p.ID_ESTADO_PACIENTES = e.ID_ESTADO_PACIENTES";
$result = $conn->query($sql);

$sqlGenero = "SELECT * FROM GENERO";
$generos = $conn->query($sqlGenero);

$sqlEstado = "SELECT * FROM ESTADO_PACIENTE";
$estados = $conn->query($sqlEstado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pacientes</title>  
    <link rel="stylesheet" href="css/pacientes.css">
    <style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 80px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4); /* un poco más suave */
}

/* Caja del modal */
.modal-content {
    background: #fbfcfe; /* mismo fondo que cards */
    margin: auto;
    padding: 25px 30px;
    border-radius: 15px;
    width: 480px;
    max-width: 90%;
    box-shadow: 0 0 15px rgba(33, 105, 58, 0.3); /* sombra con tono verde */
    font-family: 'Poppins', sans-serif;
}

/* Botón de cierre */
.close {
    float: right;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    color: #2b669a;
    transition: 0.3s;
}
.close:hover {
    color: #02457a;
}

/* Título del modal */
.modal-content h2, 
.modal-content h3 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 22px;
    text-align: center;
    font-family: 'Bebas Neue', sans-serif;
    color: #02457a; /* verde principal */
}

/* Inputs y selects */
.modal-content input, 
.modal-content select, 
.modal-content textarea {
    width: 100%;
    padding: 10px;
    margin: 10px 0; 
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    transition: border 0.3s ease;
}

.modal-content input:focus, 
.modal-content select:focus, 
.modal-content textarea:focus {
    border-color: #2b669a; /* azul al enfocar */
    outline: none;
}

/* Botones */
.btn {
    padding: 10px 18px;
    background: #2b669a;  /* azul principal */
    color: #fff;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease;
}
.btn:hover {
    background: #3e93deff; /* verde al pasar el mouse */
}

    </style>
    
</head>
<body>
    <div id="contenido" ></div>
    <header class="header" ><div class="header-content container">
        <div class="header-txt">
            <h1>PACIENTES</h1>
        </div>
    </div></header>

<!--<a href="index.php" class="btn">Volver al Inicio</a>-->

<main class="table" id="customers_table">
    <section class="table__header">
    <h2>PACIENTES</h2>
    <button class="btn" onclick="document.getElementById('modalAgregar').style.display='block'">AGREGAR PACIENTE</button>
    </section>
    
    <div class="filtros">
    <input type="text" id="buscador" placeholder="Buscar por Nombre, Apellido o DPI">
    <select id="modulos">
        <option value="tuberculosis">Tuberculosis</option>
        <option value="vih">VIH</option>
        <option value="embarazo">Embarazo</option>
    </select>
    </div>

    <table id="tablaPacientes" class="display">
        <thead>
        <tr>
            <th>DPI</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Fecha Nacimiento</th>
            <th>Género</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
         </thead>
            <tbody>
                    <?php while($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td class="dpi"><?= $row["DPI_PACIENTES"] ?></td>
                            <td class="nombres"><?= $row["NOMBRES_PACIENTES"] ?></td>
                            <td class="apellidos"><?= $row["APELLIDOS_PACIENTES"] ?></td>
                            <td><?= $row["FECHA_NACIMIENTO"] ?></td>
                            <td><?= $row["GENERO"] ?></td>
                            <td><?= $row["ESTADO"] ?></td>
                            <td>
                                <button class="btn btnAsignar" 
                                    data-id="<?= $row['ID_PACIENTES'] ?>" 
                                    data-tiene-tb="<?= $row['tiene_tb'] ? '1' : '0' ?>">
                                    Asignar T
                                </button>
                                <button class="btn" 
                                        onclick="abrirEditar(
                                            '<?= $row['ID_PACIENTES'] ?>',
                                            '<?= $row['DPI_PACIENTES'] ?>',
                                            '<?= $row['NOMBRES_PACIENTES'] ?>',
                                            '<?= $row['APELLIDOS_PACIENTES'] ?>',
                                            '<?= $row['FECHA_NACIMIENTO'] ?>',
                                            '<?= $row['ID_GENERO'] ?>',
                                            '<?= $row['ID_ESTADO_PACIENTES'] ?>',
                                            '<?= $row['DIRECCION'] ?>',
                                            '<?= $row['TELEFONO'] ?>'
                                        )">Editar</button>
                            </td>
                        </tr>
                    <?php } ?>
            </tbody>
    </table>

    <div id="modalAgregar" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalAgregar').style.display='none'">&times;</span>
        <h3>Agregar Paciente</h3>
        <form action="guardar_paciente.php" method="POST">

            <input type="text" name="dpi" placeholder="DPI" maxlength="13" required>
            <input type="text" name="nombres" placeholder="Nombres" maxlength="50" required>
            <input type="text" name="apellidos" placeholder="Apellidos" maxlength="50" required>
            <Label> Fecha de nacimiento: </Label>
            <input type="date" name="fecha" required>

            <select name="genero" required>
                <option value="">-- Seleccione Género --</option>
                <?php while($g = $generos->fetch_assoc()) { ?>
                    <option value="<?= $g['ID_GENERO'] ?>"><?= $g['NOMBRE'] ?></option>
                <?php } ?>
            </select>

            <textarea name="direccion" placeholder="Dirección" rows="2" maxlength="200" required></textarea>
            <!-- Campo de teléfono -->
            <label for="telefono" style="display:block; font-weight:bold; margin-top:8px;">Teléfono:</label>
            <div style="display:flex; align-items:center; gap:12px; border-bottom:1px solid #ccc; width:max-content; padding:4px 0;">
                <img src="https://flagcdn.com/w20/gt.png" alt="GT" width="20" height="14" style="vertical-align:middle;">
                <span style="font-size:16px;">+502</span>
                <div id="telefono-visual" 
                    style="font-family:monospace; font-size:16px; letter-spacing:5px; outline:none; cursor:text;" 
                    tabindex="0">
                    _ _ _ _ _ _ _ _
                </div>
            </div>
            <input type="hidden" id="telefono" name="telefono" required>

            <select name="estado">
                <option value="">-- Seleccione Estado --</option>
                <?php while($e = $estados->fetch_assoc()) { ?>
                    <option value="<?= $e['ID_ESTADO_PACIENTES'] ?>"><?= $e['DESCRIPCION'] ?></option>
                <?php } ?>
            </select>

            <button type="submit" class="btn">Guardar</button>
        </form>
    </div>
</div>


    <div id="modalEditar" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalEditar').style.display='none'">&times;</span>
        <h3>Editar Paciente</h3>
        <form action="editar_paciente.php" method="POST">
            
            <input type="hidden" id="edit_id" name="id">

            <input type="text" id="edit_dpi" name="dpi" placeholder="DPI" maxlength="13" required>
            <input type="text" id="edit_nombres" name="nombres" placeholder="Nombres" maxlength="50" required>
            <input type="text" id="edit_apellidos" name="apellidos" placeholder="Apellidos" maxlength="50" required>
            <input type="date" id="edit_fecha" name="fecha" required>

            <select name="genero" id="edit_genero" required>
                <option value="">-- Seleccione Género --</option>
                <?php
                $generosEdit = $conn->query($sqlGenero);
                while($g = $generosEdit->fetch_assoc()) { ?>
                    <option value="<?= $g['ID_GENERO'] ?>"><?= $g['NOMBRE'] ?></option>
                <?php } ?>
            </select>

            <textarea id="edit_direccion" name="direccion" placeholder="Dirección" required></textarea>
            <label for="edit_telefono" style="display:block; font-weight:bold; margin-top:8px;">Teléfono:</label>
            <div style="display:flex; align-items:center; gap:12px; border-bottom:1px solid #ccc; width:max-content; padding:4px 0;">
                <img src="https://flagcdn.com/w20/gt.png" alt="GT" width="20" height="14" style="vertical-align:middle;">
                <span style="font-size:16px;">+502</span>
                <div id="edit-telefono-visual"
                    style="font-family:monospace; font-size:16px; letter-spacing:5px; outline:none; cursor:text;"
                    tabindex="0">
                    _ _ _ _ _ _ _ _
                </div>
            </div>
            <input type="hidden" id="edit_telefono" name="telefono" required>


            <select name="estado" id="edit_estado">
                <option value="">-- Seleccione Estado --</option>
                <?php
                $estadosEdit = $conn->query($sqlEstado);
                while($e = $estadosEdit->fetch_assoc()) { ?>
                    <option value="<?= $e['ID_ESTADO_PACIENTES'] ?>"><?= $e['DESCRIPCION'] ?></option>
                <?php } ?>
            </select>

            <button type="submit" class="btn">Actualizar</button>
        </form>
    </div>
</div>

<script>
function abrirEditar(id, dpi, nombres, apellidos, fecha, genero, estado, direccion, telefono) {
    document.getElementById('modalEditar').style.display = 'block';
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_dpi').value = dpi;
    document.getElementById('edit_nombres').value = nombres;
    document.getElementById('edit_apellidos').value = apellidos;
    document.getElementById('edit_fecha').value = fecha;
    document.getElementById('edit_genero').value = genero;
    document.getElementById('edit_estado').value = estado;
    document.getElementById('edit_direccion').value = direccion;
    document.getElementById('edit_telefono').value = telefono;

    if (typeof setTelefonoEditar === 'function') {
        setTelefonoEditar(telefono);
    }
}

window.onclick = function(event) {
    let modales = ["modalAgregar", "modalEditar"];
    modales.forEach(id => {
        let modal = document.getElementById(id);
        if (event.target == modal) modal.style.display = "none";
    });
}
</script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
            document.addEventListener("DOMContentLoaded", function() {
                const dpiInput = document.querySelector('input[name="dpi"]');
                const form = dpiInput.closest("form");

                dpiInput.addEventListener("input", function(e) {
                    this.value = this.value.replace(/\D/g, "");

                    if (this.value.length > 13) {
                        this.value = this.value.slice(0, 13);
                    }
                });

                form.addEventListener("submit", function(e) {
                    if (dpiInput.value.length !== 13) {
                        e.preventDefault(); 
                        Swal.fire({
                            icon: "warning",
                            title: "Advertencia",
                            text: "La cantidad de dígitos del DPI no cumple con las normas (deben ser 13 dígitos).",
                            confirmButtonText: "Entendido",
                            confirmButtonColor: "#3085d6"
                        });
                    }
                });
            });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dpiInputEdit = document.getElementById("edit_dpi");
            const formEdit = dpiInputEdit ? dpiInputEdit.closest("form") : null;

            if (dpiInputEdit && formEdit) {
                dpiInputEdit.addEventListener("input", function() {
                    this.value = this.value.replace(/\D/g, "");
                    if (this.value.length > 13) {
                        this.value = this.value.slice(0, 13); 
                    }
                });

                formEdit.addEventListener("submit", function(e) {
                    if (dpiInputEdit.value.length !== 13) {
                        e.preventDefault(); 
                        Swal.fire({
                            icon: "warning",
                            title: "Advertencia",
                            text: "La cantidad de dígitos del DPI no cumple con las normas (deben ser 13 dígitos).",
                            confirmButtonText: "Entendido",
                            confirmButtonColor: "#3085d6"
                        });
                    }
                });
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const visual = document.getElementById("telefono-visual");
            const inputHidden = document.getElementById("telefono");
            let telefonoTemporal = "";

            visual.addEventListener("keydown", function(e) {
                e.preventDefault();
                if (/^\d$/.test(e.key) && telefonoTemporal.length < 8) {
                    telefonoTemporal += e.key;
                } else if (e.key === "Backspace") {
                    telefonoTemporal = telefonoTemporal.slice(0, -1);
                } else if (/^\d$/.test(e.key) && telefonoTemporal.length >= 8) {
                    Swal.fire({
                        icon: "warning",
                        title: "Advertencia",
                        text: "El número de teléfono solo puede tener 8 dígitos.",
                        confirmButtonText: "Entendido"
                    });
                }

                const numeros = telefonoTemporal.split("");
                const guiones = Array(8 - numeros.length).fill("_");
                visual.textContent = numeros.concat(guiones).join(" ");
                inputHidden.value = telefonoTemporal;
            });

            visual.addEventListener("focus", () => visual.parentElement.style.borderBottom = "2px solid #3085d6");
            visual.addEventListener("blur", () => visual.parentElement.style.borderBottom = "1px solid #ccc");
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const visualEdit = document.getElementById("edit-telefono-visual");
            const inputHiddenEdit = document.getElementById("edit_telefono");
            let telefonoEditTemporal = "";

            function actualizarVisual() {
                const numeros = telefonoEditTemporal.split("");
                const guiones = Array(8 - numeros.length).fill("_");
                visualEdit.textContent = numeros.concat(guiones).join(" ");
                inputHiddenEdit.value = telefonoEditTemporal;
            }

            window.setTelefonoEditar = function(valor) {
                telefonoEditTemporal = valor || "";
                actualizarVisual();
            }

            visualEdit.addEventListener("keydown", function(e) {
                e.preventDefault();
                if (/^\d$/.test(e.key) && telefonoEditTemporal.length < 8) {
                    telefonoEditTemporal += e.key;
                } else if (e.key === "Backspace") {
                    telefonoEditTemporal = telefonoEditTemporal.slice(0, -1);
                } else if (/^\d$/.test(e.key) && telefonoEditTemporal.length >= 8) {
                    Swal.fire({
                        icon: "warning",
                        title: "Advertencia",
                        text: "El número de teléfono solo puede tener 8 dígitos.",
                        confirmButtonText: "Entendido"
                    });
                }
                actualizarVisual();
            });

            visualEdit.addEventListener("focus", () => visualEdit.parentElement.style.borderBottom = "2px solid #3085d6");
            visualEdit.addEventListener("blur", () => visualEdit.parentElement.style.borderBottom = "1px solid #ccc");
        });
    </script>

    <script>
        const modulos = document.getElementById('modulos');
        const botones = document.querySelectorAll('.btnAsignar');

        function actualizarBotones() {
            const modulo = modulos.value;

            botones.forEach(btn => {
                const idPaciente = btn.dataset.id;
                const tieneTB = btn.dataset.tieneTb === '1';

                if (modulo === 'tuberculosis') btn.textContent = 'Asignar T';
                else if (modulo === 'vih') btn.textContent = 'Asignar V';
                else if (modulo === 'embarazo') btn.textContent = 'Asignar E';

                btn.onclick = () => {
                    if (modulo === 'tuberculosis' && tieneTB) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Paciente ya ingresado',
                            text: 'Si deseas seguir con su tratamiento ve a Pacientes con tuberculosis.',
                            confirmButtonText: 'Ok'
                        });
                        return;
                    }

                    let url = '';
                    if (modulo === 'tuberculosis') url = 'tuberculosis.php?id_paciente=' + idPaciente;
                    else if (modulo === 'vih') url = 'VIH.php?id_paciente=' + idPaciente;
                    else if (modulo === 'embarazo') url = 'Embarazo.php?id_paciente=' + idPaciente;

                    window.location.href = url;
                };
            });
        }

        actualizarBotones();

        modulos.addEventListener('change', actualizarBotones);
    </script>

    <script>
        const buscador = document.getElementById('buscador');
        const tabla = document.getElementById('tablaPacientes');
        const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        buscador.addEventListener('keyup', () => {
            const texto = buscador.value.toLowerCase();
            for (let fila of filas) {
                const nombres = fila.querySelector('.nombres').textContent.toLowerCase();
                const apellidos = fila.querySelector('.apellidos').textContent.toLowerCase();
                const dpi = fila.querySelector('.dpi').textContent.toLowerCase();
                if (nombres.includes(texto) || apellidos.includes(texto) || dpi.includes(texto)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>
