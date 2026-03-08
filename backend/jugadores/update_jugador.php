<?php

require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// en caso de no recibir nada o un id no existente
if (!$data || !isset($data['id_jugador'])) {
    echo json_encode(['ok' => false, 'error' => 'Faltan datos o el ID del jugador']);
    exit;
}

$id_jugador = $data['id_jugador'];
$nombre = $data['nombre'] ?? '';
$apellidos = $data['apellidos'] ?? '';
$alias = $data['alias'] ?? '';
$posicion = $data['posicion_habitual'] ?? '';
$estado = $data['estado'] ?? 'activo';

$sql = "UPDATE jugadores SET nombre = ?, apellidos = ?, alias = ?, posicion_habitual = ?, estado = ? WHERE id_jugador = ?";
$stmt = $conexion->prepare($sql);

$stmt->bind_param("sssssi", $nombre, $apellidos, $alias, $posicion, $estado, $id_jugador);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Jugador actualizado correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();
?>