<?php
include(__DIR__."/../conexion.php");

$sql = "select p.id_pacientes, p.nombres_pacientes, p.apellidos_pacientes, p.dpi_pacientes, p.direccion, p.telefono,
               p.fecha_nacimiento, g.id_genero, g.nombre as genero, 
               e.id_estado_pacientes, e.descripcion as estado,
               (select count(*) from registro_tuberculosis t where t.id_pacientes = p.id_pacientes) as tiene_tb
        from pacientes p
        join genero g on p.id_genero = g.id_genero
        left join estado_paciente e on p.id_estado_pacientes = e.id_estado_pacientes";

$result = $conn->query($sql);

$sqlGenero = "SELECT id_genero, nombre FROM genero";
$generos = $conn->query($sqlGenero);

$sqlEstado = "SELECT id_estado_pacientes, descripcion FROM estado_paciente";
$estados = $conn->query($sqlEstado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pacientes</title>  

     <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link rel="stylesheet" href="../css/pacientes.css">
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

/* Contenedor general */
.filtros {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 20px 0;
}

/* Input base */
#buscador {
    position: relative;
    padding: 10px 15px;
    width: 300px;
    font-size: 16px;
    border: none;
    border-radius: 30px;
    background-color: #fff;
    color: #333;
    outline: none;
    z-index: 1;
    box-shadow: 0 0 10px rgba(0, 255, 255, 0.2);
    transition: box-shadow 0.4s ease;
}

#buscador::before {
    content: "";
    position: absolute;
    top: -3px;
    left: -3px;
    right: -3px;
    bottom: -3px;
    border-radius: 35px;
    background: linear-gradient(120deg, #0530f0ff, #66c0f5ff, #0530f0ff);
    background-size: 300% 300%;
    animation: glow 4s linear infinite;
    filter: blur(10px); /* hace que el borde brille */
    z-index: -1;
    opacity: 2;
}

/* Animación del degradado giratorio */
@keyframes glow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Efecto al enfocar */
#buscador:focus {
    box-shadow: 0 0 25px rgba(0, 255, 255, 0.7);
}

/* Select (sin animación) */
#modulos {
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-size: 16px;
}
</style>
    
</head>
<body>

<?php include("../MENU/menuVIH.php") ?>

<main>
    <div id="contenido" ></div>
    <header class="header" ><div class="header-content container">
        <div class="header-txt">
            <h1>PACIENTES</h1>
        </div>
    </div></header>

<!--<a href="index.php" class="btn">Volver al Inicio</a>-->

<section class="table" id="customers_table">
    <section class="table__header">
    <h2>PACIENTES</h2>
    <button class="btn" onclick="document.getElementById('modalAgregar').style.display='block'">AGREGAR PACIENTE</button>
    </section>
    
    <div class="filtros">
        <input type="text" id="buscador" placeholder="Buscar por Nombre, Apellido o DPI">        
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
                            <td class="dpi"><?= $row["dpi_pacientes"] ?></td>
                            <td class="nombres"><?= $row["nombres_pacientes"] ?></td>
                            <td class="apellidos"><?= $row["apellidos_pacientes"] ?></td>
                            <td><?= $row["fecha_nacimiento"] ?></td>
                            <td><?= $row["genero"] ?></td>
                            <td><?= $row["estado"] ?></td>
                            <td>                                
                                <button class="btn" 
                                        onclick="abrirEditar(
                                            '<?= $row['id_pacientes'] ?>',
                                            '<?= $row['dpi_pacientes'] ?>',
                                            '<?= $row['nombres_pacientes'] ?>',
                                            '<?= $row['apellidos_pacientes'] ?>',
                                            '<?= $row['fecha_nacimiento'] ?>',
                                            '<?= $row['id_genero'] ?>',
                                            '<?= $row['id_estado_pacientes'] ?>',
                                            '<?= $row['direccion'] ?>',
                                            '<?= $row['telefono'] ?>'
                                        )"title='editar paciente'>📝</button>
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
                    <option value="<?= $g['id_genero'] ?>"><?= $g['nombre'] ?></option>
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
                    <option value="<?= $e['id_estado_pacientes'] ?>"><?= $e['descripcion'] ?></option>
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

            <div class="form-group">                
                    <label for="edit_dpi">DPI:</label>
                    <input type="text" id="edit_dpi" name="dpi" placeholder="Ingrese el DPI" maxlength="13" required>
            </div>

            <div class="form-group">
                    <label for="edit_nombres">Nombres:</label>
                    <input type="text" id="edit_nombres" name="nombres" placeholder="Ingrese los nombres" maxlength="50" required>
            </div>

            <div class="form-group">
                    <label for="edit_apellidos">Apellidos:</label>
                    <input type="text" id="edit_apellidos" name="apellidos" placeholder="Ingrese los apellidos" maxlength="50" required>
            </div>

            <div class="form-group">
                    <label for="edit_fecha">Fecha de Nacimiento:</label>
                    <input type="date" id="edit_fecha" name="fecha" required>
            </div>

            <div class="form-group">
                    <label for="edit_genero">Género:</label>
                <select name="genero" id="edit_genero" required>
                    <option value="">-- Seleccione Género --</option>
                    <?php
                    $generosEdit = $conn->query($sqlGenero);
                    while($g = $generosEdit->fetch_assoc()) { ?>
                        <option value="<?= $g['id_genero'] ?>"><?= $g['nombre'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                    <label for="edit_direccion">Dirección:</label>
                <textarea id="edit_direccion" name="direccion" placeholder="Dirección" required></textarea>
            </div>
            
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


            <div class="form-group">
                <label for="edit_estado">Estado:</label> 
                <select name="estado" id="edit_estado">
                    <option value="">-- Seleccione Estado --</option>
                    <?php
                    $estadosEdit = $conn->query($sqlEstado);
                    while($e = $estadosEdit->fetch_assoc()) { ?>
                        <option value="<?= $e['id_estado_pacientes'] ?>"><?= $e['descripcion'] ?></option>
                    <?php } ?>
                </select>
            </div>

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
    
   <script>
(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const guardar = urlParams.get('guardar');
    const editar = urlParams.get('edit');

    if (guardar === 'ok') {
        Swal.fire({
            title: 'Paciente guardado',
            text: 'La información del paciente se ha ingresado correctamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#3085d6'
        });
    } else if (guardar === 'error') {
        Swal.fire({
            title: 'Error al ingresar',
            text: '❌ Ocurrió un error al ingresar al paciente.',
            icon: 'error',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#d33'
        });
    } else if (editar === 'ok') {
        Swal.fire({
            title: 'Paciente actualizado',
            text: 'La información del paciente se ha editado correctamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#3085d6'
        });
    } else if (editar === 'error') {
        Swal.fire({
            title: 'Error al actualizar',
            text: '❌ Ocurrió un error al editar el paciente.',
            icon: 'error',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#d33'
        });
    }
})();
</script>

    
    <script src="script.js"></script>
</body>
</html>
