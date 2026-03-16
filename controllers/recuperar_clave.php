<?php
include '../includes/db.php';
include '../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $cedula = trim($_POST['cedula']);

    // Verificamos que el usuario exista y esté activo
    $stmt = $conexion->prepare("SELECT primer_nombre FROM usuarios WHERE correo = ? AND cedula_id = ? AND estado = 1");
    $stmt->execute([$correo, $cedula]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(16));
        
        // Guardamos el token en la columna 'token_recuperacion' de la tabla 'usuarios'
        $stmt_update = $conexion->prepare("UPDATE usuarios SET token_recuperacion = ? WHERE correo = ?");
        $stmt_update->execute([$token, $correo]);

        // IMPORTANTE: Aqui ajustamos la url segun el dominio
        $url = "srpiequiropedia.com/restablecer.php?token=$token";
        
        $cuerpo = "
            <div style='font-family: sans-serif; border-top: 4px solid #4a90e2; padding: 20px;'>
                <h2>Hola {$user['primer_nombre']},</h2>
                <p>Has solicitado restablecer tu contraseña en <strong>Sr. Pie</strong>.</p>
                <p>Haz clic en el siguiente botón para continuar:</p>
                <a href='$url' style='background:#4a90e2; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>Restablecer Contraseña</a>
                <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
            </div>";

        if (enviarEmail($correo, "Recuperar Contraseña - Sr. Pie", $cuerpo)) {
            echo "<script>alert('Enlace de recuperación enviado a tu correo.'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Error al enviar el correo. Intenta más tarde.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Los datos no coinciden con una cuenta activa.'); window.history.back();</script>";
    }
}
?>