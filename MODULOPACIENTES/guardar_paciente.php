<?php
include(__DIR__."/../conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dpi = $_POST["dpi"];
    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $fecha = $_POST["fecha"];
    $genero = $_POST["genero"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $estado = !empty($_POST["estado"]) ? $_POST["estado"] : NULL; 

    $sql = "insert into pacientes 
            (dpi_pacientes, nombres_pacientes, apellidos_pacientes, fecha_nacimiento, id_genero, direccion, telefono, id_estado_pacientes)
            values (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissi", $dpi, $nombres, $apellidos, $fecha, $genero, $direccion, $telefono, $estado);

    if ($stmt->execute()) {
        // Redirige con parámetro de éxito
        header("Location: pacientes.php?guardar=ok");
        exit;
    } else {
        // Redirige con parámetro de error
        header("Location: pacientes.php?guardar=error");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
