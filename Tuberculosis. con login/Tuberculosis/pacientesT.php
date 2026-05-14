<?php
include("conexion.php");

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
</head>
<body>
    <div id="contenido" ></div>
    <header class="header" ><div class="header-content container">
        <div class="header-txt">
            <h1>PACIENTES CON TB</h1>
        </div>
    </div>
    </header>

    <main class="table" id="customers_table">
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
                        $sqlTrat = "SELECT 1 FROM Seguimiento_medicamentos_tb_TDO WHERE ID_PACIENTES = $idPaciente LIMIT 1";
                        $tieneTratamiento = $conn->query($sqlTrat)->num_rows > 0;

                        echo "<tr>
                                <td>".$nombreCompleto."</td>
                                <td>".$row['tipo_paciente']."</td>
                                <td>".$row['fecha_referencia']."</td>
                                <td>".$row['rechazo']."</td>
                                <td>".$row['fecha_realizacion_prueba']."</td>
                                <td>".$row['resultado_prueba']."</td>
                                <td>".$row['fecha_registro']."</td>
                                <td>";
                                    if ($tieneTratamiento) {
                                        
                                        echo "<a href='tratamientos.php?id_paciente=$idPaciente' class='btn'>Tratamiento</a>";
                                    } else {
                                    
                                        echo "<a href='nuevo_tratamiento.php?id_paciente=$idPaciente' class='btn'>Tratamiento</a>";
                                    }
                        echo    "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay registros en la tabla.</td></tr>";
                }
                ?>
            </tbody>
        </table>

     <!--   <a href="index.php" class="btn">Volver al Inicio</a> -->
    </main>
</body>
</html>
