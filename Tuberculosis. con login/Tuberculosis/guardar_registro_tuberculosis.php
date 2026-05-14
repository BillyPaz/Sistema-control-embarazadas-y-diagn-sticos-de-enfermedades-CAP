<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_paciente = intval($_POST['id_paciente']);
    $fecha_ref = $_POST["fecha_referencia"];
    $id_tipo_paciente = $_POST["id_tipo_paciente"];
    $id_prueba_vih = $_POST["id_prueba_VIH"];
    $rechazo = $_POST["rechazo"];
    $fecha_realizacion_prueba = $_POST["fecha_realizacion_prueba"];
    $resultado_prueba = $_POST["resultado_prueba"];

    $area_que_refiere = $_POST["area_que_refiere"];
    $distrito_que_refiere = $_POST["distrito_que_refiere"];
    $servicio_que_refiere = $_POST["servicio_que_refiere"];    

    $area_alque_refiere = $_POST["area_alque_refiere"];
    $distrito_alque_refiere = $_POST["distrito_alque_refiere"];
    $servicio_alque_refiere = $_POST["servicio_alque_refiere"];    

    $motivo_referencia = $_POST["motivo_referencia"];

    $sql = "INSERT INTO registro_tuberculosis 
            (ID_PACIENTES, fecha_referencia, id_tipo_paciente, id_prueba_VIH,
            rechazo, fecha_realizacion_prueba, resultado_prueba,
            area_que_refiere, distrito_que_refiere, servicio_que_refiere,
            area_al_que_se_refiere, distrito_al_que_se_refiere, servicio_al_que_se_refiere,
            motivo_referencia, fecha_registro) 
            VALUES (
            '$id_paciente', '$fecha_ref', '$id_tipo_paciente', '$id_prueba_vih',
            '$rechazo', '$fecha_realizacion_prueba', '$resultado_prueba',
            '$area_que_refiere', '$distrito_que_refiere', '$servicio_que_refiere',
            '$area_alque_refiere', '$distrito_alque_refiere', '$servicio_alque_refiere',
            '$motivo_referencia', NOW())";


    if ($conn->query($sql) === TRUE) {
    echo "<script>
            alert('Paciente registrado correctamente');
            window.location.href = 'tuberculosis.php?id_paciente=$id_paciente';
          </script>";
    } else {
        echo "❌ Error: " . $conn->error;
    }
    
}
?>
