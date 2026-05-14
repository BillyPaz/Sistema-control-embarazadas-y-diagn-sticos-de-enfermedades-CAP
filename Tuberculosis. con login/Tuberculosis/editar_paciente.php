<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $dpi = $_POST["dpi"];
    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $fecha = $_POST["fecha"];
    $genero = $_POST["genero"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $estado = !empty($_POST["estado"]) ? $_POST["estado"] : NULL; 

    $sql = "UPDATE PACIENTES 
            SET DPI_PACIENTES = ?, 
                NOMBRES_PACIENTES = ?, 
                APELLIDOS_PACIENTES = ?, 
                FECHA_NACIMIENTO = ?, 
                ID_GENERO = ?, 
                DIRECCION = ?, 
                TELEFONO = ?, 
                ID_ESTADO_PACIENTES = ?
            WHERE ID_PACIENTES = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissii", 
    $dpi,           
    $nombres,         
    $apellidos,     
    $fecha,         
    $genero,        
    $direccion,     
    $telefono,      
    $estado,        
    $id             
);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Paciente actualizado correctamente'); window.location.href='pacientes.php';</script>";
    } else {
        echo "<script>alert('❌ Error al actualizar paciente: " . $conn->error . "'); window.location.href='pacientes.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
