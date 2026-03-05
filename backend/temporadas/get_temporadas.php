<?php

require '../conexion.php';

$temporadas = [];

$sql = "SELECT id_temporada, nombre FROM temporadas";
$stmt = $conexion->prepare($sql);
$stmt->execute();

$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $temporadas[] = $fila;
}

$stmt->close();

if (empty($temporadas)) {
    echo json_encode(['ok' => false, 'error' => 'No se encontraron temporadas']);
    exit;
}

echo json_encode(['ok' => true, 'data' => $temporadas]);

?>