<?php
include(__DIR__."/conexion.php");

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

    $sql = "update pacientes 
            set dpi_pacientes = ?, 
                nombres_pacientes = ?, 
                apellidos_pacientes = ?, 
                fecha_nacimiento = ?, 
                id_genero = ?, 
                direccion = ?, 
                telefono = ?, 
                id_estado_pacientes = ?
            where id_pacientes = ?";


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
