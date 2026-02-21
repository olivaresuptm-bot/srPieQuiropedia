<?php
// Datos de la base de datos
$host     = "localhost";
$db_name  = "srpiequiropedia"; 
$username = "root";            
$password = "";              

try {
    // Establecemos la conexión con PDO 
    $conexion = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    
    // Configuramos PDO para que lance excepciones en caso de errores
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    // Si la conexión falla
    die("Error crítico de conexión: " . $e->getMessage());
}
?>