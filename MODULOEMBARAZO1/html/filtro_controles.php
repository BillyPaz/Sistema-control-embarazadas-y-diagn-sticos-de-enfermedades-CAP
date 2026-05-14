<?php
// filtro_controles.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Filtros de Pacientes por Controles Prenatales</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
 <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <!-- Mismo look & feel que pacientes.php -->
  <link rel="stylesheet" href="../../css/pacientes.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* ===== Ajustes de UI para que coincida con pacientes.php (sin tocar la lógica) ===== */

    /* Hero grande como pacientes.php */
    .header{
      background-image: url(../../imagenes/bg.png);
      background-position: center center;
      background-repeat: no-repeat;
      background-size: cover;
      display: flex;
      position: relative;
      min-height: 38vh;
      align-items: center;
    }
    .header-txt h1{
      font-size: 56px;
      line-height: 64px;
      font-weight: 700;
      text-transform: uppercase;
      color: #02457a;
      margin-bottom: 0;
      margin-left: 180px;
      font-family: "Roboto", sans-serif;
    }
    .header-sub{
      margin-left: 180px;
      margin-top: 10px;
      color: #0b2a47;
      font-weight: 500;
      background:#ffffffd9;
      display:inline-block;
      padding:6px 12px;
      border-radius:10px;
    }

    /* Contenedor principal tipo tarjeta/tabla */
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
      color:white;
      padding: 18px 20px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
    }
    .table__header h2{
      margin:0; font-size:24px; letter-spacing:.3px; font-family:"Roboto", sans-serif;
    }
    .table__body{
      padding: 18px 20px;
    }

    /* Tabs con estética del sistema */
    .nav-tabs{
      border-bottom: 0;
      gap: 8px;
    }
    .nav-tabs .nav-link{
      border: 0;
      font-weight: 600;
      color: #334155;
      background: #eef4fb;
      border-radius: 10px 10px 0 0;
      padding: 10px 14px;
    }
    .nav-tabs .nav-link.active{
      color: #02457a;
      background:#ffffff;
      border: 0;
      box-shadow: inset 0 -3px 0 #02457a;
      border-radius: 10px 10px 0 0;
    }

    /* Barra “sticky” para filtros de cada pestaña */
    .sticky-filters{
      position: sticky;
      top: 0;
      z-index: 5;
      background: #fff;
      padding: 12px 12px 0;
      border-radius: 12px 12px 0 0;
      border-bottom: 1px solid #eef1f5;
    }
    .section-title{
      display:flex; align-items:center; gap:10px;
      font-weight:700; color:#1f2937; margin-bottom:6px;
    }
    .subtle{ color:#6b7280; margin:0 0 8px 28px; }

    /* Formularios compactos con radios del sistema */
    .form-label{ font-weight:600; color:#0f2a44; }
    .form-control, .form-select{
      border-radius:10px; border:1px solid #ccc;
    }
    .btn-main{
      background:#02457a; color:#fff; border:none; border-radius:10px; font-weight:700;
    }

    /* Tablas */
    table thead th{
      background-color:#02457a !important;
      color:white; border:0;
    }
    .table-hover tbody tr:hover{ background:#f7fbff; }
    .badge-soft{
      background:#eef6ff; color:#02457a; font-weight:600; padding:.25rem .55rem; border-radius:999px;
    }
    .value-pill{
      display:inline-flex; align-items:center; gap:6px;
      background:#f1f6ff; border:1px dashed rgba(2,69,122,.35);
      border-radius:999px; padding:.25rem .6rem; font-weight:600; color:#0f2a44;
    }

    /* Estados de carga y contador */
    .loading{ display:none; align-items:center; gap:10px; color:#64748b; font-weight:600; }

    /* Toasts */
    .toast-container{
      position: fixed; top: 20px; right: 20px; z-index: 1080;
    }

    /* Responsive */
    @media (max-width: 992px){
      .header-txt h1, .header-sub { margin-left: 24px; }
      .table__header{ flex-direction:column; align-items:flex-start; gap:10px; }
    }
  </style>
</head>
<body>
<?php include("../MENU/menuVIH.php") ?>

<main>
  <!-- Hero con diseño de pacientes.php -->
  <header class="header">
    <div class="header-content container">
      <div class="header-txt">
        <h1>FILTROS PRENATALES</h1>
        <div class="header-sub">Aplica criterios por semanas de embarazo y parámetros clínicos</div>
      </div>
    </div>
  </header>

  <!-- Contenedor principal con la misma estética -->
  <section class="table" id="customers_table">
    <section class="table__header">
      <h2>FILTROS DE PACIENTES POR CONTROLES</h2>
      <div>
        <a href="menu_embarazo.php" class="btn btn-sm" style="background:#97CADB;color:#001B48;border:none;border-radius:10px;font-weight:bold;">
          Volver
        </a>
      </div>
    </section>

    <section class="table__body">
      <div class="card border-0 shadow-0">
        <div class="card-body p-0">

          <!-- Tabs -->
          <ul class="nav nav-tabs px-2" id="tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-cb" data-bs-toggle="tab" data-bs-target="#pane-cb" type="button" role="tab">
                <i class="bi bi-rulers"></i> &lt; 12 semanas (Circ. brazo)
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-imc" data-bs-toggle="tab" data-bs-target="#pane-imc" type="button" role="tab">
                <i class="bi bi-activity"></i> ≥ 12 semanas (IMC)
              </button>
            </li>
          </ul>

          <div class="tab-content pt-0">

            <!-- ===== Circunferencia de brazo (<12 semanas) ===== -->
            <div class="tab-pane fade show active" id="pane-cb" role="tabpanel">
              <div class="sticky-filters">
                <div class="section-title mb-1">
                  <i class="bi bi-rulers" style="color:#02457a;"></i>
                  Pacientes con menos de 12 semanas — filtro por circunferencia de brazo
                </div>
                <p class="subtle">Ingresa un rango (cm) para circunferencia del brazo.</p>
              </div>

              <div class="px-3 pb-3">
                <form class="row g-2 align-items-end" id="formCB">
                  <div class="col-sm-4">
                    <label class="form-label">Mín (cm)</label>
                    <input type="number" step="0.1" class="form-control" name="min" value="0">
                  </div>
                  <div class="col-sm-4">
                    <label class="form-label">Máx (cm)</label>
                    <input type="number" step="0.1" class="form-control" name="max" value="999">
                  </div>
                  <div class="col-sm-2 d-grid">
                    <button class="btn btn-main">
                      <i class="bi bi-search"></i> Buscar
                    </button>
                  </div>
                  <input type="hidden" name="mode" value="cb">
                </form>

                <div class="d-flex align-items-center justify-content-between mt-3">
                  <div class="loading" id="loadCB">
                    <div class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></div>
                    Consultando resultados...
                  </div>
                  <div class="text-muted small" id="countCB" aria-live="polite"></div>
                </div>

                <div class="table-responsive mt-2">
                  <table class="table table-sm table-hover align-middle" id="tblCB">
                    <thead>
                      <tr>
                        <th>Paciente</th>
                        <th>DPI</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Semanas</th>
                        <th>Circ. brazo (cm)</th>
                        <th>Fecha visita</th>
                        <th>Expediente</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- ===== IMC (≥12 semanas) ===== -->
            <div class="tab-pane fade" id="pane-imc" role="tabpanel">
              <div class="sticky-filters">
                <div class="section-title mb-1">
                  <i class="bi bi-activity" style="color:#02457a;"></i>
                  Pacientes con 12 semanas o más — filtro por IMC
                </div>
                <p class="subtle">Ingresa un rango de IMC (kg/m²).</p>
              </div>

              <div class="px-3 pb-3">
                <form class="row g-2 align-items-end" id="formIMC">
                  <div class="col-sm-4">
                    <label class="form-label">IMC mín</label>
                    <input type="number" step="0.1" class="form-control" name="min" value="0">
                  </div>
                  <div class="col-sm-4">
                    <label class="form-label">IMC máx</label>
                    <input type="number" step="0.1" class="form-control" name="max" value="99">
                  </div>
                  <div class="col-sm-2 d-grid">
                    <button class="btn btn-main">
                      <i class="bi bi-search"></i> Buscar
                    </button>
                  </div>
                  <input type="hidden" name="mode" value="imc">
                </form>

                <div class="d-flex align-items-center justify-content-between mt-3">
                  <div class="loading" id="loadIMC">
                    <div class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></div>
                    Consultando resultados...
                  </div>
                  <div class="text-muted small" id="countIMC" aria-live="polite"></div>
                </div>

                <div class="table-responsive mt-2">
                  <table class="table table-sm table-hover align-middle" id="tblIMC">
                    <thead>
                      <tr>
                        <th>Paciente</th>
                        <th>DPI</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Semanas</th>
                        <th>IMC</th>
                        <th>Fecha visita</th>
                        <th>Expediente</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>

          </div> <!-- /tab-content -->
        </div>
      </div>
    </section>
  </main>

  <!-- Toasts -->
  <div class="toast-container" id="toastArea" aria-live="polite" aria-atomic="true"></div>

  <!-- JS (sin cambios de lógica) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    // Utilidad: Toast bonito
    function showToast(type, title, message){
      const id = 't' + Date.now();
      const icon = type === 'success' ? 'check-circle-fill text-success'
                 : type === 'warning' ? 'exclamation-triangle-fill text-warning'
                 : type === 'info'    ? 'info-circle-fill text-primary'
                 : 'x-circle-fill text-danger';
      const html = `
        <div class="toast align-items-center show mb-2" id="${id}" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              <i class="bi bi-${icon}"></i>
              <strong class="ms-1">${title}</strong>
              <div class="small text-muted">${message || ''}</div>
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"
              onclick="document.getElementById('${id}').remove()"></button>
          </div>
        </div>`;
      document.getElementById('toastArea').insertAdjacentHTML('beforeend', html);
      setTimeout(()=>{ const el=document.getElementById(id); if(el) el.remove(); }, 4500);
    }

    // Helper para pintar filas
    function renderRows($tbody, rows, mode){
      let html = '';
      rows.forEach(r=>{
        const semanas = (r.semanas ?? '') === '' ? '' :
                        `<span class="badge-soft"><i class="bi bi-calendar-week"></i> ${r.semanas}</span>`;
        const valor   = mode === 'cb'
                        ? (r.circ_brazo ?? '')
                        : (r.imc ?? '');
        const metric  = mode === 'cb' ? 'cm' : 'kg/m²';
        const pill    = (valor === '' ? '' :
                        `<span class="value-pill"><i class="bi bi-activity"></i> ${valor} <span class="text-muted">${metric}</span></span>`);

        html += `<tr>
          <td>${r.paciente}</td>
          <td>${r.dpi || ''}</td>
          <td>${r.telefono || ''}</td>
          <td>${r.direccion || ''}</td>
          <td>${semanas}</td>
          <td>${pill}</td>
          <td>${r.fecha_visita || ''}</td>
          <td>${r.no_expediente || ''}</td>
        </tr>`;
      });
      $tbody.html(html || `<tr><td colspan="8" class="text-center text-muted py-3"><i class="bi bi-inboxes"></i> Sin resultados</td></tr>`);
    }

    // Estado de carga
    function setLoading(which, on){
      const map = {
        cb:  { load:'#loadCB',  count:'#countCB'  },
        imc: { load:'#loadIMC', count:'#countIMC' }
      };
      const el = map[which];
      if(!el) return;
      $(el.load).css('display', on ? 'inline-flex' : 'none');
      if(on) $(el.count).text('');
    }

    // Contador
    function setCount(which, n){
      const el = which === 'cb' ? '#countCB' : '#countIMC';
      $(el).text(n + ' resultado' + (n===1?'':'s'));
    }

    // Submit CB
    $('#formCB').on('submit', function(e){
      e.preventDefault();
      const params = $(this).serialize();
      setLoading('cb', true);
      $.getJSON('../php/filtrar_pacientes.php?' + params, function(resp){
        if(resp.success){
          renderRows($('#tblCB tbody'), resp.rows, 'cb');
          setCount('cb', resp.rows.length || 0);
          if(!resp.rows.length){ showToast('info', 'Sin resultados', 'Ajusta el rango de circunferencia y vuelve a intentar.'); }
        } else {
          showToast('error', 'Error', resp.message || 'No se pudo completar la consulta.');
        }
      }).fail(function(xhr){
        let msg='Error al consultar';
        try{ const j=JSON.parse(xhr.responseText); if(j.message) msg=j.message; }catch(e){}
        showToast('error', 'Error', msg);
      }).always(function(){
        setLoading('cb', false);
      });
    });

    // Submit IMC
    $('#formIMC').on('submit', function(e){
      e.preventDefault();
      const params = $(this).serialize();
      setLoading('imc', true);
      $.getJSON('../php/filtrar_pacientes.php?' + params, function(resp){
        if(resp.success){
          renderRows($('#tblIMC tbody'), resp.rows, 'imc');
          setCount('imc', resp.rows.length || 0);
          if(!resp.rows.length){ showToast('info', 'Sin resultados', 'Prueba con otro rango de IMC.'); }
        } else {
          showToast('error', 'Error', resp.message || 'No se pudo completar la consulta.');
        }
      }).fail(function(xhr){
        let msg='Error al consultar';
        try{ const j=JSON.parse(xhr.responseText); if(j.message) msg=j.message; }catch(e){}
        showToast('error', 'Error', msg);
      }).always(function(){
        setLoading('imc', false);
      });
    });
  </script>
</body>
</html>
