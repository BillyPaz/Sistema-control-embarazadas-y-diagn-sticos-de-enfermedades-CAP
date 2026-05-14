<?php
include(__DIR__."/../../SETTINGS/php/bd.php");
$conn = ConexionBD();

$idUsuario = $_GET['idUsuario'];


$query ="SELECT u.id_usuario, u.nombre_usuario, u.apellido_usuario, r.id_rol from usuario u
join rol_usuario ru on ru.id_usuario = u.id_usuario
join rol r on r.id_rol = ru.id_rol
where u.id_usuario = :idUsuario";
$stmt =$conn->prepare($query);
$stmt->bindParam(':idUsuario',$idUsuario);
$stmt->execute();
$usuarioEdit = $stmt->fetch(PDO::FETCH_ASSOC);

$query2="SELECT * FROM rol";
$stmt2 =$conn->prepare($query2);
$stmt2->execute();
$roles = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success'=>true,
    'datosUsuarioEdit'=>$usuarioEdit,
    'roles'=>$roles
]);


?>