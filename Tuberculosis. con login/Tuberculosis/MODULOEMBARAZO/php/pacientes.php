<?php
// pacientes.php
require_once '../../SETTINGS/php/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conexion = new Conexion();
    $conn = $conexion->getConnection();
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    $sql = "SELECT ID_PACIENTES, DPI_PACIENTES, NOMBRES_PACIENTES, APELLIDOS_PACIENTES, 
                   FECHA_NACIMIENTO, TELEFONO, DIRECCION 
            FROM PACIENTES 
            WHERE NOMBRES_PACIENTES LIKE ? OR APELLIDOS_PACIENTES LIKE ? OR DPI_PACIENTES LIKE ?
            ORDER BY APELLIDOS_PACIENTES, NOMBRES_PACIENTES";
    
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