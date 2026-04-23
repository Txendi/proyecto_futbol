<?php

header('Content-Type: application/json');

// Guardamos el Excel directamente en la carpeta data/ de Python
$directorio = 'C:/xampp/htdocs/ProyectoEibarPhyton/proyecto_Eibar_Phyton/data/';

if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

// Comprobamos que se ha enviado un archivo
if (!isset($_FILES['archivo'])) {
    echo json_encode(['ok' => false, 'error' => 'No se recibió ningún archivo']);
    exit;
}

$archivo = $_FILES['archivo'];
$nombre = basename($archivo['name']);
$rutaFinal = $directorio . $nombre; // Sin timestamp para que Python lo encuentre por nombre exacto
$extension = strtolower(pathinfo($rutaFinal, PATHINFO_EXTENSION));

// Solo permitimos Excel
if ($extension !== 'xlsx' && $extension !== 'xls') {
    echo json_encode(['ok' => false, 'error' => 'Solo se permiten archivos Excel (.xlsx, .xls)']);
    exit;
}

// Movemos el archivo a la carpeta data/ de Python
if (move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
    $id_partido = $_POST['id_partido'] ?? null;

    echo json_encode([
        'ok' => true,
        'ruta' => $rutaFinal,
        'id_partido' => $id_partido,
        'nombre' => $nombre
    ]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Error al guardar el archivo en el servidor']);
}

?>