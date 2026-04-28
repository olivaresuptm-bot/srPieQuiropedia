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



// Función global para registrar eventos en la bitácora
if (!function_exists('registrar_bitacora')) {
    function registrar_bitacora($conn, $accion, $tabla, $registro_id = null, $detalle = null) {
        $usuario = $_SESSION['usuario_id'] ?? 'sistema';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        try {
            $sql = "INSERT INTO bitacora (usuario, accion, tabla, registro_id, detalle, ip) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$usuario, $accion, $tabla, $registro_id, $detalle, $ip]);
        } catch (PDOException $e) {
            // Manejo silencioso en caso de que la tabla bitacora no exista localmente aún
            error_log("Error en bitácora: " . $e->getMessage());
        }
    }
}
?>