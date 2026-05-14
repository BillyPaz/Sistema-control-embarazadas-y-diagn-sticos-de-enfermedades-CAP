let pacientes = [];
document.addEventListener("DOMContentLoaded", function() {
    // Solicitar la lista de pacientes al servidor
    $.ajax({
        url: '../php/obtenerPacientes.php',  // Cambia esta URL si es necesario
        method: 'GET',
        dataType: 'JSON',
        success: function(response) {
            if (response.success) {
                // Si la respuesta es exitosa, se pasa la lista de pacientes a la función
                console.log("Pacientes:", response.listPacientes);
                pacientes = response.listPacientes
                listarPacientes(pacientes);
            } else {
                // Si hay un error en la respuesta
                console.error("Error al obtener pacientes:", response.message);
            }
        },
        error: function(xhr, status, error) {
            // En caso de error con la solicitud AJAX
            console.error("Error de conexión:", error);
        }
    });
});

// Función para listar los pacientes en el DOM
function listarPacientes(pacientes) {
    const pacientesList = document.getElementById("patientsList");

    // Si no se encuentran pacientes
    if (pacientes.length === 0) {
        pacientesList.innerHTML = `
            <div class="text-center py-4">
                <i class="bi bi-search display-4 text-muted"></i>
                <p class="mt-3 text-muted">No se encontraron pacientes</p>
            </div>
        `;
        return;
    }


    let html = '';

    pacientes.forEach(paciente => {
        html += `
            <div class="card patient-card mb-3" data-patient-id="${paciente.id_pacientes}">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="../../img/paciente.jpeg" class="patient-avatar" alt="Avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">${paciente.pacienteNombre}</h5>
                            <p class="card-text mb-1">
                                <span class="badge bg-info">${paciente.edad} años</span>
                            </p>
                            <p class="card-text small text-muted mb-0">
                                <i class="bi bi-file-text"></i> DPI ${paciente.dpi_pacientes} | 
                                <i class="bi bi-journal"></i> Dirección: ${paciente.direccion}
                            </p>
                            <p class="card-text small text-muted">
                                <i class="bi bi-calendar"></i> Fecha nacimiento: ${paciente.fecha_nacimiento}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    // Insertamos el HTML generado en el contenedor
    pacientesList.innerHTML = html;

    // Añadimos un event listener a cada tarjeta de paciente
    document.querySelectorAll('.patient-card').forEach(card => {
        card.addEventListener('click', function() {
            const patientId = this.getAttribute('data-patient-id');  // ID del paciente
            selectPatient(patientId);  // Llamamos a la función para seleccionar al paciente
        });
    });
}

// Función que maneja la selección de un paciente
function selectPatient(patientId) {
    // Buscar el paciente en la lista de pacientes (esto debería venir del servidor o estar en una variable global)
    // Aquí vamos a suponer que tienes una variable global `pacientes` con todos los pacientes
    const pacienteSeleccionado = pacientes.find(p => p.id_pacientes == patientId);

    if (!pacienteSeleccionado) {
        console.error(`No se encontró el paciente con ID: ${patientId}`);
        return;
    }

    // Mostrar la información del paciente seleccionado
    const selectedPatientInfo = document.getElementById('selectedPatientInfo');
    const patientDetails = document.getElementById('patientDetails');

    // Mostrar la información
    patientDetails.innerHTML = `
        <div class="d-flex align-items-center mb-3">
            <img src="../../img/paciente.jpeg" class="patient-avatar" alt="Avatar">
            <div class="ms-3">
                <h4 class="mb-1">${pacienteSeleccionado.pacienteNombre}</h4>
                <p class="mb-0">${pacienteSeleccionado.edad} años</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <p><strong>DPI:</strong> ${pacienteSeleccionado.dpi_pacientes}</p>
                <p><strong>Dirección:</strong> ${pacienteSeleccionado.direccion}</p>
                <p><strong>Fecha nacimiento:</strong> ${pacienteSeleccionado.fecha_nacimiento}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Estado:</strong> ${pacienteSeleccionado.descripcion}</p>
            </div>
        </div>
    `;

    // Mostrar la sección del paciente seleccionado
    selectedPatientInfo.style.display = 'block';

    // Opcional: Si necesitas mostrar un botón o realizar alguna acción con el paciente seleccionado
    const btnStartRegister = document.getElementById('btnStartRegister');
    btnStartRegister.addEventListener('click', function() {
        // Aquí puedes añadir lógica para iniciar el registro prenatal
        console.log(`Iniciando registro prenatal para el paciente ${pacienteSeleccionado.pacienteNombre}`);
    });

    // Resaltar la tarjeta seleccionada
    document.querySelectorAll('.patient-card').forEach(card => {
        card.classList.remove('selected');  // Limpiar selección previa
    });

    // Marcar la tarjeta seleccionada
    document.querySelector(`.patient-card[data-patient-id="${patientId}"]`).classList.add('selected');
}
