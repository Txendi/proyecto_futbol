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
    // Añadimos 'csv' a las extensiones permitidas
    $extensionesPermitidas = ['xls', 'xlsx', 'csv'];

    if (!in_array($extension, $extensionesPermitidas)) {
        echo "Error: solo se permiten archivos .xls, .xlsx y .csv";
        exit;
    }

    $nuevoNombre = time() . "_" . basename($nombreOriginal);
    $rutaDestino = "../uploads/" . $nuevoNombre;

    if (move_uploaded_file($tmp, $rutaDestino)) {
        
        // 1. Inserción en el historial de importaciones
        $sql = "INSERT INTO importaciones_excel (id_partido, nombre_archivo, ruta_archivo) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iss", $id_partido, $nuevoNombre, $rutaDestino);

        if ($stmt->execute()) {
            
            // 2. Procesamiento del archivo si es CSV
            if ($extension === 'csv') {
                if (($gestor = fopen($rutaDestino, "r")) !== FALSE) {
                    fgetcsv($gestor, 10000, ","); // Saltar cabeceras

                    while (($fila_csv = fgetcsv($gestor, 10000, ",")) !== FALSE) {
                        $nombre_completo_csv = $fila_csv[0]; 
                        
                        // Buscar jugador
                        $sqlJugador = "SELECT id_jugador FROM jugadores WHERE CONCAT(nombre, ' ', apellidos) = ? LIMIT 1";
                        $stmtJ = $conexion->prepare($sqlJugador);
                        $stmtJ->bind_param("s", $nombre_completo_csv);
                        $stmtJ->execute();
                        $resJ = $stmtJ->get_result();
                        
                        if ($jugador = $resJ->fetch_assoc()) {
                            $id_jugador = $jugador['id_jugador'];
                            
                            // Ajustar estas posiciones al CSV real
                            $minutos = $fila_csv[8] ?? 0;
                            $goles = $fila_csv[9] ?? 0;
                            $asistencias = $fila_csv[11] ?? 0;

                            // Inserta en estadísticas
                            $sqlStats = "INSERT INTO estadisticas_jugador_partido (id_partido, id_jugador, minutos_jugados, goles, asistencias) 
                                         VALUES (?, ?, ?, ?, ?)";
                            $stmtS = $conexion->prepare($sqlStats);
                            $stmtS->bind_param("iiiii", $id_partido, $id_jugador, $minutos, $goles, $asistencias);
                            $stmtS->execute();
                        }
                    }
                    fclose($gestor);
                }
            }

            echo "Archivo subido y estadísticas registradas correctamente.";
        } else {
            echo "Error al guardar en la base de datos: " . $stmt->error;
        }
    } else {
        echo "Error al mover el archivo a la carpeta de destino.";
    }
}
?>