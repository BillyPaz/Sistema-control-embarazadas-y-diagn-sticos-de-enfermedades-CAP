<?php
// guardar_registro.php
require_once '../../SETTINGS/php/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $conexion = new Conexion();
    $conn = $conexion->getConnection();
    

    $conn->begin_transaction();
    
    try {
 
       $sql = "insert into registro_clinico_prenatal 
                (fecha_ingreso, no_expediente, nombre_servicio, historia_problema_actual, fur, 
                 gestas, partos, abortos, nacidos_vivos, nacidos_muertos, hijos_vivos, hijos_muertos,
                 antecedentes_vacuna_td, dosis_td, fecha_ultima_dosis_td, 
                 antecedentes_vacuna_tdap, dosis_tdap, fecha_ultima_dosis_tdap, id_pacientes) 
                values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssiiiiiiiiissssi", 
            $data['fechaIngreso'], $data['noExpediente'], $data['nombreServicio'], 
            $data['historiaProblemaActual'], $data['fur'], $data['gestas'], $data['partos'], 
            $data['abortos'], $data['nacidosVivos'], $data['nacidosMuertos'], 
            $data['hijosVivos'], $data['hijosMuertos'], $data['antecedentesVacunaTD'], 
            $data['dosisTD'], $data['fechaUltimaDosisTD'], $data['antecedentesVacunaTDAP'], 
            $data['dosisTDAP'], $data['fechaUltimaDosisTDAP'], $data['idPaciente']
        );
        
        $stmt->execute();
        $id_registro = $stmt->insert_id;
        $stmt->close();
        
        
        if (!empty($data['sintomas'])) {
           $sql_sintomas = "insert into registro_clinico_sintomas (id_registro_clinico_prenatal, id_signo_sintoma, fecha_ingreso) values (?, ?, now())";
            $stmt_sintomas = $conn->prepare($sql_sintomas);
            
            foreach ($data['sintomas'] as $sintoma) {
                $stmt_sintomas->bind_param("ii", $id_registro, $sintoma);
                $stmt_sintomas->execute();
            }
            
            $stmt_sintomas->close();
        }
        

        $sql_seguimiento = "insert into seguimiento_prenatal 
        (fur, fecha_probable_parto, circunferencia_brazo, masa_corporal, fecha_visita,
         presion_arterial, temperatura_corporal, peso_libras, respiraciones_minuto,
         fecuencia_cardiaca, hemoglobina, orina, vdrl, problema_detectado,
         sulfato_ferroso, acido_folico, id_registro_clinico_prenatal)
        values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        
        $stmt_seguimiento = $conn->prepare($sql_seguimiento);
        $stmt_seguimiento->bind_param(
            "ssiisssddiissdsii", 
            $data['fur'], $data['fechaProbableParto'], $data['circunferenciaBrazo'], 
            $data['masaCorporal'], $data['fechaVisita'], $data['presionArterial'], 
            $data['temperaturaCorporal'], $data['pesoLibras'], $data['respiracionesMinuto'], 
            $data['frecuenciaCardiaca'], $data['hemoglobina'], $data['orina'], 
            $data['vdrl'], $data['problemaDetectado'], $data['sulfatoFerroso'], 
            $data['acidoFolico'], $id_registro
        );
        
        $stmt_seguimiento->execute();
        $stmt_seguimiento->close();
        

        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Registro prenatal guardado correctamente',
            'id_registro' => $id_registro
        ]);
        
    } catch (Exception $e) {

        $conn->rollback();
        
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar el registro: ' . $e->getMessage()
        ]);
    }
    
    $conexion->close();
}
?>