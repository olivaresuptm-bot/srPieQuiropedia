<?php
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
?>