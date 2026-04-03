<?php
session_start();
require 'conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$usuario = $data['usuario'] ?? '';
$password = $data['password'] ?? '';

$sql = "SELECT id_usuario, contraseña_hash, rol FROM usuarios WHERE nombre_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    // Permite tanto hash como texto plano para facilitar tus pruebas locales
    if ($password === $fila['contraseña_hash'] || password_verify($password, $fila['contraseña_hash'])) {
        $_SESSION['id_usuario'] = $fila['id_usuario'];
        $_SESSION['rol'] = $fila['rol'];
        echo json_encode(['ok' => true, 'rol' => $fila['rol']]);
    } else {
        echo json_encode(['ok' => false, 'error' => 'Contraseña incorrecta']);
    }
} else {
    echo json_encode(['ok' => false, 'error' => 'Usuario no encontrado']);
}
?>