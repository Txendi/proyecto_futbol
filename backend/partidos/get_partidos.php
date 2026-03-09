<?php
// mismas explicaciones sencillas en todos, basandote en jugadores
require '../conexion.php';

$partidos = [];

if (isset($_GET['rival'])) {
    $rival = '%' . $_GET['rival'] . '%';

    $sql = "SELECT id_partido, id_temporada, rival, fecha, competicion, local_visitante, goles_favor, goles_contra
            FROM partidos
            WHERE rival LIKE ?
            ORDER BY fecha DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $rival);

} elseif (isset($_GET['id_temporada'])) {
    $id_temporada = $_GET['id_temporada'];

    $sql = "SELECT id_partido, id_temporada, rival, fecha, competicion, local_visitante, goles_favor, goles_contra
            FROM partidos
            WHERE id_temporada = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_temporada);

} else {
    $sql = "SELECT id_partido, id_temporada, rival, fecha, competicion, local_visitante, goles_favor, goles_contra
            FROM partidos";
    $stmt = $conexion->prepare($sql);
}

$stmt->execute();
$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $partidos[] = $fila;
}

$stmt->close();

echo json_encode(['ok' => true, 'data' => $partidos]);

?>