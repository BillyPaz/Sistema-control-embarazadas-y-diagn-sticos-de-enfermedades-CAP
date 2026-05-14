<?php
// pacientes.php
require_once __DIR__.'/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  
    $conn = conexionBD(); // Retorna un objeto PDO
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
   $sql = "select 
    p.id_pacientes, 
    p.dpi_pacientes, 
    p.nombres_pacientes, 
    p.apellidos_pacientes, 
    p.fecha_nacimiento, 
    p.telefono, 
    p.direccion
from pacientes p
left join modulo_vih v on p.id_pacientes = v.id_paciente
where v.id_paciente is null
  and (
        p.nombres_pacientes like :search 
        or p.apellidos_pacientes like :search 
        or p.dpi_pacientes like :search
      )
order by p.apellidos_pacientes, p.nombres_pacientes;
";

    
    $stmt = $conn->prepare($sql);
    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    $stmt->execute();
    
    $pacientes = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Calcular edad
        $fechaNacimiento = new DateTime($row['fecha_nacimiento']);
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
