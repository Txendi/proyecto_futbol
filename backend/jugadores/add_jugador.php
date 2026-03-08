<?php

require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// en caso de no recibir nada
if (!$data) {
    echo json_encode(['ok' => false, 'error' => 'No se han recibido los datos']);
    exit;
}

// en caso de no recibir nada los metera vacios, y el de estado en activo
$nombre = $data['nombre'] ?? '';
$apellidos = $data['apellidos'] ?? '';
$alias = $data['alias'] ?? '';
$posicion = $data['posicion_habitual'] ?? '';
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