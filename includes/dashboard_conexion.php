<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Concatenamos nombre y apellido con un espacio en medio
$nombre = $_SESSION['nombre'] ?? 'Usuario';
$apellido = $_SESSION['apellido'] ?? '';

// Esta es la variable que usarás en el HTML
$nombre_completo = trim($nombre . " " . $apellido);
$rol_usuario = $_SESSION['rol'] ?? 'Personal';
?>