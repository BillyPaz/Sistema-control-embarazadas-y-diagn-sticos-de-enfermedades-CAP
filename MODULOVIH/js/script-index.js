document.addEventListener("DOMContentLoaded", function () {
    cargarPacientes();

 
});

function cargarPacientes() {
    fetch('../php/listar_pacientes.php',{
        method: 'GET'
    })
    .then(response=>response.json())
    .then(data=>{
        llenarTablaPacientes(data.pacientes)
        console.log(data);
    })
    .catch(error=>{
        console.error('Error al cargar los pacientes:', error);
    });

}
function llenarTablaPacientes(pacientes) {
    const tbody = document.getElementById('tablaPacientes');
    tbody.innerHTML = ''; // Limpiar la tabla antes de llenarla

    pacientes.forEach(p => {
        const fila = document.createElement('tr');

        fila.innerHTML = `
            <td>${p.paciente}</td>
            <td>${p.servicio_envio || '-'}</td>
            <td>${p.servicio_refiere || '-'}</td>
            <td>${p.peso || '-'}</td>
            <td>${p.talla || '-'}</td>
            <td>${p.pulso || '-'}</td>
            <td>${p.fecha_traslado || '-'}</td>
            <td>
<button  type="button" class="btn btn-primary btnVerDetalle" data-id="${p.id_pacientes}" data-bs-toggle="modal" data-bs-target="#modalVisualizarPrenatal">
<i class="fa-solid fa-eye"></i>
</button>
                
            </td>
        `;

        tbody.appendChild(fila);
    });
}

   // Aquí ya es seguro usar jQuery
    $(document).on('click', '.btnVerDetalle', function () {
        const idPaciente = $(this).data('id');
        console.log("paciente"+ idPaciente);
        $.ajax({
            url: '../php/obtener_detalle_paciente.php',
            method: 'GET',
            data: { idPaciente: idPaciente },
            dataType: 'json',
            success:function(response){
                if(response.success){
                    console.log(response.detalle);
                    document.getElementById("visualNombrePaciente").textContent = response.detalle.NOMBRES_PACIENTES +' '+response.detalle.APELLIDOS_PACIENTES || '-'; 
                    document.getElementById("visualDpiPaciente").textContent = response.detalle.DPI_PACIENTES || '-'; 
                    document.getElementById("visualFechaTraslado").textContent = response.detalle.fecha_traslado || '-'; 
                    document.getElementById("visualServicioEnvia").textContent = response.detalle.servicio_envio || '-'; 
                    document.getElementById("visualServicioRefiere").textContent = response.detalle.servicio_refiere || '-'; 
                    document.getElementById("visualHistoriaProblema").textContent = response.detalle.historial_enfermedad || '-'; 
                    document.getElementById("visualPeso").textContent = response.detalle.peso || '-'; 
                    document.getElementById("visualTalla").textContent = response.detalle.talla || '-'; 
                    document.getElementById("visualPresionArterial").textContent = response.detalle.presion_arterial || '-'; 
                    document.getElementById("visualPulso").textContent = response.detalle.pulso || '-'; 
                    document.getElementById("visualFrecuenciaRespiratoria").textContent = response.detalle.frecuencia_respiratoria || '-'; 
                    document.getElementById("visualTensionArterial").textContent = response.detalle.tension_arterial || '-'; 
                    document.getElementById("visualExamenesRealizados").textContent = response.detalle.examenes_realizados || '-'; 
             document.getElementById("visualMotivoReferencia").textContent = response.detalle.motivo_referencia || '-'; 
            document.getElementById("visualImpresionClinica").textContent = response.detalle.impresion_clinica || '-'; 
                }
                
        }})
    });
