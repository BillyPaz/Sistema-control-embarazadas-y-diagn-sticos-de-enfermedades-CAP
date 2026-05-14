<?php
// embarazadas_form.php
// Formulario para filtrar por rango de fechas y generar el PDF
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Listado de Embarazadas</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"/>

  <!-- Estilo base del sistema (mismo look & feel de pacientes.php) -->
  <link rel="stylesheet" href="../../css/pacientes.css">

  <style>
    /* ===== Ajustes visuales para que coincida con pacientes.php (sin tocar lógica) ===== */

    /* Hero superior con la misma estética */
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

    /* Contenedor principal tipo “card/tabla” */
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
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
    }
    .table__header h2{
      margin:0; font-size:24px; letter-spacing:.3px; font-family:"Roboto", sans-serif;
    }
    .table__body{
      padding: 22px 20px;
    }
    .btn-ghost{
      background:#97CADB; color:#001B48; border:none; border-radius:10px; font-weight:bold;
    }
    .btn-ghost:hover{ background:#018ABE; color:#fff; }

    /* Tarjeta interior sin alterar la estructura existente */
    .card{
      border:0; border-radius:16px; background:#fff;
    }

    .section-title{
      display:flex; align-items:center; gap:10px;
      font-weight:700; color:#1f2937; margin-bottom:14px;
    }
    .section-title i{color:#02457a}

    .form-label{font-weight:600; color:#0f2a44}
    .form-control, .form-select{
      border-radius:10px; border:1px solid #ccc;
    }

    .helper{
      display:flex; gap:10px; align-items:center; flex-wrap:wrap;
    }
    .range-preview{
      color:#475569; background:#f1f6ff; border:1px dashed rgba(2,69,122,.35);
      padding:.35rem .6rem; border-radius:999px; font-weight:600;
      display:inline-flex; gap:.4rem; align-items:center;
    }

    /* Botón principal PDF en el mismo lenguaje visual */
    #btnPDF{
      background:#02457a;
      color:#fff;
      border:none;
      border-radius:10px;
      font-weight:700;
    }

    /* Toasts */
    .toast-container{
      position: fixed; top: 20px; right: 20px; z-index: 1080;
    }

    @media (max-width: 992px){
      .header-txt h1, .header-sub { margin-left: 24px; }
      .table__header{ flex-direction:column; align-items:flex-start; gap:10px; }
    }
  </style>
</head>
<body>

  <!-- Hero con diseño de pacientes.php -->
  <header class="header">
    <div class="header-content container">
      <div class="header-txt">
        <h1>LISTADO DE EMBARAZADAS</h1>
        <div class="header-sub">Filtra por fechas de ingreso y genera el PDF</div>
      </div>
    </div>
  </header>

  <!-- Contenedor principal con la misma estética -->
  <main class="table" id="customers_table">
    <section class="table__header">
      <h2>REPORTE — EMBARAZADAS REGISTRADAS</h2>
      <div class="d-flex gap-2">
        <a href="../html/menu_embarazo.php" class="btn btn-ghost">
          <i class="bi bi-arrow-left"></i> Volver
        </a>
      </div>
    </section>

    <section class="table__body">
      <div class="card border-0 shadow-0">
        <div class="card-body">

          <div class="section-title">
            <i class="bi bi-calendar-range"></i> Selecciona el periodo
          </div>

          <!-- Quick presets + preview -->
          <div class="row g-3 align-items-end mb-2">
            <div class="col-lg-4">
              <label class="form-label" for="fini">Fecha inicio</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="fini" name="fini" aria-describedby="helpInicio">
              </div>
              <small id="helpInicio" class="text-muted">Incluida en el rango</small>
            </div>

            <div class="col-lg-4">
              <label class="form-label" for="ffin">Fecha fin</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="ffin" name="ffin" aria-describedby="helpFin">
              </div>
              <small id="helpFin" class="text-muted">Incluida en el rango</small>
            </div>

            <div class="col-lg-4">
              <label class="form-label">Fechas</label>
              <div class="helper">
                <div class="btn-group" role="group" aria-label="Rangos rápidos">
                  <button class="btn btn-outline-primary btn-sm" type="button" data-range="hoy"><i class="bi bi-lightning-charge"></i> Hoy</button>
                  <button class="btn btn-outline-primary btn-sm" type="button" data-range="7"><i class="bi bi-clock-history"></i> Últimos 7 días</button>
                  <button class="btn btn-outline-primary btn-sm" type="button" data-range="mes"><i class="bi bi-calendar3"></i> Este mes</button>
                  <button class="btn btn-outline-primary btn-sm" type="button" data-range="mesprev"><i class="bi bi-calendar2-month"></i> Mes pasado</button>
                  <button class="btn btn-outline-primary btn-sm" type="button" data-range="anio"><i class="bi bi-calendar2-check"></i> Año en curso</button>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="range-preview" id="preview">
              <i class="bi bi-eye"></i> Rango actual: <span id="lblRange">—</span>
            </div>
            <button class="btn btn-outline-secondary btn-sm" type="button" id="btnClear">
              <i class="bi bi-eraser"></i> Limpiar
            </button>
          </div>

          <hr class="my-3"/>

          <form id="reportForm" class="row g-3 align-items-end" method="get" action="embarazadas_pdf.php" target="_blank" novalidate>
            <input type="hidden" name="fini" id="fini_hidden" />
            <input type="hidden" name="ffin" id="ffin_hidden" />

            <div class="col-lg-8">
              <!-- espacio para futuros filtros -->
            </div>
            <div class="col-lg-4">
              <div class="d-flex gap-2 justify-content-end">
                <button type="submit" class="btn" id="btnPDF">
                  <i class="bi bi-filetype-pdf"></i> Generar PDF
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </section>
  </main>

  <!-- Toasts -->
  <div class="toast-container" id="toastArea" aria-live="polite" aria-atomic="true"></div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Helpers de fecha
    function toYMD(d){
      const y = d.getFullYear();
      const m = String(d.getMonth()+1).padStart(2,'0');
      const day = String(d.getDate()).padStart(2,'0');
      return `${y}-${m}-${day}`;
    }
    function parseYMD(s){ const [y,m,d]=s.split('-').map(Number); return new Date(y, m-1, d); }

    // Rellenar preview
    function setPreview(){
      const ini = document.getElementById('fini').value || '—';
      const fin = document.getElementById('ffin').value || '—';
      document.getElementById('lblRange').textContent = `${ini} a ${fin}`;
    }

    // Toast simple
    function showToast(type, title, message){
      const id = 't'+Date.now();
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
            <button type="button" class="btn-close me-2 m-auto" aria-label="Cerrar"
              onclick="document.getElementById('${id}').remove()"></button>
          </div>
        </div>`;
      document.getElementById('toastArea').insertAdjacentHTML('beforeend', html);
      setTimeout(()=>{ const el=document.getElementById(id); if(el) el.remove(); }, 4200);
    }

    // Inicialización
    (function init(){
      const fini = document.getElementById('fini');
      const ffin = document.getElementById('ffin');

      // Máximo = hoy
      const today = new Date();
      const max = toYMD(today);
      fini.setAttribute('max', max);
      ffin.setAttribute('max', max);

      // Por defecto: este mes
      const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
      fini.value = toYMD(firstDay);
      ffin.value = max;
      setPreview();

      // Eventos de cambio
      fini.addEventListener('change', setPreview);
      ffin.addEventListener('change', setPreview);

      // Rangos rápidos
      document.querySelectorAll('[data-range]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          const mode = btn.getAttribute('data-range');
          const t = new Date();
          let a = new Date(t);
          let b = new Date(t);

          if(mode === 'hoy'){
            a = t; b = t;
          }else if(mode === '7'){
            a = new Date(t.getFullYear(), t.getMonth(), t.getDate()-6); // hoy y 6 días atrás
            b = t;
          }else if(mode === 'mes'){
            a = new Date(t.getFullYear(), t.getMonth(), 1);
            b = t;
          }else if(mode === 'mesprev'){
            const firstPrev = new Date(t.getFullYear(), t.getMonth()-1, 1);
            const lastPrev = new Date(t.getFullYear(), t.getMonth(), 0);
            a = firstPrev; b = lastPrev;
          }else if(mode === 'anio'){
            a = new Date(t.getFullYear(), 0, 1);
            b = t;
          }

          const aYMD = toYMD(a);
          const bYMD = toYMD(b);
          fini.value = aYMD;
          ffin.value = bYMD;
          setPreview();
        });
      });

      // Limpiar
      document.getElementById('btnClear').addEventListener('click', ()=>{
        fini.value = '';
        ffin.value = '';
        setPreview();
      });

      // Submit con validación
      document.getElementById('reportForm').addEventListener('submit', (e)=>{
        const vi = fini.value;
        const vf = ffin.value;

        if(!vi || !vf){
          e.preventDefault();
          showToast('warning','Fechas incompletas','Debes seleccionar fecha inicio y fin.');
          return;
        }
        if(parseYMD(vi) > parseYMD(vf)){
          e.preventDefault();
          showToast('error','Rango inválido','La fecha inicio no puede ser mayor que la fecha fin.');
          return;
        }

        // Pasar valores a inputs hidden del GET real (con target=_blank)
        document.getElementById('fini_hidden').value = vi;
        document.getElementById('ffin_hidden').value = vf;

        // UX: feedback rápido
        const btn = document.getElementById('btnPDF');
        const old = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generando...';
        setTimeout(()=>{ btn.disabled=false; btn.innerHTML=old; }, 1800);
      });
    })();
  </script>
</body>
</html>
