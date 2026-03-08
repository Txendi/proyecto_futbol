<?php

require '../conexion.php';

$jugadores = [];

// para la busqueda del input
if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    $sql = "SELECT id_jugador, nombre, apellidos, alias, posicion_habitual, estado 
            FROM jugadores 
            WHERE nombre LIKE ? OR apellidos LIKE ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $query, $query);
} else {
    // aqui es por si no se escribe nada en el input para que muestre todos 
    $sql = "SELECT id_jugador, nombre, apellidos, alias, posicion_habitual, estado FROM jugadores ORDER BY nombre ASC";
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