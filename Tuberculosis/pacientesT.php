<?php
include(__DIR__."/../conexion.php");

$sql = "SELECT rt.id_pacientes, rt.fecha_referencia, rt.rechazo, 
               rt.fecha_realizacion_prueba, rt.resultado_prueba, rt.fecha_registro,
               p.nombres_pacientes, p.apellidos_pacientes,
               tp.descripcion AS tipo_paciente
        FROM registro_tuberculosis rt
        INNER JOIN pacientes p ON rt.id_pacientes = p.id_pacientes
        INNER JOIN tipo_paciente_tb tp ON rt.id_tipo_paciente = tp.id_tipo_paciente
        ORDER BY rt.fecha_registro DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Tuberculosis</title>
    <link rel="stylesheet" href="css/pacientes.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
      .filtros {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 20px 0;
}

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
    filter: blur(10px); 
    z-index: -1;
    opacity: 2;
}

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
            <h1>PACIENTES CON TB</h1>
        </div>
    </div>
    </header>

    <section class="table" id="customers_table">

    <div class="filtros">
            <label for="buscador">Buscar por nombre del paciente:</label>
            <input type="text" id="buscador" placeholder="Ej. Nahomi Lopez">
    </div>

    <a href="reporte.php" class="btn">Reporte General</a> 
    <a href="ingresar_pacientesT.php" class="btn">Registrar Tuberculosis</a> 
    
        <table>
            <thead>
                <tr>
                    <th>Paciente</th>   
                    <th>Tipo Paciente</th>                 
                    <th>Fecha Referencia</th>
                    <th>Rechazo</th>
                    <th>Fecha Realización</th>
                    <th>Resultado</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th> 
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $nombreCompleto = $row['nombres_pacientes']." ".$row['apellidos_pacientes'];
                        $idPaciente = $row['id_pacientes'];
                        $sqlTrat = "SELECT 1 FROM seguimiento_medicamentos_tb_tdo WHERE id_pacientes= $idPaciente LIMIT 1";
                        $tieneTratamiento = $conn->query($sqlTrat)->num_rows > 0;

                       echo "<tr>
                            <td>$nombreCompleto</td>
                            <td>{$row['tipo_paciente']}</td>
                            <td>{$row['fecha_referencia']}</td>
                            <td>{$row['rechazo']}</td>
                            <td>{$row['fecha_realizacion_prueba']}</td>
                            <td>{$row['resultado_prueba']}</td>
                            <td>{$row['fecha_registro']}</td>
                            <td>";
        
                            if ($tieneTratamiento) {
                                echo "
                                    <a href='tratamientos.php?id_paciente=$idPaciente' class='btn btn-accion' title='Ver tratamiento completo'>
                                        <span class='icon'>💊</span>
                                    </a>
                                    <button class='btn btn-accion btn-ver' data-id='$idPaciente' title='Ver resumen de fase'>
                                        <span class='icon'>🔍</span>
                                    </button>
                                ";
                            } else {
                                echo "
                                    <a href='nuevo_tratamiento.php?id_paciente=$idPaciente' class='btn btn-accion' title='Iniciar tratamiento'>
                                        <span class='icon'>💊</span>
                                    </a>
                                ";
                            }

                        echo "</td></tr>";

                    }
                } else {
                    echo "<tr><td colspan='8'>No hay registros en la tabla.</td></tr>";
                }
                ?>
            </tbody>
        </table>

     <!--   <a href="index.php" class="btn">Volver al Inicio</a> -->
    </main>

 
    <div id="modalFase" class="modal" style="display:none;">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Resumen de tratamiento</h3>
        <div id="faseDetalle">
          <p>Cargando datos...</p>
        </div>
      </div>
    </div>

<style>
.modal {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex; justify-content: center; align-items: center;
  z-index: 999;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 300px;
  position: relative;
}
.close {
  position: absolute;
  top: 8px; right: 12px;
  font-size: 20px;
  cursor: pointer;
}
</style>

<script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buscador = document.getElementById('buscador');
            const filas = document.querySelectorAll('tbody tr');

            buscador.addEventListener('input', function () {
                const filtro = buscador.value.toLowerCase();

                filas.forEach(fila => {
                    const texto = fila.textContent.toLowerCase();
                    fila.style.display = texto.includes(filtro) ? '' : 'none';
                });
            });
        });
    </script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('modalFase');
  const closeBtn = document.querySelector('.close');
  const detalle = document.getElementById('faseDetalle');

  document.querySelectorAll('.btn-ver').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');

      fetch('fase_resumen.php?id_paciente=' + id)
        .then(res => res.json())
        .then(data => {
          detalle.innerHTML = `
            <p><strong>Fase actual:</strong> ${data.fase}</p>
            <p><strong>Dosis recibidas:</strong> ${data.recibidas}</p>
            <p><strong>Dosis pendientes:</strong> ${data.pendientes}</p>
          `;
          modal.style.display = 'flex';
        });
    });
  });

  closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
  });
});
</script>
 <script src="script.js"></script>
</body>
</html>
