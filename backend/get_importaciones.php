<?php
require 'conexion.php';

$importaciones = [];

$sql = "SELECT 
            i.id_importacion,
            i.nombre_archivo,
            i.ruta_archivo,
            i.fecha_subida,
            p.fecha AS fecha_partido,
            p.rival
        FROM importaciones_excel i
        INNER JOIN partidos p ON i.id_partido = p.id_partido
        ORDER BY i.fecha_subida DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $importaciones[] = $fila;
}

$stmt->close();
$conexion->close();

echo json_encode([
    'ok' => true,
    'data' => $importaciones
]);
?>