<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dpi = $_POST["dpi"];
    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $fecha = $_POST["fecha"];
    $genero = $_POST["genero"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $estado = !empty($_POST["estado"]) ? $_POST["estado"] : NULL; 

    $sql = "INSERT INTO PACIENTES 
            (DPI_PACIENTES, NOMBRES_PACIENTES, APELLIDOS_PACIENTES, FECHA_NACIMIENTO, ID_GENERO, DIRECCION, TELEFONO, ID_ESTADO_PACIENTES)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

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
