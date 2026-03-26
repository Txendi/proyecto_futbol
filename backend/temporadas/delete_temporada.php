<?php
// mismas explicaciones sencillas en todos, basandote en jugadores
require '../conexion.php';

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data || !isset($data['id_temporada'])) {
        echo json_encode(['ok' => false, 'error' => 'No se ha encontrado el ID de la temporada']);
        exit;
    }

    $id_temporada = $data['id_temporada'];

    // Comprueba si la temporada tiene algun partido asociado
    $sqlComprobacion = "SELECT COUNT(*) AS total FROM partidos WHERE id_temporada = ?";
    $stmtComprobacion = $conexion->prepare($sqlComprobacion);
    $stmtComprobacion->bind_param("i", $id_temporada);
    $stmtComprobacion->execute();
    $resultado = $stmtComprobacion->get_result();
    $fila = $resultado->fetch_assoc();

    if ($fila['total'] > 0) {
        echo json_encode([
            'ok' => false,
            'error' => 'No se puede eliminar la temporada porque tiene partidos asociados'
        ]);
        exit;
    }

    $sql = "DELETE FROM temporadas WHERE id_temporada = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_temporada);

    if ($stmt->execute()) {
        echo json_encode(['ok' => true, 'mensaje' => 'Temporada eliminada correctamente']);
    } else {
        echo json_encode(['ok' => false, 'error' => $stmt->error]);
    }

} catch (Exception $e) {
    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage()
    ]);
}

$stmtComprobacion->close();
$stmt->close();

?>