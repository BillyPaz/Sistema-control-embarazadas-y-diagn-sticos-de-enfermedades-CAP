<?php
session_start();
if(isset($_SESSION['user'])){
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$query = "select 
count(rp.id_rol)as permisos, 
r.id_rol,
r.descripcion,
r.observaciones
 from rol r
left join rol_permiso as rp on rp.id_rol = r.id_rol
group by r.descripcion, r.id_rol
";
$stmt2 = $conn->prepare($query);
$stmt2->execute();
$listRoles = $stmt2->fetchAll(PDO::FETCH_ASSOC);




$query2 = "SELECT count(*)as rol from rol
where activo = 1";
$stmt2 = $conn->prepare($query2);
$stmt2->execute();
$countRoles = $stmt2->fetch(PDO::FETCH_ASSOC);

$query3 = "SELECT count(*)as permiso from permiso
where activo = 1";
$stmt3 = $conn->prepare($query3);
$stmt3->execute();
$countPermisos = $stmt3->fetch(PDO::FETCH_ASSOC);

$query4 = "SELECT * FROM rol";
$stmt4 = $conn->prepare($query4);
$stmt4->execute();
$roles = $stmt4->fetchAll(PDO::FETCH_ASSOC);

$query5 ="SELECT us.*
FROM usuario us
LEFT JOIN rol_usuario ru ON us.id_usuario = ru.id_usuario
WHERE ru.id_rol IS NULL AND us.estado_usuario = 1;
";
$stmt5 = $conn->prepare($query5);
$stmt5->execute();
$listUsuarios = $stmt5->fetchAll(PDO::FETCH_ASSOC);

$query6 ="SELECT 
    us.id_usuario,
    us.nombre_usuario,
    r.descripcion,
    ru.fecha_registro,
    us.estado_usuario
FROM usuario us
inner JOIN rol_usuario ru ON us.id_usuario = ru.id_usuario
inner JOIN rol r ON ru.id_rol = r.id_rol";
$stmt6= $conn->prepare($query6);
$stmt6->execute();
$listUsuariosAsignaciones = $stmt6->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success'=>true,
    'listRoles'=>$listRoles,
    'countRoles'=>$countRoles,
    'countPermisos'=>$countPermisos, 
    'listUsuarios'=>$listUsuarios,
    'roles'=>$roles,
    'listUsuariosAsignaciones'=>$listUsuariosAsignaciones
]);


}
else{
    echo json_encode(
        [
            'success'=>false,
    'message'=>"No tiene acceso para acceder"
        ]
        );
}
?>