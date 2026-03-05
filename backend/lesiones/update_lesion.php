<?php

require 'conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['id_lesion'])) {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
    exit;
}

$id_lesion = $data['id_lesion'];
$fecha_inicio = $data['fecha_inicio'] ?? '';
$fecha_fin = $data['fecha_fin'] ?? null;
$tipo_lesion = $data['tipo_lesion'] ?? '';
$gravedad = $data['gravedad'] ?? 'leve';
$fecha_prevista_retorno = $data['fecha_prevista_retorno'] ?? null;
$observaciones = $data['observaciones'] ?? '';

$sql = "UPDATE lesiones SET fecha_inicio = ?, fecha_fin = ?, tipo_lesion = ?, gravedad = ?, fecha_prevista_retorno = ?, observaciones = ? WHERE id_lesion = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssssssi", $fecha_inicio, $fecha_fin, $tipo_lesion, $gravedad, $fecha_prevista_retorno, $observaciones, $id_lesion);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Lesión actualizada correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();

?>