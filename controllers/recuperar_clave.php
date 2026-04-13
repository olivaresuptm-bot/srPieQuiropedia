<?php
include '../includes/db.php';

// Establecemos la zona horaria para que coincida con Mérida
date_default_timezone_set('America/Caracas');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $cedula = trim($_POST['cedula']);

    // Verificamos que el usuario exista, coincida con su cédula y esté activo
    $stmt = $conexion->prepare("SELECT primer_nombre FROM usuarios WHERE correo = ? AND cedula_id = ? AND estado = 1");
    $stmt->execute([$correo, $cedula]);
    $user = $stmt->fetch();

    if ($user) {
        // Generamos el token de seguridad
        $token = bin2hex(random_bytes(16));
        
        // Guardamos el token temporal en la base de datos para validar el cambio
        $stmt_update = $conexion->prepare("UPDATE usuarios SET token_recuperacion = ? WHERE correo = ?");
        $stmt_update->execute([$token, $correo]);

        // Redirección directa y automática a la página de restablecer
        header("Location: ../restablecer.php?token=$token");
        exit();
              
    } else {
        // En caso de error, mantenemos la alerta para informar que los datos son incorrectos
        echo "<script>alert('Los datos ingresados no coinciden con ninguna cuenta activa en el sistema.'); window.history.back();</script>";
    }
}
?>