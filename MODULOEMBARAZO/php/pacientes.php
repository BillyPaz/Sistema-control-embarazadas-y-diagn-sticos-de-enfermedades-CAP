<?php
// pacientes.php
require_once '../../SETTINGS/php/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conexion = new Conexion();
    $conn = $conexion->getConnection();
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    $sql = "select id_pacientes, dpi_pacientes, nombres_pacientes, apellidos_pacientes, 
                   fecha_nacimiento, telefono, direccion 
            from pacientes 
            where nombres_pacientes like ? or apellidos_pacientes like ? or dpi_pacientes like ?
            order by apellidos_pacientes, nombres_pacientes";

    
    $stmt = $conn->prepare($sql);
    $searchParam = "%$search%";
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $pacientes = array();
    while($row = $result->fetch_assoc()) {

        $fechaNacimiento = new DateTime($row['FECHA_NACIMIENTO']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fechaNacimiento)->y;
        $row['EDAD'] = $edad;
        
        $pacientes[] = $row;
    }
    
    echo json_encode($pacientes);
    
    $stmt->close();
    $conexion->close();
}
?>