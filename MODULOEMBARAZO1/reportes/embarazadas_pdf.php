<?php
// embarazadas_pdf.php
// Genera PDF con listado de embarazadas por fecha de ingreso (rango)

require_once '../../SETTINGS/php/conexion.php';
require_once '../../SETTINGS/lib/fpdf/fpdf.php';

function safeDate($d) {
  // Asegura formato YYYY-MM-DD o devuelve null
  if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return $d;
  return null;
}

$fini = isset($_GET['fini']) ? safeDate($_GET['fini']) : null;
$ffin = isset($_GET['ffin']) ? safeDate($_GET['ffin']) : null;

// Si no vienen fechas válidas, usa hoy
$hoy = date('Y-m-d');
if (!$fini) $fini = $hoy;
if (!$ffin) $ffin = $hoy;

// Si fin < ini, las invertimos
if (strtotime($ffin) < strtotime($fini)) {
  $tmp = $fini;
  $fini = $ffin;
  $ffin = $tmp;
}

$conexion = new Conexion();
$conn = $conexion->getConnection();

// Consulta: une registro_clinico_prenatal con pacientes
$sql = "
  SELECT 
    r.ID_REGISTRO_CLINICO_PRENATAL,
    r.FECHA_INGRESO,
    r.NO_EXPEDIENTE,
    r.FUR,
    r.FECHA_ULTIMA_DOSIS_TD,
    r.FECHA_ULTIMA_DOSIS_TDAP,
    p.ID_PACIENTES,
    p.DPI_PACIENTES,
    p.NOMBRES_PACIENTES,
    p.APELLIDOS_PACIENTES,
    p.FECHA_NACIMIENTO,
    TIMESTAMPDIFF(YEAR, p.FECHA_NACIMIENTO, CURDATE()) AS EDAD,
    p.TELEFONO,
    p.DIRECCION
  FROM registro_clinico_prenatal r
  INNER JOIN pacientes p ON p.ID_PACIENTES = r.id_pacientes
  WHERE r.FECHA_INGRESO BETWEEN ? AND ?
  ORDER BY r.FECHA_INGRESO ASC, p.APELLIDOS_PACIENTES ASC, p.NOMBRES_PACIENTES ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $fini, $ffin);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
  // Calcula FPP (Fecha probable de parto) = FUR + 280 días, si hay FUR
  $fpp = '';
  if (!empty($row['FUR'])) {
    $fppDate = date('Y-m-d', strtotime($row['FUR'] . ' +280 days'));
    $fpp = $fppDate;
  }
  $row['FPP'] = $fpp;
  $data[] = $row;
}

$stmt->close();

// --- PDF ---
class PDF extends FPDF {
  public $titulo;
  public $subtitulo;

  function Header() {
    // Encabezado
    $this->SetFont('Arial','B',12);
    $this->Cell(190,8, utf8_decode($this->titulo), 0, 1, 'C');
    $this->SetFont('Arial','',9);
    $this->Cell(190,5, utf8_decode($this->subtitulo), 0, 1, 'C');
    $this->Ln(4);

    // Cabecera de tabla
    $this->SetFillColor(67,97,238); // azul
    $this->SetTextColor(255,255,255);
    $this->SetFont('Arial','B',8);

    $this->Cell(24,7,'Ingreso',1,0,'C',true);
    $this->Cell(24,7,utf8_decode('Expediente'),1,0,'C',true);
    $this->Cell(52,7,'Paciente',1,0,'C',true);
    $this->Cell(24,7,'DPI',1,0,'C',true);
    $this->Cell(12,7,utf8_decode('Edad'),1,0,'C',true);
    $this->Cell(18,7,utf8_decode('Teléfono'),1,0,'C',true);
    $this->Cell(36,7,'FUR / FPP',1,1,'C',true);

    $this->SetTextColor(0,0,0);
  }

  function Footer() {
    // Pie de página
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
  }

  function RowBody($row) {
    $this->SetFont('Arial','',8);

    $paciente = $row['APELLIDOS_PACIENTES'].', '.$row['NOMBRES_PACIENTES'];
    $fur_fpp = ($row['FUR'] ? $row['FUR'] : '-') . ' / ' . ($row['FPP'] ? $row['FPP'] : '-');

    // Ajustes de ancho
    $this->Cell(24,6, $row['FECHA_INGRESO'],1,0,'C');
    $this->Cell(24,6, utf8_decode($row['NO_EXPEDIENTE']),1,0,'C');

    // Paciente puede ser más largo → celda multicelda simulada
    $this->Cell(52,6, utf8_decode($paciente),1,0,'L');

    $this->Cell(24,6, $row['DPI_PACIENTES'],1,0,'C');
    $this->Cell(12,6, $row['EDAD'],1,0,'C');
    $this->Cell(18,6, $row['TELEFONO'],1,0,'C');
    $this->Cell(36,6, $fur_fpp,1,1,'C');
  }
}

$pdf = new PDF('P','mm','A4');
$pdf->titulo = 'Listado de embarazadas registradas';
$pdf->subtitulo = 'Desde: '.$fini.' Hasta '.$ffin;
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->AddPage();

// Si no hay datos
if (count($data) === 0) {
  $pdf->SetFont('Arial','I',10);
  $pdf->Cell(190,8,utf8_decode('No se encontraron registros en el rango indicado.'),0,1,'C');
} else {
  foreach ($data as $row) {
    // Salto de página seguro si queda poco espacio
    if ($pdf->GetY() > 265) {
      $pdf->AddPage();
    }
    $pdf->RowBody($row);
  }

  // Total
  $pdf->Ln(4);
  $pdf->SetFont('Arial','B',9);
  $pdf->Cell(190,6,utf8_decode('Total de registros: ').count($data),0,1,'R');
}

$pdf->Output('I', 'Listado_Embarazadas_'.$fini.'_a_'.$ffin.'.pdf');

$conexion->close();
