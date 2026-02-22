<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    $correo = $_POST['correo'];
    $nueva_clave = $_POST['nueva_clave'];

    try {
        // 1. Verificamos si el usuario existe con esa cédula y correo
        $sql = "SELECT * FROM usuarios WHERE cedula_id = ? AND correo = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$cedula, $correo]);
        $user = $stmt->fetch();

        if ($user) {
            // 2. Encriptamos la nueva clave
            $password_hash = password_hash($nueva_clave, PASSWORD_BCRYPT);

            // 3. Actualizamos en la base de datos
            $update = "UPDATE usuarios SET password = ? WHERE cedula_id = ?";
            $stmt_update = $conexion->prepare($update);
            $stmt_update->execute([$password_hash, $cedula]);

            echo "<script>alert('Contraseña actualizada con éxito'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Los datos no coinciden con nuestros registros'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}