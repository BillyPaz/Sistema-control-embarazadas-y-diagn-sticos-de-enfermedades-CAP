

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Prenatal - Sistema de Salud</title>
  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
:root {
  --primary-color: #02457a;     
  --secondary-color: #018ABE;   
  --light-bg: #f8f9fa;
  --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

body {
   background-color: var(--light-bg);
   font-family: 'Poppins', sans-serif;
   font-size: 14px; 
   color: #333;
}

.header {
 background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
}

.search-section {
    background-color: white;
    border-radius: 12px;
    box-shadow: var(--card-shadow);
    padding: 20px;
    margin-bottom: 20px;
    max-width: 900px;         /* 👈 un poco más ancho */
    margin-left: auto;
    margin-right: auto;
    font-family: 'Poppins', sans-serif;
}

.form-section {
    background-color: white;
    border-radius: 10px;
    box-shadow: var(--card-shadow);
    padding: 25px;
    margin-bottom: 25px;
    max-width: 900px;         /* 👈 un poco más ancho */
    margin-left: auto;
    margin-right: auto;
    font-family: 'Poppins', sans-serif;
}

.patient-card {
    cursor: pointer;
    transition: all 0.2s;
    border-left: 4px solid transparent;
}

.patient-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    border-left-color: var(--primary-color);
}

.patient-card.selected {
    border-left-color: var(--primary-color);
    background-color: rgba(67, 97, 238, 0.05);
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    font-family: 'Poppins', sans-serif;
}

.btn-primary:hover {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
}

.section-title {
  border-bottom: 2px solid var(--primary-color);
  padding-bottom: 6px;
  margin-bottom: 15px;
  color: var(--secondary-color);
  font-size: 14px;      /* 👈 más pequeño */
  font-weight: 700;     /* 👈 más grueso */
  letter-spacing: 0.5px;
  font-family: 'Poppins', sans-serif;
  text-transform: uppercase; /* 👈 opcional: títulos en mayúsculas */
}

.required-field::after {
    content: " *";
    color: #dc3545;
}

.search-box {
    position: relative;
}

.search-box .form-control {
    padding-left: 40px;
    font-family: 'Poppins', sans-serif;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 12px;
    color: #6c757d;
}

.patient-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--primary-color);
}

.sintomas-list {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px;
    font-family: 'Poppins', sans-serif;
}

.sintoma-item {
    padding: 5px 0;
    border-bottom: 1px solid #f0f0f0;
}

.sintoma-item:last-child {
    border-bottom: none;
}

#formRegistroPrenatal {
    display: none;
}

.alert-auto-close {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    animation: fadeOut 5s forwards;
    font-family: 'Poppins', sans-serif;
}

@keyframes fadeOut {
    0% { opacity: 1; }
    90% { opacity: 1; }
    100% { opacity: 0; display: none; }
}
</style>


