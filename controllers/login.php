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

        // VERIFICACIÓN: Comparamos la clave ingresada con el hash de la BD
        if ($user && password_verify($password_ingresada, $user['password'])) {
            // Credenciales correctas
            $_SESSION['usuario_id'] = $user['cedula_id'];
            $_SESSION['rol'] = $user['rol'];
            header("Location: ../dashboard.php");
            exit;
        } else {
            header("Location: ../index.php?error=1");
        }
        //Mensaje de erro si no concuerdan los parametros con la BD
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}