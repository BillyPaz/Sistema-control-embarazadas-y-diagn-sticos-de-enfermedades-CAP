<?php
// control_prenatal.php
require_once '../../SETTINGS/php/conexion.php';

$id_registro = isset($_GET['id_registro']) ? intval($_GET['id_registro']) : 0;
if ($id_registro <= 0) {
  die("Falta parámetro id_registro");
}

$conexion = new Conexion();
$conn = $conexion->getConnection();

// Traer datos del registro + paciente (incluye FUR para cálculo)
$sql = "
  SELECT r.ID_REGISTRO_CLINICO_PRENATAL, r.FUR, r.NO_EXPEDIENTE, r.NOMBRE_SERVICIO,
         p.ID_PACIENTES, p.NOMBRES_PACIENTES, p.APELLIDOS_PACIENTES, p.DPI_PACIENTES,
         TIMESTAMPDIFF(YEAR, p.FECHA_NACIMIENTO, CURDATE()) AS EDAD
  FROM registro_clinico_prenatal r
  INNER JOIN pacientes p ON p.ID_PACIENTES = r.id_pacientes
  WHERE r.ID_REGISTRO_CLINICO_PRENATAL = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_registro);
$stmt->execute();
$reg = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$reg) { die("Registro clínico no encontrado"); }
$fur = $reg['FUR']; // puede ser null

// Controles existentes (para pintar estado)
$sql2 = "
  SELECT CONTROL_MES, FECHA_VISITA, SEMANAS_EMBARAZO_FUR
  FROM seguimiento_prenatal
  WHERE ID_REGISTRO_CLINICO_PRENATAL = ?
  ORDER BY CONTROL_MES
";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $id_registro);
$stmt2->execute();
$res2 = $stmt2->get_result();

