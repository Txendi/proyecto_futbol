<?php

require '../conexion.php';

$partidos = [];

if (isset($_GET['rival'])) {
    $rival = '%' . $_GET['rival'] . '%';

    $sql = "SELECT id_partido, rival, fecha, competicion, local_visitante, goles_favor, goles_contra 
            FROM partidos 
            WHERE rival LIKE ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $rival);
} else {
    $sql = "SELECT id_partido, rival, fecha, competicion, local_visitante, goles_favor, goles_contra 
            FROM partidos";
    $stmt = $conexion->prepare($sql);
}

$stmt->execute();
$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $partidos[] = $fila;
}

$stmt->close();

echo json_encode(['ok' => true, 'data' => $partidos]);

?>