<?php
session_start();
require_once 'includes/db.php'; 

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$stmt = $conexion->prepare("SELECT estado FROM usuarios WHERE cedula_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$estado_actual = $stmt->fetchColumn();

if ($estado_actual != 1) {
    session_destroy();
    header("Location: index.php?error=cuenta_desactivada");
    exit;
}

$rol_usuario = $_SESSION['rol'];
?>