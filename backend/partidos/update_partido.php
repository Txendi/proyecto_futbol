<?php

require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

/* Validacion de esos campos */
if (empty($rival) || empty($fecha) || empty($id_temporada)) {
    echo json_encode(['ok' => false, 'error' => 'Faltan datos obligatorios']);
    exit;
}

/* En caso de no recibir nada o un id no existente */
if (!$data || !isset($data['id_partido'])) {
    echo json_encode(['ok' => false, 'error' => 'Faltan datos o el ID del partido']);
    exit;
}

$id_partido = $data['id_partido'];
$rival = $data['rival'] ?? '';
$fecha = $data['fecha'] ?? '';
$competicion = $data['competicion'] ?? '';
$local_visitante = $data['local_visitante'] ?? 'local';
$goles_favor = $data['goles_favor'] ?? 0;
$goles_contra = $data['goles_contra'] ?? 0;
$id_temporada = $data['id_temporada'] ?? '';

$sql = "UPDATE partidos SET rival = ?, fecha = ?, competicion = ?, local_visitante = ?, goles_favor = ?, goles_contra = ?, id_temporada = ?
        WHERE id_partido = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssssiiii", $rival, $fecha, $competicion, $local_visitante, $goles_favor, $goles_contra, $id_temporada, $id_partido);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Partido actualizado correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();

?>