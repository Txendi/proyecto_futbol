<?php

if (!isset($_FILES['excel'])) {
    echo "No se ha seleccionado ningún archivo";
    exit;
}

$archivo = $_FILES['excel'];
$nombre = time() . "_" . $archivo['name'];

$extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

if ($extension != 'xls' && $extension != 'xlsx') {
    echo "Solo se permiten archivos Excel";
    exit;
}

$ruta = "../uploads/" . $nombre;

if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
    echo "Archivo subido correctamente";
} else {
    echo "Error al subir el archivo";
}
?>