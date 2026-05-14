<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_seguimiento = $_POST['id_seguimiento'];
    $numero_dosis = $_POST['numero_dosis'];

    $sql = "INSERT INTO Calendario_seguimiento_tb (ID_SEGUIMIENTO, NUMERO_DOSIS) 
            VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_seguimiento, $numero_dosis);

    if ($stmt->execute()) {
        header("Location: tuberculosis.php?id_paciente=" . $id_seguimiento . "&msg=success");
        exit;
    } else {
        echo "Error al guardar la dosis: " . $conn->error;
    }
}
?>
