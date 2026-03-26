<?php

require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['id_jugador'])) {
    echo json_encode(['ok' => false, 'error' => 'No se ha encontrado el ID del jugador']);
    exit;
}

$id_jugador = $data['id_jugador'];

/* Segun el id que reciba eliminara cierto jugador (esta asociado con el boton, en el front) */
$sql = "DELETE FROM jugadores WHERE id_jugador = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_jugador);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Jugador eliminado correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();

?>