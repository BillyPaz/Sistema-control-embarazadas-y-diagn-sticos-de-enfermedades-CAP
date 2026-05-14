<?php
include(__DIR__."/conexion.php");

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
        echo "<script>alert('✅ Paciente agregado correctamente'); window.location.href='pacientes.php';</script>";
    } else {
        echo "<script>alert('❌ Error al agregar paciente: " . $conn->error . "'); window.location.href='pacientes.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
