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
 
        $sql = "INSERT INTO REGISTRO_CLINICO_PRENATAL 
                (FECHA_INGRESO, NO_EXPEDIENTE, NOMBRE_SERVICIO, HISTORIA_PROBLEMA_ACTUAL, FUR, 
                 GESTAS, PARTOS, ABORTOS, NACIDOS_VIVOS, NACIDOS_MUERTOS, HIJOS_VIVOS, HIJOS_MUERTOS,
                 ANTECEDENTES_VACUNA_TD, DOSIS_TD, FECHA_ULTIMA_DOSIS_TD, 
                 ANTECEDENTES_VACUNA_TDAP, DOSIS_TDAP, FECHA_ULTIMA_DOSIS_TDAP, ID_PACIENTES) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
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
            $sql_sintomas = "INSERT INTO REGISTRO_CLINICO_SINTOMAS (ID_REGISTRO_CLINICO_PRENATAL, ID_SIGNO_SINTOMA, FECHA_INGRESO) VALUES (?, ?, NOW())";
            $stmt_sintomas = $conn->prepare($sql_sintomas);
            
            foreach ($data['sintomas'] as $sintoma) {
                $stmt_sintomas->bind_param("ii", $id_registro, $sintoma);
                $stmt_sintomas->execute();
            }
            
            $stmt_sintomas->close();
        }
        

        $sql_seguimiento = "INSERT INTO SEGUIMIENTO_PRENATAL 
                           (FUR, FECHA_PROBABLE_PARTO, CIRCUNFERENCIA_BRAZO, MASA_CORPORAL, FECHA_VISITA,
                            PRESION_ARTERIAL, TEMPERATURA_CORPORAL, PESO_LIBRAS, RESPIRACIONES_MINUTO,
                            FECUENCIA_CARDIACA, HEMOGLOBINA, ORINA, VDRL, PROBLEMA_DETECTADO,
                            SULFATO_FERROSO, ACIDO_FOLICO, ID_REGISTRO_CLINICO_PRENATAL)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
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