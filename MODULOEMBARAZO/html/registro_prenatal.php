<?php
// registro_prenatal.php
session_start();
require_once __DIR__.'/../../SETTINGS/php/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConnection();

$sintomas = array();
$sql_sintomas = "SELECT id_signo_sintoma, descripcion FROM tipo_signos_sintomas ORDER BY descripcion";
$result_sintomas = $conn->query($sql_sintomas);

if ($result_sintomas && $result_sintomas->num_rows > 0) {
    while($row = $result_sintomas->fetch_assoc()) {
        $sintomas[] = $row;
    }
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro Prenatal - Sistema de Salud</title>

  <!-- Bootstrap + Icons (sin cambios funcionales) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- DISEÑO reutilizado de pacientes -->
  <link rel="stylesheet" href="../../css/pacientes.css">

  <style>
    /* Ajustes suaves para integrar tu contenido dentro del diseño de pacientes.css */

    /* Contenedor “tabla/card” del módulo (como en pacientes.php) */
    .table { width: 90%; margin: 30px auto; }

    /* Encabezado contextual (dentro del card) */
    .table__body .section-title {
      font-family: "Roboto", sans-serif;
      font-weight: 700;
      color: #02457a;
      border-bottom: 2px solid #02457a;
      padding-bottom: 8px;
      margin-bottom: 18px;
      display: flex; align-items: center; gap: 8px;
    }

    /* Caja blanca con sombra sutil para secciones (equivalente a tus “cards”) */
    .panel-soft {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.06);
      padding: 18px 18px;
      margin-bottom: 18px;
      border: 1px solid #eef2f7;
    }

    /* Buscador: icono dentro del input */
    .search-box { position: relative; }
    .search-box i {
      position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
      color:#6c757d;
    }
    .search-box .form-control { padding-left: 38px; }

    /* Tarjeta de paciente con el esquema del módulo */
    .patient-card {
      cursor: pointer;
      transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
      border-left: 4px solid transparent;
      border-radius: 12px;
      border: 1px solid #e9eef5;
      background: #fbfcfe;
    }
    .patient-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(0,0,0,.08);
      border-left-color: #02457a;
    }
    .patient-card.selected {
      border-left-color: #02457a;
      background: #f4f8ff;
    }
    .patient-avatar {
      width: 60px; height: 60px; border-radius: 50%;
      object-fit: cover; border: 2px solid #02457a;
    }

    /* Pestañas: integrar con paleta */
    .nav-pills .nav-link { color:#02457a; font-weight:600; }
    .nav-pills .nav-link.active {
      background-color:#02457a;
    }

    /* Botones coherentes con pacientes.css (.btn ya se estiliza allí) */
    .btn-primary { background:#02457a; border-color:#02457a; }
    .btn-primary:hover { background:#018ABE; border-color:#018ABE; }
    .btn-success { background:#2e8b57; border-color:#2e8b57; }
    .btn-outline-secondary { border-color:#97CADB; color:#02457a; }
    .btn-outline-secondary:hover { background:#97CADB; color:#001B48; }

    /* Listado de síntomas */
    .sintomas-list {
      max-height: 220px; overflow-y: auto;
      border: 1px solid #e9eef5; border-radius: 10px; padding: 10px;
      background: #fff;
    }
    .sintoma-item { padding: 6px 4px; border-bottom: 1px solid #f1f4f9; }
    .sintoma-item:last-child { border-bottom: 0; }

    /* Alertas flotantes consistentes */
    .alert-auto-close { position: fixed; top: 20px; right: 20px; z-index: 1050; }
  </style>
</head>
<body>

<?php include("../MENU/menuVIH.php") ?>

<main>

  <header class="header">
    <div class="header-content container">
      <div class="header-txt">
        <h1>REGISTRO PRENATAL</h1>
      </div>
    </div>
  </header>

  <!-- CONTENEDOR CARD tipo “tabla” (mismo patrón que pacientes.php) -->
  <section class="table" id="registro_prenatal_card">
    <!-- Barra superior -->
    <section class="table__header">
      <h2>REGISTRO CLÍNICO PRENATAL</h2>
      <a href="menu_embarazo.php" class="btn">
         Volver al submenú
      </a>
    </section>

    <!-- Cuerpo del “card” -->
    <section class="table__body">

      <!-- Alertas -->
      <div id="alertContainer"></div>

      <!-- Selección de paciente -->
      <div id="seleccionPaciente">
        <div class="panel-soft">
          <h3 class="section-title"><i class="bi bi-search"></i> Buscar Paciente para Registro Prenatal</h3>

          <div class="row g-2 mb-2">
            <div class="col-md-8">
              <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchPatient" placeholder="Nombre, apellido o DPI...">
              </div>
            </div>
            <div class="col-md-4 d-grid">
              <button class="btn btn-primary" id="btnSearch">
                <i class="bi bi-search"></i> Buscar
              </button>
            </div>
          </div>

          <div id="patientsList" class="mt-3">
            <div class="text-center py-4">
              <i class="bi bi-search display-4 text-muted"></i>
              <p class="mt-3 text-muted">Utilice el buscador para encontrar pacientes</p>
            </div>
          </div>
        </div>

        <div class="panel-soft" id="selectedPatientInfo" style="display:none;">
          <h3 class="section-title"><i class="bi bi-person-check"></i> Paciente Seleccionado</h3>
          <div id="patientDetails" class="mb-3"></div>
          <div class="d-grid">
            <button class="btn btn-success" id="btnStartRegister">
              <i class="bi bi-plus-circle"></i> Iniciar Registro Prenatal
            </button>
          </div>
        </div>
      </div>

      <!-- Formulario principal (oculto hasta elegir paciente) -->
      <form id="formRegistroPrenatal" style="display:none;">
        <input type="hidden" id="idPaciente" name="idPaciente">

        <div class="panel-soft">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="section-title mb-0"><i class="bi bi-person"></i> Registro Prenatal</h3>
            <button type="button" class="btn btn-outline-secondary" id="btnBackToSearch">
              <i class="bi bi-arrow-left"></i> Cambiar Paciente
            </button>
          </div>

          <div id="currentPatientInfo" class="alert alert-info mb-3"></div>

          <!-- Tabs -->
          <ul class="nav nav-pills mb-3 justify-content-center" id="prenatalTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="datos-generales-tab" data-bs-toggle="pill" data-bs-target="#datos-generales" type="button" role="tab">
                <i class="bi bi-person"></i> Datos Generales
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="antecedentes-tab" data-bs-toggle="pill" data-bs-target="#antecedentes" type="button" role="tab">
                <i class="bi bi-clock-history"></i> Antecedentes
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="vacunas-tab" data-bs-toggle="pill" data-bs-target="#vacunas" type="button" role="tab">
                <i class="bi bi-shield-plus"></i> Vacunas
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="sintomas-tab" data-bs-toggle="pill" data-bs-target="#sintomas" type="button" role="tab">
                <i class="bi bi-heart-pulse"></i> Síntomas
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="seguimiento-tab" data-bs-toggle="pill" data-bs-target="#seguimiento" type="button" role="tab">
                <i class="bi bi-clipboard-check"></i> Seguimiento
              </button>
            </li>
          </ul>

          <div class="tab-content" id="prenatalTabsContent">
            <!-- DATOS GENERALES -->
            <div class="tab-pane fade show active" id="datos-generales" role="tabpanel">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="fechaIngreso" class="form-label">Fecha de Ingreso</label>
                  <input type="date" class="form-control" id="fechaIngreso" name="fechaIngreso" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="noExpediente" class="form-label">Número de Expediente</label>
                  <input type="text" class="form-control" id="noExpediente" name="noExpediente" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="nombreServicio" class="form-label">Nombre del Servicio</label>
                  <input type="text" class="form-control" id="nombreServicio" name="nombreServicio" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="historiaProblemaActual" class="form-label">Historia del Problema Actual</label>
                  <textarea class="form-control" id="historiaProblemaActual" name="historiaProblemaActual" rows="3"></textarea>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="fur" class="form-label">Fecha de Última Regla (FUR)</label>
                  <input type="date" class="form-control" id="fur" name="fur">
                </div>
              </div>

              <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" onclick="showTab('antecedentes-tab')">
                  Siguiente <i class="bi bi-arrow-right"></i>
                </button>
              </div>
            </div>

            <!-- ANTECEDENTES -->
            <div class="tab-pane fade" id="antecedentes" role="tabpanel">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="gestas" class="form-label">Gestas</label>
                  <input type="number" class="form-control" id="gestas" name="gestas" min="0" value="0">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="partos" class="form-label">Partos</label>
                  <input type="number" class="form-control" id="partos" name="partos" min="0" value="0">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="abortos" class="form-label">Abortos</label>
                  <input type="number" class="form-control" id="abortos" name="abortos" min="0" value="0">
                </div>
              </div>

              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="nacidosVivos" class="form-label">Nacidos Vivos</label>
                  <input type="number" class="form-control" id="nacidosVivos" name="nacidosVivos" min="0" value="0">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="nacidosMuertos" class="form-label">Nacidos Muertos</label>
                  <input type="number" class="form-control" id="nacidosMuertos" name="nacidosMuertos" min="0" value="0">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="hijosVivos" class="form-label">Hijos Vivos</label>
                  <input type="number" class="form-control" id="hijosVivos" name="hijosVivos" min="0" value="0">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="hijosMuertos" class="form-label">Hijos Muertos</label>
                  <input type="number" class="form-control" id="hijosMuertos" name="hijosMuertos" min="0" value="0">
                </div>
              </div>

              <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="showTab('datos-generales-tab')">
                  <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn btn-primary" onclick="showTab('vacunas-tab')">
                  Siguiente <i class="bi bi-arrow-right"></i>
                </button>
              </div>
            </div>

            <!-- VACUNAS -->
            <div class="tab-pane fade" id="vacunas" role="tabpanel">
              <div class="row mb-4">
                <div class="col-md-12">
                  <h5 class="mb-2" style="color:#02457a;">Vacuna TD (Tétanos-Difteria)</h5>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Antecedente de Vacuna TD</label>
                  <div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="antecedentesVacunaTD" id="tdSi" value="1">
                      <label class="form-check-label" for="tdSi">Sí</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="antecedentesVacunaTD" id="tdNo" value="0" checked>
                      <label class="form-check-label" for="tdNo">No</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="dosisTD" class="form-label">Dosis TD</label>
                  <input type="number" class="form-control" id="dosisTD" name="dosisTD" min="0" value="0">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="fechaUltimaDosisTD" class="form-label">Fecha Última Dosis TD</label>
                  <input type="date" class="form-control" id="fechaUltimaDosisTD" name="fechaUltimaDosisTD">
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <h5 class="mb-2" style="color:#02457a;">Vacuna TDAP (Tétanos-Difteria-Tosferina)</h5>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Antecedente de Vacuna TDAP</label>
                  <div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="antecedentesVacunaTDAP" id="tdapSi" value="1">
                      <label class="form-check-label" for="tdapSi">Sí</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="antecedentesVacunaTDAP" id="tdapNo" value="0" checked>
                      <label class="form-check-label" for="tdapNo">No</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="dosisTDAP" class="form-label">Dosis TDAP</label>
                  <input type="number" class="form-control" id="dosisTDAP" name="dosisTDAP" min="0" value="0">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="fechaUltimaDosisTDAP" class="form-label">Fecha Última Dosis TDAP</label>
                  <input type="date" class="form-control" id="fechaUltimaDosisTDAP" name="fechaUltimaDosisTDAP">
                </div>
              </div>

              <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="showTab('antecedentes-tab')">
                  <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn btn-primary" onclick="showTab('sintomas-tab')">
                  Siguiente <i class="bi bi-arrow-right"></i>
                </button>
              </div>
            </div>

            <!-- SÍNTOMAS -->
            <div class="tab-pane fade" id="sintomas" role="tabpanel">
              <div class="mb-3">
                <label class="form-label">Seleccione los síntomas presentados</label>
                <div class="sintomas-list">
                  <?php foreach ($sintomas as $sintoma): ?>
                    <div class="sintoma-item">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="sintomas" value="<?php echo $sintoma['id_signo_sintoma']; ?>" id="sintoma<?php echo $sintoma['id_signo_sintoma']; ?>">
                        <label class="form-check-label" for="sintoma<?php echo $sintoma['id_signo_sintoma']; ?>">
                          <?php echo $sintoma['descripcion']; ?>
                        </label>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="showTab('vacunas-tab')">
                  <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn btn-primary" onclick="showTab('seguimiento-tab')">
                  Siguiente <i class="bi bi-arrow-right"></i>
                </button>
              </div>
            </div>

            <!-- SEGUIMIENTO -->
            <div class="tab-pane fade" id="seguimiento" role="tabpanel">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="fechaProbableParto" class="form-label">Fecha Probable de Parto</label>
                  <input type="date" class="form-control" id="fechaProbableParto" name="fechaProbableParto">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="circunferenciaBrazo" class="form-label">Circunferencia del Brazo (cm)</label>
                  <input type="number" class="form-control" id="circunferenciaBrazo" name="circunferenciaBrazo" min="0" step="0.1">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="masaCorporal" class="form-label">Masa Corporal (kg)</label>
                  <input type="number" class="form-control" id="masaCorporal" name="masaCorporal" min="0" step="0.1">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="fechaVisita" class="form-label">Fecha de Visita</label>
                  <input type="date" class="form-control" id="fechaVisita" name="fechaVisita">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="presionArterial" class="form-label">Presión Arterial (mmHg)</label>
                  <input type="text" class="form-control" id="presionArterial" name="presionArterial" placeholder="120/80">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="temperaturaCorporal" class="form-label">Temperatura Corporal (°C)</label>
                  <input type="number" class="form-control" id="temperaturaCorporal" name="temperaturaCorporal" min="0" step="0.1">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="pesoLibras" class="form-label">Peso (libras)</label>
                  <input type="number" class="form-control" id="pesoLibras" name="pesoLibras" min="0" step="0.1">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="respiracionesMinuto" class="form-label">Respiraciones por Minuto</label>
                  <input type="number" class="form-control" id="respiracionesMinuto" name="respiracionesMinuto" min="0">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="frecuenciaCardiaca" class="form-label">Frecuencia Cardíaca (lpm)</label>
                  <input type="number" class="form-control" id="frecuenciaCardiaca" name="frecuenciaCardiaca" min="0">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="hemoglobina" class="form-label">Hemoglobina (g/dL)</label>
                  <input type="number" class="form-control" id="hemoglobina" name="hemoglobina" min="0" step="0.1">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="orina" class="form-label">Examen de Orina</label>
                  <input type="text" class="form-control" id="orina" name="orina">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="vdrl" class="form-label">VDRL</label>
                  <input type="text" class="form-control" id="vdrl" name="vdrl">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="sulfatoFerroso" class="form-label">Sulfato Ferroso</label>
                  <select class="form-select" id="sulfatoFerroso" name="sulfatoFerroso">
                    <option value="" selected disabled>Seleccione</option>
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="acidoFolico" class="form-label">Ácido Fólico</label>
                  <select class="form-select" id="acidoFolico" name="acidoFolico">
                    <option value="" selected disabled>Seleccione</option>
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="problemaDetectado" class="form-label">Problema Detectado</label>
                  <textarea class="form-control" id="problemaDetectado" name="problemaDetectado" rows="2"></textarea>
                </div>
              </div>

              <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="showTab('sintomas-tab')">
                  <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-check-circle"></i> Guardar Registro
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>

    </section>
  </main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script.js"></script>
<script>
  $(document).ready(function () {
    var selectedPatient = null;

    $("#btnSearch").click(function () {
      buscarPacientes();
    });

    $("#searchPatient").on("input", function () {
      buscarPacientes();
    });

    function buscarPacientes() {
      var searchText = $("#searchPatient").val();
      $.ajax({
        url: "../php/pacientes.php",
        type: "GET",
        data: { search: searchText },
        dataType: "json",
        success: function (pacientes) {
          mostrarPacientes(pacientes);
        },
        error: function (xhr, status, error) {
          mostrarAlerta("Error al buscar pacientes: " + error, "danger");
        }
      });
    }

    function mostrarPacientes(pacientes) {
      var patientsList = $("#patientsList");

      if (!pacientes || pacientes.length === 0) {
        patientsList.html(
          '<div class="text-center py-4">' +
            '<i class="bi bi-search display-4 text-muted"></i>' +
            '<p class="mt-3 text-muted">No se encontraron pacientes</p>' +
          "</div>"
        );
        return;
      }

      var html = "";
      pacientes.forEach(function (paciente) {
        var isSelected = (selectedPatient && selectedPatient.id_pacientes === paciente.id_pacientes);
        var avatarUrl =
          "https://ui-avatars.com/api/?name=" +
          encodeURIComponent(paciente.nombres_pacientes + " " + paciente.apellidos_pacientes) +
          "&background=02457a&color=fff";

        html +=
          '<div class="card patient-card mb-2 ' + (isSelected ? "selected" : "") + '" data-patient-id="' + paciente.id_pacientes + '">' +
            '<div class="card-body py-3 px-3">' +
              '<div class="d-flex align-items-center">' +
                '<div class="flex-shrink-0">' +
                  '<img src="' + avatarUrl + '" class="patient-avatar" alt="Avatar">' +
                "</div>" +
                '<div class="flex-grow-1 ms-3">' +
                  '<h5 class="card-title mb-1">' + paciente.nombres_pacientes + " " + paciente.apellidos_pacientes + "</h5>" +
                  '<p class="card-text mb-1">' +
                    '<span class="badge bg-info">' + paciente.edad + " años</span>" +
                    '<span class="ms-2">DPI: ' + paciente.dpi_pacientes + "</span>" +
                  "</p>" +
                  '<p class="card-text small text-muted mb-0">' +
                    '<i class="bi bi-telephone"></i> ' + paciente.telefono + " | " +
                    '<i class="bi bi-geo-alt"></i> ' + paciente.direccion +
                  "</p>" +
                "</div>" +
              "</div>" +
            "</div>" +
          "</div>";
      });

      patientsList.html(html);

      $(".patient-card").click(function () {
        var patientId = $(this).data("patient-id");
        seleccionarPaciente(patientId, pacientes);
      });
    }

    function seleccionarPaciente(patientId, pacientes) {
      selectedPatient = pacientes.find(function (p) { return p.id_pacientes == patientId; });

      $(".patient-card").removeClass("selected");
      $('.patient-card[data-patient-id="' + patientId + '"]').addClass("selected");

      var avatarUrl =
        "https://ui-avatars.com/api/?name=" +
        encodeURIComponent(selectedPatient.nombres_pacientes + " " + selectedPatient.apellidos_pacientes) +
        "&background=02457a&color=fff";

      $("#patientDetails").html(
        '<div class="d-flex align-items-center mb-2">' +
          '<img src="' + avatarUrl + '" class="patient-avatar" alt="Avatar">' +
          '<div class="ms-3">' +
            "<h4 class=\"mb-1\">" + selectedPatient.nombres_pacientes + " " + selectedPatient.apellidos_pacientes + "</h4>" +
            "<p class=\"mb-0\">" + selectedPatient.EDAD + " años</p>" +
          "</div>" +
        "</div>" +
        '<div class="row">' +
          '<div class="col-md-6">' +
            "<p><strong>DPI:</strong> " + selectedPatient.dpi_pacientes + "</p>" +
            "<p><strong>Fecha Nacimiento:</strong> " + formatDate(selectedPatient.fecha_nacimiento) + "</p>" +
          "</div>" +
          '<div class="col-md-6">' +
            "<p><strong>Teléfono:</strong> " + selectedPatient.telefono + "</p>" +
            "<p><strong>Dirección:</strong> " + selectedPatient.direccion + "</p>" +
          "</div>" +
        "</div>"
      );

      $("#selectedPatientInfo").show();
    }

    $("#btnStartRegister").click(function () {
      if (selectedPatient) {
        $("#seleccionPaciente").hide();
        $("#formRegistroPrenatal").show();

        $("#idPaciente").val(selectedPatient.id_pacientes);

        var avatarUrl =
          "https://ui-avatars.com/api/?name=" +
          encodeURIComponent(selectedPatient.nombres_pacientes + " " + selectedPatient.apellidos_pacientes) +
          "&background=02457a&color=fff";

        $("#currentPatientInfo").html(
          '<div class="d-flex align-items-center">' +
            '<img src="' + avatarUrl + '" class="patient-avatar me-3" alt="Avatar">' +
            "<div>" +
              "<h5 class=\"mb-1\">" + selectedPatient.nombres_pacientes + " " + selectedPatient.apellidos_pacientes + " - " + selectedPatient.edad + " años</h5>" +
              "<p class=\"mb-0\">DPI: " + selectedPatient.dpi_pacientes + " | Teléfono: " + selectedPatient.telefono + "</p>" +
            "</div>" +
          "</div>"
        );

        var today = new Date().toISOString().split("T")[0];
        $("#fechaIngreso").val(today);
        $("#fechaVisita").val(today);
      }
    });

    $("#btnBackToSearch").click(function () {
      $("#seleccionPaciente").show();
      $("#formRegistroPrenatal").hide();
    });

    $("#fur").change(function () {
      if (this.value) {
        var furDate = new Date(this.value);
        var partoDate = new Date(furDate);
        partoDate.setDate(partoDate.getDate() + 280);
        var partoYYYY = partoDate.getFullYear();
        var partoMM = String(partoDate.getMonth() + 1).padStart(2, "0");
        var partoDD = String(partoDate.getDate()).padStart(2, "0");
        $("#fechaProbableParto").val(partoYYYY + "-" + partoMM + "-" + partoDD);
      }
    });

    $("#formRegistroPrenatal").submit(function (e) {
      e.preventDefault();

      var formData = {
        idPaciente: $("#idPaciente").val(),
        fechaIngreso: $("#fechaIngreso").val(),
        noExpediente: $("#noExpediente").val(),
        nombreServicio: $("#nombreServicio").val(),
        historiaProblemaActual: $("#historiaProblemaActual").val(),
        fur: $("#fur").val(),
        gestas: $("#gestas").val() || 0,
        partos: $("#partos").val() || 0,
        abortos: $("#abortos").val() || 0,
        nacidosVivos: $("#nacidosVivos").val() || 0,
        nacidosMuertos: $("#nacidosMuertos").val() || 0,
        hijosVivos: $("#hijosVivos").val() || 0,
        hijosMuertos: $("#hijosMuertos").val() || 0,
        antecedentesVacunaTD: $('input[name="antecedentesVacunaTD"]:checked').val() || 0,
        dosisTD: $("#dosisTD").val() || 0,
        fechaUltimaDosisTD: $("#fechaUltimaDosisTD").val(),
        antecedentesVacunaTDAP: $('input[name="antecedentesVacunaTDAP"]:checked').val() || 0,
        dosisTDAP: $("#dosisTDAP").val() || 0,
        fechaUltimaDosisTDAP: $("#fechaUltimaDosisTDAP").val(),
        sintomas: $('input[name="sintomas"]:checked').map(function () { return this.value; }).get(),
        fechaProbableParto: $("#fechaProbableParto").val(),
        circunferenciaBrazo: $("#circunferenciaBrazo").val(),
        masaCorporal: $("#masaCorporal").val(),
        fechaVisita: $("#fechaVisita").val(),
        presionArterial: $("#presionArterial").val(),
        temperaturaCorporal: $("#temperaturaCorporal").val(),
        pesoLibras: $("#pesoLibras").val(),
        respiracionesMinuto: $("#respiracionesMinuto").val(),
        frecuenciaCardiaca: $("#frecuenciaCardiaca").val(),
        hemoglobina: $("#hemoglobina").val(),
        orina: $("#orina").val(),
        vdrl: $("#vdrl").val(),
        problemaDetectado: $("#problemaDetectado").val(),
        sulfatoFerroso: $("#sulfatoFerroso").val(),
        acidoFolico: $("#acidoFolico").val()
      };

      $.ajax({
        url: "../php/guardar_registro.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(formData),
        dataType: "json",
        success: function (response) {
          if (response.success) {
            mostrarAlerta(response.message, "success");
            setTimeout(function () {
              $("#formRegistroPrenatal")[0].reset();
              $("#seleccionPaciente").show();
              $("#formRegistroPrenatal").hide();
              $("#selectedPatientInfo").hide();
              selectedPatient = null;
            }, 2000);
          } else {
            mostrarAlerta(response.message, "danger");
          }
        },
        error: function (xhr, status, error) {
          mostrarAlerta("Error al guardar el registro: " + error, "danger");
        }
      });
    });

    function formatDate(dateString) {
      var date = new Date(dateString);
      return date.toLocaleDateString("es-ES");
    }

    function mostrarAlerta(mensaje, tipo) {
      var alerta =
        '<div class="alert alert-' + tipo + ' alert-auto-close alert-dismissible fade show" role="alert">' +
          mensaje +
          '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
        "</div>";
      $("#alertContainer").append(alerta);
      setTimeout(function () { $(".alert-auto-close").alert("close"); }, 5000);
    }
  });

  function showTab(tabId) {
    var tabElement = document.getElementById(tabId);
    var tab = new bootstrap.Tab(tabElement);
    tab.show();
  }
</script>

</body>
</html>
 