$controles_exist = [];
while ($r = $res2->fetch_assoc()) {
  $controles_exist[(int)$r['CONTROL_MES']] = $r;
}
$stmt2->close();

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Controles Prenatales (1-8)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    body { background:#f5f7fb; }
    .card { box-shadow: 0 4px 10px rgba(0,0,0,.08); border-left:4px solid transparent; }
    .card.done { border-left-color:#28a745; background:#f0fff4; }
    .card.pending { border-left-color:#6c757d; }
  </style>
</head>
<body>
  <div class="container py-4">
    <div class="mb-4">
      <h3 class="mb-1"><i class="bi bi-clipboard-check"></i> Control Prenatal por Paciente</h3>
      <div class="text-muted">
        <b>Paciente:</b> <?= htmlspecialchars($reg['NOMBRES_PACIENTES'].' '.$reg['APELLIDOS_PACIENTES']) ?> (<?= (int)$reg['EDAD'] ?> años) | 
        <b>DPI:</b> <?= htmlspecialchars($reg['DPI_PACIENTES']) ?> | 
        <b>Expediente:</b> <?= htmlspecialchars($reg['NO_EXPEDIENTE']) ?> | 
        <b>Servicio:</b> <?= htmlspecialchars($reg['NOMBRE_SERVICIO']) ?> |
        <b>FUR:</b> <?= $fur ? htmlspecialchars($fur) : '—' ?>
      </div>
    </div>

    <div id="alertContainer"></div>

    <div class="row">
      <?php for ($mes=1; $mes<=8; $mes++): 
        $existe = isset($controles_exist[$mes]);
        $estadoClass = $existe ? 'done' : 'pending';
        $badge = $existe 
          ? '<span class="badge bg-success">Registrado</span>' 
          : '<span class="badge bg-secondary">Pendiente</span>';
        $visitaTxt = $existe && $controles_exist[$mes]['FECHA_VISITA'] ? date('d/m/Y', strtotime($controles_exist[$mes]['FECHA_VISITA'])) : '—';
        $semTxt = $existe && $controles_exist[$mes]['SEMANAS_EMBARAZO_FUR'] !== null ? intval($controles_exist[$mes]['SEMANAS_EMBARAZO_FUR']).' sem' : '—';
      ?>
      <div class="col-md-3 mb-3">
        <div class="card <?= $estadoClass ?>">
          <div class="card-body">
            <h5 class="card-title mb-1">Control <?= $mes ?> Meses</h5>
            <div class="mb-2"><?= $badge ?></div>
            <div class="small text-muted">
              <div><i class="bi bi-calendar2"></i> Visita: <?= $visitaTxt ?></div>
              <div><i class="bi bi-heart-pulse"></i> Semanas: <?= $semTxt ?></div>
            </div>
            <button class="btn btn-primary btn-sm mt-3 w-100" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalControl" 
                    data-control="<?= $mes ?>">
              <?= $existe ? 'Editar control' : 'Registrar control' ?>
            </button>
          </div>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>

  <!-- Modal captura/edición -->
  <div class="modal fade" id="modalControl" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <form id="formControl">
          <div class="modal-header">
            <h5 class="modal-title"><span id="tituloModal">Control</span> - Datos clínicos</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id_registro" value="<?= (int)$id_registro ?>">
            <input type="hidden" name="control_mes" id="control_mes">
            <input type="hidden" name="fur_base" id="fur_base" value="<?= $fur ? htmlspecialchars($fur) : '' ?>">

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Fecha visita</label>
                <input type="date" class="form-control" name="fecha_visita" id="fecha_visita" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Semanas (auto)</label>
                <input type="number" class="form-control" name="semanas" id="semanas" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label">Presión arterial (mmHg)</label>
                <input type="text" class="form-control" name="presion_arterial" id="presion_arterial" placeholder="120/80">
              </div>

              <div class="col-md-4">
                <label class="form-label">Temperatura (°C)</label>
                <input type="number" step="0.1" class="form-control" name="temperatura" id="temperatura">
              </div>
              <div class="col-md-4">
                <label class="form-label">Peso (lb)</label>
                <input type="number" step="0.1" class="form-control" name="peso" id="peso">
              </div>
              <div class="col-md-4">
                <label class="form-label">Respiraciones/min</label>
                <input type="number" class="form-control" name="respiraciones" id="respiraciones">
              </div>

              <div class="col-md-4">
                <label class="form-label">Frecuencia cardiaca (lpm)</label>
                <input type="number" class="form-control" name="fc" id="fc">
              </div>
              <div class="col-md-4">
                <label class="form-label">Hemoglobina (g/dL)</label>
                <input type="number" step="0.1" class="form-control" name="hemoglobina" id="hemoglobina">
              </div>
              <div class="col-md-4">
                <label class="form-label">Orina</label>
                <input type="text" class="form-control" name="orina" id="orina">
              </div>

              <div class="col-md-4">
                <label class="form-label">VDRL</label>
                <input type="text" class="form-control" name="vdrl" id="vdrl">
              </div>
              <div class="col-md-4">
                <label class="form-label">Sulfato ferroso</label>
                <select class="form-select" name="sulfato" id="sulfato">
                  <option value="">Seleccione</option>
                  <option value="1">Sí</option>
                  <option value="0">No</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Ácido fólico</label>
                <select class="form-select" name="acido" id="acido">
                  <option value="">Seleccione</option>
                  <option value="1">Sí</option>
                  <option value="0">No</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Problema detectado</label>
                <textarea class="form-control" rows="2" name="problema" id="problema"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Observaciones</label>
                <textarea class="form-control" rows="2" name="observaciones" id="observaciones"></textarea>
              </div>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Guardar control</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    const idRegistro = <?= (int)$id_registro ?>;
    const furBase = document.getElementById('fur_base').value;

    function semanasDesdeFUR(fur, fechaVisita) {
      if (!fur || !fechaVisita) return '';
      const d1 = new Date(fur);
      const d2 = new Date(fechaVisita);
      const diff = d2 - d1; // ms
      if (isNaN(diff)) return '';
      const semanas = Math.floor(diff / (1000*60*60*24*7));
      return semanas < 0 ? 0 : semanas;
    }

    // Abrir modal con el número de control
    const modal = document.getElementById('modalControl');
    modal.addEventListener('show.bs.modal', function (event) {
      const btn = event.relatedTarget;
      const controlMes = btn.getAttribute('data-control');
      document.getElementById('control_mes').value = controlMes;
      document.getElementById('tituloModal').innerText = `Control ${controlMes} Meses`;

      // Limpiar / precargar si existe
      limpiarModal();

      // Cargar datos del control si ya existe
      $.get('../php/obtener_controles.php', { id_registro: idRegistro, control_mes: controlMes }, function(resp) {
        if (resp.success && resp.data) {
          const d = resp.data;
          $('#fecha_visita').val(d.FECHA_VISITA || '');
          $('#semanas').val(d.SEMANAS_EMBARAZO_FUR || '');
          $('#presion_arterial').val(d.PRESION_ARTERIAL || '');
          $('#temperatura').val(d.TEMPERATURA_CORPORAL || '');
          $('#peso').val(d.PESO_LIBRAS || '');
          $('#respiraciones').val(d.RESPIRACIONES_MINUTO || '');
          $('#fc').val(d.FECUENCIA_CARDIACA || '');
          $('#hemoglobina').val(d.HEMOGLOBINA || '');
          $('#orina').val(d.ORINA || '');
          $('#vdrl').val(d.VDRL || '');
          $('#sulfato').val(d.SULFATO_FERROSO ?? '');
          $('#acido').val(d.ACIDO_FOLICO ?? '');
          $('#problema').val(d.PROBLEMA_DETECTADO || '');
          $('#observaciones').val(d.OBSERVACIONES || '');
        }
      }, 'json');
    });

    function limpiarModal() {
      $('#formControl')[0].reset();
      $('#semanas').val('');
    }

    // Autocalcular semanas
    $('#fecha_visita').on('change', function() {
      const sem = semanasDesdeFUR(furBase, this.value);
      $('#semanas').val(sem);
    });

    // Guardar
    $('#formControl').on('submit', function(e) {
      e.preventDefault();
      const payload = $(this).serialize();

      $.post('../php/guardar_control_prenatal.php', payload, function(resp) {
        if (resp.success) {
          showAlert('Control guardado correctamente', 'success');
          setTimeout(() => location.reload(), 900);
        } else {
          showAlert(resp.message || 'Error al guardar', 'danger');
        }
      }, 'json').fail(function(xhr){
        showAlert('Error de conexión: ' + xhr.statusText, 'danger');
      });
    });

    function showAlert(msg, type) {
      const html = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`;
      $('#alertContainer').html(html);
    }
  </script>
</body>
</html>
