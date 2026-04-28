<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $cedula = trim($_POST['cedula']);

    // Verificamos que el usuario exista, coincida con su cédula y esté activo
    $stmt = $conexion->prepare("SELECT primer_nombre FROM usuarios WHERE correo = ? AND cedula_id = ? AND estado = 1");
    $stmt->execute([$correo, $cedula]);
    $user = $stmt->fetch();

    if ($user) {
        // COMO YA NO HAY TOKENS EN LA BD:
        // Creamos una autorización temporal en la memoria del navegador (Sesión)
        $_SESSION['reset_autorizado'] = true;
        $_SESSION['reset_cedula'] = $cedula;

        // Redirigimos a la pantalla de cambio de clave
        header("Location: ../restablecer.php");
        exit();
              
    } else {
        echo "<script>alert('Los datos ingresados no coinciden con ninguna cuenta activa en el sistema.'); window.history.back();</script>";
    }
}
?>