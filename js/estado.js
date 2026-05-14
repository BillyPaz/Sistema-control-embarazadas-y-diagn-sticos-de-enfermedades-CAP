document.addEventListener("DOMContentLoaded", () => {
    const faseSelect = document.getElementById("fase");
    const dosisInput = document.getElementById("dosis_recibida");
    const estadoInput = document.getElementById("estado_seguimiento");
    const dosisPendientesInput = document.getElementById("dosis_pendientes");

    function actualizarEstado() {
        const fase = parseInt(faseSelect.value);
        const dosis = parseInt(dosisInput.value) || 0;

        let total = 0;
        if (fase === 1) total = 50;
        if (fase === 2) total = 75;

        let estado = "INICIO";
        let pendientes = total - dosis;

        if (dosis >= total) {
            estado = "FINALIZADO";
            pendientes = 0;
        } else if (dosis > 5) {
            estado = "EN PROCESO";
        }

        estadoInput.value = estado;
        dosisPendientesInput.value = pendientes >= 0 ? pendientes : 0;
    }

    dosisInput.addEventListener("input", actualizarEstado);
    faseSelect.addEventListener("change", actualizarEstado);
});

