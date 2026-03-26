<?php

require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

/* Recoje los datos, si no recibe nada, los mete vacios */
$nombre = $data['nombre'] ?? '';
$apellidos = $data['apellidos'] ?? '';
$alias = $data['alias'] ?? '';
$posicion = $data['posicion_habitual'] ?? '';
$estado = $data['estado'] ?? 'activo';

/* Validacion para nombre y apellido, que no esten vacios */
if (empty($data['nombre']) || empty($data['apellidos'])) {
    echo json_encode(['ok' => false, 'error' => 'Nombre y apellidos obligatorios']);
    exit;
}

/* Por si no recibe nada */
if (!$data) {
    echo json_encode(['ok' => false, 'error' => 'No se han recibido los datos']);
    exit;
}

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