<?php
require_once '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password_ingresada = $_POST['password'];

    try {
        // Esta es la busqueda del usuario por cedula o correo
        $sql = "SELECT * FROM usuarios WHERE cedula_id = ? OR correo = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$usuario, $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // VERIFICACIÓN de datos
        if ($user && password_verify($password_ingresada, $user['password'])) {
            $_SESSION['usuario_id'] = $user['cedula_id'];
            $_SESSION['rol'] = $user['rol'];
            
            // Esta seccion la hice para que salga el nombre del usuario en bienvenida
            $_SESSION['nombre'] = $user['primer_nombre'];
            $_SESSION['apellido'] = $user['primer_apellido'];
            
            header("Location: ../dashboard.php");
            exit;
        }
        //Mensaje de error si no concuerdan los parametros
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}