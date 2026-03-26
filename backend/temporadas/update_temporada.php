<?php

require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

/* En caso de no recibir nada o un id no existente */
if (!$data || !isset($data['id_temporada'])) {
    echo json_encode(['ok' => false, 'error' => 'Faltan datos o el ID de la temporada']);
    exit;
}

$id_temporada = $data['id_temporada'];
$nombre = $data['nombre'] ?? '';
$fecha_inicio = $data['fecha_inicio'] ?? '';
$fecha_fin = $data['fecha_fin'] ?? '';
$activa = !empty($data['activa']) ? 1 : 0;

/* Validacion de datos nombre y fecha */
if (empty($nombre) || empty($fecha_inicio)) {
    echo json_encode(['ok' => false, 'error' => 'Nombre y fecha de inicio son obligatorios']);
    exit;
}

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