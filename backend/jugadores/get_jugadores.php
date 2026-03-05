<?php

require '../conexion.php';

$jugadores = [];

if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    $sql = "SELECT id_jugador, nombre, apellidos, alias, posicion_habitual, estado 
            FROM jugadores 
            WHERE nombre LIKE ? OR apellidos LIKE ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $query, $query);
} else {
    $sql = "SELECT id_jugador, nombre, apellidos, alias, posicion_habitual, estado FROM jugadores";
    $stmt = $conexion->prepare($sql);
}

$stmt->execute();
$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $jugadores[] = $fila;
}

$stmt->close();

echo json_encode(['ok' => true, 'data' => $jugadores]);

?>