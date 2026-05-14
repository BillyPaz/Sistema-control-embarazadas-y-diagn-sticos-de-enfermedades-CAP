<?php
session_start();
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();
    
$query = "SELECT 
    SUBSTRING(us.nombre_usuario, 1, 1) AS inicial,
    us.id_usuario, 
    us.nombre_usuario, 
    us.correo, 
    COALESCE(r.descripcion, 'Sin rol asginado') AS nombre,
    us.estado_usuario as activo  
FROM usuario us
LEFT JOIN rol_usuario ru ON us.id_usuario = ru.id_usuario
LEFT JOIN rol r ON r.id_rol = ru.id_rol";
$stmt = $conn->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$query2 = "SELECT COUNT(*) as totalUsuarios FROM usuario";
$stmt2 = $conn->prepare($query2);
$stmt2->execute();
$totalUsuarios = $stmt2->fetch(PDO::FETCH_ASSOC);

$query3 = "SELECT * FROM rol";
$stmt3 = $conn->prepare($query3);
$stmt3->execute();
$roles = $stmt3->fetchAll(PDO::FETCH_ASSOC);

$query4 = "SELECT
  SUM(CASE WHEN estado_usuario = 1 THEN 1 ELSE 0 END) AS activos,
  SUM(CASE WHEN estado_usuario = 0 THEN 1 ELSE 0 END) AS inactivos
FROM usuario;";
$stmt4 = $conn->prepare($query4);
$stmt4->execute();
$conteoUsuariosActivos = $stmt4->fetch  (PDO::FETCH_ASSOC);

$query5 = "SELECT count(ru.id_rol) as cantRol from usuario u
inner join rol_usuario ru on ru.id_usuario = u.id_usuario
inner join rol as r on r.id_rol = ru.id_rol
where r.descripcion   = 'Administrador'";
$stmt5 = $conn->prepare($query5);
$stmt5->execute();
$cantAdmonRol = $stmt5->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'listUsuarios' => $usuarios,
    'totalUsuarios' => $totalUsuarios['totalUsuarios'],
    'listRoles' => $roles,
    'conteoUsuariosActivos' => $conteoUsuariosActivos,
    'cantAdmonRolT'=>$cantAdmonRol['cantRol']
]);

?>