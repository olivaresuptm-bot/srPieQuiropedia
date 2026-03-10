<?php
include '../includes/db.php';

// Verificamos si el token viene por la URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // 1. Buscamos si existe un usuario con ese token de aprobación y estado pendiente (0)
        $stmt = $conexion->prepare("SELECT primer_nombre, primer_apellido, rol FROM usuarios WHERE token = ? AND estado = 0");
        $stmt->execute([$token]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // 2. Definimos las variables ANTES del include para que el HTML las reconozca
            $nombre_completo = $usuario['primer_nombre'] . " " . $usuario['primer_apellido'];
            $rol_usuario = ucfirst($usuario['rol']); // Usamos la variable que espera aprobar.php

            // 3. Activamos al usuario y borramos el token de la base de datos
            $update = $conexion->prepare("UPDATE usuarios SET estado = 1, token = NULL WHERE token = ?");
            $update->execute([$token]);

            // 4. Incluimos el diseño visual de éxito
            include '../acceso_aprobar.php';
            exit; 
            
        } else {
            // Si el token no existe, es incorrecto o ya fue usado (estado ya es 1)
            mostrarError("El enlace es inválido, ha expirado o el usuario ya fue aprobado anteriormente.");
        }
    } catch (PDOException $e) {
        mostrarError("Error en el servidor: " . $e->getMessage());
    }
} else {
    mostrarError("No se proporcionó un token de seguridad válido.");
}

function mostrarError($mensaje) {
    include '../acceso_negado.php';
    exit;
}
?>