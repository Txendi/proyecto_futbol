<?php

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// ===== CONFIGURACION DE LA BASE DE DATOS =====

/* Casa (Windows) */
define('SERVIDOR', 'localhost');
define('BBDD', 'sistema_futbol');
define('USUARIO', 'root');
define('CLAVE', '');

/* Clase */
/* define('SERVIDOR', 'endika1dawt');
define('BBDD', 'sistema_futbol');
define('USUARIO', 'root');
define('CLAVE', 'root'); */

// Manejo del preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Conexion a la base de datos
$conexion = new mysqli(SERVIDOR, USUARIO, CLAVE, BBDD);
$conexion->set_charset('utf8mb4');

if ($conexion->connect_error) {
    echo json_encode(['ok' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

?>