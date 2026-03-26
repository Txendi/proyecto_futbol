<?php

require '../conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

/* Recoje los datos, si no recibe nada, los mete vacios o 0*/
$id_temporada = $data['id_temporada'] ?? '';
$rival = $data['rival'] ?? '';
$fecha = $data['fecha'] ?? '';
$competicion = $data['competicion'] ?? '';
$local_visitante = $data['local_visitante'] ?? 'local';
$goles_favor = $data['goles_favor'] ?? 0;
$goles_contra = $data['goles_contra'] ?? 0;

/* Validacion para id rival y fecha, que no esten vacios */
if (empty($id_temporada) || empty($rival) || empty($fecha)) {
    echo json_encode(['ok' => false, 'error' => 'Faltan datos obligatorios']);
    exit;
}

/* Por si no recibe nada */
if (!$data) {
    echo json_encode(['ok' => false, 'error' => 'No se han recibido los datos']);
    exit;
}

$sql = "INSERT INTO partidos (id_temporada, rival, fecha, competicion, local_visitante, goles_favor, goles_contra) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("issssii", $id_temporada, $rival, $fecha, $competicion, $local_visitante, $goles_favor, $goles_contra);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Partido creado correctamente']);
} else {
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();

?>