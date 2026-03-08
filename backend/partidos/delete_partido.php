<?php

require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['id_partido'])) {
    echo json_encode(['ok' => false, 'error' => 'No se ha encontrado el ID del partido']);
    exit;
}

$id_partido = $data['id_partido'];

$sql = "DELETE FROM partidos WHERE id_partido = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_partido);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Partido eliminado correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();

?>