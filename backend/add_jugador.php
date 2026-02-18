<?php

require 'conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['ok' => false, 'error' => 'No se han recibido los datos']);
    exit;
}

$nombre = $data['nombre'] ?? '';
$apellidos = $data['apellidos'] ?? '';
$alias = $data['alias'] ?? '';
$posicion = $data['posicion'] ?? '';
$estado = $data['estado'] ?? 'activo';

$sql = "INSERT INTO jugadores (nombre, apellidos, alias, posicion_habitual, estado) VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssss", $nombre, $apellidos, $alias, $posicion, $estado);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Jugador creado correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();

?>