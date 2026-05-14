<?php
// pacientes.php
require_once 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  
    $conn = conexionBD(); // Retorna un objeto PDO
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    $sql = "SELECT 
    P.ID_PACIENTES, 
    P.DPI_PACIENTES, 
    P.NOMBRES_PACIENTES, 
    P.APELLIDOS_PACIENTES, 
    P.FECHA_NACIMIENTO, 
    P.TELEFONO, 
    P.DIRECCION
FROM PACIENTES P
LEFT JOIN MODULO_VIH V ON P.ID_PACIENTES = V.ID_PACIENTE
WHERE V.ID_PACIENTE IS NULL
  AND (
        P.NOMBRES_PACIENTES LIKE :search 
        OR P.APELLIDOS_PACIENTES LIKE :search 
        OR P.DPI_PACIENTES LIKE :search
      )
ORDER BY P.APELLIDOS_PACIENTES, P.NOMBRES_PACIENTES;
";
    
    $stmt = $conn->prepare($sql);
    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    $stmt->execute();
    
    $pacientes = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Calcular edad
        $fechaNacimiento = new DateTime($row['FECHA_NACIMIENTO']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fechaNacimiento)->y;
        $row['EDAD'] = $edad;
        
        $pacientes[] = $row;
    }

    echo json_encode($pacientes);

    // Cerrar conexión (opcional en PDO)
    $conn = null;
}
?>