</head>
<body>

    <header class="header text-white py-4 mb-4">
        <div class="container">
            <h1><i class="bi bi-clipboard2-pulse"></i> Sistema de Registro de Diagnósticos</h1>
            <p class="mb-0">Registro Clínico VIH</p>
        </div>
    </header>

    <div class="container mb-5">
   
        <div id="alertContainer"></div>
        
      
        <div id="seleccionPaciente">
            <div class="search-section">
                <h3 class="section-title"><i class="bi bi-search"></i> Buscar Paciente para Registro de VIH</h3>
                
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" id="searchPatient" placeholder="Nombre, apellido o DPI...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100" id="btnSearch">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </div>
                
                <div id="patientsList">
                    <div class="text-center py-4">
                        <i class="bi bi-search display-4 text-muted"></i>
                        <p class="mt-3 text-muted">Utilice el buscador para encontrar pacientes</p>
                    </div>
                </div>
            </div>
            
            <div class="search-section" id="selectedPatientInfo" style="display: none;">
                <h3 class="section-title"><i class="bi bi-person-check"></i> Paciente Seleccionado</h3>
                <div id="patientDetails" class="mb-3"></div>
                
                <div class="d-grid">
                    <button class="btn btn-success" id="btnStartRegister">
                        <i class="bi bi-plus-circle"></i> Iniciar Registro de VIH
                    </button>
                </div>
            </div>
        </div>
        
        
        <form id="formRegistroPrenatal">
            <input type="hidden" id="idPaciente" name="idPaciente">
            
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="section-title mb-0"><i class="bi bi-person"></i> Registro VIH</h3>
                    <button type="button" class="btn btn-outline-secondary" id="btnBackToSearch">
                        <i class="bi bi-arrow-left"></i> Cambiar Paciente
                    </button>
                </div>
                
                <div id="currentPatientInfo" class="alert alert-info mb-4"></div>
                
             
                <ul class="nav nav-pills mb-4 justify-content-center" id="prenatalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="datos-generales-tab" data-bs-toggle="pill" data-bs-target="#datos-generales" type="button" role="tab">
                            <i class="bi bi-person"></i> Datos Generales
                        </button>
                    </li>
                   
                </ul>

                <div class="tab-content" id="prenatalTabsContent">
     
                    <div class="tab-pane fade show active" id="datos-generales" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fechaTraslado" class="form-label required-field">Fecha de Traslado</label>
                                <input type="date" class="form-control" id="fechaTraslado" name="fechaTraslado" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="servicioEnvia" class="form-label required-field">Servicio que envía</label>
                                <input type="text" class="form-control" id="servicioEnvia" name="servicioEnvia" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="servicioRefiere" class="form-label required-field">Servicio que refiere</label>
                                <input type="text" class="form-control" id="servicioRefiere" name="servicioRefiere" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="historiaProblemaActual" class="form-label">Historia del Problema Actual</label>
                                <textarea class="form-control" id="historiaProblemaActual" name="historiaProblemaActual" rows="3"></textarea>
                            </div>
                        </div>
                        <h3 class="section-title" >EXAMEN FISICO</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="peso" class="form-label">Peso</label>
                                <input type="text" class="form-control" id="peso" name="peso">
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="talla" class="form-label">Talla</label>
                                <input type="number" class="form-control" id="talla" name="talla">
                            </div>
                            <div class="col-md-6 mb-3">
    <label for="presionArterial" class="form-label">Presión Arterial</label>
    <div class="input-group">
        <input type="number" class="form-control" id="presionArterial" name="presionArterial">
        <span class="input-group-text">mmHg</span>
    </div>
</div>
<div class="col-md-6 mb-3">
    <label for="pulso" class="form-label">Pulso</label>
    <div class="input-group">
        <input type="number" class="form-control" id="pulso" name="pulso">
        <span class="input-group-text">lpm</span>
    </div>
</div>

                             <div class="col-md-6 mb-3">
    <label for="frecuenciaRespiratoria" class="form-label">Frecuancia Respiratoria</label>
    <div class="input-group">
        <input type="number" class="form-control" id="frecuenciaRespiratoria" name="frecuenciaRespiratoria">
        <span class="input-group-text">rpm</span>
    </div>
</div>
                          <div class="col-md-6 mb-3">
    <label for="tensionArterial" class="form-label">Tensión arterial</label>
    <div class="input-group">
        <input type="number" class="form-control" id="tensionArterial" name="TensionArterial">
        <span class="input-group-text">mmHg</span>
    </div>
