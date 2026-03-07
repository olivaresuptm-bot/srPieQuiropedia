<?php
require_once '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password_ingresada = $_POST['password'];

    try {
        $sql = "SELECT * FROM usuarios WHERE (cedula_id = ? OR correo = ?) AND estado = 1 LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$usuario, $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // VERIFICACIÓN de datos
        if ($user && password_verify($password_ingresada, $user['password'])) {
            $_SESSION['usuario_id'] = $user['cedula_id'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['nombre'] = $user['primer_nombre'];
            $_SESSION['apellido'] = $user['primer_apellido'];
            
            header("Location: ../dashboard.php");
            exit;
        } else {
           
            echo "<script>alert('Acceso denegado: Usuario inactivo o credenciales incorrectas.'); window.location.href='../index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}