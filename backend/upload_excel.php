<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_partido = $_POST['id_partido'] ?? null;

    if (!$id_partido) {
        echo "Error: no se ha seleccionado ningún partido.";
        exit;
    }

    if (!isset($_FILES['excel'])) {
        echo "Error: no se ha enviado ningún archivo.";
        exit;
    }

    $archivo = $_FILES['excel'];
    $nombreOriginal = $archivo['name'];
    $tmp = $archivo['tmp_name'];
    $error = $archivo['error'];

    if ($error !== 0) {
        echo "Error al subir el archivo.";
        exit;
    }

    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    $extensionesPermitidas = ['xls', 'xlsx'];

    if (!in_array($extension, $extensionesPermitidas)) {
        echo "Error: solo se permiten archivos .xls y .xlsx";
        exit;
    }

    $nuevoNombre = time() . "_" . basename($nombreOriginal);
    $rutaDestino = "../uploads/" . $nuevoNombre;

    if (move_uploaded_file($tmp, $rutaDestino)) {

        $sql = "INSERT INTO importaciones_excel (id_partido, nombre_archivo, ruta_archivo)
                VALUES (?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iss", $id_partido, $nuevoNombre, $rutaDestino);

        if ($stmt->execute()) {
            echo "Archivo subido y registrado correctamente.";
        } else {
            echo "El archivo se subió, pero hubo un error al guardarlo en la base de datos: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error al mover el archivo a la carpeta uploads.";
    }

    $conexion->close();
}
?>