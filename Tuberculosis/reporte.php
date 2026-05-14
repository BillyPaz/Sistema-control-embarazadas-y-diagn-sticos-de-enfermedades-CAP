<?php
include(__DIR__."/conexion.php");
$sql = "SELECT 
    s.id_seguimiento,
    p.nombres_pacientes,
    p.apellidos_pacientes,
    p.dpi_pacientes,
    f.nombre_fase AS fase_terminada,
    s.fecha_inicio,
    MAX(c.fecha_registro) AS fecha_finalizacion,
    e.resultado,
    f2.nombre_fase AS siguiente_fase
FROM seguimiento_medicamentos_tb_tdo s
JOIN pacientes p ON s.id_pacientes = p.id_pacientes
JOIN fase_tb f ON s.id_fase = f.id_fase
JOIN calendario_seguimiento_tb c ON s.id_seguimiento = c.id_seguimiento
JOIN evaluaciones_tb e ON s.id_seguimiento = e.id_seguimiento
LEFT JOIN seguimiento_medicamentos_tb_tdo s2 ON s2.id_pacientes = s.id_pacientes AND s2.fecha_inicio > s.fecha_inicio
LEFT JOIN fase_tb f2 ON s2.id_fase = f2.id_fase

GROUP BY s.id_seguimiento, e.resultado, f.nombre_fase, p.nombres_pacientes, p.apellidos_pacientes, f2.nombre_fase
ORDER BY fecha_finalizacion DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
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
        </div>
    </div>
    </header>


    <section class="table">
        <div class="table__header">
            <h2>Reportes</h2>
        </div>

        <div class="filtros">
            <label for="buscador">Buscar por nombre, DPI o resultado:</label>
            <input type="text" id="buscador" placeholder="Ej. Nahomi, 123456789, Positivo">
        </div>

        <div class="table__body">
            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>DPI</th>
                        <th>Fase terminada</th>
                        <th>Fecha inicio</th>
                        <th>Fecha finalización</th>
                        <th>Resultado</th>
                        <th>Fase siguiente</th>
                        <th>Reporte</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;">No hay pacientes con tratamiento finalizado.</td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['nombres_pacientes'] . ' ' . $row['apellidos_pacientes'] ?></td>
                                <td><?= $row['DPI_PACIENTES'] ?></td>
                                <td><?= $row['fase_terminada'] ?></td>
                                <td><?= date('Y-m-d', strtotime($row['fecha_inicio'])) ?></td>
                                <td><?= date('Y-m-d', strtotime($row['fecha_finalizacion'])) ?></td>
                                <td><?= $row['RESULTADO'] ?></td>
                                <td><?= $row['siguiente_fase'] ?? '—' ?></td>
                                <td>
                                    <a class="btn" href="generar_reporte.php?id_seguimiento=<?= $row['id_seguimiento'] ?>" title="Descargar reporte">
                                        📄 Descargar
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    </main>
    <script> src </script>

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
<script src="script.js"></script>
</body>
</html>
