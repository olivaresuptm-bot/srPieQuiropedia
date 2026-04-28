<?php
require_once '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiamos la entrada por seguridad: eliminamos cualquier cosa que NO sea un número
    $usuario_cedula = preg_replace('/[^0-9]/', '', $_POST['usuario']);
    $password_ingresada = $_POST['password'];

    try {
        // ACTUALIZACIÓN: La consulta ahora SOLO busca por cedula_id, ya no por correo
        $sql = "SELECT * FROM usuarios WHERE cedula_id = ? AND estado = 1 LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$usuario_cedula]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // VERIFICACIÓN de datos
        if ($user && password_verify($password_ingresada, $user['password'])) {
            $_SESSION['usuario_id'] = $user['cedula_id'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['nombre'] = $user['primer_nombre'];
            $_SESSION['apellido'] = $user['primer_apellido'];
            
            header("Location: ../modulos/gestion_pacientes.php");
            exit;
        } else {
            // Mensaje más claro y adaptado al uso exclusivo de cédula
            echo "<script>alert('Acceso denegado: Cédula no registrada, inactiva o contraseña incorrecta.'); window.location.href='../index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>