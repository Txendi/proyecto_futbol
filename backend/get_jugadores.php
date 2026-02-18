<?php

require 'conexion.php';

$jugadores = [];

$sql = ('SELECT id_jugador, nombre, apellidos, alias, posicion_habitual, estado FROM jugadores ');
$stmt = $conexion->prepare($sql);
$stmt->execute();

$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $jugadores[] = $fila;
}

$stmt->close();

echo json_encode(['ok' => true, 'data' => $jugadores]);

?>