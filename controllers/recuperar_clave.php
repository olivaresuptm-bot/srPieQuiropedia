<?php
include '../includes/db.php';
include '../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $cedula = trim($_POST['cedula']);

    $stmt = $conexion->prepare("SELECT primer_nombre FROM usuarios WHERE correo = ? AND cedula_id = ? AND estado = 1");
    $stmt->execute([$correo, $cedula]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(16));
        $conexion->prepare("UPDATE usuarios SET token_recuperacion = ? WHERE correo = ?")->execute([$token, $correo]);

        $url = "srpiequiropedia.com/restablecer.php?token=$token";
        $cuerpo = "<h2>Hola {$user['primer_nombre']}</h2><p>Restablece tu clave aquí:</p><a href='$url'>Restablecer Contraseña</a>";

        enviarEmail($correo, "Recuperar Contraseña", $cuerpo);
        echo "<script>alert('Enlace enviado al correo.'); window.location.href='../index.php';</script>";
    } else {
        echo "<script>alert('Datos inválidos o cuenta no aprobada.'); window.history.back();</script>";
    }
}