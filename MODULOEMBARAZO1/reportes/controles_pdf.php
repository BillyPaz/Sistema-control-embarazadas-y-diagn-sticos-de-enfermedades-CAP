<?php
require_once '../../SETTINGS/php/conexion.php';
require_once '../../SETTINGS/lib/fpdf/fpdf.php';

$idRegistro = isset($_GET['idRegistro'])?intval($_GET['idRegistro']):0;
if($idRegistro<=0){ die('Registro inválido'); }

$conexion = new Conexion();
$conn = $conexion->getConnection();

// Encabezado paciente + registro
$info = $conn->prepare("
  SELECT r.NO_EXPEDIENTE, r.FECHA_INGRESO, r.FUR,
         p.NOMBRES_PACIENTES, p.APELLIDOS_PACIENTES, p.DPI_PACIENTES,
         TIMESTAMPDIFF(YEAR,p.FECHA_NACIMIENTO,CURDATE()) AS EDAD
  FROM registro_clinico_prenatal r
  JOIN pacientes p ON p.ID_PACIENTES=r.id_pacientes
  WHERE r.ID_REGISTRO_CLINICO_PRENATAL=?");
$info->bind_param("i",$idRegistro);
$info->execute();
$hdr = $info->get_result()->fetch_assoc();
$info->close();

$q = $conn->prepare("
  SELECT CONTROL_NUM, FECHA_VISITA, SEMANAS_EMBARAZO_FUR,
         CIRCUNFERENCIA_BRAZO, MASA_CORPORAL, PRESION_ARTERIAL, TEMPERATURA_CORPORAL,
         PESO_LIBRAS, RESPIRACIONES_MINUTO, FECUENCIA_CARDIACA, HEMOGLOBINA, ORINA, VDRL,
         SULFATO_FERROSO, ACIDO_FOLICO, PROBLEMA_DETECTADO
  FROM seguimiento_prenatal
  WHERE ID_REGISTRO_CLINICO_PRENATAL=?
  ORDER BY CONTROL_NUM ASC");
$q->bind_param("i",$idRegistro);
$q->execute();
$res = $q->get_result();
$rows=[];
while($r=$res->fetch_assoc()){$rows[]=$r;}
$q->close();

class PDF extends FPDF{
  function Header(){
    $this->SetFont('Arial','B',12);
    $this->Cell(190,8,utf8_decode('Controles Prenatales (1 a 8)'),0,1,'C');
    $this->Ln(2);
  }
  function Footer(){
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
  }
}
$pdf = new PDF('P','mm','A4'); $pdf->AliasNbPages(); $pdf->SetMargins(10,10,10);
$pdf->AddPage();

$pdf->SetFont('Arial','',9);
$pdf->Cell(95,6,utf8_decode('Paciente: ').utf8_decode($hdr['APELLIDOS_PACIENTES'].', '.$hdr['NOMBRES_PACIENTES']),0,0);
$pdf->Cell(95,6,'DPI: '.$hdr['DPI_PACIENTES'],0,1);
$pdf->Cell(95,6,'Edad: '.$hdr['EDAD'].' años',0,0);
$pdf->Cell(95,6,'Expediente: '.utf8_decode($hdr['NO_EXPEDIENTE']),0,1);
$pdf->Cell(95,6,'Ingreso: '.$hdr['FECHA_INGRESO'],0,0);
$pdf->Cell(95,6,'FUR: '.($hdr['FUR']?:'-'),0,1);
$pdf->Ln(2);

// Cabecera
$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(67,97,238); $pdf->SetTextColor(255);
$cols = ['Ctrl','Visita','Sem','PA','Temp','Peso','Resp','FC','HB','Orina','VDRL','SF','AF'];
$w    = [10,22,10,18,12,12,12,12,12,20,18,8,8];
for($i=0;$i<count($cols);$i++){ $pdf->Cell($w[$i],7,utf8_decode($cols[$i]),1,0,'C',true); }
$pdf->Ln();
$pdf->SetTextColor(0); $pdf->SetFont('Arial','',8);

// Filas 1..8 (si faltan, deja vacías)
$byControl = [];
foreach($rows as $r){ $byControl[intval($r['CONTROL_NUM'])] = $r; }

for($c=1;$c<=8;$c++){
  $r = $byControl[$c] ?? [];
  $pdf->Cell($w[0],6,$c,1,0,'C');
  $pdf->Cell($w[1],6,$r['FECHA_VISITA']??'',1,0,'C');
  $pdf->Cell($w[2],6,$r['SEMANAS_EMBARAZO_FUR']??'',1,0,'C');
  $pdf->Cell($w[3],6,utf8_decode($r['PRESION_ARTERIAL']??''),1,0,'C');
  $pdf->Cell($w[4],6,$r['TEMPERATURA_CORPORAL']??'',1,0,'C');
  $pdf->Cell($w[5],6,$r['PESO_LIBRAS']??'',1,0,'C');
  $pdf->Cell($w[6],6,$r['RESPIRACIONES_MINUTO']??'',1,0,'C');
  $pdf->Cell($w[7],6,$r['FECUENCIA_CARDIACA']??'',1,0,'C');
  $pdf->Cell($w[8],6,$r['HEMOGLOBINA']??'',1,0,'C');
  $pdf->Cell($w[9],6,utf8_decode($r['ORINA']??''),1,0,'C');
  $pdf->Cell($w[10],6,utf8_decode($r['VDRL']??''),1,0,'C');
  $pdf->Cell($w[11],6,isset($r['SULFATO_FERROSO'])? $r['SULFATO_FERROSO']:'',1,0,'C');
  $pdf->Cell($w[12],6,isset($r['ACIDO_FOLICO'])? $r['ACIDO_FOLICO']:'',1,1,'C');
}

$pdf->Ln(2);
$pdf->SetFont('Arial','I',8);
$pdf->MultiCell(190,5,utf8_decode('Nota: las semanas de embarazo se calculan automáticamente desde FUR y la fecha de visita.'));

$pdf->Output('I','ControlesPrenatales_'.$idRegistro.'.pdf');
$conexion->close();
