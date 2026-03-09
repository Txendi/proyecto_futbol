<?php
// mismas explicaciones sencillas en todos, basandote en jugadores
require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['id_temporada'])) {
    echo json_encode(['ok' => false, 'error' => 'Faltan datos o el ID de la temporada']);
    exit;
}

$id_temporada = $data['id_temporada'];
$nombre = $data['nombre'] ?? '';
$fecha_inicio = $data['fecha_inicio'] ?? '';
$fecha_fin = $data['fecha_fin'] ?? '';
$activa = !empty($data['activa']) ? 1 : 0;

$sql = "UPDATE temporadas SET nombre = ?, fecha_inicio = ?, fecha_fin = ?, activa = ? WHERE id_temporada = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssii", $nombre, $fecha_inicio, $fecha_fin, $activa, $id_temporada);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Temporada actualizada correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();

?>