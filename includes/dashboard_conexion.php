<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Concatenamos nombre y apellido con un espacio en medio para que salga en la bienvenida 1er nombre y 1er apellido
$nombre = $_SESSION['nombre'] ?? 'Usuario';
$apellido = $_SESSION['apellido'] ?? '';

// Esta es la variable que se usan en el HTML para que salga el nombre completo en bienvenida
$nombre_completo = trim($nombre . " " . $apellido);
$rol_usuario = $_SESSION['rol'] ?? 'Personal';
?>