function imprimirPrenatal() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        unit: 'mm',
        format: 'a4',
        orientation: 'portrait'
    });

    // Obtener los datos del modal
    const nombrePaciente = document.getElementById('visualNombrePaciente').textContent;
    const dpiPaciente = document.getElementById('visualDpiPaciente').textContent;
    const fechaTraslado = document.getElementById('visualFechaTraslado').textContent;
    const servicioEnvia = document.getElementById('visualServicioEnvia').textContent;
    const servicioRefiere = document.getElementById('visualServicioRefiere').textContent;
    const historiaProblema = document.getElementById('visualHistoriaProblema').textContent;
    const peso = document.getElementById('visualPeso').textContent;
    const talla = document.getElementById('visualTalla').textContent;
    const presionArterial = document.getElementById('visualPresionArterial').textContent;
    const pulso = document.getElementById('visualPulso').textContent;
    const frecuenciaRespiratoria = document.getElementById('visualFrecuenciaRespiratoria').textContent;
    const tensionArterial = document.getElementById('visualTensionArterial').textContent;
    const examenesRealizados = document.getElementById('visualExamenesRealizados').textContent;
    const motivoReferencia = document.getElementById('visualMotivoReferencia').textContent;
    const impresionClinica = document.getElementById('visualImpresionClinica').textContent;

    const today = new Date();
    const fechaGeneracion = today.toLocaleDateString() + " " + today.toLocaleTimeString();

    let y = 20; // Posición vertical inicial

    // Encabezado
    doc.setFontSize(18);
    doc.setTextColor(0, 102, 204);
    doc.setFont(undefined, 'bold');
    doc.text('REPORTE DE TRASLADO PRENATAL', 105, y, { align: 'center' });
    
    y += 8;
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.setFont(undefined, 'normal');
    doc.text('Sistema de Gestión de Salud', 105, y, { align: 'center' });
    
    y += 6;
    doc.text(`Generado: ${fechaGeneracion}`, 105, y, { align: 'center' });

    // Línea decorativa
    doc.setDrawColor(0, 123, 255);
    doc.setLineWidth(0.5);
    doc.line(15, y + 5, 195, y + 5);

    y += 15;

    // Sección: DATOS DEL PACIENTE
    doc.setFillColor(40, 167, 69);
    doc.rect(15, y, 180, 8, 'F');
    doc.setTextColor(255);
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.text('DATOS DEL PACIENTE', 20, y + 6);

    y += 12;
    doc.setTextColor(0);
    doc.setFontSize(11);
    doc.setFont(undefined, 'bold');
    doc.text('Nombre Completo:', 20, y);
    doc.setFont(undefined, 'normal');
    doc.text(nombrePaciente, 55, y);
    
    y += 6;
    doc.setFont(undefined, 'bold');
    doc.text('DPI/CUI:', 20, y);
    doc.setFont(undefined, 'normal');
    doc.text(dpiPaciente, 55, y);

    y += 12;

    // Sección: DATOS GENERALES DEL TRASLADO
    doc.setFillColor(0, 123, 255);
    doc.rect(15, y, 180, 8, 'F');
    doc.setTextColor(255);
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.text('DATOS GENERALES DEL TRASLADO', 20, y + 6);

    y += 12;
    doc.setTextColor(0);
    doc.setFontSize(10);
    doc.setFont(undefined, 'bold');
    doc.text('Fecha de Traslado:', 20, y);
    doc.setFont(undefined, 'normal');
    doc.text(fechaTraslado, 55, y);
    
    y += 6;
    doc.setFont(undefined, 'bold');
    doc.text('Servicio que Envía:', 20, y);
    doc.setFont(undefined, 'normal');
    doc.text(servicioEnvia, 55, y);
    
    y += 6;
    doc.setFont(undefined, 'bold');
    doc.text('Servicio que Refiere:', 20, y);
    doc.setFont(undefined, 'normal');
    doc.text(servicioRefiere, 55, y);

    y += 12;

    // Sección: HISTORIA DEL PROBLEMA ACTUAL
    doc.setFillColor(108, 117, 125);
    doc.rect(15, y, 180, 8, 'F');
    doc.setTextColor(255);
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.text('HISTORIA DEL PROBLEMA ACTUAL', 20, y + 6);

    y += 12;
    doc.setTextColor(0);
    doc.setFontSize(10);
    doc.setFont(undefined, 'normal');
    const historiaLines = doc.splitTextToSize(historiaProblema, 170);
    doc.text(historiaLines, 20, y);
    y += historiaLines.length * 5 + 10;

    // Sección: EXAMEN FÍSICO
    doc.setFillColor(255, 193, 7);
    doc.rect(15, y, 180, 8, 'F');
    doc.setTextColor(0);
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.text('EXAMEN FÍSICO', 20, y + 6);

    y += 12;
    doc.setFontSize(10);

    // Primera fila de signos vitales
    drawVitalSign('Peso', peso, '', 20, y);
    drawVitalSign('Talla', talla, 'cm', 75, y);
    drawVitalSign('Presión Arterial', presionArterial, 'mmHg', 130, y);

    y += 15;

    // Segunda fila de signos vitales
    drawVitalSign('Pulso', pulso, 'lpm', 20, y);
    drawVitalSign('Frecuencia Respiratoria', frecuenciaRespiratoria, 'rpm', 75, y);
    drawVitalSign('Tensión Arterial', tensionArterial, 'mmHg', 130, y);

    y += 20;

    // Sección: INFORMACIÓN ADICIONAL
    doc.setFillColor(111, 66, 193);
    doc.rect(15, y, 180, 8, 'F');
    doc.setTextColor(255);
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.text('INFORMACIÓN ADICIONAL', 20, y + 6);

    y += 12;
    doc.setTextColor(0);
    doc.setFontSize(10);

    // Exámenes Realizados
    doc.setFont(undefined, 'bold');
    doc.text('Exámenes Realizados:', 20, y);
    y += 5;
    doc.setFont(undefined, 'normal');
    const examenesLines = doc.splitTextToSize(examenesRealizados, 170);
    doc.text(examenesLines, 20, y);
    y += examenesLines.length * 5 + 10;

    // Motivo de Referencia
    doc.setFont(undefined, 'bold');
    doc.text('Motivo de la Referencia:', 20, y);
    y += 5;
    doc.setFont(undefined, 'normal');
    const motivoLines = doc.splitTextToSize(motivoReferencia, 170);
    doc.text(motivoLines, 20, y);
    y += motivoLines.length * 5 + 10;

    // Impresión Clínica
    doc.setFont(undefined, 'bold');
    doc.text('Impresión Clínica:', 20, y);
    y += 5;
    doc.setFont(undefined, 'normal');
    const impresionLines = doc.splitTextToSize(impresionClinica, 170);
    doc.text(impresionLines, 20, y);

    // Footer
    doc.setFontSize(9);
    doc.setTextColor(100);
    doc.text('Documento generado automáticamente por el Sistema de Gestión de Salud.', 105, 285, { align: 'center' });

    // Función auxiliar para dibujar signos vitales
    function drawVitalSign(label, value, unit, x, yPos) {
        doc.setFont(undefined, 'bold');
        doc.text(`${label}:`, x, yPos);
        doc.setFont(undefined, 'normal');
        doc.text(value || 'N/A', x + 30, yPos);
        if (unit) {
            doc.text(unit, x + 45, yPos);
        }
    }

    // Descargar el PDF
    const fileName = `traslado_prenatal_${nombrePaciente.replace(/\s+/g, '_')}.pdf`;
    doc.save(fileName);
}
