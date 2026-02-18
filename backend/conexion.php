<?php

error_reporting(0);
ini_set('display_errors', 0);

// --- CONFIGURACIÓN WINDOWS (Casa) ---
// define('SERVIDOR', 'localhost');
// define('BBDD', 'sistema_scoring_futbol');
// define('USUARIO', 'root');
// define('CLAVE', '');

define('SERVIDOR', 'localhost');
define('BBDD', 'sistema_scoring_futbol');
define('USUARIO', 'root');
define('CLAVE', 'root');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$conexion = new mysqli(SERVIDOR, USUARIO, CLAVE, BBDD);
$conexion->set_charset('utf8mb4');

if ($conexion->connect_error) {
    echo json_encode(['ok' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

?>