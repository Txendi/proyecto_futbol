<?php
// mismas explicaciones sencillas en todos, basandote en jugadores
require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['id_lesion'])) {
    echo json_encode(['ok' => false, 'error' => 'No se ha recibido el ID de la lesión']);
    exit;
}

$id_lesion = $data['id_lesion'];

/* Segun el id que reciba eliminara cierto lesion */
$sql = "DELETE FROM lesiones WHERE id_lesion = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_lesion);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Lesión eliminada correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();

?>