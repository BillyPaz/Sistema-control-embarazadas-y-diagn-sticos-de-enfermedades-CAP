<?php
$servername = "localhost";  
$username   = "u522243577_rot";        
$password   = "p7FBwWG+N";           
$database   = "u522243577_centroNpSalud"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("❌ Conexión fallida: " . $conn->connect_error);
} else {

}
?>
