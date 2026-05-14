<?php
function ConexionBD(){
    $host = "localhost";
    $port = "3306";
    $dbname = "";
    $username = "";
    $password = "+N";

    try {
        $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
    catch(PDOException $e) {
        // Mensaje más detallado
        error_log("ERROR DE CONEXIÓN: " . $e->getMessage());
        throw new Exception("No se pudo conectar a PostgreSQL. Detalles: " . $e->getMessage());
    }
}

?>
