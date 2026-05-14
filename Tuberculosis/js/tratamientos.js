document.addEventListener("DOMContentLoaded", function() {
    const fase = document.getElementById("fase");
    const dosisRecibida = document.getElementById("dosis_recibida");
    const dosisPendientes = document.getElementById("dosis_pendientes");

    let totalDosis = 0;

    fase.addEventListener("change", function() {
        if (fase.value == "1") {
            totalDosis = 50;
        } else if (fase.value == "2") {
            totalDosis = 75;
        } else {
            totalDosis = 0;
        }
        actualizarPendientes();
    });

    dosisRecibida.addEventListener("input", actualizarPendientes);

    function actualizarPendientes() {
        let recibidas = parseInt(dosisRecibida.value) || 0;
        let pendientes = totalDosis - recibidas;
        if (pendientes < 0) pendientes = 0;
        dosisPendientes.value = pendientes;
    }
});