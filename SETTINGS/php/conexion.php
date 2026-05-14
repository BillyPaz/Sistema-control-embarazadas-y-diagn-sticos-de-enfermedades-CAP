<?php
class Conexion {
    private $servername = "localhost";
    private $username = "u522243577_rot";
    private $password = "p7FBwWG+N";
    private $dbname = "u522243577_centroNpSalud";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        
        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
        
        $this->conn->set_charset("utf8");
    }

    public function getConnection() {
        return $this->conn;
    }

    public function close() {
        $this->conn->close();
    }
}
?>
