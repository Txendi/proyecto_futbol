<?php
// mismas explicaciones sencillas en todos, basandote en jugadores
require '../conexion.php';

$temporadas = [];

$sql = "SELECT id_temporada, nombre, fecha_inicio, fecha_fin, activa FROM temporadas";
$stmt = $conexion->prepare($sql);
$stmt->execute();

$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $temporadas[] = $fila;
}

$stmt->close();

echo json_encode(['ok' => true, 'data' => $temporadas]);

?>