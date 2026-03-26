<?php
// mismas explicaciones sencillas en todos, basandote en jugadores
require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

/* Recoje los datos, si no recibe nada, los mete vacios */
$nombre = $data['nombre'] ?? '';
$fecha_inicio = $data['fecha_inicio'] ?? '';
$fecha_fin = $data['fecha_fin'] ?? '';
$activa = !empty($data['activa']) ? 1 : 0;

if (empty($nombre) || empty($fecha_inicio)) {
    echo json_encode(['ok' => false, 'error' => 'Nombre y fecha de inicio son obligatorios']);
    exit;
}

/* Por si no recibe nada */
if (!$data) {
    echo json_encode(['ok' => false, 'error' => 'No se han recibido los datos']);
    exit;
}

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