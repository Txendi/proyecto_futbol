<?php

require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['ok' => false, 'error' => 'No se han recibido los datos']);
    exit;
}

$nombre = $data['nombre'] ?? '';
$fecha_inicio = $data['fecha_inicio'] ?? '';
$fecha_fin = $data['fecha_fin'] ?? '';
$activa = $data['activa'] ?? false;

$sql = "INSERT INTO temporadas (nombre, fecha_inicio, fecha_fin, activa) VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssi", $nombre, $fecha_inicio, $fecha_fin, $activa);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Temporada creada correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();

?>