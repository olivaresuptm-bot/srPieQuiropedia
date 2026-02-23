<?php
// controllers/recuperar_clave.php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim($_POST['cedula']);
    $correo = trim($_POST['correo']);
    $nueva_clave = $_POST['nueva_clave'];

    // 1. Validación de fortaleza de clave (Regex)
    $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\/\*\$\%]).{8,}$/';
    if (!preg_match($patron, $nueva_clave)) {
        echo "<script>alert('La clave no cumple con los requisitos de seguridad.'); window.history.back();</script>";
        exit;
    }

    try {
        // 2. Verificar si el usuario existe (Cédula y Correo)
        $sql_verif = "SELECT * FROM usuarios WHERE cedula_id = ? AND correo = ? LIMIT 1";
        $stmt_verif = $conexion->prepare($sql_verif);
        $stmt_verif->execute([$cedula, $correo]);
        $user = $stmt_verif->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Iniciar transacción para asegurar ambas tablas
            $conexion->beginTransaction();

            // 3. Registrar en la tabla recuperacion_claves
            $token = bin2hex(random_bytes(16)); // Generamos un token único
            $expiracion = date("Y-m-d H:i:s", strtotime('+1 hour')); // Expira en 1 hora
            
            $sql_recu = "INSERT INTO recuperacion_claves (correo, token, expiracion, utilizado) VALUES (?, ?, ?, 1)";
            $stmt_recu = $conexion->prepare($sql_recu);
            $stmt_recu->execute([$correo, $token, $expiracion]);

            // 4. Actualizar la clave en la tabla usuarios
            $password_hash = password_hash($nueva_clave, PASSWORD_BCRYPT);
            $sql_upd = "UPDATE usuarios SET password = ? WHERE correo = ?";
            $stmt_upd = $conexion->prepare($sql_upd);
            $stmt_upd->execute([$password_hash, $correo]);

            $conexion->commit();

            echo "<script>alert('¡Éxito! Su contraseña ha sido actualizada.'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Los datos (Cédula/Correo) no coinciden con nuestros registros.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        if ($conexion->inTransaction()) $conexion->rollBack();
        die("Error en la base de datos: " . $e->getMessage());
    }
}