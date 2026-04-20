<?php

header('Content-Type: application/json');

// Creamos la carpeta uploads si no existe
$directorio = '../uploads/';
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

// Comprobamos que se ha enviado un archivo
if (!isset($_FILES['archivo'])) {
    echo json_encode(['ok' => false, 'error' => 'No se recibió ningún archivo']);
    exit;
}

$archivo   = $_FILES['archivo'];
$nombre    = basename($archivo['name']);
$rutaFinal = $directorio . time() . '_' . $nombre;
$extension = strtolower(pathinfo($rutaFinal, PATHINFO_EXTENSION));

// Solo para permitir Excel
if ($extension !== 'xlsx' && $extension !== 'xls') {
    echo json_encode(['ok' => false, 'error' => 'Solo se permiten archivos Excel (.xlsx, .xls)']);
    exit;
}

// Mover el archivo a la carpeta uploads
if (move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
    $id_partido = $_POST['id_partido'] ?? null;

    echo json_encode([
        'ok'         => true,
        'ruta'       => $rutaFinal,
        'id_partido' => $id_partido,
        'nombre'     => $nombre
    ]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Error al guardar el archivo en el servidor']);
}

?>