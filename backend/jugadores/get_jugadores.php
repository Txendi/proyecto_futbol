<?php

require '../conexion.php';

$jugadores = [];

/* Busqueda del input */
if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    $sql = "SELECT id_jugador, nombre, apellidos, alias, posicion_habitual, estado  /* Busqueda por nombre apellido y alias */
            FROM jugadores 
            WHERE nombre LIKE ? OR apellidos LIKE ? OR alias LIKE ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $query, $query, $query);
} else { /* Busqueda en caso de dejar input vacio */
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