<?php
date_default_timezone_set('America/Caracas');
// Conexion con la BD con Xampp
$host     = "localhost";
$db_name  = "srpiequiropedia"; 
$username = "root";            
$password = "";              

try {
    /* Establecemos la conexión con PDO (PHP Data Objects) es una capa que php 
    utiliza para comunicarse con la bd de manera segura*/
    $conexion = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    
    // Configuramos PDO para que lance excepciones en caso de errores
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("Error crítico de conexión: " . $e->getMessage());
}

// ============================================
// CONFIGURAR VARIABLES PARA LOS TRIGGERS
// ============================================
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pasar el usuario y IP actual a los triggers
if (isset($_SESSION['usuario_id'])) {
    $stmt = $conexion->prepare("SET @usuario_actual = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmt = $conexion->prepare("SET @ip_actual = ?");
    $stmt->execute([$ip]);
} else {
    $conexion->exec("SET @usuario_actual = NULL");
    $conexion->exec("SET @ip_actual = NULL");
}
// ============================================
?>