</div>
       
                             <div class="col-md-6 mb-3">
                                <label for="examenesRealizados" class="form-label">Exámenes realizados</label>
                                <textarea type="text" class="form-control" id="examenesRealizados"   name="examenesRealizados"></textarea>

                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="motivoReferencia" class="form-label">Motivo de la referencia</label>
                                <textarea type="date" class="form-control" id="motivoReferencia" name="motivoReferencia"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="impresionClinica" class="form-label">Impresión clinica</label>
                                <textarea type="date" class="form-control" id="impresionClinica" name="impresionClinica"></textarea>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btnSaveVih "  >
                                Guardar 
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            let selectedPatient = null;
            

            $('#btnSearch').click(function() {
                buscarPacientes();
            });
            
            $('#searchPatient').on('input', function() {
                buscarPacientes();
            });
            
            function buscarPacientes() {
                const searchText = $('#searchPatient').val();
                
                $.ajax({
                    url: '../php/pacientes.php',
                    type: 'GET',
                    data: { search: searchText },
                    dataType: 'json',
                    success: function(pacientes) {
                        mostrarPacientes(pacientes);
                    },
                    error: function(xhr, status, error) {
                        mostrarAlerta('Error al buscar pacientes: ' + error, 'danger');
                    }
                });
            }
            
            function mostrarPacientes(pacientes) {
                const patientsList = $('#patientsList');
                
                if (pacientes.length === 0) {
                    patientsList.html(`
                        <div class="text-center py-4">
                            <i class="bi bi-search display-4 text-muted"></i>
                            <p class="mt-3 text-muted">No se encontraron pacientes</p>
                        </div>
                    `);
                    return;
                }
                
                let html = '';
                
                pacientes.forEach(paciente => {
                    const isSelected = selectedPatient && selectedPatient.ID_PACIENTES === paciente.ID_PACIENTES;
                    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(paciente.NOMBRES_PACIENTES + ' ' + paciente.APELLIDOS_PACIENTES)}&background=4361ee&color=fff`;
                    
                    html += `
                        <div class="card patient-card mb-3 ${isSelected ? 'selected' : ''}" data-patient-id="${paciente.ID_PACIENTES}">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <img src="${avatarUrl}" class="patient-avatar" alt="Avatar">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title mb-1">${paciente.NOMBRES_PACIENTES} ${paciente.APELLIDOS_PACIENTES}</h5>
                                        <p class="card-text mb-1">
                                            <span class="badge bg-info">${paciente.EDAD} años</span>
                                            <span class="ms-2">DPI: ${paciente.DPI_PACIENTES}</span>
                                        </p>
                                        <p class="card-text small text-muted mb-0">
                                            <i class="bi bi-telephone"></i> ${paciente.TELEFONO} | 
                                            <i class="bi bi-geo-alt"></i> ${paciente.DIRECCION}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                patientsList.html(html);
                

                $('.patient-card').click(function() {
                    const patientId = $(this).data('patient-id');
                    seleccionarPaciente(patientId, pacientes);
                });
            }
            
            function seleccionarPaciente(patientId, pacientes) {
                selectedPatient = pacientes.find(p => p.ID_PACIENTES == patientId);
                
                // Actualizar interfaz
                $('.patient-card').removeClass('selected');
                $(`.patient-card[data-patient-id="${patientId}"]`).addClass('selected');
                

                const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedPatient.NOMBRES_PACIENTES + ' ' + selectedPatient.APELLIDOS_PACIENTES)}&background=4361ee&color=fff`;
                
                $('#patientDetails').html(`
                    <div class="d-flex align-items-center mb-3">
                        <img src="${avatarUrl}" class="patient-avatar" alt="Avatar">
                        <div class="ms-3">
                            <h4 class="mb-1">${selectedPatient.NOMBRES_PACIENTES} ${selectedPatient.APELLIDOS_PACIENTES}</h4>
                            <p class="mb-0">${selectedPatient.EDAD} años</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>DPI:</strong> ${selectedPatient.DPI_PACIENTES}</p>
                            <p><strong>Fecha Nacimiento:</strong> ${formatDate(selectedPatient.FECHA_NACIMIENTO)}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Teléfono:</strong> ${selectedPatient.TELEFONO}</p>
                            <p><strong>Dirección:</strong> ${selectedPatient.DIRECCION}</p>
                        </div>
                    </div>
                `);
                
                $('#selectedPatientInfo').show();
            }
            

            $('#btnStartRegister').click(function() {
                if (selectedPatient) {

                    $('#seleccionPaciente').hide();
                    $('#formRegistroPrenatal').show();

                    $('#idPaciente').val(selectedPatient.ID_PACIENTES);
                    

                    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedPatient.NOMBRES_PACIENTES + ' ' + selectedPatient.APELLIDOS_PACIENTES)}&background=4361ee&color=fff`;
                    
                    $('#currentPatientInfo').html(`
                        <div class="d-flex align-items-center">
                            <img src="${avatarUrl}" class="patient-avatar me-3" alt="Avatar">
                            <div>
                                <h5 class="mb-1">${selectedPatient.NOMBRES_PACIENTES} ${selectedPatient.APELLIDOS_PACIENTES} - ${selectedPatient.EDAD} años</h5>
                                <p class="mb-0">DPI: ${selectedPatient.DPI_PACIENTES} | Teléfono: ${selectedPatient.TELEFONO}</p>
                            </div>
                        </div>
                    `);
                    

                    const today = new Date().toISOString().split('T')[0];
                    $('#fechaIngreso').val(today);
                    $('#fechaVisita').val(today);
                }
            });
            

            $('#btnBackToSearch').click(function() {
                $('#seleccionPaciente').show();
                $('#formRegistroPrenatal').hide();
            });
            

            $('#fur').change(function() {
                if (this.value) {
                    const furDate = new Date(this.value);
                    const partoDate = new Date(furDate);
                    partoDate.setDate(partoDate.getDate() + 280); 
                    
                    const partoYYYY = partoDate.getFullYear();
                    const partoMM = String(partoDate.getMonth() + 1).padStart(2, '0');
                    const partoDD = String(partoDate.getDate()).padStart(2, '0');
                    
                    $('#fechaProbableParto').val(`${partoYYYY}-${partoMM}-${partoDD}`);
                }
            });
            
    
           $(document).on('click', '.btnSaveVih', function(e) {
                e.preventDefault();
                
      
                const formData = {
                    idPaciente: $('#idPaciente').val(),
                    fechaTraslado: $("#fechaTraslado").val(),
                    servicioEnvia: $("#servicioEnvia").val(),
                    servicioRefiere:$("#servicioRefiere").val(),
                    historiaProblemaActual: $("#historiaProblemaActual").val(),
                    peso:$("#peso").val(),
                    talla:$("#talla").val(),
                    pulso:$("#pulso").val(),    
                    presionArterial:$("#presionArterial").val(),
                    frecuenciaRespiratoria:$("#frecuenciaRespiratoria").val(),
                    tensionArterial:$("#tensionArterial").val(),
                    examenesRealizados:$("#examenesRealizados").val(),
                    motivoReferencia:$("#motivoReferencia").val(),
                    impresionClinica:$("#impresionClinica").val()
                };
                
          
                $.ajax({
                    url: '../php/guardar_registro.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            mostrarAlerta(response.message, 'success');
                           
                            setTimeout(() => {
                                $('#formRegistroPrenatal')[0].reset();
                                $('#seleccionPaciente').show();
                                $('#formRegistroPrenatal').hide();
                                $('#selectedPatientInfo').hide();
                                selectedPatient = null;
                            }, 2000);
                        } else {
                            mostrarAlerta(response.message, 'danger');
                            window.location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        mostrarAlerta('Error al guardar el registro: ' + error, 'danger');
                    }
                });
            });
            
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('es-ES');
            }
            
            function mostrarAlerta(mensaje, tipo) {
                const alerta = `
                    <div class="alert alert-${tipo} alert-auto-close alert-dismissible fade show" role="alert">
                        ${mensaje}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                $('#alertContainer').append(alerta);
                
  
                setTimeout(() => {
                    $('.alert-auto-close').alert('close');
                }, 5000);
            }
        });

        function showTab(tabId) {
            const tabElement = document.getElementById(tabId);
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    </script>
</body>
</html>