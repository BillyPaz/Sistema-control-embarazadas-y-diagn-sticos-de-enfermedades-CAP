<?php
// controles_prenatales_modales.php
require_once '../../SETTINGS/php/conexion.php';
$conexion = new Conexion();
$conn = $conexion->getConnection();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Controles Prenatales (Modales 1–8)</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <!-- Estilos globales del sistema (mismo look & feel de pacientes.php) -->
  <link rel="stylesheet" href="../../css/pacientes.css">

  <style>
    /* ===== Ajustes visuales puntuales para este submódulo, SIN cambiar la lógica ===== */

    /* Fondo y tipografía (heredados de pacientes.css) */
    body { background-color:#ffffff; }

    /* Encabezado grande con la misma estética que pacientes.php */
    .header{
      background-image: url(../../imagenes/bg.png);
      background-position:center center;
      background-repeat:no-repeat;
      background-size:cover;
      display:flex; position:relative; min-height:38vh; align-items:center;
    }
    .header-txt h1{
      font-size:56px; line-height:64px; font-weight:700; text-transform:uppercase;
      color:#02457a; margin-bottom:0; margin-left: 180px; font-family:"Roboto", sans-serif;
    }
    .header-sub{
      margin-left: 180px; margin-top:10px; color:#0b2a47; font-weight:500;
      background:#ffffffd9; display:inline-block; padding:6px 12px; border-radius:10px;
    }

    /* Contenedor principal tipo “card/table” igual a pacientes.php */
    main.table{
      width: 90%;
      margin: 30px auto 70px auto;
      background: #fff;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0px 4px 8px rgba(0,0,0,.2);
    }

    .table__header{
      background-color:#02457a;
      color:#fff;
      padding: 18px 20px;
      display:flex; align-items:center; justify-content:space-between; gap:14px;
    }
    .table__header h2{
      margin:0; font-size:24px; letter-spacing:.3px; font-family: "Roboto", sans-serif;
    }

    /* Fila de filtros superior (mismo patrón que pacientes.php) */
    .filtros {
      display:flex; justify-content:space-between; align-items:center;
      gap:12px; padding: 16px 20px; background:#fbfcfe; border-bottom:1px solid #eef1f5;
    }
    .filtros .search-wrap-inline{
      display:flex; gap:10px; align-items:center; width:100%;
    }
    #q{
      flex:1; min-width:260px;
      height:42px; border-radius:10px; border:1px solid #ccc; padding:0 12px;
      font-size:14px;
    }
    .btn-inline{
      display:inline-block; background:#02457a; color:#fff; padding:10px 14px;
      border-radius:10px; border:none; font-weight:600; font-family:"Roboto", sans-serif;
    }
    .btn-inline:disabled{opacity:.7; cursor:not-allowed;}
    .btn-outline{
      background:#97CADB; color:#001B48; border:none; border-radius:10px; padding:10px 14px; font-weight:bold;
    }
    .btn-outline:hover{ background:#018ABE; color:#fff; }

    /* Tarjetas y chips con el mismo tono del sistema */
    .card { border:0; box-shadow: 0 2px 6px rgba(0,0,0,.06); border-radius:14px; }
    .patient-chip{
      display:inline-flex; align-items:center; gap:10px;
      background:#f1f6ff; border:1px dashed rgba(2,69,122,.35);
      color:#0f2a44; padding:8px 12px; border-radius:999px; font-weight:600;
    }
    .info-badge{
      background:#eef6ff; color:#02457a; border-radius:999px; padding:6px 12px; font-weight:600;
    }
    .muted{ color:#6b7280; }

    /* Tabla de controles embebida dentro del “card/table” */
    .table-controls thead th{
      background:#02457a; color:#fff; border:0; white-space:nowrap;
    }
    .table-controls tbody tr:hover{ background:#f7fbff; }
    .btn-ctl{ border-radius:10px; font-weight:700 }

    /* Modales con encabezado azul del sistema */
    .modal-header{
      background-color:#02457a; color:#fff; border:0;
    }
    .modal-content{ border-radius:16px; }
    .form-label{ font-weight:600; color:#0f2a44; }

    /* Reubicar el spinner del botón “Entregar” sin tocar JS */
    #spnBuscar{ vertical-align: -2px; }

    /* Margen responsive */
    @media (max-width: 992px){
      .header-txt h1, .header-sub { margin-left: 24px; }
      .filtros{ flex-direction:column; align-items:stretch; }
      .table__header{ flex-direction:column; align-items:flex-start; gap:10px; }
    }
  </style>
</head>
<body>

  <!-- Hero con el mismo diseño que pacientes.php -->
  <header class="header">
    <div class="header-content container">
      <div class="header-txt">
        <h1>CONTROLES PRENATALES</h1>
        <div class="header-sub">Registra y edita los controles (1–8) para el embarazo seleccionado</div>
      </div>
    </div>
  </header>

  <!-- Contenedor tipo “tabla” del sistema -->
  <main class="table" id="customers_table">
    <section class="table__header">
      <h2>SEGUIMIENTO Y CONTROL</h2>
      <div>
        <a href="menu_embarazo.php" class="btn btn-outline btn-sm" title="Volver al menú de embarazo">
          Volver
        </a>
      </div>
    </section>

    <!-- Barra de filtros superior con el buscador (reusa IDs/JS originales) -->
    <div class="filtros">
      <form id="frmBuscar" class="w-100" role="search">
        <div class="search-wrap-inline">
          <input id="q" placeholder="Buscar paciente por nombre o apellido" autocomplete="off">
          <button id="btnBuscar" type="submit" class="btn-inline">
            <span id="spnBuscar" class="spinner-border spinner-border-sm me-2 d-none"></span>
            Entregar
          </button>
        </div>
      </form>
    </div>

    <!-- Zona de resultados de búsqueda (cards) -->
    <div style="padding: 18px 20px;">
      <div id="listaPacientes" class="mt-2"></div>
    </div>

    <!-- Panel de registro/selección de embarazo -->
    <div class="card mb-4 mx-3" id="panelRegistro" style="display:none;">
      <div class="card-body">
        <div class="mb-2" style="display:flex; align-items:center; gap:10px; font-weight:700; color:#1f2937;">
          <i class="bi bi-person-check" style="color:#02457a;"></i> Paciente seleccionado
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
          <div id="infoPaciente"></div>
          <span class="info-badge"><b>FUR:</b> <span id="lblFUR">-</span></span>
          <span class="info-badge"><b>FPP estimada:</b> <span id="lblFPP">-</span></span>
        </div>

        <div class="row g-2 align-items-end mb-3">
          <div class="col-md-6">
            <label class="form-label">Registro Clínico Prenatal (embarazo)</label>
            <select id="cmbRegistro" class="form-select"></select>
          </div>
        </div>

        <!-- Tabla de controles -->
        <div class="table-responsive">
          <table class="table table-controls align-middle">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Control</th>
                <th>Descripción</th>
                <th class="text-center">Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=1; $i<=8; $i++): ?>
              <tr>
                <td class="text-center"><span class="badge text-bg-primary">#<?php echo $i; ?></span></td>
                <td><strong>Control <?php echo $i; ?></strong> <span class="muted">(Mes)</span></td>
                <td class="muted">Registrar/editar datos del control mensual <?php echo $i; ?>.</td>
                <td class="text-center">
                  <button type="button" class="btn btn-outline-primary btn-ctl"
                          data-bs-toggle="modal" data-bs-target="#modalControl<?php echo $i; ?>"
                          onclick="abrirControl(<?php echo $i; ?>)">
                    <i class="bi bi-pencil-square"></i> Abrir
                  </button>
                </td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>

        <input type="hidden" id="idRegistro" value="">
      </div>
    </div>
  </main>

  <!-- ===== Modales 1..8 (mismo contenido, solo skin visual) ===== -->
  <?php for ($i=1; $i<=8; $i++): ?>
  <div class="modal fade" id="modalControl<?php echo $i; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-clipboard-pulse"></i> Control <?php echo $i; ?> <span class="muted" style="color:#dbeafe;">(Mes)</span></h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <form id="formC<?php echo $i; ?>" autocomplete="off">
            <div class="row g-3">
              <div class="col-6">
                <label class="form-label">Fecha visita</label>
                <input type="date" class="form-control" name="c<?php echo $i; ?>_fechaVisita" oninput="autoSemanas(<?php echo $i; ?>)">
              </div>
              <div class="col-6">
                <label class="form-label">Semanas (auto)</label>
                <input type="number" class="form-control" name="c<?php echo $i; ?>_semanas" readonly>
              </div>

              <div class="col-6">
                <label class="form-label">Circ. brazo (cm)</label>
                <input type="number" step="0.1" class="form-control" name="c<?php echo $i; ?>_circBrazo">
              </div>
              <div class="col-6">
                <label class="form-label">Masa corporal (kg)</label>
                <input type="number" step="0.1" class="form-control" name="c<?php echo $i; ?>_masa">
              </div>

              <div class="col-6">
                <label class="form-label">Presión arterial (mmHg)</label>
                <input type="text" class="form-control" name="c<?php echo $i; ?>_pa" placeholder="120/80">
              </div>
              <div class="col-6">
                <label class="form-label">Temperatura (°C)</label>
                <input type="number" step="0.1" class="form-control" name="c<?php echo $i; ?>_temp">
              </div>

              <div class="col-6">
                <label class="form-label">Peso (lb)</label>
                <input type="number" step="0.1" class="form-control" name="c<?php echo $i; ?>_peso">
              </div>
              <div class="col-6">
                <label class="form-label">Resp/min</label>
                <input type="number" class="form-control" name="c<?php echo $i; ?>_resp">
              </div>

              <div class="col-6">
                <label class="form-label">Frecuencia cardiaca (lpm)</label>
                <input type="number" class="form-control" name="c<?php echo $i; ?>_fc">
              </div>
              <div class="col-6">
                <label class="form-label">Hemoglobina (g/dL)</label>
                <input type="number" step="0.1" class="form-control" name="c<?php echo $i; ?>_hb">
              </div>

              <div class="col-6">
                <label class="form-label">Orina</label>
                <input type="text" class="form-control" name="c<?php echo $i; ?>_orina">
              </div>
              <div class="col-6">
                <label class="form-label">VDRL</label>
                <input type="text" class="form-control" name="c<?php echo $i; ?>_vdrl">
              </div>

              <!-- NUEVO: Síntomas de peligro -->
              <div class="col-6">
                <label class="form-label">Presenta signo o síntomas de peligro</label>
                <select class="form-select" name="c<?php echo $i; ?>_sintomaPeligro">
                  <option value="">Seleccione</option>
                  <option value="SI">Sí</option>
                  <option value="NO">No</option>
                </select>
              </div>

              <!-- NUEVO: VIH (1/0) -->
              <div class="col-6">
                <label class="form-label">VIH</label>
                <select class="form-select" name="c<?php echo $i; ?>_vih">
                  <option value="">Seleccione</option>
                  <option value="1">Sí</option>
                  <option value="0">No</option>
                </select>
              </div>

              <!-- NUEVO: Papanicolau / Infecciones -->
              <div class="col-6">
                <label class="form-label">Papanicolau</label>
                <input type="text" class="form-control" name="c<?php echo $i; ?>_papanicolao" placeholder="Resultado / fecha">
              </div>
              <div class="col-6">
                <label class="form-label">Infecciones</label>
                <input type="text" class="form-control" name="c<?php echo $i; ?>_infecciones" placeholder="Descripción">
              </div>

              <!-- Suministros / suplementos -->
              <div class="col-6">
                <label class="form-label">Sulfato ferroso</label>
                <select class="form-select" name="c<?php echo $i; ?>_sf">
                  <option value="">Seleccione</option>
                  <option value="1">Sí</option>
                  <option value="0">No</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label">Ácido fólico</label>
                <select class="form-select" name="c<?php echo $i; ?>_af">
                  <option value="">Seleccione</option>
                  <option value="1">Sí</option>
                  <option value="0">No</option>
                </select>
              </div>

              <!-- NUEVO: Vacunación Td (dosis) -->
              <div class="col-6">
                <label class="form-label">Vacunación madre Td (dosis administrada)</label>
                <input type="number" class="form-control" name="c<?php echo $i; ?>_vacunaDosis" min="0">
              </div>

              <div class="col-12">
                <label class="form-label">Problema detectado</label>
                <textarea class="form-control" rows="2" name="c<?php echo $i; ?>_problema" placeholder="Observaciones, hallazgos o alertas..."></textarea>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" onclick="guardarControl(<?php echo $i; ?>)">
            <i class="bi bi-check-circle"></i> Guardar control <?php echo $i; ?>
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php endfor; ?>

  <!-- JS (SIN CAMBIOS DE LÓGICA) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    let pacienteSel=null, furActual=null, cacheControles={};
    function ymd(d){return new Date(d).toISOString().slice(0,10);}

    function buscarPacientes(){
      const q = $('#q').val();
      if(!q || !q.trim()){ $('#listaPacientes').empty().addClass('d-none'); $('#spnBuscar').addClass('d-none'); return; }
      $('#spnBuscar').removeClass('d-none');
      $.getJSON('../php/pacientes.php',{search:q}, function(rows){
        const cont = $('#listaPacientes').removeClass('d-none');
        if(!rows || !rows.length){ cont.html('<div class="text-muted px-2 py-2"><i class="bi bi-search"></i> Sin resultados</div>'); return; }
        let html='';
        rows.forEach(r=>{
          html += '<div class="card mb-2">' +
            '<div class="card-body d-flex justify-content-between align-items-center">' +
              '<div>' +
                '<div class="patient-chip"><i class="bi bi-person-circle"></i> <b>' + r.NOMBRES_PACIENTES + ' ' + r.APELLIDOS_PACIENTES + '</b></div>' +
                '<div class="small text-muted mt-1">DPI: ' + r.DPI_PACIENTES + ' • ' + r.EDAD + ' años</div>' +
              '</div>' +
              '<button class="btn-inline btn-sm" onclick="elegirPaciente(' + r.ID_PACIENTES + ',\'' + r.NOMBRES_PACIENTES + '\',\'' + r.APELLIDOS_PACIENTES + '\')">' +
                '<i class="bi bi-check2-circle"></i> Elegir' +
              '</button>' +
            '</div>' +
          '</div>';
        });
        cont.html(html);
      }).always(()=>$('#spnBuscar').addClass('d-none'));
    }

    $('#frmBuscar').on('submit', e=>{e.preventDefault(); buscarPacientes();});
    $('#btnBuscar').on('click', e=>{e.preventDefault(); buscarPacientes();});
    $('#q').on('input', buscarPacientes);

    function elegirPaciente(id,n,a){
      pacienteSel = {id, nombre:n+' '+a};
      $('#panelRegistro').show();
      $('#infoPaciente').html('<span class="patient-chip"><i class="bi bi-person-badge"></i> ' + pacienteSel.nombre + ' <span class="muted">| ID ' + id + '</span></span>');
      cargarRegistros(id);
      $('#listaPacientes').empty().addClass('d-none');
      $('#q').val('').blur();
    }

    function cargarRegistros(idPaciente){
      $.getJSON('../php/listar_registros_por_paciente.php',{idPaciente}, function(resp){
        const cmb = $('#cmbRegistro').empty();
        if(!resp || !resp.length){
          cmb.append('<option value="">-- Sin registros --</option>');
          $('#lblFUR').text('-'); $('#lblFPP').text('-'); $('#idRegistro').val(''); return;
        }
        resp.forEach(r=>{
          cmb.append('<option data-fur="'+(r.FUR||'')+'" value="'+r.ID_REGISTRO_CLINICO_PRENATAL+'">'+r.NO_EXPEDIENTE+' • Ingreso '+r.FECHA_INGRESO+'</option>');
        });
        cmb.trigger('change');
      });
    }

    $('#cmbRegistro').on('change', function(){
      const idReg = $(this).val();
      furActual = $('#cmbRegistro option:selected').data('fur') || null;
      $('#lblFUR').text(furActual || '-');
      if(furActual){ const fpp = new Date(furActual); fpp.setDate(fpp.getDate()+280); $('#lblFPP').text(ymd(fpp)); } else { $('#lblFPP').text('-'); }
      $('#idRegistro').val(idReg);
      cacheControles = {};
      if(idReg) cargarControles(idReg);
    });

    function cargarControles(idRegistro){
      $.getJSON('../php/obtener_controles.php',{idRegistro}, function(rows){
        if(!rows) return;
        rows.forEach(r=>{
          cacheControles[r.CONTROL_NUM] = {
            fechaVisita: r.FECHA_VISITA || '',
            semanas: r.SEMANAS_EMBARAZO_FUR || '',
            circBrazo: r.CIRCUNFERENCIA_BRAZO || '',
            masa: r.MASA_CORPORAL || '',
            pa: r.PRESION_ARTERIAL || '',
            temp: r.TEMPERATURA_CORPORAL || '',
            peso: r.PESO_LIBRAS || '',
            resp: r.RESPIRACIONES_MINUTO || '',
            fc: r.FECUENCIA_CARDIACA || '',
            hb: r.HEMOGLOBINA || '',
            orina: r.ORINA || '',
            vdrl: r.VDRL || '',
            sintomaPeligro: r.SINTOMA_PELIGRO || '',
            vih: (r.VIH===0 || r.VIH===1) ? String(r.VIH) : (r.VIH||''),
            papanicolao: r.PAPANICOLAO || '',
            infecciones: r.INFECCIONES || '',
            vacunaDosis: (r.VACUNA_DOSIS!=null? String(r.VACUNA_DOSIS):''),
            sf: r.SULFATO_FERROSO==null? '' : String(r.SULFATO_FERROSO),
            af: r.ACIDO_FOLICO==null? '' : String(r.ACIDO_FOLICO),
            problema: r.PROBLEMA_DETECTADO || ''
          };
        });
      });
    }

    function abrirControl(n){
      const data = cacheControles[n] || null;
      const form = document.getElementById('formC'+n); if(!form) return;
      Array.from(form.elements).forEach(function(el){
        if(el.tagName==='INPUT' || el.tagName==='TEXTAREA') el.value='';
        if(el.tagName==='SELECT') el.value='';
      });
      if(data){
        form.querySelector('[name="c'+n+'_fechaVisita"]').value = data.fechaVisita || '';
        form.querySelector('[name="c'+n+'_semanas"]').value    = data.semanas || '';
        form.querySelector('[name="c'+n+'_circBrazo"]').value  = data.circBrazo || '';
        form.querySelector('[name="c'+n+'_masa"]').value       = data.masa || '';
        form.querySelector('[name="c'+n+'_pa"]').value         = data.pa || '';
        form.querySelector('[name="c'+n+'_temp"]').value       = data.temp || '';
        form.querySelector('[name="c'+n+'_peso"]').value       = data.peso || '';
        form.querySelector('[name="c'+n+'_resp"]').value       = data.resp || '';
        form.querySelector('[name="c'+n+'_fc"]').value         = data.fc || '';
        form.querySelector('[name="c'+n+'_hb"]').value         = data.hb || '';
        form.querySelector('[name="c'+n+'_orina"]').value      = data.orina || '';
        form.querySelector('[name="c'+n+'_vdrl"]').value       = data.vdrl || '';
        form.querySelector('[name="c'+n+'_sintomaPeligro"]').value = data.sintomaPeligro || '';
        form.querySelector('[name="c'+n+'_vih"]').value            = (data.vih!=null? data.vih:'');
        form.querySelector('[name="c'+n+'_papanicolao"]').value    = data.papanicolao || '';
        form.querySelector('[name="c'+n+'_infecciones"]').value    = data.infecciones || '';
        form.querySelector('[name="c'+n+'_vacunaDosis"]').value    = data.vacunaDosis || '';
        form.querySelector('[name="c'+n+'_sf"]').value             = (data.sf!=null? data.sf:'');
        form.querySelector('[name="c'+n+'_af"]').value             = (data.af!=null? data.af:'');
        form.querySelector('[name="c'+n+'_problema"]').value       = data.problema || '';
      }
    }

    function autoSemanas(n){
      if(!furActual) return;
      const form = document.getElementById('formC'+n);
      const fechaVisita = form.querySelector('[name="c'+n+'_fechaVisita"]').value;
      if(!fechaVisita) return;
      const w = Math.floor((new Date(fechaVisita) - new Date(furActual)) / (1000*60*60*24) / 7);
      form.querySelector('[name="c'+n+'_semanas"]').value = isFinite(w) ? Math.max(0,w) : '';
    }
    window.autoSemanas = autoSemanas;
    window.abrirControl = abrirControl;

    function guardarControl(n){
      const idRegistro = $('#idRegistro').val();
      if(!idRegistro){ alert('Seleccione un registro clínico.'); return; }
      const f = document.getElementById('formC'+n);

      const payload = {
        idRegistro,
        controles: [{
          controlNum: n,
          fechaVisita: f['c'+n+'_fechaVisita'].value || null,
          circBrazo:   f['c'+n+'_circBrazo'].value   || null,
          masa:        f['c'+n+'_masa'].value        || null,
          pa:          f['c'+n+'_pa'].value          || null,
          temp:        f['c'+n+'_temp'].value        || null,
          peso:        f['c'+n+'_peso'].value        || null,
          resp:        f['c'+n+'_resp'].value        || null,
          fc:          f['c'+n+'_fc'].value          || null,
          hb:          f['c'+n+'_hb'].value          || null,
          orina:       f['c'+n+'_orina'].value       || null,
          vdrl:        f['c'+n+'_vdrl'].value        || null,

          // NUEVOS CAMPOS
          sintomaPeligro: f['c'+n+'_sintomaPeligro'].value || null,   // 'SI' / 'NO'
          vih:            (f['c'+n+'_vih'].value===''? null : f['c'+n+'_vih'].value),
          papanicolao:    f['c'+n+'_papanicolao'].value || null,
          infecciones:    f['c'+n+'_infecciones'].value || null,
          vacunaDosis:    (f['c'+n+'_vacunaDosis'].value===''? null : f['c'+n+'_vacunaDosis'].value),

          sf:             (f['c'+n+'_sf'].value===''? null : f['c'+n+'_sf'].value),
          af:             (f['c'+n+'_af'].value===''? null : f['c'+n+'_af'].value),
          problema:       f['c'+n+'_problema'].value    || null
        }]
      };

      $.ajax({
        url:'../php/guardar_controles.php',
        method:'POST',
        contentType:'application/json',
        data: JSON.stringify(payload),
        dataType:'json',
        success:function(r){
          if(r.success){
            cacheControles[n] = {
              fechaVisita: payload.controles[0].fechaVisita || '',
              semanas: f['c'+n+'_semanas'].value || '',
              circBrazo: payload.controles[0].circBrazo || '',
              masa: payload.controles[0].masa || '',
              pa: payload.controles[0].pa || '',
              temp: payload.controles[0].temp || '',
              peso: payload.controles[0].peso || '',
              resp: payload.controles[0].resp || '',
              fc: payload.controles[0].fc || '',
              hb: payload.controles[0].hb || '',
              orina: payload.controles[0].orina || '',
              vdrl: payload.controles[0].vdrl || '',
              sintomaPeligro: payload.controles[0].sintomaPeligro || '',
              vih: payload.controles[0].vih==null? '' : String(payload.controles[0].vih),
              papanicolao: payload.controles[0].papanicolao || '',
              infecciones: payload.controles[0].infecciones || '',
              vacunaDosis: payload.controles[0].vacunaDosis==null? '' : String(payload.controles[0].vacunaDosis),
              sf: payload.controles[0].sf==null? '' : String(payload.controles[0].sf),
              af: payload.controles[0].af==null? '' : String(payload.controles[0].af),
              problema: payload.controles[0].problema || ''
            };

            const modalEl = document.getElementById('modalControl'+n);
            bootstrap.Modal.getInstance(modalEl).hide();
            alert('Control ' + n + ' guardado correctamente.');
          } else {
            alert('Error: ' + (r.message || 'No se pudo guardar'));
          }
        },
        error:function(){ alert('Error de conexión al guardar'); }
      });
    }
    window.guardarControl = guardarControl;
  </script>
</body>
</html>
