<?php


include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$idRol = isset($_GET['id_rol']) ? intval($_GET['id_rol']) : 0;

$sql = "SELECT 
        p.id_permiso,
        p.descripcion AS permiso,
        p.observaciones,
        CASE 
            WHEN rp.id_permiso IS NOT NULL THEN 1 
            ELSE 0 
        END AS asignado
    FROM permiso p
    LEFT JOIN rol_permiso rp 
        ON rp.id_permiso = p.id_permiso AND rp.id_rol =:idRol
    WHERE p.activo = 1
    ORDER BY p.descripcion

";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode([
    'success'=>true,
    'modulos'=>$data
]);


?>