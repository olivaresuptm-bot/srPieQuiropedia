<?php
session_start();

// Validamos que el usuario pasó por el proceso correctamente
if (!isset($_SESSION['reset_autorizado']) || !isset($_SESSION['reset_cedula'])) {
    die("Acceso denegado.");
}

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_clave = $_POST['nueva_clave'];
    $cedula_id = $_SESSION['reset_cedula'];

    // 1. Doble validación en el servidor
    $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\/\*\$\%\!]).{8,}$/';
    if (!preg_match($patron, $nueva_clave)) {
        echo "<script>alert('La clave no cumple con los requisitos de seguridad.'); window.history.back();</script>";
        exit;
    }

    // 2. Encriptamos la nueva clave
    $password_hash = password_hash($nueva_clave, PASSWORD_BCRYPT);

    try {
        // 3. Actualizamos en la Base de Datos
        $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE cedula_id = ?");
        $stmt->execute([$password_hash, $cedula_id]);

        // 4. DESTRUIMOS EL PASE VIP. El proceso ha terminado.
        unset($_SESSION['reset_autorizado']);
        unset($_SESSION['reset_cedula']);

        echo "<script>alert('✅ Contraseña actualizada con éxito. Ya puedes iniciar sesión con tu nueva clave.'); window.location.href='../index.php';</script>";

    } catch (PDOException $e) {
        echo "<script>alert('❌ Error de base de datos: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>