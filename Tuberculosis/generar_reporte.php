<?php
require_once __DIR__ . '/libsPDF/dompdf/autoload.inc.php';
include("conexion.php");

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id_seguimiento'])) {
    die("Error: No se proporcionó el ID del seguimiento.");
}

$id_seguimiento = intval($_GET['id_seguimiento']);

$sql = "select 
    s.id_seguimiento,
    p.nombres_pacientes,
    p.apellidos_pacientes,
    p.dpi_pacientes,
    f.nombre_fase as fase_terminada,
    s.fecha_inicio,
    max(c.fecha_registro) as fecha_finalizacion,
    e.resultado,
    f2.nombre_fase as siguiente_fase
from seguimiento_medicamentos_tb_tdo s
join pacientes p on s.id_pacientes = p.id_pacientes
join fase_tb f on s.id_fase = f.id_fase
join calendario_seguimiento_tb c on s.id_seguimiento = c.id_seguimiento
join evaluaciones_tb e on s.id_seguimiento = e.id_seguimiento
left join seguimiento_medicamentos_tb_tdo s2 
    on s2.id_pacientes = s.id_pacientes and s2.fecha_inicio > s.fecha_inicio
left join fase_tb f2 on s2.id_fase = f2.id_fase
where s.id_seguimiento = $id_seguimiento
group by s.id_seguimiento";

$result = $conn->query($sql);
if ($result->num_rows === 0) {
    die("No se encontraron datos para este seguimiento.");
}

$row = $result->fetch_assoc();

$fechaInicio = date('Y-m-d', strtotime($row['fecha_inicio']));
$fechaFinal = date('Y-m-d', strtotime($row['fecha_finalizacion']));

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$html = "
<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        color: #02457a;
        background-color: #ffffff;
        margin: 20px;
    }

    .encabezado {
        text-align: center;
        border-bottom: 2px solid #02457a;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .encabezado img {
        width: 110px;
        height: auto;
        margin-bottom: 5px;
    }

    .titulo-centro {
        font-size: 16px;
        font-weight: bold;
        color: #02457a;
    }

    .subtitulo {
        font-size: 13px;
        font-weight: normal;
        color: #0288d1;
    }

    h2 {
        text-align: center;
        background-color: #02457a;
        color: white;
        padding: 8px;
        border-radius: 5px;
        font-size: 17px;
    }

    .datos {
        margin: 20px 0;
        font-size: 13px;
        line-height: 1.6;
    }

    .datos p {
        margin: 4px 0;
    }

    hr {
        border: 1px solid #0288d1;
        margin: 15px 0;
    }

    .pie {
        text-align: center;
        font-size: 11px;
        color: #555;
        margin-top: 25px;
        border-top: 1px solid #ccc;
        padding-top: 5px;
    }
</style>
</head>
<body>

<div class='encabezado'>   
    <div class='titulo-centro'>Nuevo Progreso, San Marcos</div>
    <div class='subtitulo'>PUESTO DE SALUD</div>
</div>

<h2>Reporte de Seguimiento del Paciente</h2>

<div class='datos'>
    <p><strong>Nombre del paciente:</strong> {$row['nombres_pacientes']} {$row['apellidos_pacientes']}</p>
    <p><strong>DPI:</strong> {$row['dpi_pacientes']}</p>
    <p><strong>Fase terminada:</strong> {$row['fase_terminada']}</p>
    <p><strong>Fecha de inicio:</strong> {$fechaInicio}</p>
    <p><strong>Fecha de finalización:</strong> {$fechaFinal}</p>
    <p><strong>Resultado final:</strong> {$row['resultado']}</p>
    <p><strong>Siguiente fase:</strong> " . ($row['siguiente_fase'] ?? '—') . "</p>
</div>

<hr>

<div class='pie'>
    <p>Reporte generado automáticamente por el sistema de control de tuberculosis.</p>
    <p>© Puesto de Salud Nuevo Progreso, San Marcos</p>
</div>

</body>
</html>
";

// Cargar contenido HTML al PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Descargar automáticamente
$dompdf->stream("reporte_paciente_{$id_seguimiento}.pdf", ["Attachment" => true]);
?>
