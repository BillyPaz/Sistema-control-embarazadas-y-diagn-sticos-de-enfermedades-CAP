<?php
$servername = "localhost";  
$username   = "root";        
$password   = "umg2025";           
$database   = "centro_de_salud_np"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("❌ Conexión fallida: " . $conn->connect_error);
} else {

}
?>
