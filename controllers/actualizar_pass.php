<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiamos el token para evitar espacios accidentales
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $nueva = $_POST['nueva_clave'];

    if (empty($token)) {
        echo "<script>alert('Token no recibido.'); window.location.href='../index.php';</script>";
        exit;
    }

    // Encriptamos la clave con BCRYPT (el estándar actual)
    $hash = password_hash($nueva, PASSWORD_BCRYPT);

    try {
        // Buscamos el token en la columna correcta según tu SQL
        $stmt = $conexion->prepare("UPDATE usuarios SET password = ?, token_recuperacion = NULL WHERE token_recuperacion = ?");
        $stmt->execute([$hash, $token]);

        // Si rowCount es mayor a 0, significa que el token existía y se actualizó
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('¡Contraseña actualizada con éxito!'); window.location.href='../index.php';</script>";
        } else {
            // El token no existe o ya fue usado (ya es NULL en la BD)
            echo "<script>alert('El enlace es inválido o ya ha sido utilizado.'); window.location.href='../index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}