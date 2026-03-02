<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $nueva = $_POST['nueva_clave'];
    $hash  = password_hash($nueva, PASSWORD_BCRYPT);

    $stmt = $conexion->prepare("UPDATE usuarios SET password = ?, token_recuperacion = NULL WHERE token_recuperacion = ?");
    $stmt->execute([$hash, $token]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Contraseña actualizada.'); window.location.href='../index.php';</script>";
    } else {
        echo "<script>alert('Token inválido.'); window.location.href='../index.php';</script>";
    }
}