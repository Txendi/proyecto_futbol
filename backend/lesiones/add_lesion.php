<?php
// mismas explicaciones sencillas en todos, basandote en jugadores
require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['ok' => false, 'error' => 'No se han recibido los datos']);
    exit;
}

$id_jugador = $data['id_jugador'] ?? '';
$fecha_inicio = $data['fecha_inicio'] ?? '';
$fecha_fin = $data['fecha_fin'] ?? null;
$tipo_lesion = $data['tipo_lesion'] ?? '';
$gravedad = $data['gravedad'] ?? 'leve';
$fecha_prevista_retorno = $data['fecha_prevista_retorno'] ?? null;
$observaciones = $data['observaciones'] ?? '';

$sql = "INSERT INTO lesiones (id_jugador, fecha_inicio, fecha_fin, tipo_lesion, gravedad, fecha_prevista_retorno, observaciones) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("issssss", $id_jugador, $fecha_inicio, $fecha_fin, $tipo_lesion, $gravedad, $fecha_prevista_retorno, $observaciones);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Lesion registrada correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();

?>