<?php

require '../conexion.php';

$lesiones = [];

if (isset($_GET['id_jugador'])) {
    $id_jugador = $_GET['id_jugador'];

    $sql = ('SELECT * FROM lesiones WHERE id_jugador = ?');
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_jugador);

} else {
    $sql = ('SELECT * FROM lesiones');
    $stmt = $conexion->prepare($sql);
}

$stmt->execute();
$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $lesiones[] = $fila;
}

$stmt->close();

echo json_encode(['ok' => true, 'data' => $lesiones]);

?>