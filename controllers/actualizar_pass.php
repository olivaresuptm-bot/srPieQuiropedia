<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibimos el token oculto y la nueva contraseña del formulario
    $token = trim($_POST['token']);
    $nueva_clave = $_POST['nueva_clave'];

    // Validamos que la contraseña cumpla con tus requisitos de seguridad
    $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\/\*\$\%\!]).{8,}$/';
    if (!preg_match($patron, $nueva_clave)) {
        echo "<script>alert('La clave no cumple con los requisitos de seguridad (Mínimo 8 caracteres, mayúscula, minúscula, número y un carácter especial /*$%).'); window.history.back();</script>";
        exit;
    }

    try {
        // Verificamos si el token todavía existe y es válido
        $stmt = $conexion->prepare("SELECT primer_nombre FROM usuarios WHERE token_recuperacion = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            // Encriptamos la nueva contraseña
            $password_hash = password_hash($nueva_clave, PASSWORD_BCRYPT);
            
            // Actualizamos la clave en la base de datos y BORRAMOS el token para que no se pueda volver a usar
            $stmt_update = $conexion->prepare("UPDATE usuarios SET password = ?, token_recuperacion = NULL WHERE token_recuperacion = ?");
            $stmt_update->execute([$password_hash, $token]);

            // Redirigimos al login con mensaje de éxito (sin enviar correo)
            echo "<script>
                    alert('¡Excelente! Tu contraseña ha sido actualizada con éxito. Ya puedes iniciar sesión.'); 
                    window.location.href='../index.php';
                  </script>";
                  
        } else {
            // Si el token no existe (alguien intentó entrar directo al link viejo)
            echo "<script>alert('❌ El enlace de recuperación es inválido o ya fue utilizado.'); window.location.href='../index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error crítico: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>